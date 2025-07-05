@extends('admin.layouts.admin')

@section('title', 'Daftar Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Admin</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($admins as $key => $admin)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admins->firstItem() + $key }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($admin->photo)
                                <img class="h-10 w-10 rounded-full object-cover" 
                                     src="{{ asset('upload/admin_images/'.$admin->photo) }}" 
                                     alt="{{ $admin->name }}">
                                @else
                                <img class="h-10 w-10 rounded-full object-cover" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&color=7F9CF5&background=EBF4FF" 
                                     alt="{{ $admin->name }}">
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->email }}
                        </td>
                                                <td class="px-6 py-4 whitespace-nowrap">                            @php                                $roleClass = $admin->role == 'superadmin' ? 'bg-indigo-100 text-indigo-800' : 'bg-cyan-100 text-cyan-800';                                $roleText = $admin->role ?? 'admin';                            @endphp                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">                                {{ $roleText }}                            </span>                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.admin-users.show', $admin->id) }}" class="inline-flex items-center justify-center w-24 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors">
                                <i class="fas fa-eye mr-1.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-user-shield text-gray-300 text-4xl mb-3"></i>
                                <p>Tidak ada data admin</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $admins->links() }}
        </div>
    </div>
</div>
@endsection 