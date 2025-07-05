@extends('admin.layouts.admin')

@section('title', 'Ganti Password')

@section('content')
<div class="py-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ganti Password</h1>
        <p class="text-gray-600">Perbarui password akun admin Anda</p>
    </div>
    
    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-sm p-6 max-w-lg mx-auto">
        <form method="POST" action="{{ route('admin.update.password') }}">
            @csrf
            
            <div class="mb-6">
                <label for="old_password" class="block mb-2 text-sm font-medium text-gray-700">Password Lama</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="old_password" name="old_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-10 p-2.5 @error('old_password') @enderror" placeholder="••••••••" required>
                </div>
                @error('old_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="new_password" class="block mb-2 text-sm font-medium text-gray-700">Password Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-key text-gray-400"></i>
                    </div>
                    <input type="password" id="new_password" name="new_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-10 p-2.5 @error('new_password') @enderror" placeholder="••••••••" required>
                </div>
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="new_password_confirmation" class="block mb-2 text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-check-double text-gray-400"></i>
                    </div>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-10 p-2.5" placeholder="••••••••" required>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.profile') }}" class="text-gray-600 hover:underline text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Profil
                </a>
                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    <i class="fas fa-save mr-1"></i> Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 