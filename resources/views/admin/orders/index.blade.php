@extends('admin.layouts.admin')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h1>
    </div>

    <livewire:admin.order-list />
</div>
@endsection 