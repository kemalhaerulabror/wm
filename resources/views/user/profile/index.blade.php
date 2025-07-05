@extends('user.layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        
        <!-- Pesan notifikasi alternatif yang digunakan dengan JavaScript -->
        <div id="jsSuccessMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle mr-2"></i>
                <span id="jsSuccessText"></span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Profil Saya</h1>
            </div>
            
            <div class="bg-gray-50 p-4 sm:p-6 rounded-lg mb-6">
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                    <div class="flex-1">
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-500 text-sm mb-1">Nama</h3>
                            <p class="text-lg">{{ $user->name }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-500 text-sm mb-1">Email</h3>
                            <p class="text-lg">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-500 text-sm mb-1">No. Telepon</h3>
                            <p class="text-lg">{{ $user->phone ?? 'Belum diatur' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-4">
                    <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm text-center">
                        Edit Profil
                    </a>
                    
                    <a href="{{ route('profile.change.password') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded text-sm text-center">
                        Ganti Password
                    </a>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('profile.orders') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-shopping-bag mr-2"></i> Lihat Riwayat Pesanan
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada update profil dari session storage
        if (sessionStorage.getItem('profileUpdated')) {
            // Tampilkan pesan sukses
            const successMessage = document.getElementById('jsSuccessMessage');
            const successText = document.getElementById('jsSuccessText');
            
            successText.textContent = 'Profil berhasil diperbarui';
            successMessage.classList.remove('hidden');
            
            // Hapus dari session storage
            sessionStorage.removeItem('profileUpdated');
            
            // Sembunyikan pesan setelah 5 detik
            setTimeout(function() {
                successMessage.classList.add('hidden');
            }, 5000);
        }
        
        // Cek apakah ada update password dari session storage
        if (sessionStorage.getItem('passwordUpdated')) {
            // Tampilkan pesan sukses
            const successMessage = document.getElementById('jsSuccessMessage');
            const successText = document.getElementById('jsSuccessText');
            
            successText.textContent = 'Password berhasil diperbarui';
            successMessage.classList.remove('hidden');
            
            // Hapus dari session storage
            sessionStorage.removeItem('passwordUpdated');
            
            // Sembunyikan pesan setelah 5 detik
            setTimeout(function() {
                successMessage.classList.add('hidden');
            }, 5000);
        }
    });
</script>
@endsection 