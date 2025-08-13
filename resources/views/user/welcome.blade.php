<!-- Motor Rekomendasi/Populer (Featured Products) -->
<section class="container mx-auto px-4 py-4 sm:py-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base sm:text-xl font-bold">REKOMENDASI UNTUK ANDA</h2>
        <a href="{{ route('products.category', 'all') }}" class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">Lihat Semua</a>
    </div>
    
    <!-- Konsisten: Mobile 2 kolom, Desktop 4 kolom -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4">
        @forelse($featuredProducts as $product)
        <!-- Item Produk -->
        <div class="bg-white border border-gray-200 rounded-lg hover:shadow-md transition overflow-hidden">
            <a href="{{ route('products.detail', $product->slug) }}" class="block">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-32 sm:h-36 object-cover">
                <div class="p-3">
                    <p class="text-gray-800 text-sm font-medium mb-2 leading-tight overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</p>
                    <div>
                        <span class="text-gray-800 font-bold text-sm">{{ $product->formatted_price }}</span>
                    </div>
                </div>
            </a>
            <div class="px-3 pb-3">
                <div class="flex space-x-2 items-center">
                    @if($product->stock > 0)
                        @auth
                        <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs rounded py-2 px-3 transition flex justify-center items-center">
                            Beli
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs rounded py-2 px-3 transition flex justify-center items-center">
                            Login
                        </a>
                        @endauth
                        <livewire:cart.add-to-cart-button :productId="$product->id" />
                    @else
                        <span class="flex-grow bg-gray-400 text-white text-xs rounded py-2 px-3 flex justify-center items-center cursor-not-allowed">
                            Sold Out
                        </span>
                        <div class="w-10"></div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8">
            <p class="text-gray-500">Belum ada produk rekomendasi.</p>
        </div>
        @endforelse
    </div>
</section>

<!-- Semua Produk -->
<section class="container mx-auto px-4 py-4 sm:py-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base sm:text-xl font-bold">SEMUA PRODUK</h2>
    </div>
    
    <!-- Konsisten: Mobile 2 kolom, Desktop 4 kolom - SATU GRID SAJA -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
        @forelse($allProducts->take(10) as $product)
        <!-- Produk Item -->
        <div class="bg-white border border-gray-200 rounded-lg hover:shadow-md transition overflow-hidden">
            <a href="{{ route('products.detail', $product->slug) }}" class="block">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-32 sm:h-36 object-cover">
                <div class="p-3">
                    <p class="text-gray-800 text-sm font-medium mb-2 leading-tight overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</p>
                    <div>
                        <span class="text-gray-800 font-bold text-sm">{{ $product->formatted_price }}</span>
                    </div>
                </div>
            </a>
            <div class="px-3 pb-3">
                <div class="flex space-x-2 items-center">
                    @if($product->stock > 0)
                        @auth
                        <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs rounded py-2 px-3 transition flex justify-center items-center">
                            Beli
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs rounded py-2 px-3 transition flex justify-center items-center">
                            Login
                        </a>
                        @endauth
                        <livewire:cart.add-to-cart-button :productId="$product->id" />
                    @else
                        <span class="flex-grow bg-gray-400 text-white text-xs rounded py-2 px-3 flex justify-center items-center cursor-not-allowed">
                            Sold Out
                        </span>
                        <div class="w-10"></div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8">
            <p class="text-gray-500">Belum ada produk tersedia.</p>
        </div>
        @endforelse
    </div>
    
    <!-- Tombol Lihat Lebih Lengkap -->
    <div class="flex justify-center mt-6 sm:mt-8">
        <a href="{{ route('products.category', 'all') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-center transition duration-300 ease-in-out">
            Lihat Lebih Lengkap
        </a>
    </div>
</section>