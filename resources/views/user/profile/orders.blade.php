@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6">Pesanan Saya</h1>
    
    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button id="tab-all" class="tab-btn active py-3 px-4 border-b-2 border-blue-500 text-sm font-medium text-blue-600">
                    Semua Pesanan
                </button>
                <button id="tab-pending" class="tab-btn py-3 px-4 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Menunggu Pembayaran
                </button>
                <button id="tab-processing" class="tab-btn py-3 px-4 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Diproses
                </button>
                <button id="tab-completed" class="tab-btn py-3 px-4 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Selesai
                </button>
                <button id="tab-cancelled" class="tab-btn py-3 px-4 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Dibatalkan
                </button>
            </nav>
        </div>
    </div>
    
    <div id="orders-container">
        <!-- Tab Content -->
        <div id="all-orders" class="tab-content">
            @if(count($orders) > 0)
                @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-4 order-item" 
                     data-status="{{ $order->status }}" 
                     data-payment="{{ $order->payment_status }}">
                    <div class="p-4 sm:p-5 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                            <div>
                                <span class="block text-xs text-gray-500 mb-1">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                                <span class="block text-sm font-medium">{{ $order->order_number }}</span>
                            </div>
                            
                            <div class="mt-2 sm:mt-0">
                                @if($order->payment_status == 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Sudah Dibayar
                                    </span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                        Menunggu Pembayaran
                                    </span>
                                @elseif($order->payment_status == 'cancelled' || $order->payment_status == 'failed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-t border-b border-gray-100 py-4 -mx-4 px-4 sm:-mx-5 sm:px-5">
                            @foreach($order->items->take(2) as $item)
                            <div class="flex items-start py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-md overflow-hidden">
                                    @if($item->product)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-800 line-clamp-1">{{ $item->product_name }}</p>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-gray-500">{{ $item->quantity }} x {{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</span>
                                        <span class="text-xs font-medium">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if(count($order->items) > 2)
                            <div class="text-center py-2">
                                <span class="text-xs text-gray-500">+ {{ count($order->items) - 2 }} produk lainnya</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-4">
                            <div class="order-2 sm:order-1 mt-4 sm:mt-0">
                                <span class="text-xs text-gray-500">Total Pesanan:</span>
                                <span class="text-sm font-bold ml-1">{{ 'Rp ' . number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="order-1 sm:order-2 w-full sm:w-auto flex flex-wrap justify-end gap-2">
                                <a href="{{ route('checkout.success', $order->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded bg-white hover:bg-gray-50 text-gray-700">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    Detail
                                </a>
                                
                                @if($order->payment_status == 'pending')
                                <a href="{{ route('checkout.payment', $order->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-xs font-medium rounded bg-blue-500 hover:bg-blue-600 text-white">
                                    <i class="fas fa-credit-card mr-1.5"></i>
                                    Bayar
                                </a>
                                @endif
                                
                                @if($order->payment_status == 'paid')
                                <a href="{{ route('checkout.invoice', $order->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-green-500 text-xs font-medium rounded bg-white hover:bg-green-50 text-green-600">
                                    <i class="fas fa-file-invoice mr-1.5"></i>
                                    Invoice
                                </a>
                                @endif
                                
                                @if($order->needs_user_confirmation)
                                <form action="{{ route('profile.orders.confirm', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-green-500 text-xs font-medium rounded bg-green-500 hover:bg-green-600 text-white">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Konfirmasi Pesanan Selesai
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        
                        @if($order->status == 'completed' && $order->user_confirmed)
                        <div class="mt-3 text-center pt-3 border-t border-gray-100">
                            <span class="text-xs text-green-600 flex items-center justify-center">
                                <i class="fas fa-check-circle mr-1"></i>
                                Pesanan ini telah selesai pada {{ $order->user_confirmed_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
            <div id="empty-state-default" class="text-center py-12 bg-white rounded-lg shadow-sm">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-bag text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-1">Belum ada pesanan</h3>
                <p class="text-sm text-gray-500 mb-6">Anda belum memiliki riwayat pesanan</p>
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Mulai Belanja
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const orderItems = document.querySelectorAll('.order-item');
        const defaultEmptyState = document.getElementById('empty-state-default');
        let filteredEmptyState = null;
        
        // Function to filter orders based on selected tab
        function filterOrders(tabId) {
            const container = document.getElementById('all-orders');
            let hasVisibleOrders = false;
            
            orderItems.forEach(item => {
                const status = item.dataset.status;
                const payment = item.dataset.payment;
                
                // First hide all items
                item.style.display = 'none';
                
                if (tabId === 'tab-all') {
                    item.style.display = 'block';
                    hasVisibleOrders = true;
                } else if (tabId === 'tab-pending' && payment === 'pending') {
                    item.style.display = 'block';
                    hasVisibleOrders = true;
                } else if (tabId === 'tab-processing' && status === 'processing') {
                    item.style.display = 'block';
                    hasVisibleOrders = true;
                } else if (tabId === 'tab-completed' && status === 'completed') {
                    item.style.display = 'block';
                    hasVisibleOrders = true;
                } else if (tabId === 'tab-cancelled' && (status === 'cancelled' || payment === 'cancelled' || payment === 'failed')) {
                    item.style.display = 'block';
                    hasVisibleOrders = true;
                }
            });
            
            // Manage empty state visibility
            if (defaultEmptyState) {
                defaultEmptyState.style.display = 'none';
            }
            
            if (filteredEmptyState) {
                filteredEmptyState.remove();
                filteredEmptyState = null;
            }
            
            // If there are no orders for the selected filter, show empty state
            if (!hasVisibleOrders) {
                // Check if we're on "all" tab and default empty state exists
                if (tabId === 'tab-all' && defaultEmptyState) {
                    defaultEmptyState.style.display = 'block';
                } else {
                    // Use filtered empty state for other tabs
                    const emptyStateHtml = `
                        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-bag text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-1">Tidak ada pesanan</h3>
                            <p class="text-sm text-gray-500 mb-6">Anda tidak memiliki pesanan dengan status ini</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Mulai Belanja
                            </a>
                        </div>
                    `;
                    
                    filteredEmptyState = document.createElement('div');
                    filteredEmptyState.innerHTML = emptyStateHtml;
                    filteredEmptyState.id = 'filtered-empty-state';
                    container.appendChild(filteredEmptyState);
                }
            }
        }
        
        // Set up tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active state from all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Add active state to clicked tab
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                
                // Filter orders based on selected tab
                filterOrders(this.id);
            });
        });
        
        // Check if URL has a hash to activate a specific tab
        const hashTab = window.location.hash ? 'tab-' + window.location.hash.substring(1) : null;
        if (hashTab && document.getElementById(hashTab)) {
            document.getElementById(hashTab).click();
        } else {
            // Default to "All" tab
            document.getElementById('tab-all').click();
        }
    });
</script>
@endpush

@endsection