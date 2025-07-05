@extends('admin.layouts.admin')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <div class="bg-green-100 text-green-800 p-4 rounded-lg inline-block mb-4">
            <i class="fas fa-check-circle text-5xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pembayaran Berhasil!</h1>
        <p class="text-gray-600 mt-2">Pembayaran untuk pesanan #{{ $order->order_number }} telah berhasil diproses.</p>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Rincian Pesanan</h2>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">Nomor Pesanan:</p>
                <p class="font-medium">{{ $order->order_number }}</p>
            </div>
            
            <div>
                <p class="text-gray-600">Tanggal Pesanan:</p>
                <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-gray-600">Status Pembayaran:</p>
                <p class="font-medium">
                    @if($order->payment_status == 'paid')
                        <span class="text-green-600">Dibayar</span>
                    @else
                        <span class="text-yellow-600">{{ ucfirst($order->payment_status) }}</span>
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-gray-600">Status Pesanan:</p>
                <p class="font-medium">{{ ucfirst($order->status) }}</p>
            </div>
            
            <div>
                <p class="text-gray-600">Metode Pembayaran:</p>
                <p class="font-medium">{{ $order->payment_method }}</p>
            </div>
            
            <div>
                <p class="text-gray-600">Total Pembayaran:</p>
                <p class="font-medium text-green-600">{{ $order->formatted_total }}</p>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between mt-6">
        <a href="{{ route('admin.orders.admin-created') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
            <i class="fas fa-history mr-2"></i> Lihat Pesanan Admin
        </a>
        
        <div class="flex space-x-2">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700">
                <i class="fas fa-eye mr-2"></i> Detail Pesanan
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                <i class="fas fa-file-invoice mr-2"></i> Cetak Invoice
            </a>
        </div>
    </div>
</div>
@endsection 