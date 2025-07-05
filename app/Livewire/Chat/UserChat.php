<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserChat extends Component
{
    public $message = '';
    public $chatMessages = [];
    public $chatOpen = false;
    public $unreadCount = 0;

    protected $listeners = ['new-message-for-user' => 'refreshMessages'];

    public function mount()
    {
        $this->loadMessages();
        $this->countUnreadMessages();
    }

    public function loadMessages()
    {
        if (Auth::check()) {
            $this->chatMessages = Chat::where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
        }
    }

    public function countUnreadMessages()
    {
        if (Auth::check()) {
            $this->unreadCount = Chat::where('user_id', Auth::id())
                ->where('is_admin', true)
                ->where('is_read', false)
                ->count();
        }
    }

    public function toggleChat()
    {
        $this->chatOpen = !$this->chatOpen;
        
        if ($this->chatOpen) {
            // Menandai semua pesan dari admin sebagai telah dibaca saat chat dibuka
            Chat::where('user_id', Auth::id())
                ->where('is_admin', true)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            $this->unreadCount = 0;
            $this->loadMessages();
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->message)) || !Auth::check()) {
            return;
        }

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_admin' => false,
            'is_read' => false,
        ]);

        $this->message = '';
        $this->loadMessages();

        // Trigger event untuk admin
        $this->dispatch('new-message-for-admin');
    }

    public function refreshMessages()
    {
        $this->loadMessages();
        $this->countUnreadMessages();
    }

    public function render()
    {
        return view('livewire.chat.user-chat');
    }
}
