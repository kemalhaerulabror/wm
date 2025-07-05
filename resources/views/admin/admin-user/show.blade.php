@extends('admin.layouts.admin')

@section('title', 'Detail Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Admin</h1>
        <a href="{{ route('admin.admin-users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex flex-col md:flex-row items-start">
            <div class="flex-shrink-0 mb-6 md:mb-0 md:mr-6">
                @if($admin->photo)
                <img class="h-48 w-48 rounded-lg object-cover shadow-md" 
                     src="{{ asset('upload/admin_images/'.$admin->photo) }}" 
                     alt="{{ $admin->name }}">
                @else
                <div class="h-48 w-48 rounded-lg bg-gray-200 flex items-center justify-center shadow-md">
                    <img class="h-48 w-48 rounded-lg object-cover" 
                         src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&color=7F9CF5&background=EBF4FF&size=200" 
                         alt="{{ $admin->name }}">
                </div>
                @endif
            </div>
            
            <div class="flex-1">
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                                Informasi Pribadi
                            </h2>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Nama Lengkap</p>
                                <p class="font-medium">{{ $admin->name }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $admin->email }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Role</p>
                                @php
                                    $roleClass = $admin->role == 'superadmin' ? 'bg-indigo-100 text-indigo-800' : 'bg-cyan-100 text-cyan-800';
                                    $roleText = ucfirst($admin->role ?? 'admin');
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                    {{ $roleText }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-gray-500"></i>
                                Informasi Akun
                            </h2>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">ID Admin</p>
                                <p class="font-medium">{{ $admin->id }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Tanggal Registrasi</p>
                                <p class="font-medium">
                                    @if($admin->created_at)
                                    {{ $admin->created_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                                    @else
                                    Tidak ada data
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Terakhir Update</p>
                                <p class="font-medium">
                                    @if($admin->updated_at)
                                    {{ $admin->updated_at->setTimezone('Asia/Jakarta')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                                    @else
                                    Tidak ada data
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 