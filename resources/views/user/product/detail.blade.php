@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Product Image -->
            <div class="md:w-2/5">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-auto object-cover">
            </div>
            
            <!-- Product Details -->
            <div class="md:w-3/5 p-4 md:p-6">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>
                
                <div class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                    {{ $product->formatted_price }}
                </div>
                
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-700 mb-2">Informasi Produk</h2>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><span class="font-medium">Kategori:</span> {{ $product->category }}</li>
                        <li><span class="font-medium">Merek:</span> {{ $product->brand }}</li>
                        <li><span class="font-medium">Stok:</span> {{ $product->stock }} tersedia</li>
                        <li><span class="font-medium">Terjual:</span> {{ $product->sold }} unit</li>
                    </ul>
                </div>
                
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-700 mb-2">Deskripsi</h2>
                    <div class="text-sm text-gray-600">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
                
                <div class="flex space-x-3 items-center">
    @if($product->stock > 0)
        @auth
        <a href="{{ route('checkout.buy-now', $product->id) }}" id="buyButton" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded font-medium text-sm md:text-base transition flex justify-center items-center">
            Beli Sekarang
        </a>
        <div class="w-auto">
            <livewire:cart.add-to-cart-button :productId="$product->id" :showQuantity="false" wire:key="product-detail-{{ $product->id }}" />
        </div>
        @else
        <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded font-medium text-sm md:text-base transition flex justify-center items-center">
            Beli (login)
        </a>
        <div class="w-auto">
            <a href="{{ route('login') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded font-medium text-sm md:text-base transition flex justify-center items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13h10M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                Cart
            </a>
        </div>
        @endauth
    @else
        <span class="flex-grow bg-gray-400 text-white py-3 px-4 rounded font-medium text-sm md:text-base flex justify-center items-center cursor-not-allowed">
            Sold Out
        </span>
        <div class="w-10"></div>
    @endif
</div>
            </div>
        </div>
    </div>
    
    <!-- Produk Acak/Rekomendasi -->
    <div class="mt-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Produk Lainnya</h2>
        
        @if(isset($randomProducts) && $randomProducts->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($randomProducts as $randomProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <a href="{{ route('products.detail', $randomProduct->slug) }}">
                            <img src="{{ $randomProduct->image_url }}" alt="{{ $randomProduct->name }}" class="w-full h-40 object-cover">
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-800 mb-1 truncate">{{ $randomProduct->name }}</h3>
                                <p class="text-sm font-bold text-gray-800">{{ $randomProduct->formatted_price }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($randomProduct->stock > 0)
                                        {{ $randomProduct->stock }} tersedia
                                    @else
                                        <span class="text-red-500 font-medium">Sold Out</span>
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">Tidak ada produk rekomendasi saat ini.</p>
        @endif
    </div>
</div>
@endsection