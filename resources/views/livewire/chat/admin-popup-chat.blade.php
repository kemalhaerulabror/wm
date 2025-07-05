<div>
    <!-- Icon Chat di pojok kanan bawah untuk admin -->
    <div class="fixed bottom-5 right-5 z-40">
        <button wire:click="toggleChat" class="relative flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-comments text-xl"></i>
            
            <!-- Badge untuk pesan belum dibaca -->
            @if($totalUnreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs rounded-full">
                {{ $totalUnreadCount }}
            </span>
            @endif
        </button>
    </div>
    
    <!-- Chat Box Popup -->
    <div class="fixed bottom-20 right-5 z-40 w-[360px] sm:w-[450px] bg-white rounded-lg shadow-xl overflow-hidden transition-all duration-300 transform {{ $chatOpen ? 'scale-100' : 'scale-0' }}"
        style="{{ $chatOpen ? 'opacity: 1;' : 'opacity: 0; pointer-events: none;' }}">
        
        <!-- Chat Header -->
        <div class="bg-gray-800 text-white p-3 flex justify-between items-center">
            <h3 class="font-medium">Live Chat Admin</h3>
            <button wire:click="toggleChat" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex h-[500px]">
            <!-- User List -->
            <div class="w-1/3 border-r overflow-y-auto" wire:poll.10s="refreshMessages">
                @forelse($users as $user)
                    <div 
                        wire:key="user-{{ $user['id'] }}"
                        wire:click="selectUser({{ $user['id'] }})" 
                        class="p-2 flex items-center hover:bg-gray-100 cursor-pointer {{ $selectedUserId == $user['id'] ? 'bg-blue-50' : '' }}"
                    >
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-600 text-xs"></i>
                        </div>
                        <div class="ml-2 flex-grow overflow-hidden">
                            <p class="font-medium text-gray-800 text-sm truncate">{{ $user['name'] }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user['email'] }}</p>
                        </div>
                        @if(isset($unreadCounts[$user['id']]) && $unreadCounts[$user['id']] > 0)
                            <div class="flex-shrink-0">
                                <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                    {{ $unreadCounts[$user['id']] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-center text-gray-500 text-sm">
                        Belum ada pengguna chat
                    </div>
                @endforelse
            </div>
            
            <!-- Chat Area -->
            <div class="w-2/3 flex flex-col">
                @if($selectedUserId)
                    <!-- Selected User Header -->
                    <div class="bg-gray-100 p-2 border-b flex items-center">
                        @if($selectedUser = collect($users)->firstWhere('id', $selectedUserId))
                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-gray-600 text-xs"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-medium text-gray-800 text-sm">{{ $selectedUser['name'] }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div class="flex-grow p-3 overflow-y-auto" id="admin-popup-messages" wire:poll.3s="refreshMessages">
                        @forelse($chatMessages as $chat)
                            <div class="mb-3 {{ $chat['is_admin'] ? 'text-right' : 'text-left' }}">
                                <div class="inline-block max-w-[80%] {{ $chat['is_admin'] ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg px-3 py-2 text-sm">
                                    {{ $chat['message'] }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($chat['created_at'])->setTimezone('Asia/Jakarta')->format('H:i') }}
                                </div>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-full text-gray-500">
                                <p class="text-sm">Belum ada pesan.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="border-t p-2">
                        <form wire:submit.prevent="sendMessage" class="flex">
                            <input 
                                wire:model="message" 
                                type="text" 
                                class="flex-grow border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                placeholder="Ketik pesan..."
                            >
                            <button 
                                type="submit" 
                                class="bg-blue-600 text-white px-3 py-2 rounded-r-lg hover:bg-blue-700"
                            >
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full bg-gray-50">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-comments text-2xl mb-2"></i>
                            <p class="text-sm">Pilih pengguna untuk chat</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollToBottom = () => {
                const chatMessages = document.getElementById('admin-popup-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            };
            
            @this.on('refreshMessages', () => {
                setTimeout(scrollToBottom, 50);
            });
            
            @this.on('selectUser', () => {
                setTimeout(scrollToBottom, 100);
            });
        });
    </script>
</div>
