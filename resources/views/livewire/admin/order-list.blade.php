<div
    wire:poll.{{ $isPolling ? $pollingInterval : '' }}
>
    <!-- Statistik Pesanan -->
    <div class="grid grid-cols-2 xs:grid-cols-2 md:grid-cols-5 gap-2 sm:gap-4 mb-3 sm:mb-6">
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4 border-l-4 border-gray-400">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-shopping-cart text-base sm:text-xl text-gray-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <h3 class="text-sm sm:text-xl font-bold">{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4 border-l-4 border-yellow-400">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-clock text-base sm:text-xl text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Menunggu</p>
                    <h3 class="text-sm sm:text-xl font-bold">{{ $pendingOrders }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4 border-l-4 border-blue-400">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-spinner text-base sm:text-xl text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Diproses</p>
                    <h3 class="text-sm sm:text-xl font-bold">{{ $processingOrders }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4 border-l-4 border-green-400">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-check-circle text-base sm:text-xl text-green-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Selesai</p>
                    <h3 class="text-sm sm:text-xl font-bold">{{ $completedOrders }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4 border-l-4 border-red-400">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-times-circle text-base sm:text-xl text-red-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Dibatalkan</p>
                    <h3 class="text-sm sm:text-xl font-bold">{{ $cancelledOrders }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter dan Pencarian -->
    <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 mb-3 sm:mb-6">
        <!-- Header Filter Section -->
        <div class="border-b border-gray-200 pb-2 mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h3 class="text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-0 flex items-center">
                <i class="fas fa-filter mr-1.5 text-gray-500"></i>Filter & Pencarian
            </h3>
            <div class="w-full sm:w-auto flex items-center">
                <button wire:click="resetFilters" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:py-1.5 border border-transparent text-xs font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                    <i class="fas fa-times-circle mr-1.5"></i>
                    Reset Filter
                </button>
            </div>
        </div>
        
        <!-- Filter Controls -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
            <!-- Search Input -->
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="search" class="block text-xs font-medium text-gray-600 mb-1">Cari Pesanan</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-xs"></i>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        id="search" 
                        class="block w-full pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Nomor pesanan / customer"
                    >
                </div>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-xs font-medium text-gray-600 mb-1">Status Pesanan</label>
                <select 
                    wire:model.live="statusFilter" 
                    id="statusFilter" 
                    class="block w-full border border-gray-300 rounded-md py-1.5 pl-2.5 pr-8 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Pembayaran</option>
                    <option value="processing">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            
            <!-- Date Filter -->
            <div>
                <label for="dateFilter" class="block text-xs font-medium text-gray-600 mb-1">Rentang Waktu</label>
                <select 
                    wire:model.live="dateFilter" 
                    id="dateFilter" 
                    class="block w-full border border-gray-300 rounded-md py-1.5 pl-2.5 pr-8 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                </select>
            </div>
            
            <!-- Pagination & Auto Refresh Controls -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tampilan</label>
                <div class="flex space-x-2">
                    <select 
                        wire:model.live="perPage" 
                        class="border border-gray-300 rounded-md py-1.5 pl-2 pr-7 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    
                    <button 
                        wire:click="togglePolling" 
                        class="flex-1 inline-flex justify-center items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md {{ $isPolling ? 'text-green-700 bg-green-100 hover:bg-green-200' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}"
                    >
                        <i class="fas {{ $isPolling ? 'fa-sync-alt fa-spin' : 'fa-sync-alt' }} mr-1.5"></i>
                        {{ $isPolling ? 'Auto' : 'Manual' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabel Pesanan -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-3 sm:mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('order_number')">
                            <div class="flex items-center">
                                No. Pesanan
                                @if ($sortField === 'order_number')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="hidden sm:table-cell px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th scope="col" class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('total_amount')">
                            <div class="flex items-center">
                                Total
                                @if ($sortField === 'total_amount')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="hidden xs:table-cell px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pembayaran
                        </th>
                        <th scope="col" class="hidden sm:table-cell px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                            <div class="flex items-center">
                                Tanggal
                                @if ($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-2 sm:px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="hidden sm:table-cell px-2 sm:px-4 py-2 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-6 w-6 sm:h-8 sm:w-8">
                                    @if($order->user)
                                    <img class="h-6 w-6 sm:h-8 sm:w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $order->user->name }}">
                                    @elseif($order->customer_name)
                                        <img class="h-6 w-6 sm:h-8 sm:w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $order->customer_name }}">
                                    @else
                                        <img class="h-6 w-6 sm:h-8 sm:w-8 rounded-full" src="https://ui-avatars.com/api/?name=Customer&color=7F9CF5&background=EBF4FF" alt="Customer">
                                    @endif
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    @if($order->user)
                                    <div class="text-xs font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500 hidden sm:block">{{ $order->user->email }}</div>
                                    @elseif($order->customer_name)
                                        <div class="text-xs font-medium text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-xs text-gray-500 hidden sm:block">{{ $order->customer_email ?? '-' }}</div>
                                    @else
                                        <div class="text-xs font-medium text-gray-900">Pelanggan</div>
                                        <div class="text-xs text-gray-500 hidden sm:block">-</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs text-gray-500">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-2 sm:px-4 py-2 whitespace-nowrap">
                            @php
                                $statusClass = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ][$order->status] ?? 'bg-gray-100 text-gray-800';
                                
                                $statusLabel = [
                                    'pending' => 'Menunggu',
                                    'processing' => 'Diproses',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan'
                                ][$order->status] ?? 'Tidak Diketahui';
                            @endphp
                            <span class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="hidden xs:table-cell px-2 sm:px-4 py-2 whitespace-nowrap">
                            @php
                                $paymentClass = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ][$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                
                                $paymentLabel = [
                                    'pending' => 'Menunggu',
                                    'paid' => 'Dibayar',
                                    'failed' => 'Gagal',
                                    'cancelled' => 'Dibatalkan'
                                ][$order->payment_status] ?? 'Tidak Diketahui';
                            @endphp
                            <span class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentClass }}">
                                {{ $paymentLabel }}
                            </span>
                        </td>
                        <td class="hidden sm:table-cell px-2 sm:px-4 py-2 whitespace-nowrap text-xs text-gray-500">
                            {{ $order->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMM YYYY HH:mm') }}
                        </td>
                        <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-indigo-100 text-indigo-700 text-xs sm:text-sm rounded-md hover:bg-indigo-200">
                                <i class="fas fa-eye mr-1 sm:mr-1.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-2 sm:px-4 py-4 text-sm text-center text-gray-500">
                            <div class="flex flex-col items-center py-6">
                                <i class="fas fa-folder-open text-gray-300 text-2xl sm:text-3xl mb-2"></i>
                                <p>Tidak ada pesanan yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-2 sm:px-4 py-3 bg-gray-50">
            {{ $orders->links() }}
        </div>
    </div>
</div> 