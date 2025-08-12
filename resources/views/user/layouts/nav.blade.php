<nav class="bg-gray-800 text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <!-- Top navbar - hanya tampil di desktop -->
    <div class="hidden md:block container mx-auto px-2 sm:px-4">
        <!-- Upper navbar - information & links -->
        <div class="flex justify-between py-1 sm:py-2 text-[10px] xs:text-xs sm:text-sm border-b border-gray-700">
            <div></div>
            <div class="flex space-x-2 sm:space-x-6">
                <div class="relative">
                    <livewire:notification.notification-list />
                    <livewire:notification.notification-counter />
                </div>
                @guest
                <a href="{{ route('register') }}" class="hover:text-gray-300">Daftar</a>
                <span>|</span>
                <a href="{{ route('login') }}" class="hover:text-gray-300">Masuk</a>
                @else
                <div class="relative" id="user-dropdown-container">
                    <button id="user-dropdown-btn" class="hover:text-gray-300 flex items-center">
                        <span class="mr-1">{{ Auth::user()->name }}</span>
                        <i id="user-arrow" class="fa-solid fa-chevron-down text-[8px] xs:text-xs transition-transform duration-300"></i>
                    </button>
                    <div id="user-dropdown-menu" class="hidden absolute right-0 bg-white text-gray-800 shadow-lg rounded-md z-30 mt-1" style="min-width: 180px;">
                        <div class="p-2 xs:p-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center text-gray-700">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="ml-2">
                                    <p class="text-[10px] xs:text-xs font-medium">{{ Auth::user()->name }}</p>
                                    <p class="text-[8px] xs:text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.index') }}" class="block px-3 py-1 text-[10px] xs:text-xs hover:bg-gray-100">Profil Saya</a>
                            <a href="{{ route('profile.orders') }}" class="block px-3 py-1 text-[10px] xs:text-xs hover:bg-gray-100">Pesanan Saya</a>
                            <a href="{{ route('logout.get') }}" onclick="localStorage.removeItem('rememberedEmail')" class="block px-3 py-1 text-[10px] xs:text-xs text-red-600 hover:bg-gray-100">Keluar</a>
                        </div>
                    </div>
                </div>
                @endguest
            </div>
        </div>
        
        <!-- Main navbar with search -->
        <div class="flex items-center py-2 sm:py-4">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-1 sm:space-x-2 mr-1 sm:mr-4 md:mr-8 hover:text-gray-300">
                <i class="fa-solid fa-motorcycle text-lg xs:text-xl sm:text-2xl"></i>
                <span class="text-sm xs:text-base sm:text-xl font-bold">Wipa Motor</span>
            </a>
            
            <!-- Search bar -->
            <div class="flex-grow mx-1 sm:mx-4">
                <livewire:product.product-search />
            </div>
            
            <!-- User actions -->
            <div class="flex items-center ml-1 xs:ml-2 sm:ml-4">
                <a href="{{ route('cart.index') }}" class="relative hover:text-gray-300">
                    <i class="fa-solid fa-shopping-cart text-lg xs:text-xl sm:text-2xl"></i>
                    <livewire:cart.cart-counter />
                </a>
            </div>
        </div>
    </div>
    
    <!-- Category navbar - hanya tampil di desktop -->
    <div class="hidden md:block bg-gray-700 relative">
        <div class="container mx-auto px-2 sm:px-4 overflow-hidden">
            <div class="flex justify-start xs:justify-center gap-2 sm:gap-4 md:gap-8 py-2 sm:py-3 overflow-x-auto text-[10px] xs:text-xs sm:text-sm scrollbar-hide">
                <!-- All Categories Dropdown -->
                <div id="categories-container" class="relative inline-block">
                    <button id="category-dropdown-btn" class="flex items-center whitespace-nowrap hover:text-gray-300 font-medium text-[10px] xs:text-xs sm:text-sm">
                        KATEGORI
                        <i id="category-arrow" class="fa-solid fa-chevron-down ml-1 text-[8px] xs:text-xs transition-transform duration-300"></i>
                    </button>
                </div>
                
                <a href="{{ route('products.category', 'motor-matic') }}" class="whitespace-nowrap hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Matic</a>
                <a href="{{ route('products.category', 'motor-bebek') }}" class="whitespace-nowrap hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Bebek</a>
                <a href="{{ route('products.category', 'motor-sport') }}" class="whitespace-nowrap hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Sport</a>
            </div>
        </div>
    </div>
    
    <!-- Dropdown menu (dibuat di luar struktur navbar untuk menghindari masalah positioning) -->
    <div id="mega-menu-dropdown" class="hidden bg-white text-gray-800 shadow-xl py-2 sm:py-4 rounded-md fixed z-[60]" style="width: 80%; max-width: 300px;">
        <div class="px-2 sm:px-4">
            <div class="mb-2 sm:mb-4">
                <h3 class="font-bold text-gray-700 mb-1 sm:mb-2 border-b border-gray-200 pb-1 text-xs sm:text-sm">Tipe Motor</h3>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a href="{{ route('products.category', 'motor-matic') }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Motor Matic</a></li>
                    <li><a href="{{ route('products.category', 'motor-bebek') }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Motor Bebek</a></li>
                    <li><a href="{{ route('products.category', 'motor-sport') }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Motor Sport</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-bold text-gray-700 mb-1 sm:mb-2 border-b border-gray-200 pb-1 text-xs sm:text-sm">Merek Motor</h3>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a href="{{ route('products.category', ['category' => 'all', 'brand' => 'honda']) }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Honda</a></li>
                    <li><a href="{{ route('products.category', ['category' => 'all', 'brand' => 'yamaha']) }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Yamaha</a></li>
                    <li><a href="{{ route('products.category', ['category' => 'all', 'brand' => 'suzuki']) }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Suzuki</a></li>
                    <li><a href="{{ route('products.category', ['category' => 'all', 'brand' => 'kawasaki']) }}" class="text-gray-600 hover:text-gray-900 block text-[10px] xs:text-xs sm:text-sm">Kawasaki</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu button and dropdown - hanya tampil di mobile -->
    <div class="md:hidden container mx-auto px-2 sm:px-4 py-3">
        <!-- Logo dan Search untuk mobile -->
        <div class="flex items-center justify-between mb-3">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-1 hover:text-gray-300">
                <i class="fa-solid fa-motorcycle text-lg"></i>
                <span class="text-sm xs:text-base font-bold">Wipa Motor</span>
            </a>
            
            <div class="flex items-center">
                <div class="relative hover:text-gray-300 mr-4">
                    <livewire:notification.notification-list />
                    <livewire:notification.notification-counter />
                </div>
                
                <a href="{{ route('cart.index') }}" class="relative hover:text-gray-300 mr-4">
                    <i class="fa-solid fa-shopping-cart text-lg"></i>
                    <livewire:cart.cart-counter />
                </a>
                
                <button type="button" id="mobile-menu-button" class="text-white text-xs">
                    <i class="fa-solid fa-bars"></i><span class="ml-1">Menu</span>
                </button>
            </div>
        </div>
        
        <!-- Search bar untuk mobile -->
        <div class="mb-3">
            <livewire:product.product-search />
        </div>
        
        <!-- Mobile menu -->
        <div class="hidden" id="mobile-menu">
            <div class="py-2 border-t border-gray-700">
                <!-- Auth -->
                <div class="flex justify-between mb-4">
                    @guest
                    <a href="{{ route('login') }}" class="bg-gray-700 hover:bg-gray-600 px-2 sm:px-4 py-1 sm:py-2 rounded-md w-full text-center mr-1 sm:mr-2 text-[10px] xs:text-xs sm:text-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-2 sm:px-4 py-1 sm:py-2 rounded-md w-full text-center ml-1 sm:ml-2 text-[10px] xs:text-xs sm:text-sm">Daftar</a>
                    @else
                    <div class="w-full">
                        <div class="flex items-center mb-2 pb-1 border-b border-gray-600">
                            <div class="bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center text-gray-700">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="ml-2">
                                <p class="text-[10px] xs:text-xs font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-[8px] xs:text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <a href="{{ route('profile.index') }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Profil Saya</a>
                            <a href="{{ route('profile.orders') }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Pesanan Saya</a>
                            <a href="{{ route('logout.get') }}" onclick="localStorage.removeItem('rememberedEmail')" class="block text-left text-[10px] xs:text-xs text-red-400 hover:text-red-300">Keluar</a>
                        </div>
                    </div>
                    @endguest
                </div>
                
                <!-- Categories untuk mobile -->
                <div class="flex flex-col space-y-1 xs:space-y-2 sm:space-y-3 mb-2 sm:mb-4">
                    <div class="text-gray-400 text-[10px] xs:text-xs uppercase font-bold pb-1 border-b border-gray-700">Tipe Motor</div>
                    <a href="{{ route('products.category', 'motor-matic') }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Matic</a>
                    <a href="{{ route('products.category', 'motor-bebek') }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Bebek</a>
                    <a href="{{ route('products.category', 'motor-sport') }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Motor Sport</a>
                    
                    <div class="text-gray-400 text-[10px] xs:text-xs uppercase font-bold pt-1 sm:pt-2 pb-1 border-b border-gray-700">Merek Motor</div>
                    <a href="{{ route('products.category', ['category' => 'all', 'brand' => 'honda']) }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Honda</a>
                    <a href="{{ route('products.category', ['category' => 'all', 'brand' => 'yamaha']) }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Yamaha</a>
                    <a href="{{ route('products.category', ['category' => 'all', 'brand' => 'suzuki']) }}" class="hover:text-gray-300 text-[10px] xs:text-xs sm:text-sm">Suzuki</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Category dropdown toggle with arrow animation dan posisi dinamis
        const categoryDropdownBtn = document.getElementById('category-dropdown-btn');
        const megaMenuDropdown = document.getElementById('mega-menu-dropdown');
        const categoryArrow = document.getElementById('category-arrow');
        const categoriesContainer = document.getElementById('categories-container');
        
        // Fungsi untuk memposisikan dropdown tepat di bawah tombol
        function positionDropdown() {
            const btnRect = categoryDropdownBtn.getBoundingClientRect();
            
            // Atur posisi dropdown tepat di bawah tombol "KATEGORI"
            // Gunakan posisi client karena navbar sudah fixed
            megaMenuDropdown.style.top = btnRect.bottom + 'px';
            
            // Untuk perangkat mobile, tengahkan dropdown
            if (window.innerWidth < 640) {
                megaMenuDropdown.style.left = '10px';
                megaMenuDropdown.style.transform = 'none';
                megaMenuDropdown.style.width = 'calc(100% - 20px)';
                megaMenuDropdown.style.maxWidth = 'none';
            } else {
                megaMenuDropdown.style.left = btnRect.left + 'px';
                megaMenuDropdown.style.transform = 'none';
                megaMenuDropdown.style.width = '80%';
                megaMenuDropdown.style.maxWidth = '300px';
            }
        }
        
        // Toggle dropdown
        categoryDropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Posisikan dropdown sebelum ditampilkan
            positionDropdown();
            
            // Toggle visibility
            megaMenuDropdown.classList.toggle('hidden');
            
            // Rotate arrow
            if (megaMenuDropdown.classList.contains('hidden')) {
                categoryArrow.style.transform = 'rotate(0deg)';
            } else {
                categoryArrow.style.transform = 'rotate(180deg)';
            }
        });
        
        // Update posisi saat resize
        window.addEventListener('resize', function() {
            if (!megaMenuDropdown.classList.contains('hidden')) {
                positionDropdown();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!categoryDropdownBtn.contains(e.target) && !megaMenuDropdown.contains(e.target)) {
                megaMenuDropdown.classList.add('hidden');
                categoryArrow.style.transform = 'rotate(0deg)';
            }
        });

        // User dropdown toggle
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdownMenu = document.getElementById('user-dropdown-menu');
        const userArrow = document.getElementById('user-arrow');

        if (userDropdownBtn) {
            userDropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle visibility
                userDropdownMenu.classList.toggle('hidden');
                
                // Rotate arrow
                if (userDropdownMenu.classList.contains('hidden')) {
                    userArrow.style.transform = 'rotate(0deg)';
                } else {
                    userArrow.style.transform = 'rotate(180deg)';
                }
            });

            // Close user dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                    userArrow.style.transform = 'rotate(0deg)';
                }
            });
        }
    });
</script>

<style>
/* Sembunyikan scrollbar namun tetap memungkinkan scroll */
.scrollbar-hide {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;  /* Chrome, Safari and Opera */
}
</style>
