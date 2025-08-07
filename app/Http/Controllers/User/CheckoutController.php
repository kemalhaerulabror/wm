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
    public function index(Request $request)
{
    // Cek apakah ada parameter 'direct_buy' di URL.
    // Ini menentukan apakah ini alur "Beli Sekarang" atau checkout dari keranjang.
    $directBuy = $request->has('direct_buy');

    if ($directBuy) {
        // Ambil ID produk dan kuantitas dari URL.
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::find($productId);
        
        // Logika validasi stok untuk pembelian langsung.
        // Jika produk tidak ditemukan atau stok tidak cukup, kembalikan dengan pesan error.
        if (!$product) {
            return redirect()->route('products.category', 'all')->with('error', 'Produk tidak ditemukan.');
        }
        if ($product->stock < $quantity) {
            return redirect()->route('products.detail', $product->slug)->with('error', 'Maaf, stok produk tidak mencukupi atau telah habis.');
        }
        
        // Buat objek produk palsu yang sama dengan struktur cartItems untuk di-pass ke view.
        // Ini agar view checkout bisa menampilkan detail produk dengan benar.
        $cartItems = collect([(object)[
            'product' => $product,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => $quantity,
        ]]);
        
        $total = $product->price * $quantity;
        $productSlug = $product->slug;
        
        return view('user.checkout.index', compact('cartItems', 'total', 'directBuy', 'productSlug'));
    } else {
        // Logika checkout normal dari keranjang (TIDAK ADA PERUBAHAN)
        $cartItems = Cart::where('user_id', Auth::id())->get();
        // ... kode lainnya ...
    }
}

    /**
     * Tambahkan produk ke checkout langsung dari halaman detail
     */
    public function buyNow(Request $request, $id)
{
    // Cek apakah pengguna sudah login.
    // Ini memastikan hanya pengguna terautentikasi yang bisa melakukan pembelian.
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    // Ambil produk dari database. Jika tidak ditemukan, akan otomatis menampilkan 404.
    $product = Product::findOrFail($id);
    
    // Ambil jumlah kuantitas dari form, default-nya 1.
    // Ini mengantisipasi jika ada form yang mengirimkan kuantitas lebih dari satu.
    $quantity = $request->input('quantity', 1);
    
    // Validasi stok produk. Jika tidak cukup, kembalikan ke halaman sebelumnya dengan pesan error.
    if ($product->stock < $quantity) {
        return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi.');
    }
    
    // Alihkan pengguna ke halaman checkout dengan menyertakan data produk di URL.
    // Data ini akan diakses oleh method `index` di halaman checkout.
    return redirect()->route('checkout.index', [
        'direct_buy' => true,
        'product_id' => $product->id,
        'quantity' => $quantity
    ]);
}

    /**
     * Proses checkout dan buat order
     */
    public function store(Request $request)
{
    // Menggunakan DB::transaction untuk memastikan semua operasi database berhasil.
    // Jika ada yang gagal, semua perubahan akan di-rollback.
    return DB::transaction(function() use ($request) {
        // Cek apakah ini alur "Beli Sekarang" dengan memeriksa parameter dari request.
        $directBuy = $request->has('product_id');
        
        if ($directBuy) {
            // Ambil ID produk dan kuantitas dari request, bukan dari session.
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity');

            // Hitung total dan ambil produk dengan `lockForUpdate` untuk mencegah
            // masalah saat ada beberapa user yang membeli produk yang sama secara bersamaan.
            $product = Product::lockForUpdate()->find($productId);
            
            // ... (logika pembuatan order dan order item tetap sama)
            // ...

            // Perubahan ini membuat alur "Beli Sekarang" lebih stabil.
        } else {
            // Logika checkout dari keranjang (TIDAK ADA PERUBAHAN)
            $cartItems = Cart::where('user_id', Auth::id())->get();
            // ... kode lainnya ...
        }
        
        // ... (Logika Midtrans tetap sama)
        // ...
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