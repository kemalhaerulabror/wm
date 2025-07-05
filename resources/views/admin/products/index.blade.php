@extends('admin.layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-3 sm:py-5">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Daftar Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded text-sm sm:text-base inline-flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline text-sm sm:text-base">{{ session('success') }}</span>
    </div>
    @endif

    <livewire:admin.product-list />
</div>
@endsection 