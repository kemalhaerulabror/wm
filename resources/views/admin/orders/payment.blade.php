@extends('admin.layouts.admin')

@section('title', 'Pembayaran Pesanan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-xl font-semibold mb-4">Pembayaran Pesanan #{{ $order->order_number }}</h1>
    
    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded relative mb-4" role="alert">
            <p class="font-medium">Pesanan telah dibuat, silahkan selesaikan pembayaran</p>
            <p class="text-sm">Mengarahkan pelanggan ke metode pembayaran yang dipilih...</p>
        </div>
        
        <div class="mb-4">
            <h2 class="text-lg font-medium mb-2">Detail Pesanan</h2>
            <div class="border rounded-md p-4 bg-gray-50">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    <div class="text-sm text-gray-600">Nomor Pesanan:</div>
                    <div class="text-sm font-medium">{{ $order->order_number }}</div>
                    
                    <div class="text-sm text-gray-600">Total Pembayaran:</div>
                    <div class="text-sm font-medium text-green-600">{{ $order->formatted_total }}</div>
                    
                    <div class="text-sm text-gray-600">Status Pembayaran:</div>
                    <div class="text-sm font-medium">
                        @if($order->payment_status == 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu Pembayaran</span>
                        @elseif($order->payment_status == 'paid')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Sudah Dibayar</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col items-center justify-center mt-6">
            <div class="w-full max-w-md">
                <div class="mb-4 text-center">
                    <p class="mb-2">Klik tombol di bawah untuk melanjutkan ke halaman pembayaran</p>
                    <a href="{{ $order->payment_url }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md">
                        Bayar Sekarang
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-500 mb-2">Atau tampilkan halaman ini pada perangkat pelanggan</p>
                    <p class="text-sm text-gray-500">Setelah pembayaran selesai, invoice dapat dicetak dari halaman detail pesanan</p>
                </div>
            </div>
        </div>
        
        <!-- Info box langsung ditampilkan -->
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative mt-4">
            <p class="font-medium">Info:</p>
            <p class="text-sm">Tunjukkan halaman ini pada perangkat pelanggan atau klik "Bayar Sekarang" untuk mengarahkan ke halaman pembayaran Midtrans.</p>
        </div>
    </div>
    
    <div class="mt-6 border-t pt-4 flex justify-between">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pesanan
        </a>
        <div>
            <form action="{{ route('admin.orders.store') }}" method="POST" class="inline-block">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md mr-2">
                    Buat Pesanan Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 