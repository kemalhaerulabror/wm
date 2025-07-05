@extends('user.layouts.app')

@section('title', 'Ganti Password')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-4 sm:p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Ganti Password</h1>
            </div>

            <form action="{{ route('profile.update.password') }}" method="POST" id="passwordForm">
                @csrf
                
                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg mb-6">
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <a href="{{ route('profile.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                    </a>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('passwordForm').addEventListener('submit', function() {
        // Menyimpan flag bahwa form password telah disubmit ke session storage
        sessionStorage.setItem('passwordUpdated', 'true');
    });
</script>
@endsection 