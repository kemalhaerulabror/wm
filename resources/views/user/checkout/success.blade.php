@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6">
        @if($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
        Pesanan Dibatalkan
        @else
        Pesanan Berhasil
        @endif
    </h1>
    
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 sm:p-6">
                <div class="text-center mb-6">
                    @if($order->payment_status == 'paid')
                    <i class="fas fa-check-circle text-green-500 text-3xl sm:text-5xl mb-3"></i>
                    @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                    <i class="fas fa-times-circle text-red-500 text-3xl sm:text-5xl mb-3"></i>
                    @else
                    <i class="fas fa-clock text-yellow-500 text-3xl sm:text-5xl mb-3"></i>
                    @endif
                    <h2 class="text-base sm:text-lg font-medium text-gray-800">
                        @if($order->payment_status == 'paid')
                        Terima Kasih Atas Pesanan Anda!
                        @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                        Pesanan Telah Dibatalkan
                        @else
                        Menunggu Pembayaran
                        @endif
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2">Nomor Pesanan: <span class="font-semibold">{{ $order->order_number }}</span></p>
                    
                    @if($order->payment_status == 'paid')
                    <div class="mt-4 inline-flex items-center bg-green-100 text-green-800 text-sm font-medium px-4 py-2 rounded-full">
                        <i class="fas fa-check-circle mr-2"></i> Pembayaran Berhasil
                    </div>
                    @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                    <div class="mt-4 inline-flex items-center bg-red-100 text-red-800 text-sm font-medium px-4 py-2 rounded-full">
                        <i class="fas fa-times-circle mr-2"></i> Pesanan Dibatalkan
                    </div>
                    @else
                    <div class="mt-4 inline-flex items-center bg-yellow-100 text-yellow-800 text-sm font-medium px-4 py-2 rounded-full">
                        <i class="fas fa-clock mr-2"></i> Menunggu Konfirmasi Pembayaran
                    </div>
                    @endif
                </div>
                
                <div class="border-t border-gray-200 py-4">
                    <h3 class="text-sm font-medium text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jml</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="px-3 py-3 text-xs">
                                        <div class="flex items-center">
                                            @if($item->product)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-8 h-8 rounded object-cover mr-2">
                                            @endif
                                            <span class="text-gray-800">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-right text-gray-500">{{ $item->formatted_price }}</td>
                                    <td class="px-3 py-3 text-xs text-right text-gray-500">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-xs text-right font-medium text-gray-800">{{ $item->formatted_subtotal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-3 py-2 text-xs text-right font-bold">Total:</td>
                                    <td class="px-3 py-2 text-xs text-right font-bold">{{ $order->formatted_total }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-xs font-medium text-gray-800 mb-2">Informasi Pembeli</h3>
                        <div class="text-xs text-gray-600">
                            <p class="mb-1">{{ Auth::user()->name }}</p>
                            <p class="mb-1">{{ Auth::user()->email }}</p>
                            <p>{{ Auth::user()->phone ?? 'Tidak ada nomor telepon' }}</p>
                        </div>
                    </div>
                    
                    <div class="border
                    @if($order->payment_status == 'paid')
                        border-green-200 bg-green-50
                    @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                    @else
                    @endif
                    rounded-lg p-4">
                        <h3 class="text-xs font-medium text-gray-800 mb-2">Status Pesanan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Status Pesanan:</span>
                                @if($order->status == 'cancelled')
                                <span class="font-medium text-red-600">Dibatalkan</span>
                                @else
                                <span class="font-medium text-blue-600">{{ $order->status_label }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Status Pembayaran:</span>
                                @if($order->payment_status == 'paid')
                                <span class="font-medium text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i> Sudah Dibayar
                                </span>
                                @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                                <span class="font-medium text-red-600 flex items-center">
                                    <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                                </span>
                                @else
                                <span class="font-medium text-yellow-600">
                                    Menunggu Pembayaran
                                </span>
                                @endif
                            </div>
                            @if($order->payment_method)
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Metode Pembayaran:</span>
                                <span class="font-medium 
                                @if($order->payment_status == 'paid')
                                    text-green-600
                                @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                                @else
                                @endif">{{ $order->payment_method }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Tanggal Pesanan:</span>
                                <span class="font-medium text-gray-600">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                            </div>
                            @if($order->payment_status == 'paid')
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Terima kasih:</span>
                                <span class="font-medium text-green-600">Pembayaran Anda telah diterima</span>
                            </div>
                            @endif
                            @if($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Catatan:</span>
                                <span class="font-medium text-red-600">Pesanan ini telah dibatalkan</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($order->payment_status == 'paid')
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                    <p class="text-sm text-green-700 font-medium mb-2">Pembayaran Anda telah berhasil dikonfirmasi!</p>
                    <p class="text-xs text-green-600 mb-3">Pesanan Anda sedang diproses dan akan segera ditindaklanjuti.</p>
                    
                    <a href="{{ route('checkout.invoice', $order->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Download Invoice
                    </a>
                </div>
                @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-center">
                    <p class="text-sm text-red-700 font-medium mb-2">Pesanan ini telah dibatalkan</p>
                    <p class="text-xs text-red-600 mb-3">Stok produk telah dikembalikan ke inventaris.</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="flex justify-center space-x-4 mb-6">
            @if($order->payment_status == 'paid')
            <a href="{{ route('profile.orders') }}" class="py-2 px-4 bg-white border border-blue-500 text-blue-600 hover:bg-blue-50 rounded-md text-xs sm:text-sm font-medium inline-flex items-center">
                <i class="fas fa-shopping-bag mr-2"></i>
                Cek Pesanan Saya
            </a>
            
            <a href="{{ route('home') }}" class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs sm:text-sm font-medium">
                Lanjutkan Belanja
            </a>
            @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed' || $order->status == 'cancelled')
            <a href="{{ route('home') }}" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs sm:text-sm font-medium">
                Kembali ke Beranda
            </a>
            @else
            @if($order->payment_url)
            <a href="{{ $order->payment_url }}" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs sm:text-sm font-medium">
                Lanjutkan Pembayaran
            </a>
            @endif
            
            <a href="{{ route('home') }}" class="py-2 px-4 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md text-xs sm:text-sm font-medium">
                Kembali ke Beranda
            </a>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Jika redirect dari Midtrans payment, refresh halaman setelah beberapa detik
    // untuk memastikan status pembayaran sudah terupdate
    if (window.location.search.includes('status_code=200') || 
        window.location.search.includes('transaction_status=settlement')) {
        setTimeout(function() {
            window.location.href = window.location.pathname;
        }, 3000);
    }
});
</script>

@endsection 