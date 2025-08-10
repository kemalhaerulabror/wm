@extends('user.layouts.app')

@section('content')
    <!-- Banner/Carousel -->
    <section class="container mx-auto px-3 sm:px-4 lg:px-6 pt-2 sm:pt-4">
        <!-- Main Carousel -->
        <div class="relative rounded-lg overflow-hidden shadow-lg" id="mainCarousel">
            <div class="carousel-container">
                <div class="relative">
                    <!-- Slide 1 -->
                    <div class="carousel-slide transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/1.jpg') }}" alt="Banner Wipa Motor" class="w-full h-32 sm:h-48 md:h-64 lg:h-80 xl:h-96 object-cover">
                    </div>
                    
                    <!-- Slide 2 -->
                    <div class="carousel-slide hidden transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/2.jpg') }}" alt="Promo Motor" class="w-full h-32 sm:h-48 md:h-64 lg:h-80 xl:h-96 object-cover">
                    </div>
                    
                    <!-- Slide 3 -->
                    <div class="carousel-slide hidden transition-opacity duration-500 ease-in-out">
                        <img src="{{ asset('img/3.jpg') }}" alt="Servis Motor" class="w-full h-32 sm:h-48 md:h-64 lg:h-80 xl:h-96 object-cover">
                    </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <button class="carousel-prev absolute left-1 sm:left-2 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 backdrop-blur-sm rounded-full p-1.5 sm:p-2 text-white z-10 transition-all">
                <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
            </button>
            <button class="carousel-next absolute right-1 sm:right-2 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 backdrop-blur-sm rounded-full p-1.5 sm:p-2 text-white z-10 transition-all">
                <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
            </button>
            
            <!-- Carousel Indicators -->
            <div class="carousel-indicators absolute bottom-2 sm:bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1.5 sm:space-x-2">
                <span class="carousel-indicator active w-2 h-2 sm:w-2.5 sm:h-2.5 bg-white rounded-full cursor-pointer transition-all duration-300 ease-in-out shadow-sm" data-slide-index="0"></span>
                <span class="carousel-indicator w-2 h-2 sm:w-2.5 sm:h-2.5 bg-white/50 rounded-full cursor-pointer transition-all duration-300 ease-in-out shadow-sm" data-slide-index="1"></span>
                <span class="carousel-indicator w-2 h-2 sm:w-2.5 sm:h-2.5 bg-white/50 rounded-full cursor-pointer transition-all duration-300 ease-in-out shadow-sm" data-slide-index="2"></span>
            </div>
        </div>
    </section>

    <!-- Kategori -->
    <section class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-6 lg:py-8">
        <div class="text-center mb-3 sm:mb-6">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">KATEGORI</h2>
            <div class="w-16 h-1 bg-blue-600 mx-auto mt-2"></div>
        </div>
        
        <!-- Mobile: Grid 3 kolom, Desktop: Flex horizontal -->
        <div class="grid grid-cols-3 gap-3 sm:gap-4 md:flex md:justify-center md:items-center md:space-x-8 lg:space-x-12 py-2 sm:py-4">
            <a href="{{ route('products.category', 'motor-matic') }}" class="text-center hover:opacity-80 transition-all">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-3 sm:p-4 lg:p-5 inline-block mx-auto mb-2 shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-motorcycle text-blue-600 text-xl sm:text-2xl lg:text-3xl"></i>
                </div>
                <p class="text-xs sm:text-sm lg:text-base font-medium text-gray-700">Motor Matic</p>
            </a>
            <a href="{{ route('products.category', 'motor-bebek') }}" class="text-center hover:opacity-80 transition-all">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-3 sm:p-4 lg:p-5 inline-block mx-auto mb-2 shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-bicycle text-green-600 text-xl sm:text-2xl lg:text-3xl"></i>
                </div>
                <p class="text-xs sm:text-sm lg:text-base font-medium text-gray-700">Motor Bebek</p>
            </a>
            <a href="{{ route('products.category', 'motor-sport') }}" class="text-center hover:opacity-80 transition-all">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-3 sm:p-4 lg:p-5 inline-block mx-auto mb-2 shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-gauge-high text-red-600 text-xl sm:text-2xl lg:text-3xl"></i>
                </div>
                <p class="text-xs sm:text-sm lg:text-base font-medium text-gray-700">Motor Sport</p>
            </a>
        </div>
    </section>

    <!-- Motor Rekomendasi/Populer (Featured Products) -->
    <section class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-6 lg:py-8 bg-gray-50">
        <div class="flex items-center justify-between mb-3 sm:mb-6">
            <div>
                <h2 class="text-base sm:text-xl lg:text-2xl font-bold text-gray-800">REKOMENDASI UNTUK ANDA</h2>
                <div class="w-12 h-1 bg-blue-600 mt-2"></div>
            </div>
            <a href="{{ route('products.category', 'all') }}" class="text-xs sm:text-sm lg:text-base text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Responsive Grid: 2 cols mobile, 3 cols tablet, 4 cols desktop, 6 cols xl -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2 sm:gap-3 lg:gap-4">
            @forelse($featuredProducts as $product)
            <!-- Item Produk -->
            <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <a href="{{ route('products.detail', $product->slug) }}" class="block">
                    <div class="relative">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-24 sm:h-32 lg:h-36 object-cover">
                        <!-- Badge jika stock rendah -->
                        @if($product->stock > 0 && $product->stock <= 5)
                        <div class="absolute top-1 right-1">
                            <span class="bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">Stok {{ $product->stock }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-2 sm:p-3">
                        <p class="text-gray-800 text-xs sm:text-sm lg:text-base font-medium mb-1 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</p>
                        <div class="mt-1 sm:mt-2">
                            <span class="text-blue-600 font-bold text-sm sm:text-base lg:text-lg">{{ $product->formatted_price }}</span>
                        </div>
                    </div>
                </a>
                <div class="px-2 sm:px-3 pb-2 sm:pb-3">
                    <div class="flex space-x-1 sm:space-x-2 items-center">
                        @if($product->stock > 0)
                            @auth
                            <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Beli
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Login
                            </a>
                            @endauth
                            <div class="flex-shrink-0">
                                <livewire:cart.add-to-cart-button :productId="$product->id" />
                            </div>
                        @else
                            <span class="flex-grow bg-gray-400 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 flex justify-center items-center cursor-not-allowed font-medium">
                                Sold Out
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-4xl mb-4">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <p class="text-gray-500 text-sm sm:text-base">Belum ada produk rekomendasi.</p>
            </div>
            @endforelse
        </div>
    </section>

    <!-- Semua Produk -->
    <section class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-6 lg:py-8">
        <div class="flex items-center justify-between mb-3 sm:mb-6">
            <div>
                <h2 class="text-base sm:text-xl lg:text-2xl font-bold text-gray-800">SEMUA PRODUK</h2>
                <div class="w-12 h-1 bg-blue-600 mt-2"></div>
            </div>
        </div>
        
        <!-- Mobile: 2 kolom, Tablet: 3 kolom, Desktop: 5 kolom -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3 lg:gap-4 mb-3 sm:mb-4">
            @forelse($allProducts->take(5) as $product)
            <!-- Produk Item Baris 1 -->
            <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <a href="{{ route('products.detail', $product->slug) }}" class="block">
                    <div class="relative">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-24 sm:h-32 lg:h-36 object-cover">
                        @if($product->stock > 0 && $product->stock <= 5)
                        <div class="absolute top-1 right-1">
                            <span class="bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">Stok {{ $product->stock }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-2 sm:p-3">
                        <p class="text-gray-800 text-xs sm:text-sm lg:text-base font-medium mb-1 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</p>
                        <div class="mt-1 sm:mt-2">
                            <span class="text-blue-600 font-bold text-sm sm:text-base lg:text-lg">{{ $product->formatted_price }}</span>
                        </div>
                    </div>
                </a>
                <div class="px-2 sm:px-3 pb-2 sm:pb-3">
                    <div class="flex space-x-1 sm:space-x-2 items-center">
                        @if($product->stock > 0)
                            @auth
                            <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Beli
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Login
                            </a>
                            @endauth
                            <div class="flex-shrink-0">
                                <livewire:cart.add-to-cart-button :productId="$product->id" />
                            </div>
                        @else
                            <span class="flex-grow bg-gray-400 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 flex justify-center items-center cursor-not-allowed font-medium">
                                Sold Out
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
        
        <!-- Baris Kedua - Hanya tampil jika ada produk lebih dari 5 -->
        @if($allProducts->count() > 5)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
            @forelse($allProducts->skip(5)->take(5) as $product)
            <!-- Produk Item Baris 2 -->
            <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <a href="{{ route('products.detail', $product->slug) }}" class="block">
                    <div class="relative">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-24 sm:h-32 lg:h-36 object-cover">
                        @if($product->stock > 0 && $product->stock <= 5)
                        <div class="absolute top-1 right-1">
                            <span class="bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">Stok {{ $product->stock }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-2 sm:p-3">
                        <p class="text-gray-800 text-xs sm:text-sm lg:text-base font-medium mb-1 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</p>
                        <div class="mt-1 sm:mt-2">
                            <span class="text-blue-600 font-bold text-sm sm:text-base lg:text-lg">{{ $product->formatted_price }}</span>
                        </div>
                    </div>
                </a>
                <div class="px-2 sm:px-3 pb-2 sm:pb-3">
                    <div class="flex space-x-1 sm:space-x-2 items-center">
                        @if($product->stock > 0)
                            @auth
                            <a href="{{ route('products.detail', $product->slug) }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Beli
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 transition-all flex justify-center items-center font-medium">
                                Login
                            </a>
                            @endauth
                            <div class="flex-shrink-0">
                                <livewire:cart.add-to-cart-button :productId="$product->id" />
                            </div>
                        @else
                            <span class="flex-grow bg-gray-400 text-white text-xs sm:text-sm rounded-lg py-1.5 sm:py-2 px-2 sm:px-3 flex justify-center items-center cursor-not-allowed font-medium">
                                Sold Out
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
        @endif
        
        @if(count($allProducts) == 0)
        <div class="text-center py-12">
            <div class="text-gray-400 text-4xl mb-4">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-gray-500 text-sm sm:text-base">Belum ada produk tersedia.</p>
        </div>
        @endif
        
        <!-- Tombol Lihat Lebih Lengkap -->
        <div class="flex justify-center mt-6 sm:mt-8">
            <a href="{{ route('products.category', 'all') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-6 sm:px-8 py-2.5 sm:py-3 text-center transition-all duration-300 shadow-md hover:shadow-lg">
                <i class="fa-solid fa-grid-2 mr-2"></i>
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
    let isTransitioning = false; // Prevent multiple transitions
    
    // Setup awal, sembunyikan semua slide kecuali yang aktif
    function setupSlides() {
        slides.forEach((slide, index) => {
            if (index !== 0) {
                slide.style.display = 'none';
                slide.classList.add('hidden');
            } else {
                slide.style.display = 'block';
                slide.classList.remove('hidden');
            }
        });
    }
    
    // Fungsi untuk mengganti slide dengan fade effect
    function showSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        // Fade out current slide
        slides[currentSlide].style.opacity = '0';
        
        setTimeout(() => {
            // Hide current slide
            slides[currentSlide].style.display = 'none';
            slides[currentSlide].classList.add('hidden');
            
            // Show new slide
            slides[index].style.display = 'block';
            slides[index].classList.remove('hidden');
            slides[index].style.opacity = '0';
            
            // Force reflow
            slides[index].offsetHeight;
            
            // Fade in new slide
            slides[index].style.opacity = '1';
            
            // Update indicators
            indicators[currentSlide].classList.remove('bg-white');
            indicators[currentSlide].classList.add('bg-white/50');
            indicators[index].classList.remove('bg-white/50');
            indicators[index].classList.add('bg-white');
            
            currentSlide = index;
            
            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }, 250);
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
    
    // Set initial opacity
    slides.forEach((slide, index) => {
        slide.style.opacity = index === 0 ? '1' : '0';
        slide.style.transition = 'opacity 0.5s ease-in-out';
    });
    
    // Event listeners untuk navigasi
    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        nextSlide();
        resetInterval();
    });
    
    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        prevSlide();
        resetInterval();
    });
    
    // Event listeners untuk indikator
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function(e) {
            e.preventDefault();
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
    
    // Pause on hover (desktop only)
    if (window.matchMedia('(hover: hover)').matches) {
        carousel.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        carousel.addEventListener('mouseleave', () => {
            startInterval();
        });
    }
    
    // Handle visibility change (pause when tab not active)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(slideInterval);
        } else {
            startInterval();
        }
    });
    
    // Mulai auto slide
    startInterval();
    
    // Touch/swipe support for mobile
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
});
</script>

@endsection