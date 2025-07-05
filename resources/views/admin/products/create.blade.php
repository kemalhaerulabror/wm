@extends('admin.layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-3 sm:py-5">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Tambah Produk Baru</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Lengkapi form berikut untuk menambahkan produk baru</p>
    </div>

    <div class="card p-4 sm:p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Nama Produk -->
                <div>
                    <label for="name" class="form-label text-sm sm:text-base">Nama Produk</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="form-input text-sm sm:text-base @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="form-label text-sm sm:text-base">Kategori</label>
                    <select name="category" id="category" class="form-input text-sm sm:text-base @error('category') border-red-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="form-label text-sm sm:text-base">Brand</label>
                    <select name="brand" id="brand" class="form-input text-sm sm:text-base @error('brand') border-red-500 @enderror">
                        <option value="">Pilih Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ old('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                    @error('brand')
                        <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="form-label text-sm sm:text-base">Harga (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm sm:text-base">Rp</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" 
                               class="form-input pl-10 text-sm sm:text-base @error('price') border-red-500 @enderror">
                    </div>
                    @error('price')
                        <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok -->
                <div>
                    <label for="stock" class="form-label text-sm sm:text-base">Stok</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" 
                           class="form-input text-sm sm:text-base @error('stock') border-red-500 @enderror">
                    @error('stock')
                        <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar Produk -->
                <div class="md:col-span-2">
                    <label class="form-label text-sm sm:text-base">Gambar Produk</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm">
                            <div class="flex flex-col items-center">
                                <div class="w-full mb-3 sm:mb-4 text-center">
                                    <p class="text-xs sm:text-sm font-medium text-gray-700 mb-1">Format: JPG, PNG, JPEG, GIF (maks. 2MB)</p>
                                </div>
                                <!-- Upload Zone -->
                                <div class="relative w-full">
                                    <label for="image" class="flex flex-col items-center justify-center w-full h-36 sm:h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                        <div class="flex flex-col items-center justify-center pt-4 pb-5 sm:pt-5 sm:pb-6" id="upload-placeholder">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-2 sm:mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                            <p class="text-xs text-gray-500" id="file-name-display">Pilih file gambar</p>
                                        </div>
                                        <input id="image" name="image" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                                @error('image')
                                    <p class="form-error mt-2 text-center text-xs sm:text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Preview -->
                        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm flex flex-col">
                            <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">Preview:</p>
                            <div class="flex-grow flex items-center justify-center bg-gray-100 rounded-lg h-36 sm:h-48 overflow-hidden preview-container">
                                <div id="preview-placeholder" class="text-center px-4">
                                    <svg class="mx-auto w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-xs sm:text-sm text-gray-500">Preview gambar akan muncul di sini</p>
                                </div>
                                <img id="preview-image" src="#" alt="Preview" class="hidden max-w-full max-h-full object-contain">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="form-label text-sm sm:text-base">Status</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" name="status" id="status_active" value="1" checked 
                                   class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                            <label for="status_active" class="ml-2 block text-xs sm:text-sm text-gray-700">Aktif</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="status" id="status_inactive" value="0" 
                                   class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                            <label for="status_inactive" class="ml-2 block text-xs sm:text-sm text-gray-700">Nonaktif</label>
                        </div>
                    </div>
                </div>

                <!-- Featured -->
                <div>
                    <label class="form-label text-sm sm:text-base">Featured</label>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                               class="w-4 h-4 text-gray-600 border-gray-300 rounded focus:ring-gray-500">
                        <label for="is_featured" class="ml-2 block text-xs sm:text-sm text-gray-700">
                            Tampilkan di bagian "Rekomendasi Untuk Anda"
                        </label>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-4 sm:mt-6">
                <label for="description" class="form-label text-sm sm:text-base">Deskripsi</label>
                <textarea name="description" id="description" rows="4" 
                          class="form-input text-sm sm:text-base @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error text-xs sm:text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4 sm:mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="btn-secondary text-xs sm:text-sm py-2 px-3 sm:px-4">
                    Batal
                </a>
                <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded text-xs sm:text-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const previewImage = document.getElementById('preview-image');
        const previewPlaceholder = document.getElementById('preview-placeholder');
        const fileNameDisplay = document.getElementById('file-name-display');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Update file name display
                const fileName = file.name;
                fileNameDisplay.textContent = fileName.length > 20 
                    ? fileName.substring(0, 17) + '...' 
                    : fileName;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    previewPlaceholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                // Reset if no file selected
                fileNameDisplay.textContent = 'Pilih file gambar';
                previewImage.classList.add('hidden');
                previewPlaceholder.classList.remove('hidden');
            }
        });
    });
</script>
@endsection

@endsection 