<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\NotificationController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminOrderController extends Controller
{
    /**
     * Tampilkan form pembuatan pesanan baru oleh admin
     */
    public function create()
    {
        // Ambil produk yang masih tersedia stoknya
        $products = Product::where('stock', '>', 0)
                        ->where('status', true)
                        ->get();
                        
        return view('admin.orders.create', compact('products'));
    }

    /**
     * Simpan pesanan baru yang dibuat admin
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,midtrans',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Mulai transaksi database
        return DB::transaction(function() use ($request) {
            // Ambil produk yang dipilih
            $product = Product::find($request->product_id);
            
            // Periksa stok produk
            if ($product->stock < $request->quantity) {
                return redirect()->back()->with('error', 'Stok produk tidak mencukupi');
            }
            
            // Hitung total harga
            $total = $product->price * $request->quantity;
            
            // Buat nomor order unik
            $randomString = strtoupper(Str::random(6));
            $orderNumber = 'WP-ADM-' . $randomString;
            
            // Buat order baru tanpa user_id
            $order = Order::create([
                'user_id' => null, // Tidak menggunakan user_id
                'order_number' => $orderNumber,
                'total_amount' => $total,
                'payment_status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
                'payment_method' => $request->payment_method === 'cash' ? 'Cash (Admin)' : 'Pembayaran via Midtrans',
                'status' => $request->payment_method === 'cash' ? 'processing' : 'pending',
                'created_by_admin_id' => Auth::guard('admin')->id(),
                // Simpan informasi pelanggan sebagai metadata
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
            ]);
            
            // Buat order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'subtotal' => $product->price * $request->quantity,
            ]);
            
            // Update stok produk
            $product->stock -= $request->quantity;
            $product->sold += $request->quantity;
            $product->save();
            
            // Jika pembayaran cash, langsung selesai
            if ($request->payment_method === 'cash') {
                return redirect()->route('admin.orders.create')
                    ->with('success', 'Pesanan berhasil dibuat dengan pembayaran cash');
            }
            
            // Jika pembayaran Midtrans, lanjutkan seperti checkout biasa
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
            
            // URL untuk callback dan finish
            $finishUrl = route('admin.orders.success', $order->id);
            
            // Data untuk Midtrans
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '',
                ],
                'callbacks' => [
                    'finish' => $finishUrl
                ],
                'enable_payments' => [
                    'credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 
                    'shopeepay', 'gopay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay',
                    'other_qris', 'indomaret', 'alfamart'
                ],
                'item_details' => [
                    [
                        'id' => $product->id,
                        'price' => (int) $product->price,
                        'quantity' => $request->quantity,
                        'name' => $product->name,
                    ]
                ]
            ];
            
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
                return redirect()->route('admin.orders.payment', $order->id);
                
            } catch (\Exception $e) {
                // Jika gagal, tampilkan error
                Log::error('Midtrans Error', ['message' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        });
    }
    
    /**
     * Tampilkan halaman pembayaran Midtrans
     */
    public function payment($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.payment', compact('order'));
    }
    
    /**
     * Tampilkan daftar semua pesanan yang dibuat oleh admin
     */
    public function adminCreated()
    {
        $orders = Order::whereNotNull('created_by_admin_id')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        return view('admin.orders.admin-created', compact('orders'));
    }
    
    /**
     * Cetak invoice untuk pesanan
     */
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('admin.orders.invoice', compact('order'));
        
        return $pdf->stream('Invoice-'.$order->order_number.'.pdf');
    }
    
    /**
     * Tampilkan halaman sukses setelah pembayaran Midtrans
     */
    public function success($id)
    {
        $order = Order::findOrFail($id);
        
        // Log untuk debugging
        Log::info('Admin Checkout Success', [
            'query_params' => request()->all(),
            'order_status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method
        ]);
        
        // Update status pesanan jika datang dari callback Midtrans
        if (request()->has('transaction_status') || request()->has('status_code')) {
            $transactionStatus = request()->query('transaction_status');
            $statusCode = request()->query('status_code');
            
            // Update status berdasarkan parameter
            if ($statusCode == '200' || in_array($transactionStatus, ['settlement', 'capture'])) {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();
                
                // Update payment method dengan data dari Midtrans API
                $this->updatePaymentMethodFromTransaction($order);
            }
        }
        
        // Fix untuk sandbox: Jika payment_method masih NULL atau default padahal pembayaran sudah berhasil
        if ($order->payment_status == 'paid' && (empty($order->payment_method) || $order->payment_method == 'Pembayaran via Midtrans')) {
            Log::info('Payment method still NULL or default despite paid status. Getting transaction details from Midtrans');
            
            // Coba dapatkan detail transaksi langsung dari Midtrans API
            $this->updatePaymentMethodFromTransaction($order);
        }
        
        return view('admin.orders.success', compact('order'));
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
} 