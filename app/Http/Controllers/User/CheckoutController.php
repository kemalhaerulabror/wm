<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\NotificationController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    /**
     * Tampilkan form checkout
     */
    public function index()
    {
        // Periksa apakah ini pembelian langsung
        $directBuy = session('direct_buy', false);
        $directBuyProduct = session('direct_buy_product', null);
        
        if ($directBuy && $directBuyProduct) {
            // Ini adalah pembelian langsung, tidak perlu memeriksa keranjang
            
            // Periksa stok produk terlebih dahulu
            $product = Product::find($directBuyProduct['id']);
            if (!$product) {
                return redirect()->route('products.category', 'all')->with('error', 'Produk tidak ditemukan.');
            }
            
            if ($product->stock < $directBuyProduct['quantity']) {
                return redirect()->route('products.detail', $product->slug)->with('error', 'Maaf, stok produk tidak mencukupi atau telah habis.');
            }
            
            // Buat array cartItems khusus dengan produk dari session
            $cartItems = collect([(object)[
                'product' => (object)[
                    'id' => $directBuyProduct['id'],
                    'name' => $directBuyProduct['name'],
                    'image_url' => $directBuyProduct['image_url'],
                ],
                'product_id' => $directBuyProduct['id'],
                'price' => $directBuyProduct['price'],
                'quantity' => $directBuyProduct['quantity'],
            ]]);
            
            // Hitung total
            $total = $directBuyProduct['price'] * $directBuyProduct['quantity'];
            
            // Simpan slug produk untuk tombol kembali
            $productSlug = $directBuyProduct['slug'];
            
            return view('user.checkout.index', compact('cartItems', 'total', 'directBuy', 'productSlug'));
        } else {
            // Ini adalah checkout normal dari keranjang
            // Ambil semua item di keranjang user
            $cartItems = Cart::where('user_id', Auth::id())->get();
            
            // Jika keranjang kosong, redirect ke halaman cart dengan pesan
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong. Tambahkan produk sebelum checkout.');
            }
            
            // Periksa stok semua produk di keranjang sebelum menampilkan halaman checkout
            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                
                if (!$product) {
                    return redirect()->route('cart.index')->with('error', 'Salah satu produk tidak ditemukan.');
                }
                
                if ($product->stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Maaf, stok produk "' . $product->name . '" tidak mencukupi atau telah habis.');
                }
            }
            
            // Hitung total
            $total = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            
            return view('user.checkout.index', compact('cartItems', 'total'));
        }
    }

    /**
     * Tambahkan produk ke checkout langsung dari halaman detail
     */
    /**
 * Tambahkan produk ke checkout langsung dari halaman detail
 */
