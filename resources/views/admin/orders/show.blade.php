@extends('admin.layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $order->order_number }}</h1>
        <div class="flex space-x-2">
            @if($order->payment_status === 'paid')
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-file-invoice mr-2"></i>Invoice
            </a>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Informasi Pesanan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Informasi Umum -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-lg">Informasi Pesanan</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Nomor Pesanan:</span>
                    <p class="font-semibold">{{ $order->order_number }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Tanggal Pesan:</span>
                    <p class="font-semibold">{{ $order->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Total:</span>
                    <p class="font-semibold text-lg">{{ $order->formatted_total }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Status:</span>
                    @php
                        $statusClass = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ][$order->status] ?? 'bg-gray-100 text-gray-800';
                        
                        $statusLabel = $order->status_label;
                    @endphp
                    <div class="mt-1">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Status Pembayaran:</span>
                    @php
                        $paymentClass = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ][$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                        
                        $paymentLabel = [
                            'pending' => 'Menunggu Pembayaran',
                            'paid' => 'Sudah Dibayar',
                            'failed' => 'Gagal',
                            'cancelled' => 'Dibatalkan'
                        ][$order->payment_status] ?? 'Tidak Diketahui';
                    @endphp
                    <div class="mt-1">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $paymentClass }}">
                            {{ $paymentLabel }}
                        </span>
                    </div>
                </div>
                
                <!-- Tambahkan informasi metode pembayaran di sini -->
                <div class="mt-4">
                    <span class="text-sm text-gray-600">Metode Pembayaran:</span>
                    <p class="font-semibold">{{ $order->payment_method ?? 'Tidak Tersedia' }}</p>
                </div>
                
                @if($order->payment_status === 'paid')
                <div class="mt-4">
                    <span class="text-sm text-gray-600">Tanggal Pembayaran:</span>
                    <p class="font-semibold">{{ optional($order->updated_at)->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</p>
                </div>
                @endif
                
                
            </div>
        </div>

        <!-- Informasi Customer -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-lg">Informasi Customer</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 mr-3">
                        <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name ?? 'Customer') }}&color=7F9CF5&background=EBF4FF" alt="{{ $order->customer_name ?? 'Customer' }}">
                    </div>
                    <div>
                        <h3 class="font-medium">{{ $order->customer_name ?? 'Customer Tidak Diketahui' }}</h3>
                        <p class="text-sm text-gray-500">{{ $order->customer_email ?? 'Email tidak tersedia' }}</p>
                    </div>
                </div>
                @if($order->customer_phone)
                <div class="mb-4">
                    <span class="text-sm text-gray-600">No. Telepon:</span>
                    <p class="font-semibold">{{ $order->customer_phone }}</p>
                </div>
                @endif
                <div class="text-sm text-gray-600">
                    <p>Pesanan dibuat oleh Admin pada {{ $order->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY') }}</p>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-lg">Update Status Pesanan</h2>
            </div>
            <div class="p-6">
                @if($order->status == 'processing')
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <input type="hidden" name="status" value="completed">
                    
                    <p class="mb-3 text-sm text-gray-600">Pesanan sudah siap untuk diselesaikan?</p>
                    
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-check-circle mr-2"></i> Tandai Pesanan Selesai
                        </button>
                    </div>
                </form>
                @else
                    @if($order->status == 'pending')
                        <div class="text-center py-4 text-yellow-600">
                            <i class="fas fa-clock text-3xl mb-2"></i>
                            <p>Pesanan masih menunggu pembayaran</p>
                        </div>
                    @elseif($order->status == 'completed')
                        @if($order->user_confirmed)
                            <div class="text-center py-4 text-green-600">
                                <i class="fas fa-check-circle text-3xl mb-2"></i>
                                <p>Pesanan telah selesai dan dikonfirmasi oleh pelanggan</p>
                                <p class="text-sm mt-2">Tanggal konfirmasi: {{ $order->user_confirmed_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                            </div>
                        @else
                            <div class="text-center py-4 text-blue-600">
                                <i class="fas fa-info-circle text-3xl mb-2"></i>
                                <p>Pesanan sudah selesai dari admin</p>
                                <p class="text-sm mt-2">Menunggu konfirmasi dari pelanggan</p>
                            </div>
                        @endif
                    @elseif($order->status == 'cancelled')
                        <div class="text-center py-4 text-red-600">
                            <i class="fas fa-times-circle text-3xl mb-2"></i>
                            <p>Pesanan telah dibatalkan</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Detail Produk -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-lg">Detail Produk</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($item->product && $item->product->image_url)
                                    <img class="h-10 w-10 rounded object-cover" src="{{ asset($item->product->image_url) }}" alt="{{ $item->product_name }}">
                                    @else
                                    <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-motorcycle text-gray-400"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $item->product_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->formatted_price }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->formatted_subtotal }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            Total Pembayaran:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $order->formatted_total }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection 