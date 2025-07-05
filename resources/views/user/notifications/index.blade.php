@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8" 
     x-data="{ 
        showDeleteModal: false, 
        showDeleteAllModal: false,
        deleteUrl: '',
        deleteBulk: false
     }">
    <div class="flex justify-between items-center mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-xl md:text-2xl font-bold">Notifikasi</h1>
        <a href="{{ route('home') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-chevron-left mr-1"></i> Kembali ke Beranda
        </a>
    </div>
    
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 sm:px-4 sm:py-3 rounded relative mb-4 sm:mb-6">
        <span class="block sm:inline text-xs sm:text-sm">{{ session('success') }}</span>
    </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-base sm:text-lg font-medium">Semua Notifikasi</h2>
            @if(count($notifications) > 0)
            <div class="flex gap-2 sm:gap-4">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800">
                        Tandai semua dibaca
                    </button>
                </form>
                <button type="button" 
                        @click="showDeleteAllModal = true; deleteBulk = true"
                        class="text-xs sm:text-sm text-red-600 hover:text-red-800">
                    Hapus yang sudah dibaca
                </button>
            </div>
            @endif
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
            <div class="p-4 sm:p-6 hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                        @if($notification->type == 'success')
                            <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-green-100 text-green-500 rounded-full">
                                <i class="fa-solid fa-check text-xs sm:text-sm"></i>
                            </span>
                        @elseif($notification->type == 'error')
                            <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-red-100 text-red-500 rounded-full">
                                <i class="fa-solid fa-times text-xs sm:text-sm"></i>
                            </span>
                        @elseif($notification->type == 'warning')
                            <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 text-yellow-500 rounded-full">
                                <i class="fa-solid fa-exclamation text-xs sm:text-sm"></i>
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 text-blue-500 rounded-full">
                                <i class="fa-solid fa-info text-xs sm:text-sm"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <a href="{{ $notification->link }}" class="text-sm sm:text-base font-medium {{ $notification->is_read ? 'text-gray-700' : 'text-blue-600' }}">
                                {{ $notification->title }}
                            </a>
                            <div class="text-xs text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-600 mb-2">{{ $notification->message }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <div>
                                <!-- Informasi model dihapus karena redundan -->
                            </div>
                            <div class="flex gap-2">
                                @if(!$notification->is_read)
                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                        Tandai dibaca
                                    </button>
                                </form>
                                @endif
                                <button type="button" 
                                        @click="showDeleteModal = true; deleteUrl = '{{ route('notifications.destroy', $notification->id) }}'"
                                        class="text-xs text-red-600 hover:text-red-800">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center">
                <i class="fa-solid fa-bell-slash text-gray-300 text-2xl sm:text-3xl mb-2"></i>
                <p class="text-sm sm:text-base text-gray-500 mb-4">Anda belum memiliki notifikasi</p>
                <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-xs sm:text-sm">
                    Kembali ke Beranda
                </a>
            </div>
            @endforelse
        </div>
        
        @if(count($notifications) > 0)
        <div class="p-4 sm:p-6 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
    
    <!-- Modal Konfirmasi Hapus Single -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="relative w-11/12 max-w-md p-6 mx-auto bg-white rounded-lg shadow-lg"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fa-solid fa-trash text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus notifikasi ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-center space-x-3">
                    <button @click="showDeleteModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Hapus Bulk -->
    <div x-show="showDeleteAllModal" 
         class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="relative w-11/12 max-w-md p-6 mx-auto bg-white rounded-lg shadow-lg"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fa-solid fa-trash text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus semua notifikasi yang sudah dibaca? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-center space-x-3">
                    <button @click="showDeleteAllModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <form action="{{ route('notifications.destroy-read') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 