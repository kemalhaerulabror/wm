@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6">Checkout</h1>
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded relative mb-4 sm:mb-6">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline text-xs sm:text-sm">{{ session('error') }}</span>
    </div>
    @endif
    
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-base sm:text-lg font-medium mb-3 sm:mb-4">Konfirmasi Pemesanan</h2>
                
                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    
                    {{-- PERBAIKAN DARI SINI --}}
                    @if(isset($directBuy) && $directBuy)
                    <input type="hidden" name="direct_buy" value="1">
                    <input type="hidden" name="product_id" value="{{ $cartItems->first()->product_id }}">
                    <input type="hidden" name="quantity" value="{{ $cartItems->first()->quantity }}">
                    @endif
                    {{-- SAMPAI SINI --}}
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">Setelah melakukan checkout, Anda akan diarahkan ke halaman pembayaran untuk menyelesaikan pesanan Anda.</p>
                        <p class="text-sm text-gray-600 mb-4">Pastikan Anda memiliki metode pembayaran yang didukung oleh Midtrans (kartu kredit, transfer bank, e-wallet, dll).</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                        @if(isset($directBuy) && $directBuy && isset($productSlug))
                        <a href="{{ route('products.detail', $productSlug) }}" class="py-2 px-4 border border-gray-300 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-50 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Kembali ke Produk</span>
                        </a>
                        @else
                        <a href="{{ route('cart.index') }}" class="py-2 px-4 border border-gray-300 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-50 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Kembali ke Keranjang</span>
                        </a>
                        @endif
                        
                        <button type="submit" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-md text-white text-sm font-medium flex items-center justify-center">
                            <span>Lanjutkan ke Pembayaran</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6 sticky top-20">
                <h2 class="text-base sm:text-lg font-medium mb-3 sm:mb-4">Ringkasan Order</h2>
                
                <div class="divide-y divide-gray-200">
                    @foreach($cartItems as $item)
                    <div class="py-3 flex items-start">
                        <div class="flex-shrink-0 h-14 w-14 sm:h-16 sm:w-16">
                            <img class="h-full w-full object-cover rounded" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs sm:text-sm text-gray-800 line-clamp-2">{{ $item->product->name }}</p>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $item->quantity }} x {{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</span>
                                <span class="text-xs font-medium">{{ 'Rp ' . number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 space-y-2">
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="flex justify-between text-base font-bold">
                            <span class="text-gray-800">Total</span>
                            <span>{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 