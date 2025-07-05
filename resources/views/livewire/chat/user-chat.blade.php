<div>
    <!-- Icon Chat dan WhatsApp di pojok kanan bawah -->
    <div class="fixed bottom-5 right-5 z-40 flex flex-col items-end space-y-3">
        <!-- WhatsApp Button -->
        <a href="https://wa.me/6281382139622" target="_blank" class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500 text-white shadow-lg hover:bg-green-600 transition-colors">
            <i class="fab fa-whatsapp text-xl"></i>
        </a>
        
        <!-- Live Chat Button (hanya untuk user yang login) -->
        @auth
        <button wire:click="toggleChat" class="relative flex items-center justify-center w-12 h-12 rounded-full bg-blue-500 text-white shadow-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-comments text-xl"></i>
            
            <!-- Badge untuk pesan belum dibaca -->
            @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs rounded-full">
                {{ $unreadCount }}
            </span>
            @endif
        </button>
        @endauth
    </div>
    
    <!-- Chat Box -->
    <div class="fixed bottom-20 right-5 z-40 w-80 sm:w-96 bg-white rounded-lg shadow-xl overflow-hidden transition-all duration-300 transform {{ $chatOpen ? 'scale-100' : 'scale-0' }}"
        style="{{ $chatOpen ? 'opacity: 1;' : 'opacity: 0; pointer-events: none;' }}">
        
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-3 flex justify-between items-center">
            <h3 class="font-medium">Live Chat</h3>
            <button wire:click="toggleChat" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Chat Messages -->
        <div class="p-3 h-80 overflow-y-auto" id="chat-messages" wire:poll.3s="refreshMessages">
            @if(count($chatMessages) > 0)
                @foreach($chatMessages as $chat)
                    <!-- Message -->
                    <div class="mb-3 {{ $chat['is_admin'] ? 'text-left' : 'text-right' }}">
                        <div class="{{ $chat['is_admin'] ? 'bg-gray-200 text-gray-800' : 'bg-blue-600 text-white' }} inline-block rounded-lg px-3 py-2 max-w-[80%]">
                            {{ $chat['message'] }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($chat['created_at'])->setTimezone('Asia/Jakarta')->format('H:i') }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="flex items-center justify-center h-full text-gray-500">
                    <p>Mulai chat dengan kami.</p>
                </div>
            @endif
        </div>
        
        <!-- Chat Input -->
        <div class="border-t p-3">
            <form wire:submit.prevent="sendMessage" class="flex">
                <input wire:model="message" type="text" class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ketik pesan...">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Auto scroll to bottom when new messages arrive
        document.addEventListener('livewire:initialized', () => {
            const scrollToBottom = () => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            };
            
            @this.on('refreshMessages', () => {
                setTimeout(scrollToBottom, 50);
            });
            
            @this.on('toggleChat', () => {
                setTimeout(scrollToBottom, 100);
            });
        });
    </script>
</div>
