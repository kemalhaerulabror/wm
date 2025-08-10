@extends('user.layouts.app')

@section('content')
    <!-- Banner/Carousel -->
    <section class="container mx-auto px-4 pt-4">
        <!-- Main Carousel -->
        <div class="relative rounded-lg overflow-hidden" id="mainCarousel">
            <div class="carousel-container">
                <div class="relative">
                    <!-- Slide 1 -->
                    <div class="carousel-slide transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/1.jpg') }}" alt="Banner Wipa Motor" class="w-full h-40 sm:h-56 md:h-72 lg:h-80 object-cover">
                    </div>
                    
                    <!-- Slide 2 -->
                    <div class="carousel-slide hidden transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/2.jpg') }}" alt="Promo Motor" class="w-full h-40 sm:h-56 md:h-72 lg:h-80 object-cover">
                    </div>
                    
                    <!-- Slide 3 -->
                    <div class="carousel-slide hidden transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/3.jpg') }}" alt="Servis Motor" class="w-full h-40 sm:h-56 md:h-72 lg:h-80 object-cover">
                    </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <button class="carousel-prev absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-white/90 rounded-full p-1 sm:p-2 text-gray-800 z-10">
                <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
            </button>
            <button class="carousel-next absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-white/90 rounded-full p-1 sm:p-2 text-gray-800 z-10">
                <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
            </button>
            
            <!-- Carousel Indicators -->
            <div class="carousel-indicators absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1 sm:space-x-2">
                <span class="carousel-indicator active w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full cursor-pointer transition-colors duration-300 ease-in-out" data-slide-index="0"></span>
                <span class="carousel-indicator w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white/50 rounded-full cursor-pointer transition-colors duration-300 ease-in-out" data-slide-index="1"></span>
                <span class="carousel-indicator w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white/50 rounded-full cursor-pointer transition-colors duration-300 ease-in-out" data-slide-index="2"></span>
            </div>
        </div>
    </section>

    <!-- Kategori -->
    <section class="container mx-auto px-4 py-4 sm:py-8">
        <div class="text-center mb-4">
            <h2 class="text-lg sm:text-xl font-bold">KATEGORI</h2>
        </div>
        
        <!-- Mobile: Flex horizontal dengan scroll, Desktop: Flex horizontal tengah -->
        <div class="flex justify-start sm:justify-center overflow-x-auto sm:overflow-x-visible space-x-6 sm:space-x-12 py-2 sm:py-4">
            <a href="{{ route('products.category', 'motor-matic') }}" class="flex-shrink-0 text-center hover:opacity-80">
                <div class="bg-gray-100 rounded-full p-3 sm:p-4 inline-block mx-auto mb-2">
                    <i class="fa-solid fa-motorcycle text-gray-700 text-xl sm:text-2xl"></i>
                </div>
                <p class="text-xs sm:text-sm font-medium whitespace-nowrap">Motor Matic</p>
            </a>
            <a href="{{ route('products.category', 'motor-bebek') }}" class="flex-shrink-0 text-center hover:opacity-80">
                <div class="bg-gray-100 rounded-full p-3 sm:p-4 inline-block mx-auto mb-2">
                    <i class="fa-solid fa-bicycle text-gray-700 text-xl sm:text-2xl"></i>
                </div>
                <p class="text-xs sm:text-sm font-medium whitespace-nowrap">Motor Bebek</p>
            </a>
            <a href="{{ route('products.category', 'motor-sport') }}" class="flex-shrink-0 text-center hover:opacity-80">
                <div class="bg-gray-100 rounded-full p-3 sm:p-4 inline-block mx-auto mb-2">
                    <i class="fa-solid fa-gauge-high text-gray-700 text-xl sm:text-2xl"></i>
                </div>
                <p class="text-xs sm:text-sm font-medium whitespace-nowrap">Motor Sport</p>
            </a>
        </div>
    </section>

    <!-- Motor Rekomendasi/Populer (Featured Products) -->
    <section class="container mx-auto px-4 py-4 sm:py-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-xl font-bold">REKOMENDASI UNTUK ANDA</h2>
            <a href="{{ route('products.category', 'all') }}" class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">Lihat Semua</a>
        </div>
        
        <!-- Mobile: 2 kolom, Tablet: 3 kolom, Desktop: 6 kolom -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
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
        
        <!-- Mobile: 2 kolom, Desktop: 5 kolom -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 mb-4">
            @forelse($allProducts->take(5) as $product)
            <!-- Produk Item Baris 1 -->
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
            @endforelse
        </div>
        
        <!-- Baris kedua produk -->
        @if($allProducts->count() > 5)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
            @forelse($allProducts->skip(5)->take(5) as $product)
            <!-- Produk Item Baris 2 -->
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
            @endforelse
        </div>
        @endif
        
        @if(count($allProducts) == 0)
        <div class="text-center py-8">
            <p class="text-gray-500">Belum ada produk tersedia.</p>
        </div>
        @endif
        
        <!-- Tombol Lihat Lebih Lengkap -->
        <div class="flex justify-center mt-6 sm:mt-8">
            <a href="{{ route('products.category', 'all') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-center transition duration-300 ease-in-out">
                Lihat Lebih Lengkap
            </a>
        </div>
    </section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi carousel
    const carousel = document.getElementById('mainCarousel');
    const slides = carousel.querySelectorAll('.carousel-slide');
    const prevBtn = carousel.querySelector('.carousel-prev');
    const nextBtn = carousel.querySelector('.carousel-next');
    const indicators = carousel.querySelectorAll('.carousel-indicator');
    let currentSlide = 0;
    let slideInterval;
    const intervalTime = 5000; // waktu auto slide (5 detik)
    
    // Setup awal, sembunyikan semua slide kecuali yang aktif
    function setupSlides() {
        slides.forEach((slide, index) => {
            if (index !== 0) {
                slide.style.display = 'none';
            } else {
                slide.style.display = 'block';
            }
        });
    }
    
    // Fungsi untuk mengganti slide
    function showSlide(index) {
        // Sembunyikan semua slide
        slides.forEach(slide => {
            slide.style.display = 'none';
        });
        
        // Reset semua indikator
        indicators.forEach(indicator => {
            indicator.classList.remove('bg-white');
            indicator.classList.add('bg-white/50');
        });
        
        // Tampilkan slide yang aktif
        slides[index].style.display = 'block';
        
        // Aktifkan indikator yang sesuai
        indicators[index].classList.remove('bg-white/50');
        indicators[index].classList.add('bg-white');
        
        // Update indeks slide saat ini
        currentSlide = index;
    }
    
    // Fungsi untuk slide berikutnya
    function nextSlide() {
        let newIndex = currentSlide + 1;
        if (newIndex >= slides.length) {
            newIndex = 0;
        }
        showSlide(newIndex);
    }
    
    // Fungsi untuk slide sebelumnya
    function prevSlide() {
        let newIndex = currentSlide - 1;
        if (newIndex < 0) {
            newIndex = slides.length - 1;
        }
        showSlide(newIndex);
    }
    
    // Inisialisasi
    setupSlides();
    
    // Event listeners untuk navigasi
    nextBtn.addEventListener('click', function() {
        nextSlide();
        resetInterval();
    });
    
    prevBtn.addEventListener('click', function() {
        prevSlide();
        resetInterval();
    });
    
    // Event listeners untuk indikator
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            showSlide(index);
            resetInterval();
        });
    });
    
    // Auto slide
    function startInterval() {
        slideInterval = setInterval(nextSlide, intervalTime);
    }
    
    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }
    
    // Touch/swipe support untuk mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next slide
                nextSlide();
            } else {
                // Swipe right - previous slide  
                prevSlide();
            }
            resetInterval();
        }
    }
    
    // Mulai auto slide
    startInterval();
});
</script>

@endsection