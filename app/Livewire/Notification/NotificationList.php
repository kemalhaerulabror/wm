<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class NotificationList extends Component
{
    public $notifications = [];
    public $isOpen = false;
    
    // Listener untuk event Laravel
    protected $listeners = [
        'notification.created' => 'loadNotifications',
        'refreshNotifications' => 'loadNotifications',
        'order.paid' => 'loadNotifications'
    ];

    public function boot()
    {
        // Mendaftarkan listener untuk event Laravel
        \Illuminate\Support\Facades\Event::listen('notification.created', function () {
            $this->loadNotifications();
            
            // Log untuk debugging event
            \Illuminate\Support\Facades\Log::info('NotificationList caught notification.created event');
        });
        
        // Listener khusus untuk event order paid
        \Illuminate\Support\Facades\Event::listen('order.paid', function ($event) {
            \Illuminate\Support\Facades\Log::info('NotificationList caught order.paid event', [
                'event_data' => $event
            ]);
            $this->loadNotifications();
        });
    }

    public function mount()
    {
        if (Auth::check()) {
            $this->loadNotifications();
        }
    }

    #[On('notificationAdded')]
    #[On('order.paid')]
    public function loadNotifications()
    {
        if (Auth::check()) {
            $this->notifications = Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            // Log untuk memastikan notifikasi dimuat
            \Illuminate\Support\Facades\Log::info('Notifikasi dimuat dalam komponen Livewire', [
                'user_id' => Auth::id(),
                'count' => count($this->notifications)
            ]);
        }
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
        // Refresh notifications when dropdown is opened
        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->loadNotifications();
            
            // Dispatch event untuk memperbarui counter notifikasi
            $this->dispatch('notificationRead');
            
            // Gunakan event global untuk memperbarui counter dan komponen lain
            event('notification.read', ['user_id' => Auth::id()]);
            
            // Emit juga ke browser untuk refresh
            $this->dispatch('refreshUnreadCount');
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
                
            $this->loadNotifications();
            
            // Dispatch event untuk memperbarui counter notifikasi
            $this->dispatch('allNotificationsRead');
            $this->dispatch('refreshUnreadCount');
            
            // Gunakan event global untuk memperbarui counter dan komponen lain
            event('notification.read', ['user_id' => Auth::id(), 'all' => true]);
        }
    }

    public function render()
    {
        return view('livewire.notification.notification-list');
    }
}
