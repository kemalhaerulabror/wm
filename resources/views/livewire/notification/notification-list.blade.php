<div 
    x-data="{ 
        open: @entangle('isOpen'),
        toggle() { this.open = !this.open },
        close() { this.open = false }
    }" 
    @click.away="close()"
    class="relative"
>
    <!-- Tombol notifikasi -->
    <button @click="toggle()" class="hover:text-gray-300 relative">
        <i class="fa-solid fa-bell text-sm xs:text-base sm:text-lg"></i>
    </button>
    
    <!-- Dropdown notifikasi -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute -right-25 xs:-right-24 sm:-right-16 md:right-0 mt-2 bg-white text-gray-800 shadow-lg rounded-md z-30 w-64 sm:w-80 md:w-96"
        style="display: none;"
    >
        <div class="p-2 sm:p-3 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-xs sm:text-sm font-medium">Notifikasi</h3>
            @if(count($notifications) > 0)
            <button wire:click="markAllAsRead" class="text-[10px] xs:text-xs text-blue-600 hover:text-blue-800">Tandai semua dibaca</button>
            @endif
        </div>
        
        <div class="max-h-60 sm:max-h-72 overflow-y-auto">
            @if(count($notifications) > 0)
                @foreach($notifications as $notification)
                <div class="p-2 sm:p-3 border-b border-gray-100 hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($notification->type == 'success')
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-500 rounded-full">
                                    <i class="fa-solid fa-check text-[10px] xs:text-xs"></i>
                                </span>
                            @elseif($notification->type == 'error')
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-500 rounded-full">
                                    <i class="fa-solid fa-times text-[10px] xs:text-xs"></i>
                                </span>
                            @elseif($notification->type == 'warning')
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-100 text-yellow-500 rounded-full">
                                    <i class="fa-solid fa-exclamation text-[10px] xs:text-xs"></i>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-500 rounded-full">
                                    <i class="fa-solid fa-info text-[10px] xs:text-xs"></i>
                                </span>
                            @endif
                        </div>
                        <div class="ml-2 flex-1">
                            <a href="{{ $notification->link }}" 
                               wire:click="markAsRead({{ $notification->id }})" 
                               class="text-[10px] xs:text-xs font-medium block">
                                {{ $notification->title }}
                            </a>
                            <p class="text-[8px] xs:text-[10px] text-gray-500">{{ $notification->message }}</p>
                            <span class="text-[8px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="p-4 text-center">
                    <span class="text-[10px] xs:text-xs text-gray-500">Tidak ada notifikasi</span>
                </div>
            @endif
        </div>
        
        @if(count($notifications) > 0)
        <div class="p-2 sm:p-3 border-t border-gray-100 text-center">
            <a href="{{ route('notifications.index') }}" class="text-[10px] xs:text-xs text-blue-600 hover:text-blue-800">Lihat semua notifikasi</a>
        </div>
        @endif
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Refresh notifikasi setiap 30 detik
            setInterval(function() {
                if (typeof window.Livewire !== 'undefined') {
                    Livewire.dispatch('refreshNotifications');
                }
            }, 30000);
            
            // Tambahkan listener untuk event notification.created dari Laravel
            window.addEventListener('notification.created', function(e) {
                if (typeof window.Livewire !== 'undefined') {
                    console.log('JS Event: notification.created received');
                    Livewire.dispatch('refreshNotifications');
                }
            });
            
            // Tambahkan listener untuk event order.paid dari Laravel
            window.addEventListener('order.paid', function(e) {
                if (typeof window.Livewire !== 'undefined') {
                    console.log('JS Event: order.paid received', e.detail);
                    Livewire.dispatch('refreshNotifications');
                }
            });
        });
    </script>
</div>
