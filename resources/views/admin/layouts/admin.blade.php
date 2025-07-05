<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Admin Dashboard MotorShop</title>
    
    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @livewireStyles
    @yield('styles')
</head>
<body class="antialiased bg-gray-100 flex h-screen overflow-hidden">
    <!-- Top Header - Fixed Position -->
    <header class="bg-admin-header text-white admin-header-height shadow-md z-30 fixed top-0 left-0 right-0 w-full">
        <div class="h-full w-full flex items-center justify-between px-4">
            <!-- Left Side - Toggle and Title -->
            <div class="flex items-center">
                <button id="sidebarToggle" class="p-2 rounded-md hover:bg-gray-700 mr-2">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-semibold hidden lg:block">@yield('title', 'Dashboard')</h1>
            </div>
            
            <!-- Right Side - Admin dropdown -->
            <div class="flex items-center">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-700">
                        <span>{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        @if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->photo)
                            <img class="h-8 w-8 rounded-full object-cover" 
                                src="{{ asset('upload/admin_images/'.Auth::guard('admin')->user()->photo) }}" 
                                alt="Profile">
                        @else
                            <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i> Profil
                        </a>
                        <a href="{{ route('admin.change.password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i> Ganti Password
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 transition-transform duration-300 bg-admin-sidebar text-white sidebar-open lg:sidebar-open mt-16">
        <!-- Sidebar Content -->
        @include('admin.layouts.sidebar')
    </div>
    
    <!-- Main Content Area -->
    <div id="contentArea" class="flex-1 flex flex-col transition-all duration-300 content-with-sidebar lg:content-with-sidebar overflow-auto mt-16">
        <!-- Main Content -->
        <main class="p-4 md:p-6 flex-grow overflow-auto">
            @yield('content')
        </main>
    </div>
    
    <!-- Admin Chat Component -->
    <livewire:chat.admin-popup-chat />
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const contentArea = document.getElementById('contentArea');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            
            // Fungsi untuk menampilkan atau menyembunyikan sidebar
            function toggleSidebar(show) {
                if (show) {
                    // Tampilkan sidebar
                    sidebar.classList.add('sidebar-open');
                    sidebar.classList.remove('sidebar-closed');
                    
                    // Ubah margin konten pada desktop
                    if (window.innerWidth >= 1024) {
                        contentArea.classList.add('content-with-sidebar');
                        contentArea.classList.remove('content-full');
                    }
                } else {
                    // Sembunyikan sidebar
                    sidebar.classList.remove('sidebar-open');
                    sidebar.classList.add('sidebar-closed');
                    
                    // Ubah margin konten pada desktop
                    if (window.innerWidth >= 1024) {
                        contentArea.classList.remove('content-with-sidebar');
                        contentArea.classList.add('content-full');
                    }
                }
                
                // Simpan status sidebar ke localStorage
                localStorage.setItem('sidebarHidden', !show);
            }
            
            // Periksa status sidebar di localStorage
            const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
            
            // Atur status sidebar saat halaman dimuat
            toggleSidebar(!sidebarHidden);
            
            // Toggle sidebar saat tombol diklik
            sidebarToggle.addEventListener('click', function() {
                const isVisible = sidebar.classList.contains('sidebar-open');
                toggleSidebar(!isVisible);
            });
            
            // Atur margin awal pada mobile
            if (window.innerWidth < 1024) {
                contentArea.classList.remove('content-with-sidebar');
                contentArea.classList.add('content-full');
                
                // Sembunyikan sidebar di mobile saat halaman dimuat
                if (!sidebarHidden) {
                    sidebar.classList.remove('sidebar-open');
                    sidebar.classList.add('sidebar-closed');
                }
            }
            
            // Atur ulang saat ukuran layar berubah
            window.addEventListener('resize', function() {
                if (window.innerWidth < 1024) {
                    contentArea.classList.remove('content-with-sidebar');
                    contentArea.classList.add('content-full');
                } else {
                    if (!sidebar.classList.contains('sidebar-closed')) {
                        contentArea.classList.add('content-with-sidebar');
                        contentArea.classList.remove('content-full');
                    }
                }
            });
            
            // Tutup sidebar saat klik di luar pada mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024 &&
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) &&
                    sidebar.classList.contains('sidebar-open')) {
                    toggleSidebar(false);
                }
            });
        });
    </script>
    
    @livewireScripts
    @yield('scripts')
</body>
</html>
