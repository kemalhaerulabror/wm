<div class="py-4 px-3 text-white">
    <div class="mb-6">
        <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider">Dashboard</div>
        <ul class="mt-3 space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-2"></i>
                    <span class="sidebar-item-text">Dashboard</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="mb-6">
        <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider sidebar-item-text">Katalog</div>
        <ul class="mt-3 space-y-1">
            <li>
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.products.*') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-motorcycle w-5 mr-2"></i>
                    <span class="sidebar-item-text">Produk Motor</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="mb-6">
        <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider sidebar-item-text">Penjualan</div>
        <ul class="mt-3 space-y-1">
            <li>
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.create') && !request()->routeIs('admin.orders.admin-created') && !request()->routeIs('admin.orders.payment') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-shopping-cart w-5 mr-2"></i>
                    <span class="sidebar-item-text">Pesanan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.create') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.orders.create') || request()->routeIs('admin.orders.payment') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-cash-register w-5 mr-2"></i>
                    <span class="sidebar-item-text">Buat Pesanan</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="mb-6">
        <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider sidebar-item-text">Pengguna</div>
        <ul class="mt-3 space-y-1">
            <li>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-users w-5 mr-2"></i>
                    <span class="sidebar-item-text">Pelanggan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.admin-users.index') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.admin-users.*') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-user-shield w-5 mr-2"></i>
                    <span class="sidebar-item-text">Admin</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="mb-6">
        <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider sidebar-item-text">Pengaturan</div>
        <ul class="mt-3 space-y-1">
            <!-- <li>
                <a href="#" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md">
                    <i class="fas fa-cog w-5 mr-2"></i>
                    <span class="sidebar-item-text">Umum</span>
                </a>
            </li>
            <li> -->
                <a href="{{ route('admin.profile') }}" 
                   class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-md {{ request()->routeIs('admin.profile') ? 'bg-gray-600 text-white' : '' }}">
                    <i class="fas fa-user-cog w-5 mr-2"></i>
                    <span class="sidebar-item-text">Profil</span>
                </a>
            </li>
        </ul>
    </div>
</div>