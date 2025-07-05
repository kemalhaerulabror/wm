@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6">Keranjang Belanja</h1>
    
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 sm:px-4 sm:py-3 rounded relative mb-3 sm:mb-4">
        <span class="block sm:inline text-xs sm:text-sm">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded relative mb-3 sm:mb-4">
        <span class="block sm:inline text-xs sm:text-sm">{{ session('error') }}</span>
    </div>
    @endif
    
    @if(count($cartItems) > 0)
    <!-- Desktop/Tablet View (Table) -->
    <div class="hidden sm:block bg-white rounded-lg shadow-md overflow-hidden mb-4 sm:mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th scope="col" class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th scope="col" class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cartItems as $item)
                    <tr>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 sm:h-16 w-12 sm:w-16">
                                    <img class="h-12 sm:h-16 w-12 sm:w-16 object-cover rounded" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <div class="text-xs sm:text-sm text-gray-900">{{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')
                                <div class="flex border border-gray-300 rounded-md">
                                    <button type="button" onclick="decreaseQuantity('{{ $item->id }}')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-10 sm:w-12 border-0 text-center text-xs sm:text-sm focus:ring-0">
                                    <button type="button" onclick="increaseQuantity('{{ $item->id }}', {{ $item->product->stock }})" class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <button type="submit" class="ml-2 text-sm text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <div class="text-xs sm:text-sm text-gray-900 font-medium">{{ 'Rp ' . number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" id="remove-form-{{ $item->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs sm:text-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Mobile View (Cards) -->
    <div class="sm:hidden space-y-3">
        @foreach($cartItems as $item)
        <div class="bg-white rounded-lg shadow-md p-3">
            <div class="flex items-start mb-3">
                <div class="flex-shrink-0 h-16 w-16">
                    <img class="h-16 w-16 object-cover rounded" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                </div>
                <div class="ml-3 flex-1">
                    <div class="text-sm font-medium text-gray-900 mb-1">{{ $item->product->name }}</div>
                    <div class="text-xs font-bold text-gray-900">{{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</div>
                </div>
                <form action="{{ route('cart.remove', $item->id) }}" method="POST" id="remove-form-mobile-{{ $item->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            
            <div class="flex justify-between items-center">
                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                    @csrf
                    @method('PATCH')
                    <div class="flex border border-gray-300 rounded-md">
                        <button type="button" onclick="decreaseQuantity('mobile-{{ $item->id }}')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <input type="number" name="quantity" id="quantity-mobile-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-8 border-0 text-center text-xs focus:ring-0">
                        <button type="button" onclick="increaseQuantity('mobile-{{ $item->id }}', {{ $item->product->stock }})" class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                    <button type="submit" class="ml-1 text-xs text-gray-500 hover:text-gray-700">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </form>
                
                <div class="text-xs text-gray-900 font-medium">
                    <span class="text-gray-500 mr-1">Subtotal:</span>
                    {{ 'Rp ' . number_format($item->price * $item->quantity, 0, ',', '.') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="flex flex-col md:flex-row md:justify-between mt-4">
        <!-- Continue Shopping -->
        <div class="mb-4 md:mb-0">
            <a href="{{ route('home') }}" class="flex items-center text-gray-700 hover:text-gray-900 text-sm">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Lanjutkan Belanja</span>
            </a>
        </div>
        
        <!-- Cart Summary -->
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 w-full md:w-1/3">
            <h2 class="text-base sm:text-lg font-medium mb-3 sm:mb-4">Ringkasan Belanja</h2>
            
            <div class="flex justify-between mb-2">
                <span class="text-sm text-gray-700">Subtotal</span>
                <span class="text-sm font-medium">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
            </div>
            
            <div class="border-t border-gray-200 pt-3 sm:pt-4 mt-3 sm:mt-4">
                <div class="flex justify-between mb-3 sm:mb-4">
                    <span class="text-sm sm:text-lg font-bold">Total</span>
                    <span class="text-sm sm:text-lg font-bold">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
                </div>
                
                @auth
                <a href="{{ route('checkout.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-3 sm:px-4 rounded w-full flex items-center justify-center text-xs sm:text-sm">
                    <i class="fas fa-shopping-bag mr-2"></i> Lanjut ke Pembayaran
                </a>
                @else
                <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-3 sm:px-4 rounded w-full flex items-center justify-center text-xs sm:text-sm">
                    <i class="fas fa-shopping-bag mr-2"></i> Login untuk Checkout
                </a>
                @endauth
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-8 sm:py-12 bg-white rounded-lg shadow-md">
        <i class="fas fa-shopping-cart text-gray-400 text-3xl sm:text-5xl mb-3 sm:mb-4"></i>
        <h2 class="text-base sm:text-xl font-medium text-gray-700 mb-2">Keranjang belanja Anda kosong</h2>
        <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">Tambahkan produk untuk melanjutkan belanja</p>
        <a href="{{ route('home') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-4 sm:px-6 rounded inline-flex items-center text-xs sm:text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Belanja Sekarang
        </a>
    </div>
    @endif
</div>

<script>
    function decreaseQuantity(id) {
        const input = document.getElementById('quantity-' + id);
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        } else {
            // Jika kuantitas akan menjadi 0 atau kurang, hapus item dari keranjang
            if (id.includes('mobile-')) {
                const realId = id.replace('mobile-', '');
                document.getElementById('remove-form-mobile-' + realId).submit();
            } else {
                document.getElementById('remove-form-' + id).submit();
            }
        }
    }
    
    function increaseQuantity(id, maxStock) {
        const input = document.getElementById('quantity-' + id);
        const currentValue = parseInt(input.value);
        if (currentValue < maxStock) {
            input.value = currentValue + 1;
        }
    }
</script>
@endsection
