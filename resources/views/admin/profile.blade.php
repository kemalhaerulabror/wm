@extends('admin.layouts.admin')

@section('title', 'Profil Admin')

@section('content')
<div class="py-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Admin</h1>
        <p class="text-gray-600">Kelola informasi profil Anda</p>
    </div>
    
    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Foto Profil -->
            <div class="md:col-span-1">
                <div class="flex flex-col items-center">
                    <div class="mb-4">
                        @if($adminData->photo)
                            <img class="h-32 w-32 rounded-full object-cover border-4 border-gray-200" 
                                src="{{ asset('upload/admin_images/'.$adminData->photo) }}" 
                                alt="Foto Profil">
                        @else
                            <div class="h-32 w-32 rounded-full bg-gray-600 flex items-center justify-center border-4 border-gray-200">
                                <i class="fas fa-user text-white text-5xl"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $adminData->name }}</h3>
                    <p class="text-gray-500">{{ $adminData->email }}</p>
                    <p class="text-sm text-gray-700 bg-gray-200 px-3 py-1 rounded-full mt-2">{{ ucfirst($adminData->role) }}</p>
                </div>
            </div>
            
            <!-- Form Edit Profil -->
            <div class="md:col-span-2">
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="name" name="name" value="{{ $adminData->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ $adminData->email }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="photo" class="block mb-2 text-sm font-medium text-gray-700">Foto Profil</label>
                        <input type="file" id="photo" name="photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG atau JPEG (Maks. 2MB)</p>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.change.password') }}" class="text-gray-600 hover:underline text-sm">
                            <i class="fas fa-key mr-1"></i> Ganti Password
                        </a>
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 