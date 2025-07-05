@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div id="loading-overlay" class="fixed inset-0 bg-transparent z-50 flex items-center justify-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-gray-800"></div>
    </div>
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold mb-2">Hasil Pencarian: "{{ $query }}"</h1>
            <p class="text-gray-600">{{ $products->total() }} produk ditemukan</p>
        </div>
        
        <!-- Filter Urutan -->
        <div class="mt-4 md:mt-0">
            <form id="sort-form" action="{{ isset($category) ? route('products.category', $category ?? 'all') : route('search') }}" method="GET" class="flex items-center space-x-2">
                @if(!isset($category))
                    <input type="hidden" name="query" value="{{ $query }}">
                @endif
                @if(request()->has('brand'))
                    <input type="hidden" name="brand" value="{{ request()->input('brand') }}">
                @endif
                <label for="sort" class="text-sm text-gray-600">Urutkan:</label>
                <select name="sort" id="sort" onchange="showLoader(); setTimeout(() => document.getElementById('sort-form').submit(), 50)" class="text-sm border border-gray-300 rounded p-1 focus:outline-none focus:ring-1 focus:ring-gray-800">
                    <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </form>
        </div>
    </div>
    
    @if($products->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
            <a href="{{ route('products.detail', $product->slug) }}" class="block">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                <div class="p-3">
                    <h3 class="text-sm font-medium text-gray-800 truncate mb-1">{{ $product->name }}</h3>
                    <p class="text-gray-600 font-bold">{{ $product->formatted_price }}</p>
                </div>
            </a>
            <div class="p-3 pt-0">
                <div class="flex space-x-2 items-center">
                    @if($product->stock > 0)
                        @auth
                        <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded py-2 px-3 transition flex justify-center items-center">
                            Beli
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded py-2 px-3 transition flex justify-center items-center">
                            Beli (login)
                        </a>
                        @endauth
                        <livewire:cart.add-to-cart-button :productId="$product->id" wire:key="search-{{ $product->id }}" />
                    @else
                        <span class="flex-grow bg-gray-400 text-white text-xs sm:text-sm rounded py-2 px-3 flex justify-center items-center cursor-not-allowed">
                            Sold Out
                        </span>
                        <div class="w-10"></div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-white rounded-lg shadow-md">
        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
        <h2 class="text-xl font-medium text-gray-700 mb-2">Tidak ada hasil yang ditemukan</h2>
        <p class="text-gray-500 mb-6">Coba dengan kata kunci yang berbeda</p>
        <a href="{{ route('home') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Loading overlay
    function showLoader() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }
    
    // Jika halaman sedang dimuat, sembunyikan overlay saat selesai
    window.addEventListener('load', function() {
        document.getElementById('loading-overlay').classList.add('hidden');
    });
    
    // Jika pengguna menekan tombol back, sembunyikan overlay
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.getElementById('loading-overlay').classList.add('hidden');
        }
    });
</script>
@endpush 