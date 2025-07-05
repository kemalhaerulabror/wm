<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminPopupChat extends Component
{
    public $message = '';
    public $users = [];
    public $chatMessages = [];
    public $selectedUserId = null;
    public $unreadCounts = [];
    public $chatOpen = false;
    public $totalUnreadCount = 0;

    protected $listeners = [
        'new-message-for-admin' => 'refreshMessages'
    ];

    public function mount()
    {
        $this->loadUsers();
        $this->countUnreadMessages();
    }

    public function loadUsers()
    {
        // Ambil user yang pernah chat
        $userIds = Chat::select('user_id')
            ->distinct()
            ->pluck('user_id');
        
        $this->users = User::whereIn('id', $userIds)
            ->get()
            ->toArray();
    }

    public function toggleChat()
    {
        $this->chatOpen = !$this->chatOpen;
        
        if ($this->chatOpen) {
            $this->loadUsers();
            $this->loadMessages();
        }
    }

    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;
        $this->loadMessages();
        
        // Menandai semua pesan dari user ini sebagai telah dibaca
        Chat::where('user_id', $userId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $this->countUnreadMessages();
    }

    public function loadMessages()
    {
        if ($this->selectedUserId) {
            $this->chatMessages = Chat::where('user_id', $this->selectedUserId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
        }
    }

    public function countUnreadMessages()
    {
        $this->unreadCounts = [];
        $totalCount = 0;
        
        foreach ($this->users as $user) {
            $count = Chat::where('user_id', $user['id'])
                ->where('is_admin', false)
                ->where('is_read', false)
                ->count();
                
            $this->unreadCounts[$user['id']] = $count;
            $totalCount += $count;
        }
        
        $this->totalUnreadCount = $totalCount;
    }

    public function sendMessage()
    {
        if (empty(trim($this->message)) || !$this->selectedUserId || !Auth::guard('admin')->check()) {
            return;
        }

        $chat = Chat::create([
            'user_id' => $this->selectedUserId,
            'admin_id' => Auth::guard('admin')->id(),
            'message' => $this->message,
            'is_admin' => true,
            'is_read' => false,
        ]);

        $this->message = '';
        $this->loadMessages();

        // Trigger event untuk user
        $this->dispatch('new-message-for-user');
    }

    public function refreshMessages()
    {
        $this->loadUsers();
        $this->loadMessages();
        $this->countUnreadMessages();
    }

    public function render()
    {
        return view('livewire.chat.admin-popup-chat');
    }
}
