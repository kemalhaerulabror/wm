@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6">Pembayaran</h1>
    
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 sm:p-6">
                <div class="text-center mb-6">
                    <i class="fas fa-check-circle text-green-500 text-3xl sm:text-5xl mb-3"></i>
                    <h2 class="text-base sm:text-lg font-medium text-gray-800">Pesanan Berhasil Dibuat</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 mb-4">Nomor Pesanan: <span class="font-semibold">{{ $order->order_number }}</span></p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h3 class="text-sm font-medium text-gray-800 mb-2">Ringkasan Pesanan</h3>
                    <div class="flex justify-between text-xs sm:text-sm mb-1">
                        <span class="text-gray-600">Total Pembayaran</span>
                        <span class="font-bold">{{ $order->formatted_total }}</span>
                    </div>
                    <div class="flex justify-between text-xs sm:text-sm">
                        <span class="text-gray-600">Status Pembayaran</span>
                        <span class="font-medium">
                            @if($order->payment_status == 'pending')
                                <span class="text-yellow-600">Menunggu Pembayaran</span>
                            @elseif($order->payment_status == 'paid')
                                <span class="text-green-600">Sudah Dibayar</span>
                            @else
                                <span class="text-red-600">{{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4 sm:p-6 mb-4">
                    <h3 class="text-sm font-medium text-gray-800 mb-3">Instruksi Pembayaran</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Silakan lakukan pembayaran dengan mengklik tombol di bawah ini. Anda akan diarahkan ke halaman pembayaran aman dari Midtrans.</p>
                    
                    <div class="flex flex-col space-y-3">
                        <a href="{{ $order->payment_url }}" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs sm:text-sm font-medium text-center">
                            Bayar Sekarang
                        </a>
                        
                        <a href="{{ route('checkout.success', $order->id) }}" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded text-xs sm:text-sm font-medium text-center border border-gray-200">
                            Saya Sudah Bayar
                        </a>
                        
                        <button type="button" id="cancelButton" class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded text-xs sm:text-sm font-medium border border-red-200">
                            Batalkan Pesanan
                        </button>
                        
                        <form id="cancelForm" action="{{ route('checkout.cancel', $order->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
                
                <div class="text-center text-xs sm:text-sm text-gray-500">
                    <p>Pembayaran harus dilakukan dalam waktu 24 jam.</p>
                    <p>Jika Anda mengalami kendala, mohon hubungi customer service kami.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal Dialog Konfirmasi Pembatalan -->
<div id="cancelModal" class="fixed inset-0 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden border border-gray-200">
        <div class="p-5 sm:p-6">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
            </div>
            
            <h3 class="text-center text-lg sm:text-xl font-bold text-gray-800 mb-2">Batalkan Pesanan?</h3>
            
            <div class="text-center mb-6">
                <p class="text-sm text-gray-600 mb-2">Apakah Anda yakin ingin membatalkan pesanan ini?</p>
                <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                
                <div class="mt-4 py-2 px-4 bg-yellow-50 border border-yellow-100 rounded-md text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i> Stok produk akan dikembalikan dan pesanan akan dibatalkan secara permanen.
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row-reverse space-y-3 sm:space-y-0 sm:space-x-3 sm:space-x-reverse">
                <button id="confirmCancel" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm text-center">
                    Ya, Batalkan Pesanan
                </button>
                <button id="cancelCancel" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg text-sm text-center">
                    Tidak, Kembali
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cancelButton = document.getElementById('cancelButton');
        const cancelModal = document.getElementById('cancelModal');
        const confirmCancel = document.getElementById('confirmCancel');
        const cancelCancel = document.getElementById('cancelCancel');
        const cancelForm = document.getElementById('cancelForm');
        
        // Tampilkan modal saat tombol batalkan diklik
        cancelButton.addEventListener('click', function() {
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        });
        
        // Sembunyikan modal saat tombol kembali diklik
        cancelCancel.addEventListener('click', function() {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
        });
        
        // Submit form pembatalan saat konfirmasi
        confirmCancel.addEventListener('click', function() {
            cancelForm.submit();
        });
        
        // Sembunyikan modal ketika klik di luar modal
        cancelModal.addEventListener('click', function(e) {
            if (e.target === cancelModal) {
                cancelModal.classList.add('hidden');
                cancelModal.classList.remove('flex');
            }
        });
    });
</script>
@endpush

@endsection 