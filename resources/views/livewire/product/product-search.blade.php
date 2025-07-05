<div class="relative w-full" x-data="{ focused: false }" @click.outside="$wire.hideResults()">
    <div class="flex">
        <input 
            wire:model.live.debounce.{{ $searchDebounce }}ms="search" 
            wire:keydown.enter="searchAll"
            type="text" 
            placeholder="Cari motor..." 
            class="w-full py-1 sm:py-2 px-2 sm:px-4 text-[10px] xs:text-xs sm:text-sm rounded-l-md border-0 text-gray-800 bg-white focus:ring-0"
            @focus="focused = true"
        >
        <button 
            wire:click="searchAll" 
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-1 xs:px-2 sm:px-5 rounded-r-md"
        >
            <i class="fa-solid fa-search text-[10px] xs:text-xs sm:text-sm"></i>
        </button>
    </div>
    
    <!-- Search Results Dropdown -->
    @if($showResults && count($results) > 0)
    <div class="absolute z-50 w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 overflow-hidden">
        <ul class="max-h-[300px] overflow-y-auto">
            @foreach($results as $product)
            <li class="border-b border-gray-100 last:border-b-0">
                <a href="{{ route('products.detail', $product->slug) }}" class="block hover:bg-gray-50 p-2">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                            <img src="{{ $product->image_url }}" class="h-full w-full object-cover rounded" alt="{{ $product->name }}">
                        </div>
                        <div class="ml-2">
                            <p class="text-[10px] xs:text-xs sm:text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-[8px] xs:text-[10px] sm:text-xs text-gray-500">{{ $product->formatted_price }}</p>
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        <div class="bg-gray-50 p-2 text-center">
            <button 
                wire:click="searchAll" 
                class="text-[10px] xs:text-xs text-gray-600 hover:text-gray-900 w-full"
            >
                Lihat Semua Hasil
            </button>
        </div>
    </div>
    @elseif($showResults && $search && count($results) == 0)
    <div class="absolute z-50 w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 p-3 text-center">
        <p class="text-[10px] xs:text-xs text-gray-500">Tidak ada hasil untuk "{{ $search }}"</p>
    </div>
    @endif
</div>
