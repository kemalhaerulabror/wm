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
     * Tampilkan form checkout.
     * Perbaikan: Mengambil data dari sesi flash, dan menghapus sesi setelah digunakan.
     */
    public function index()
    {
        $directBuy = session('direct_buy', false);
        $directBuyProduct = session('direct_buy_product', null);
        
        if ($directBuy && $directBuyProduct) {
            // Ini adalah pembelian langsung, tidak perlu memeriksa keranjang
            $product = Product::find($directBuyProduct['id']);
            if (!$product) {
                return redirect()->route('products.category', 'all')->with('error', 'Produk tidak ditemukan.');
            }
            if ($product->stock < $directBuyProduct['quantity']) {
                return redirect()->route('products.detail', $product->slug)->with('error', 'Maaf, stok produk tidak mencukupi atau telah habis.');
            }
            
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
            
            $total = $directBuyProduct['price'] * $directBuyProduct['quantity'];
            $productSlug = $directBuyProduct['slug'];
            
            // Hapus data sesi pembelian langsung setelah digunakan untuk mencegah
            // masalah saat pengguna kembali ke halaman utama.
            session()->forget(['direct_buy', 'direct_buy_product']);
            
            return view('user.checkout.index', compact('cartItems', 'total', 'directBuy', 'productSlug'));
        } else {
            // Ini adalah checkout normal dari keranjang
            $cartItems = Cart::where('user_id', Auth::id())->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong. Tambahkan produk sebelum checkout.');
            }
            
            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                if (!$product) {
                    return redirect()->route('cart.index')->with('error', 'Salah satu produk tidak ditemukan.');
                }
                if ($product->stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Maaf, stok produk "' . $product->name . '" tidak mencukupi atau telah habis.');
                }
            }
            
            $total = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            
            return view('user.checkout.index', compact('cartItems', 'total'));
        }
    }

    /**
     * Tambahkan produk ke checkout langsung dari halaman detail.
     * Perbaikan: Menggunakan session flash untuk memastikan data tersedia di redirect berikutnya.
     */
    public function buyNow(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);

        if ($product->stock < $quantity) {
            return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi.');
        }
        
        // Simpan data di session flash, yang hanya bertahan satu request.
        session()->flash('direct_buy', true);
        session()->flash('direct_buy_product', [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image_url' => $product->image_url,
            'quantity' => $quantity,
            'slug' => $product->slug
        ]);
        
        return redirect()->route('checkout.index');
    }

    /**
     * Proses checkout dan buat order.
     * Perbaikan: Mengambil data dari session dan menghapusnya setelah order dibuat.
     */
    public function store(Request $request)
    {
        return DB::transaction(function() {
            $directBuy = session('direct_buy', false);
            $directBuyProduct = session('direct_buy_product', null);
            
            if ($directBuy && $directBuyProduct) {
                // Logika pembelian langsung
                $total = $directBuyProduct['price'] * $directBuyProduct['quantity'];
                $product = Product::lockForUpdate()->find($directBuyProduct['id']);
                
                if (!$product) {
                    return redirect()->back()->with('error', 'Produk tidak ditemukan.');
                }
                if ($product->stock < $directBuyProduct['quantity']) {
                    return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi atau telah habis.');
                }
                
                $lastOrderId = Order::max('id') ?? 0;
                $randomString = strtoupper(Str::random(6));
                $orderNumber = 'WP-' . $randomString;
                
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'total_amount' => $total,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $directBuyProduct['id'],
                    'product_name' => $directBuyProduct['name'],
                    'price' => $directBuyProduct['price'],
                    'quantity' => $directBuyProduct['quantity'],
                    'subtotal' => $directBuyProduct['price'] * $directBuyProduct['quantity'],
                ]);
                
                $product->stock -= $directBuyProduct['quantity'];
                $product->sold += $directBuyProduct['quantity'];
                $product->save();
                
                // Hapus sesi setelah order dibuat
                session()->forget(['direct_buy', 'direct_buy_product']);
            } else {
                // Logika checkout dari keranjang
                $cartItems = Cart::where('user_id', Auth::id())->get();
                
                if ($cartItems->isEmpty()) {
                    return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
                }
                
                foreach ($cartItems as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (!$product) {
                        return redirect()->back()->with('error', 'Salah satu produk tidak ditemukan.');
                    }
                    if ($product->stock < $item->quantity) {
                        return redirect()->back()->with('error', 'Maaf, stok produk "' . $product->name . '" tidak mencukupi atau telah habis.');
                    }
                }
                
                $total = $cartItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                });
                
                $lastOrderId = Order::max('id') ?? 0;
                $randomString = strtoupper(Str::random(6));
                $orderNumber = 'WP-' . $randomString;
                
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'total_amount' => $total,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->price * $item->quantity,
                    ]);
                    
                    $product = Product::find($item->product_id);
                    $product->stock -= $item->quantity;
                    $product->sold += $item->quantity;
                    $product->save();
                }
                
                Cart::where('user_id', Auth::id())->delete();
            }
            
            // Konfigurasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
            $finishUrl = route('checkout.success', $order->id);
            
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
                'callbacks' => [ 'finish' => $finishUrl ],
                'enable_payments' => ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'shopeepay', 'gopay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'other_qris', 'indomaret', 'alfamart']
            ];
            
            if ($directBuy && $directBuyProduct) {
                $midtransParams['item_details'][] = ['id' => $directBuyProduct['id'], 'price' => (int) $directBuyProduct['price'], 'quantity' => $directBuyProduct['quantity'], 'name' => $directBuyProduct['name'],];
            } else {
                foreach ($cartItems as $item) {
                    $midtransParams['item_details'][] = ['id' => $item->product_id, 'price' => (int) $item->price, 'quantity' => $item->quantity, 'name' => $item->product->name,];
                }
            }
            
            try {
                $snap = Snap::createTransaction($midtransParams);
                $paymentUrl = $snap->redirect_url;
                if (!empty($snap->token)) {
                    $order->payment_code = $snap->token;
                }
                $order->payment_url = $paymentUrl;
                $order->save();
                return redirect()->route('checkout.payment', $order->id);
            } catch (\Exception $e) {
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
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        return view('user.checkout.payment', compact('order'));
    }

    /**
     * Callback dari Midtrans
     */
    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Raw', ['data' => $request->all()]);
        try {
            $orderNumber = $request->input('order_id');
            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');
            $fraudStatus = $request->input('fraud_status', null);
            $vaNumbers = $request->input('va_numbers', []);
            $permataVaNumber = $request->input('permata_va_number', null);
            $paymentCode = $request->input('payment_code', null);
            $billKey = $request->input('bill_key', null);
            $billerCode = $request->input('biller_code', null);
            
            Log::info('Midtrans Callback Params', ['order_id' => $orderNumber, 'transaction_status' => $transactionStatus, 'payment_type' => $paymentType, 'va_numbers' => $vaNumbers, 'permata_va_number' => $permataVaNumber, 'payment_code' => $paymentCode, 'bill_key' => $billKey, 'biller_code' => $billerCode, 'raw_data' => $request->all()]);
            
            $order = Order::where('order_number', $orderNumber)->first();
            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderNumber]);
                return response('Order not found', 404);
            }
            if ($fraudStatus == 'deny') {
                Log::warning('Fraud transaction detected', ['order_id' => $orderNumber]);
                $order->payment_status = 'deny';
                $order->status = 'cancelled';
                $order->save();
                if ($order->user_id) {
                    $notificationController = new NotificationController();
                    $notificationController->createOrderCancelledNotification($order);
                }
                return response('OK, fraud', 200);
            }
            $previousStatus = $order->payment_status;
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
            $order->save();
            if ($order->user_id) {
                $notificationController = new NotificationController();
                if ($previousStatus != 'paid' && $order->payment_status == 'paid') {
                    $notificationController->createOrderSuccessNotification($order);
                    Log::info('Notification for paid order created', ['order_id' => $order->id, 'payment_status' => $order->payment_status]);
                } elseif ($previousStatus != 'failed' && $order->payment_status == 'failed') {
                    $notificationController->createOrderCancelledNotification($order);
                }
            }
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
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
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        Log::info('Checkout Success', ['query_params' => request()->all(), 'order_status' => $order->status, 'payment_status' => $order->payment_status, 'payment_method' => $order->payment_method]);
        if (request()->has('transaction_status') || request()->has('status_code')) {
            $transactionStatus = request()->query('transaction_status');
            $statusCode = request()->query('status_code');
            $paymentType = request()->query('payment_type');
            Log::info('Trying to update from redirect', ['transaction_status' => $transactionStatus, 'status_code' => $statusCode, 'payment_type' => $paymentType, 'all_params' => request()->all()]);
            if ($statusCode == '200' || in_array($transactionStatus, ['settlement', 'capture'])) {
                $previousStatus = $order->payment_status;
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();
                $this->updatePaymentMethodFromTransaction($order);
                if ($previousStatus != 'paid' && $order->user_id) {
                    $notificationController = new NotificationController();
                    $notificationController->createOrderSuccessNotification($order);
                    Log::info('Notification for paid order created on success page', ['order_id' => $order->id, 'previous_status' => $previousStatus, 'current_status' => $order->payment_status]);
                }
            }
        }
        if ($order->payment_status == 'paid' && (empty($order->payment_method) || $order->payment_method == 'Pembayaran via Midtrans')) {
            Log::info('Payment method still NULL or default despite paid status. Getting transaction details from Midtrans');
            $this->updatePaymentMethodFromTransaction($order);
        }
        if ($order->payment_status == 'paid' && $order->user_id) {
            $notificationCount = Notification::where('user_id', $order->user_id)
                ->where('related_model_type', 'Order')
                ->where('related_model_id', $order->id)
                ->where('type', 'success')
                ->count();
            if ($notificationCount == 0) {
                $notificationController = new NotificationController();
                $notificationController->createOrderSuccessNotification($order);
                Log::info('Created missing success notification for paid order', ['order_id' => $order->id, 'payment_status' => $order->payment_status]);
            }
        }
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
                $order = Order::find($id);
                if ($order && $order->payment_status == 'paid' && (empty($order->payment_method) || $order->payment_method == 'Pembayaran via Midtrans')) {
                    $updated = $this->updatePaymentMethodFromTransaction($order);
                    if (!$updated) {
                        $order->payment_method = 'Pembayaran via Midtrans';
                        $order->save();
                    }
                    Log::info('Fixed payment method for single order', ['order_id' => $id, 'method' => $order->payment_method]);
                }
            } else {
                $orders = Order::where('payment_status', 'paid')
                    ->where(function($query) {
                        $query->whereNull('payment_method')->orWhere('payment_method', 'Pembayaran via Midtrans');
                    })
                    ->get();
                $count = 0;
                foreach ($orders as $order) {
                    $updated = $this->updatePaymentMethodFromTransaction($order);
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
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            $url = Config::$isProduction ? 'https://api.midtrans.com/v2/'.$orderNumber.'/status' : 'https://api.sandbox.midtrans.com/v2/'.$orderNumber.'/status';
            $auth = base64_encode(Config::$serverKey . ':');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Basic ' . $auth]);
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
            $transaction = $this->getTransactionStatus($order->order_number);
            if (!$transaction) {
                return false;
            }
            Log::info('Transaction data for payment method', ['transaction' => $transaction]);
            if (isset($transaction['channel']) && !empty($transaction['channel'])) {
                $paymentMethod = $transaction['channel'];
                Log::info('Using channel value from transaction', ['channel' => $paymentMethod]);
            } elseif (isset($transaction['payment_type'])) {
                switch ($transaction['payment_type']) {
                    case 'bank_transfer': $paymentMethod = "Bank Transfer"; break;
                    case 'gopay': $paymentMethod = "GoPay"; break;
                    case 'qris': $paymentMethod = "QRIS"; break;
                    case 'shopeepay': $paymentMethod = "ShopeePay"; break;
                    case 'cstore': $store = isset($transaction['store']) ? $transaction['store'] : '';
                        if ($store == 'indomaret') { $paymentMethod = "Indomaret"; } elseif ($store == 'alfamart') { $paymentMethod = "Alfamart"; } else { $paymentMethod = ucfirst($store); }
                        break;
                    case 'echannel': $paymentMethod = "Mandiri Bill"; break;
                    case 'credit_card': $paymentMethod = "Credit Card"; break;
                    default: $paymentMethod = ucfirst(str_replace('_', ' ', $transaction['payment_type']));
                }
            } elseif (isset($transaction['channel_response_code'])) {
                $channel = strtolower($transaction['channel_response_code']);
                if (strpos($channel, 'bca') !== false) { $paymentMethod = "Bank Transfer"; } elseif (strpos($channel, 'bri') !== false) { $paymentMethod = "Bank Transfer"; } elseif (strpos($channel, 'bni') !== false) { $paymentMethod = "Bank Transfer"; } elseif (strpos($channel, 'permata') !== false) { $paymentMethod = "Bank Transfer"; } elseif (strpos($channel, 'mandiri') !== false) { $paymentMethod = "Bank Transfer"; } elseif (strpos($channel, 'echannel') !== false || strpos($channel, 'mandiri_bill') !== false) { $paymentMethod = "Mandiri Bill"; } elseif (strpos($channel, 'gopay') !== false) { $paymentMethod = "GoPay"; } elseif (strpos($channel, 'shopeepay') !== false) { $paymentMethod = "ShopeePay"; } elseif (strpos($channel, 'qris') !== false) { $paymentMethod = "QRIS"; } elseif (strpos($channel, 'indomaret') !== false) { $paymentMethod = "Indomaret"; } elseif (strpos($channel, 'alfamart') !== false) { $paymentMethod = "Alfamart"; } else { $paymentMethod = ucfirst($channel); }
            } else {
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
            if (request()->isMethod('get')) {
                return redirect()->route('checkout.cancelled', $id);
            }
            $order = Order::with('items')->findOrFail($id);
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
            if ($order->payment_status !== 'pending') {
                return redirect()->back()->with('error', 'Hanya pesanan dengan status menunggu pembayaran yang dapat dibatalkan.');
            }
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->sold -= $item->quantity;
                    $product->save();
                    Log::info('Stok dikembalikan', ['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => $item->quantity, 'new_stock' => $product->stock]);
                }
            }
            $order->status = 'cancelled';
            $order->payment_status = 'cancelled';
            $order->save();
            $notificationController = new NotificationController();
            $notificationController->createOrderCancelledNotification($order);
            Log::info('Pesanan dibatalkan', ['order_id' => $order->id, 'order_number' => $order->order_number]);
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
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
        if ($order->payment_status !== 'paid') {
            return redirect()->route('checkout.success', $order->id)->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah dibayar.');
        }
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('user.checkout.invoice', compact('order'));
        return $pdf->stream('Invoice-'.$order->order_number.'.pdf');
    }
}