<div class="relative">
    <!-- Flash message sebagai toast yang muncul dari bawah layar -->
    @if(session()->has('success') || session()->has('error'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed bottom-2 xs:bottom-3 sm:bottom-6 left-1/2 transform -translate-x-1/2 z-50 w-[95%] xs:w-[85%] sm:w-auto sm:min-w-[280px] sm:max-w-sm {{ session()->has('success') ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' }} border-l-4 p-1.5 xs:p-2 sm:p-3 rounded-md shadow-md sm:shadow-lg"
        >
            <div class="flex items-center">
                <i class="fas {{ session()->has('success') ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }} text-xs xs:text-sm mr-1.5 xs:mr-2"></i>
                <p class="text-[10px] xs:text-xs sm:text-sm font-medium">{{ session('success') ?? session('error') }}</p>
            </div>
        </div>
    @endif
    
    @if($showQuantity)
    <div class="flex items-center mb-2">
        <button wire:click="decreaseQuantity" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded-l-md">
            <i class="fas fa-minus text-xs"></i>
        </button>
        <span class="px-4 py-1 bg-gray-100 text-sm">{{ $quantity }}</span>
        <button wire:click="increaseQuantity" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded-r-md">
            <i class="fas fa-plus text-xs"></i>
        </button>
    </div>
    @endif
    
    <button wire:click="addToCart" 
        class="flex-shrink-0 bg-gray-800 hover:bg-gray-700 text-white text-xs rounded-md w-10 h-9 flex items-center justify-center {{ $stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
        <i class="fas fa-shopping-cart"></i>
    </button>
    
    @if($stock <= 0)
        <p class="text-xs text-red-500 mt-1">Stok habis</p>
    @endif
</div>

@push('styles')
<style>
    /* Custom breakpoint untuk mobile extra small (< 375px) */
    @media (max-width: 374px) {
        .xs\:bottom-3 {
            bottom: 0.75rem;
        }
        .xs\:w-\[85\%\] {
            width: 85%;
        }
        .xs\:p-2 {
            padding: 0.5rem;
        }
        .xs\:mr-2 {
            margin-right: 0.5rem;
        }
        .xs\:text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }
        .xs\:text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
    }
</style>
@endpush