public function buyNow($id)
{
    // Pastikan user sudah login
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    // Ambil produk berdasarkan ID
    $product = Product::findOrFail($id);
    
    // Periksa stok produk
    if ($product->stock < 1) {
        return redirect()->route('products.detail', $product->slug)
            ->with('error', 'Maaf, stok produk tidak tersedia.');
    }
    
    // PERBAIKAN: Hapus session lama terlebih dahulu untuk menghindari konflik
    session()->forget(['direct_buy', 'direct_buy_product']);
    
    // Simpan informasi produk yang akan dibeli langsung di session
    $directBuyData = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'image_url' => $product->image_url,
        'quantity' => 1,
        'slug' => $product->slug
    ];
    
    // PERBAIKAN: Set session dengan method yang lebih eksplisit
    session()->put('direct_buy', true);
    session()->put('direct_buy_product', $directBuyData);
    
    // PERBAIKAN: Regenerate session ID untuk memastikan data tersimpan
    session()->save();
    
    // Log untuk debugging
    Log::info('Buy Now Session Set', [
        'product_id' => $product->id,
        'session_direct_buy' => session('direct_buy'),
        'session_product' => session('direct_buy_product'),
        'user_id' => Auth::id()
    ]);
    
    // Redirect ke halaman checkout
    return redirect()->route('checkout.index');
}

    /**
     * Proses checkout dan buat order
     */
    public function store(Request $request)
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function() {
            // Periksa apakah ini pembelian langsung
            $directBuy = session('direct_buy', false);
            $directBuyProduct = session('direct_buy_product', null);
            
            if ($directBuy && $directBuyProduct) {
                // Ini adalah pembelian langsung
                // Hitung total
                $total = $directBuyProduct['price'] * $directBuyProduct['quantity'];
                
                // Periksa stok produk - dengan mengunci baris untuk menghindari race condition
                $product = Product::lockForUpdate()->find($directBuyProduct['id']);
                
                // Pastikan produk ditemukan dan stok mencukupi
                if (!$product) {
                    return redirect()->back()->with('error', 'Produk tidak ditemukan.');
                }
                
                if ($product->stock < $directBuyProduct['quantity']) {
                    return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi atau telah habis.');
                }
                
                // Buat nomor order unik
                $lastOrderId = Order::max('id') ?? 0;
                $nextId = $lastOrderId + 1;
                $randomString = strtoupper(Str::random(6));
                $orderNumber = 'WP-' . $randomString;
                
                // Buat order baru
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'total_amount' => $total,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                
                // Buat order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $directBuyProduct['id'],
                    'product_name' => $directBuyProduct['name'],
                    'price' => $directBuyProduct['price'],
                    'quantity' => $directBuyProduct['quantity'],
                    'subtotal' => $directBuyProduct['price'] * $directBuyProduct['quantity'],
                ]);
                
                // Update stok produk
                $product->stock -= $directBuyProduct['quantity'];
                $product->sold += $directBuyProduct['quantity'];
                $product->save();
                
                // Hapus data pembelian langsung dari session
                session()->forget(['direct_buy', 'direct_buy_product']);
                
            } else {
                // Ini adalah checkout dari keranjang
                // Ambil cart items
                $cartItems = Cart::where('user_id', Auth::id())->get();
                
                // Jika keranjang kosong, redirect ke halaman cart dengan pesan
                if ($cartItems->isEmpty()) {
                    return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
                }
                
                // Cek stok semua produk di keranjang sebelum memproses
                foreach ($cartItems as $item) {
                    // Kunci baris produk untuk menghindari race condition
                    $product = Product::lockForUpdate()->find($item->product_id);
                    
                    if (!$product) {
                        return redirect()->back()->with('error', 'Salah satu produk tidak ditemukan.');
                    }
                    
                    if ($product->stock < $item->quantity) {
                        return redirect()->back()->with('error', 'Maaf, stok produk "' . $product->name . '" tidak mencukupi atau telah habis.');
                    }
                }
                
                // Hitung total
                $total = $cartItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                });
                
                // Buat nomor order unik
                $lastOrderId = Order::max('id') ?? 0;
                $nextId = $lastOrderId + 1;
                $randomString = strtoupper(Str::random(6));
                $orderNumber = 'WP-' . $randomString;
                
                // Buat order baru
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'total_amount' => $total,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                
                // Buat order items
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->price * $item->quantity,
                    ]);
                    
                    // Update stok produk
                    $product = Product::find($item->product_id);
                    $product->stock -= $item->quantity;
                    $product->sold += $item->quantity;
                    $product->save();
                }
                
                // Hapus cart items
                Cart::where('user_id', Auth::id())->delete();
            }
            
            // Set konfigurasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
            
            // URL untuk callback, finish dan cancel
            $finishUrl = route('checkout.success', $order->id);
            
            // Data untuk Midtrans
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? '',
                ],
                'callbacks' => [
                    'finish' => $finishUrl
                ],
                'enable_payments' => [
                    'credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 
                    'shopeepay', 'gopay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay',
                    'other_qris', 'indomaret', 'alfamart'
                ]
            ];
            
            // Tambahkan item details untuk Midtrans
            if ($directBuy && $directBuyProduct) {
                // Untuk pembelian langsung
                $midtransParams['item_details'][] = [
                    'id' => $directBuyProduct['id'],
                    'price' => (int) $directBuyProduct['price'],
                    'quantity' => $directBuyProduct['quantity'],
                    'name' => $directBuyProduct['name'],
                ];
            } else {
                // Untuk checkout dari keranjang
                foreach ($cartItems as $item) {
                    $midtransParams['item_details'][] = [
                        'id' => $item->product_id,
                        'price' => (int) $item->price,
                        'quantity' => $item->quantity,
                        'name' => $item->product->name,
                    ];
                }
            }
            
            try {
                // Get Snap Payment Page URL
                $snap = Snap::createTransaction($midtransParams);
                $paymentUrl = $snap->redirect_url;
                
                // Jika ada token, simpan juga token untuk verifikasi
                if (!empty($snap->token)) {
                    $order->payment_code = $snap->token;
                }
                
                // Update order dengan payment URL
                $order->payment_url = $paymentUrl;
                $order->save();
                
                // Redirect ke halaman pembayaran
                return redirect()->route('checkout.payment', $order->id);
                
            } catch (\Exception $e) {
                // Jika gagal, tampilkan error
                Log::error('Midtrans Error', ['message' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        });
    }

    /**
     * Tampilkan halaman pembayaran
     */
    public function payment($id)
    {
        $order = Order::findOrFail($id);
        
        // Pastikan user hanya bisa melihat order miliknya
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('user.checkout.payment', compact('order'));
    }

    /**
     * Callback dari Midtrans untuk update status pembayaran
     */
    public function callback(Request $request)
    {
        // Log semua data yang masuk untuk debugging
        Log::info('Midtrans Callback Raw', ['data' => $request->all()]);
        
        try {
            // Ambil order number dari request
            $orderNumber = $request->input('order_id');
            
            // Ambil transaction_status dari request
            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');
            $fraudStatus = $request->input('fraud_status', null);
            
            // Informasi tambahan untuk deteksi payment method
            $vaNumbers = $request->input('va_numbers', []);
            $permataVaNumber = $request->input('permata_va_number', null);
            $paymentCode = $request->input('payment_code', null);
            $billKey = $request->input('bill_key', null);
            $billerCode = $request->input('biller_code', null);
            
            Log::info('Midtrans Callback Params', [
                'order_id' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'va_numbers' => $vaNumbers,
                'permata_va_number' => $permataVaNumber,
                'payment_code' => $paymentCode,
                'bill_key' => $billKey,
                'biller_code' => $billerCode,
                'raw_data' => $request->all()
            ]);
            
            // Cari order berdasarkan order number
            $order = Order::where('order_number', $orderNumber)->first();
            
            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderNumber]);
                return response('Order not found', 404);
            }
            
            // Jika fraud_status tersedia dan status-nya adalah deny, maka deny transaksi
            if ($fraudStatus == 'deny') {
                Log::warning('Fraud transaction detected', ['order_id' => $orderNumber]);
                $order->payment_status = 'deny';
                $order->status = 'cancelled';
                $order->save();
                
                // Tambahkan notifikasi pesanan dibatalkan
                if ($order->user_id) {
                    $notificationController = new NotificationController();
                    $notificationController->createOrderCancelledNotification($order);
                }
                
                return response('OK, fraud', 200);
            }
            
            $previousStatus = $order->payment_status;
            
            // Update status berdasarkan transaction_status
            if (!empty($transactionStatus)) {
                switch ($transactionStatus) {
                    case 'capture':
                    case 'settlement':
                        $order->payment_status = 'paid';
                        $order->status = 'processing';
                        break;
                    case 'pending':
                        $order->payment_status = 'pending';
                        $order->status = 'pending';
                        break;
                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $order->payment_status = 'failed';
                        $order->status = 'cancelled';
                        break;
                }
            }
            
            // Save order
            $order->save();
            
            // Tambahkan notifikasi sesuai perubahan status
            if ($order->user_id) {
                $notificationController = new NotificationController();
                
                if ($previousStatus != 'paid' && $order->payment_status == 'paid') {
                    // Jika pembayaran sukses
                    $notificationController->createOrderSuccessNotification($order);
                    
                    // Log untuk memastikan notifikasi pembayaran dibuat
                    Log::info('Notification for paid order created', [
                        'order_id' => $order->id,
                        'payment_status' => $order->payment_status
                    ]);
                } elseif ($previousStatus != 'failed' && $order->payment_status == 'failed') {
                    // Jika pembayaran gagal/dibatalkan
                    $notificationController->createOrderCancelledNotification($order);
                }
            }
            
            // Setelah update status, dapatkan detail transaksi untuk mendapatkan payment method yang akurat
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                // Gunakan direct API untuk mendapatkan detail transaksi terbaru
                $this->updatePaymentMethodFromTransaction($order);
            }
            
            Log::info('Order updated successfully', ['order' => $order->toArray()]);
            
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Error in callback', ['error' => $e->getMessage()]);
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Tampilkan halaman checkout sukses
     */
    public function success($id)
    {
        $order = Order::with('items')->findOrFail($id);
        
        // Pastikan user hanya bisa melihat order miliknya
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Log untuk debugging
        Log::info('Checkout Success', [
            'query_params' => request()->all(),
            'order_status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method
        ]);
        
        // Update status pesanan jika datang dari callback Midtrans
        if (request()->has('transaction_status') || request()->has('status_code')) {
            $transactionStatus = request()->query('transaction_status');
            $statusCode = request()->query('status_code');
            $paymentType = request()->query('payment_type');
            
            Log::info('Trying to update from redirect', [
                'transaction_status' => $transactionStatus,
                'status_code' => $statusCode,
                'payment_type' => $paymentType,
                'all_params' => request()->all()
            ]);
            
            // Update status berdasarkan parameter
            if ($statusCode == '200' || in_array($transactionStatus, ['settlement', 'capture'])) {
                $previousStatus = $order->payment_status; // simpan status sebelumnya
                
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();
                
                // Update payment method dengan data dari Midtrans API
                $this->updatePaymentMethodFromTransaction($order);
                
                // Buat notifikasi jika status sebelumnya bukan paid
                if ($previousStatus != 'paid' && $order->user_id) {
                    $notificationController = new NotificationController();
                    $notificationController->createOrderSuccessNotification($order);
                    
                    Log::info('Notification for paid order created on success page', [
                        'order_id' => $order->id,
                        'previous_status' => $previousStatus,
                        'current_status' => $order->payment_status
                    ]);
                }
            }
        }
        
        // Fix untuk sandbox: Jika payment_method masih NULL atau default padahal pembayaran sudah berhasil
        if ($order->payment_status == 'paid' && (empty($order->payment_method) || $order->payment_method == 'Pembayaran via Midtrans')) {
            Log::info('Payment method still NULL or default despite paid status. Getting transaction details from Midtrans');
            
            // Coba dapatkan detail transaksi langsung dari Midtrans API
            $this->updatePaymentMethodFromTransaction($order);
        }
        
        // Jika status pembayaran sudah paid, tapi belum ada notifikasi sukses, buat notifikasinya
        if ($order->payment_status == 'paid' && $order->user_id) {
            $notificationCount = Notification::where('user_id', $order->user_id)
                ->where('related_model_type', 'Order')
                ->where('related_model_id', $order->id)
                ->where('type', 'success')
                ->count();
                
            if ($notificationCount == 0) {
                $notificationController = new NotificationController();
                $notificationController->createOrderSuccessNotification($order);
                
                Log::info('Created missing success notification for paid order', [
                    'order_id' => $order->id,
                    'payment_status' => $order->payment_status
                ]);
            }
        }
        
        // Reload order untuk memastikan data terbaru
        $order = Order::with('items')->findOrFail($id);
        
        return view('user.checkout.success', compact('order'));
    }

    /**
     * Fix order yang memiliki payment_method NULL
     */
    private function fixPaymentMethod($id = null)
    {
        try {
            if ($id) {
                // Jika ID diberikan, hanya update order tersebut
                $order = Order::find($id);
                if ($order && $order->payment_status == 'paid' && (empty($order->payment_method) || $order->payment_method == 'Pembayaran via Midtrans')) {
                    // Gunakan API Midtrans untuk mendapatkan detail transaksi
                    $updated = $this->updatePaymentMethodFromTransaction($order);
                    
                    // Jika tidak berhasil update dari Midtrans API, gunakan default
                    if (!$updated) {
                        $order->payment_method = 'Pembayaran via Midtrans';
                        $order->save();
                    }
                    
                    Log::info('Fixed payment method for single order', ['order_id' => $id, 'method' => $order->payment_method]);
                }
            } else {
                // Update semua order yang payment_status = paid tetapi payment_method NULL atau default
                $orders = Order::where('payment_status', 'paid')
                    ->where(function($query) {
                        $query->whereNull('payment_method')
                            ->orWhere('payment_method', 'Pembayaran via Midtrans');
                    })
                    ->get();
                
                $count = 0;
                foreach ($orders as $order) {
                    // Coba dapatkan detail transaksi dari Midtrans API
                    $updated = $this->updatePaymentMethodFromTransaction($order);
                    
                    // Jika tidak berhasil update dari Midtrans API, gunakan default
                    if (!$updated) {
                        $order->payment_method = 'Pembayaran via Midtrans';
                        $order->save();
                    }
                    
                    $count++;
                }
                
                Log::info('Fixed payment method for multiple orders', ['count' => $count]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error fixing payment method', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get transaction status dari Midtrans API
     */
    private function getTransactionStatus($orderNumber)
    {
        try {
            // Set konfigurasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            
            // Melakukan request ke Midtrans Status API
            $url = Config::$isProduction 
                ? 'https://api.midtrans.com/v2/'.$orderNumber.'/status' 
                : 'https://api.sandbox.midtrans.com/v2/'.$orderNumber.'/status';
            
            $auth = base64_encode(Config::$serverKey . ':');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . $auth
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response === false) {
                Log::error('Error fetching transaction status', ['order_number' => $orderNumber, 'error' => curl_error($ch)]);
                return null;
            }
            
            $transaction = json_decode($response, true);
            Log::info('Transaction details from Midtrans', ['data' => $transaction]);
            
            return $transaction;
        } catch (\Exception $e) {
            Log::error('Error getting transaction status', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Update payment method berdasarkan detail transaksi
     */
    private function updatePaymentMethodFromTransaction($order)
    {
        try {
            // Mendapatkan detail transaksi dari Midtrans
            $transaction = $this->getTransactionStatus($order->order_number);
            
            if (!$transaction) {
                return false;
            }
            
            // Log seluruh data transaksi untuk debugging
            Log::info('Transaction data for payment method', ['transaction' => $transaction]);
            
            // Prioritaskan menggunakan nilai 'channel' jika tersedia (sesuai di dashboard Midtrans)
            if (isset($transaction['channel']) && !empty($transaction['channel'])) {
                $paymentMethod = $transaction['channel'];
                Log::info('Using channel value from transaction', ['channel' => $paymentMethod]);
            }
            // Gunakan payment_type jika channel tidak tersedia
            elseif (isset($transaction['payment_type'])) {
                // Coba dapatkan channel yang sesuai dengan format di dashboard Midtrans
                switch ($transaction['payment_type']) {
                    case 'bank_transfer':
                        $paymentMethod = "Bank Transfer";
                        break;
                    case 'gopay':
                        $paymentMethod = "GoPay";
                        break;
                    case 'qris':
                        $paymentMethod = "QRIS";
                        break;
                    case 'shopeepay':
                        $paymentMethod = "ShopeePay";
                        break;
                    case 'cstore':
                        $store = isset($transaction['store']) ? $transaction['store'] : '';
                        if ($store == 'indomaret') {
                            $paymentMethod = "Indomaret";
                        } elseif ($store == 'alfamart') {
                            $paymentMethod = "Alfamart";
                        } else {
                            $paymentMethod = ucfirst($store);
                        }
                        break;
                    case 'echannel':
                        $paymentMethod = "Mandiri Bill";
                        break;
                    case 'credit_card':
                        $paymentMethod = "Credit Card";
                        break;
                    default:
                        $paymentMethod = ucfirst(str_replace('_', ' ', $transaction['payment_type']));
                }
            }
            // Jika tidak tersedia payment_type, coba cek channel_response_code
            elseif (isset($transaction['channel_response_code'])) {
                $channel = strtolower($transaction['channel_response_code']);
                
                if (strpos($channel, 'bca') !== false) {
                    $paymentMethod = "Bank Transfer";
                } elseif (strpos($channel, 'bri') !== false) {
                    $paymentMethod = "Bank Transfer";
                } elseif (strpos($channel, 'bni') !== false) {
                    $paymentMethod = "Bank Transfer";
                } elseif (strpos($channel, 'permata') !== false) {
                    $paymentMethod = "Bank Transfer";
                } elseif (strpos($channel, 'mandiri') !== false) {
                    $paymentMethod = "Bank Transfer";
                } elseif (strpos($channel, 'echannel') !== false || strpos($channel, 'mandiri_bill') !== false) {
                    $paymentMethod = "Mandiri Bill";
                } elseif (strpos($channel, 'gopay') !== false) {
                    $paymentMethod = "GoPay";
                } elseif (strpos($channel, 'shopeepay') !== false) {
                    $paymentMethod = "ShopeePay";
                } elseif (strpos($channel, 'qris') !== false) {
                    $paymentMethod = "QRIS";
                } elseif (strpos($channel, 'indomaret') !== false) {
                    $paymentMethod = "Indomaret";
                } elseif (strpos($channel, 'alfamart') !== false) {
                    $paymentMethod = "Alfamart";
                } else {
                    $paymentMethod = ucfirst($channel);
                }
            }
            // Default fallback
            else {
                $paymentMethod = 'Pembayaran via Midtrans';
            }
            
            $order->payment_method = $paymentMethod;
            $order->save();
            Log::info('Updated payment method from transaction details', ['method' => $paymentMethod]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating payment method from transaction', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Membatalkan pesanan dan mengembalikan stok produk
     */
    public function cancel($id)
    {
        try {
            // Jika request menggunakan GET, redirect ke halaman cancelled
            if (request()->isMethod('get')) {
                return redirect()->route('checkout.cancelled', $id);
            }
        
            $order = Order::with('items')->findOrFail($id);
            
            // Pastikan user hanya bisa membatalkan pesanan miliknya
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
            
            // Pastikan pesanan masih dalam status pending/belum dibayar
            if ($order->payment_status !== 'pending') {
                return redirect()->back()->with('error', 'Hanya pesanan dengan status menunggu pembayaran yang dapat dibatalkan.');
            }
            
            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->sold -= $item->quantity;
                    $product->save();
                    
                    Log::info('Stok dikembalikan', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item->quantity,
                        'new_stock' => $product->stock
                    ]);
                }
            }
            
            // Update status pesanan menjadi cancelled
            $order->status = 'cancelled';
            $order->payment_status = 'cancelled';
            $order->save();
            
            // Tambahkan notifikasi pesanan dibatalkan
            $notificationController = new NotificationController();
            $notificationController->createOrderCancelledNotification($order);
            
            Log::info('Pesanan dibatalkan', ['order_id' => $order->id, 'order_number' => $order->order_number]);
            
            // Redirect ke halaman cancelled khusus
            return redirect()->route('checkout.cancelled', $order->id);
        } catch (\Exception $e) {
            Log::error('Error membatalkan pesanan', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan halaman pesanan dibatalkan
     */
    public function cancelled($id)
    {
        $order = Order::findOrFail($id);
        
        // Pastikan user hanya bisa melihat order miliknya
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('user.checkout.cancelled', compact('order'));
    }

    /**
     * Generate dan download invoice pesanan
     */
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        
        // Pastikan user hanya bisa melihat invoice miliknya
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
        
        // Pastikan pesanan sudah dibayar
        if ($order->payment_status !== 'paid') {
            return redirect()->route('checkout.success', $order->id)
                ->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah dibayar.');
        }
        
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('user.checkout.invoice', compact('order'));
        
        return $pdf->stream('Invoice-'.$order->order_number.'.pdf');
    }
} 