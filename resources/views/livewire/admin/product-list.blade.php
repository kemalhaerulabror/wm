<div>
    <div class="py-2 sm:py-4">
        <!-- Filter Panel -->
        <div class="bg-white shadow-md rounded-lg mb-4 sm:mb-6 p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4">
                <div class="w-full sm:flex-1 min-w-[200px]">
                    <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs sm:text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            id="search" 
                            wire:model.live.debounce.300ms="search" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-8 sm:pl-10 p-2 sm:p-2.5"
                            placeholder="Cari nama atau deskripsi..."
                        >
                    </div>
                </div>
                
                <div class="w-full sm:w-32 md:w-40">
                    <label for="categoryFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select 
                        id="categoryFilter" 
                        wire:model.live="categoryFilter" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2 sm:p-2.5"
                    >
                        <option value="">Semua</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full sm:w-32 md:w-40">
                    <label for="brandFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Merek</label>
                    <select 
                        id="brandFilter" 
                        wire:model.live="brandFilter" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2 sm:p-2.5"
                    >
                        <option value="">Semua</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full sm:w-32 md:w-40">
                    <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select 
                        id="status" 
                        wire:model.live="status" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2 sm:p-2.5"
                    >
                        <option value="">Semua</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                
                <div class="w-full sm:flex-none sm:self-end mt-2 sm:mt-0">
                    <button 
                        wire:click="resetFilters" 
                        class="w-full sm:w-auto text-white bg-gray-700 hover:bg-gray-600 font-medium rounded-lg text-xs sm:text-sm px-3 sm:px-4 py-2 sm:py-2.5"
                    >
                        <i class="fas fa-sync-alt mr-1"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Products List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Gambar</th>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                                Nama
                                @if ($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Brand</th>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer" wire:click="sortBy('price')">
                                Harga
                                @if ($sortField === 'price')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer" wire:click="sortBy('stock')">
                                Stok
                                @if ($sortField === 'stock')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $key => $product)
                        <tr>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $key + $products->firstItem() }}
                            </td>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-8 w-12 sm:h-12 sm:w-16 object-cover rounded">
                            </td>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                {{ $product->name }}
                            </td>
                            <td class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $product->category }}
                            </td>
                            <td class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $product->brand }}
                            </td>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $product->formatted_price }}
                            </td>
                            <td class="hidden sm:table-cell px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $product->stock }}
                            </td>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                @if($product->status)
                                    <span class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                <div class="flex space-x-1 sm:space-x-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-2 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 text-center">
                                Tidak ada produk yang tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-2 sm:px-6 py-3 sm:py-4 bg-gray-50">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
