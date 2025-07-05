@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 sm:p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                        <i class="fas fa-times text-red-500 text-3xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Pesanan Dibatalkan</h2>
                    <p class="text-sm text-gray-600">Nomor Pesanan: <span class="font-semibold">{{ $order->order_number }}</span></p>
                    
                    <div class="mt-4 py-2 px-4 bg-red-50 border border-red-100 rounded-md inline-block">
                        <p class="text-sm text-red-600">Stok produk telah dikembalikan</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="text-center text-sm text-gray-600 mb-4">
                        <p>Anda telah membatalkan pesanan ini. Jika Anda memiliki pertanyaan, silakan hubungi customer service kami.</p>
                    </div>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('home') }}" class="py-2 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 