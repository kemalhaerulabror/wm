<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use App\Models\Notification;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationCounter extends Component
{
    public $unreadCount = 0;
    
    // Listener untuk event Laravel
    protected $listeners = [
        'notification.created' => 'updateCount',
        'refreshNotifications' => 'updateCount',
        'notificationRead' => 'updateCount',
        'allNotificationsRead' => 'updateCount',
        'order.paid' => 'updateCount',
        'refreshUnreadCount' => 'updateCount'
    ];
    
    public function boot()
    {
        // Mendaftarkan listener untuk event Laravel
        \Illuminate\Support\Facades\Event::listen('notification.created', function () {
            $this->updateCount();
            \Illuminate\Support\Facades\Log::info('NotificationCounter: notification.created event received');
        });
        
        // Listener untuk order paid
        \Illuminate\Support\Facades\Event::listen('order.paid', function ($event) {
            $this->updateCount();
            \Illuminate\Support\Facades\Log::info('NotificationCounter: order.paid event received', [
                'event_data' => $event
            ]);
        });
        
        // Listener untuk notification read
        \Illuminate\Support\Facades\Event::listen('notification.read', function () {
            $this->updateCount();
        });
    }

    public function mount()
    {
        if (Auth::check()) {
            $this->updateCount();
        }
    }

    #[On('refreshUnreadCount')]
    #[On('notificationRead')]
    public function updateCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
                
            \Illuminate\Support\Facades\Log::info('Notification counter updated', [
                'user_id' => Auth::id(),
                'unread_count' => $this->unreadCount
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notification.notification-counter');
    }
}
