@extends('admin.layouts.admin')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Pelanggan</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-6">
                <img class="h-32 w-32 rounded-full object-cover" 
                     src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF&size=128" 
                     alt="{{ $user->name }}">
            </div>
            
            <div class="flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Nama Lengkap</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">No. Telepon</p>
                            <p class="font-medium">{{ $user->phone ?: 'Tidak ada' }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Status Verifikasi</p>
                            @php
                                $verificationClass = $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $verificationStatus = $user->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi';
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $verificationClass }}">
                                {{ $verificationStatus }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun</h2>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Tanggal Registrasi</p>
                            <p class="font-medium">{{ $user->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Terakhir Update</p>
                            <p class="font-medium">{{ $user->updated_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Tanggal Verifikasi</p>
                            <p class="font-medium">{{ $user->email_verified_at ? $user->email_verified_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm').' WIB' : 'Belum diverifikasi' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($user->orders->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Pesanan</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @php
                                        $statusColors = [
                                            'PENDING' => 'bg-yellow-100 text-yellow-800',
                                            'PAID' => 'bg-green-100 text-green-800',
                                            'CANCELLED' => 'bg-red-100 text-red-800',
                                            'SHIPPING' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    {{ $statusClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 