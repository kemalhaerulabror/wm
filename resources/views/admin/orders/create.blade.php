@extends('admin.layouts.admin')

@section('title', 'Buat Pesanan Baru')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Perbaikan untuk Select2 */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        display: flex;
        align-items: center;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0;
        padding-right: 0;
        display: flex;
        align-items: center;
    }
    
    .select2-dropdown {
        border-color: #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #4f46e5;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.25rem;
        border-color: #d1d5db;
        padding: 0.375rem 0.75rem;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        outline: 2px solid #6366f1;
        border-color: #a5b4fc;
    }
</style>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-xl font-semibold mb-6">Buat Pesanan Baru</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Pelanggan -->
            <div class="col-span-1">
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h2 class="text-lg font-medium mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
                        Informasi Pelanggan
                    </h2>
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-10" required>
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-10" required>
                        @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-1">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon <span class="text-red-600">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-10" required>
                        @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Informasi Produk -->
            <div class="col-span-1">
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h2 class="text-lg font-medium mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-motorcycle text-indigo-600 mr-2"></i>
                        Informasi Produk
                    </h2>
                    
                    <div class="mb-4 product-selection-container">
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk <span class="text-red-600">*</span></label>
                        <select name="product_id" id="product_id" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price }}" 
                                    data-stock="{{ $product->stock }}" 
                                    data-image="{{ $product->image_url }}"
                                    data-name="{{ $product->name }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} - {{ $product->formatted_price }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Preview gambar produk yang dipilih -->
                    <div id="product-preview" class="mb-4 hidden">
                        <div class="bg-white rounded-lg p-4 border border-indigo-100 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <img id="selected-product-image" src="" alt="Preview Produk" class="h-24 w-24 object-cover rounded-lg border border-gray-300 shadow-sm">
                                </div>
                                <div class="ml-4">
                                    <div id="selected-product-name" class="text-lg font-medium text-gray-800"></div>
                                    <div id="selected-product-price" class="text-base text-green-600 font-medium mt-1"></div>
                                    <div id="selected-product-stock" class="text-sm text-gray-500 mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-600">*</span></label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-10" required>
                            @error('quantity')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">Stok tersedia: <span id="available-stock" class="font-medium">-</span></p>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Harga</label>
                            <div class="bg-white rounded-md border border-gray-300 p-2 h-10 flex items-center">
                                <div class="text-lg font-medium text-green-600" id="total-price">Rp 0</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran <span class="text-red-600">*</span></label>
                        <div class="space-y-2 bg-white p-3 rounded-md border border-gray-300">
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded">
                                <input type="radio" name="payment_method" id="payment_cash" value="cash" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ old('payment_method') == 'cash' ? 'checked' : '' }} required>
                                <label for="payment_cash" class="ml-2 flex items-center text-sm text-gray-700">
                                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                    Tunai (Cash)
                                </label>
                            </div>
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded">
                                <input type="radio" name="payment_method" id="payment_midtrans" value="midtrans" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ old('payment_method') == 'midtrans' ? 'checked' : '' }}>
                                <label for="payment_midtrans" class="ml-2 flex items-center text-sm text-gray-700">
                                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                    Pembayaran Online (Midtrans)
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex flex-wrap justify-between">
            <a href="{{ route('admin.orders.admin-created') }}" class="flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-300 rounded-md shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mb-2 sm:mb-0">
                <i class="fas fa-history mr-2"></i>
                Pesanan Admin
            </a>
            
            <div class="flex">
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-2">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i>
                    Buat Pesanan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- jQuery harus dimuat sebelum Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Custom template untuk Select2
        function customTemplateResult(data) {
            if (!data.id) {
                return data.text;
            }
            
            const $option = $(data.element);
            const imageUrl = $option.data('image');
            const productName = $option.data('name');
            const price = $option.data('price');
            const stock = $option.data('stock');
            
            const formattedPrice = new Intl.NumberFormat('id-ID', {
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);
            
            const $result = $(
                '<div class="flex py-2">' +
                    '<div class="flex-shrink-0 w-12 h-12 mr-3">' +
                        '<img src="' + imageUrl + '" class="w-full h-full object-cover rounded" ' +
                        'onerror="this.onerror=null;this.src=\'https://via.placeholder.com/48?text=No+Image\';">' +
                    '</div>' +
                    '<div class="flex flex-col">' +
                        '<div class="font-medium">' + productName + '</div>' +
                        '<div class="text-sm text-green-600">' + formattedPrice + '</div>' +
                        '<div class="text-xs text-gray-500">Stok: ' + stock + '</div>' +
                    '</div>' +
                '</div>'
            );
            
            return $result;
        }
        
        // Custom template untuk item yang terpilih
        function customTemplateSelection(data) {
            if (!data.id) {
                return data.text;
            }
            
            const $option = $(data.element);
            const imageUrl = $option.data('image');
            const productName = $option.data('name');
            
            const $selection = $(
                '<div class="flex items-center">' +
                    '<div class="flex-shrink-0 w-6 h-6 mr-2">' +
                        '<img src="' + imageUrl + '" class="w-full h-full object-cover rounded" ' +
                        'onerror="this.onerror=null;this.src=\'https://via.placeholder.com/24?text=No+Image\';">' +
                    '</div>' +
                    '<span>' + productName + '</span>' +
                '</div>'
            );
            
            return $selection;
        }
        
        // Inisialisasi Select2
        $('.product-select').select2({
            placeholder: "Cari dan pilih produk...",
            allowClear: true,
            templateResult: customTemplateResult,
            templateSelection: customTemplateSelection
        });
        
        // Update preview produk
        function updateProductPreview() {
            const selectedOption = $('#product_id option:selected');
            
            if (selectedOption.val()) {
                const imageUrl = selectedOption.data('image');
                const productName = selectedOption.data('name');
                const price = selectedOption.data('price');
                const stock = selectedOption.data('stock');
                
                const formattedPrice = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(price);
                
                // Update preview
                $('#selected-product-image').attr('src', imageUrl);
                $('#selected-product-image').on('error', function() {
                    $(this).attr('src', 'https://via.placeholder.com/128?text=No+Image');
                });
                $('#selected-product-name').text(productName);
                $('#selected-product-price').text(formattedPrice);
                $('#selected-product-stock').text(`Stok tersedia: ${stock}`);
                
                // Tampilkan preview
                $('#product-preview').removeClass('hidden');
            } else {
                // Sembunyikan preview jika tidak ada produk yang dipilih
                $('#product-preview').addClass('hidden');
            }
        }
        
        // Update informasi stok dan harga ketika produk dipilih
        $('#product_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const stock = selectedOption.data('stock');
            const price = selectedOption.data('price');
            
            $('#available-stock').text(stock || '-');
            updateTotalPrice();
            
            // Update max quantity
            $('#quantity').attr('max', stock);
            
            // Update preview
            updateProductPreview();
        });
        
        // Update total harga saat quantity berubah
        $('#quantity').on('input', function() {
            updateTotalPrice();
        });
        
        // Fungsi untuk memperbarui total harga
        function updateTotalPrice() {
            const selectedOption = $('#product_id option:selected');
            const price = selectedOption.data('price') || 0;
            const quantity = $('#quantity').val() || 0;
            
            const total = price * quantity;
            const formattedTotal = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(total);
            
            $('#total-price').text(formattedTotal);
        }
        
        // Initialize on page load
        if ($('#product_id').val()) {
            const selectedOption = $('#product_id option:selected');
            const stock = selectedOption.data('stock');
            
            $('#available-stock').text(stock || '-');
            updateTotalPrice();
            updateProductPreview();
        }
    });
</script>
@endsection 