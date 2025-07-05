@extends('user.layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-4 sm:p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Profil</h1>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                @csrf
                
                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg mb-6">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" value="{{ $user->email }}" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-md shadow-sm bg-gray-100" disabled>
                        <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <a href="{{ route('profile.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                    </a>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('profileForm').addEventListener('submit', function() {
        // Menyimpan flag bahwa form telah disubmit ke session storage
        sessionStorage.setItem('profileUpdated', 'true');
    });
</script>
@endsection 