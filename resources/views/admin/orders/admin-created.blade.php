@extends('admin.layouts.admin')

@section('title', 'Riwayat Pesanan Admin')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">Riwayat Pesanan yang Dibuat oleh Admin</h1>
        <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-1"></i> Buat Pesanan Baru
        </a>
    </div>
    
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
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-3 px-4 border-b text-left">Order #</th>
                    <th class="py-3 px-4 border-b text-left">Pelanggan</th>
                    <th class="py-3 px-4 border-b text-left">Total</th>
                    <th class="py-3 px-4 border-b text-left">Metode Pembayaran</th>
                    <th class="py-3 px-4 border-b text-left">Status Pembayaran</th>
                    <th class="py-3 px-4 border-b text-left">Status Pesanan</th>
                    <th class="py-3 px-4 border-b text-left">Tanggal</th>
                    <th class="py-3 px-4 border-b text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b">{{ $order->order_number }}</td>
                        <td class="py-3 px-4 border-b">
                            {{ $order->customer_name ?? 'Pelanggan Tidak Diketahui' }}
                            <div class="text-xs text-gray-500">{{ $order->customer_email ?? '' }}</div>
                        </td>
                        <td class="py-3 px-4 border-b font-medium">{{ $order->formatted_total }}</td>
                        <td class="py-3 px-4 border-b">{{ $order->payment_method ?: 'Tidak tersedia' }}</td>
                        <td class="py-3 px-4 border-b">
                            @if($order->payment_status == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu</span>
                            @elseif($order->payment_status == 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Dibayar</span>
                            @elseif($order->payment_status == 'failed' || $order->payment_status == 'cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ ucfirst($order->payment_status) }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b">
                            @if($order->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu</span>
                            @elseif($order->status == 'processing')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Diproses</span>
                            @elseif($order->status == 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Selesai</span>
                            @elseif($order->status == 'cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Dibatalkan</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b">
                            <div>{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 border-b">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($order->payment_status == 'paid')
                                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="text-green-600 hover:text-green-800" target="_blank">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                                
                                @if($order->payment_status == 'pending' && $order->payment_url)
                                    <a href="{{ route('admin.orders.payment', $order->id) }}" class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 px-4 border-b text-center text-gray-500">
                            Belum ada pesanan yang dibuat. Klik "Buat Pesanan Baru" untuk membuat pesanan admin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection 