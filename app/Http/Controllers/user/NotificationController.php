<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.notifications.index', compact('notifications'));
    }

    /**
     * Membuat notifikasi ketika pesanan berhasil dibayar
     */
    public function createOrderSuccessNotification($order)
    {
        try {
            if (!Auth::check() && !$order->user_id) {
                return false;
            }
            
            $userId = Auth::check() ? Auth::id() : $order->user_id;
            
            $notification = NotificationService::orderSuccess($userId, $order);
            
            Log::info('Notifikasi pesanan berhasil dibuat', [
                'user_id' => $userId,
                'order_id' => $order->id,
                'notification_id' => $notification->id
            ]);
            
            // Trigger event untuk memperbarui komponen Livewire
            $this->triggerNotificationEvent();
            
            // Dispatch event khusus untuk pembayaran berhasil dengan data order
            event('order.paid', ['order' => $order, 'notification_id' => $notification->id]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error membuat notifikasi pesanan berhasil', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null
            ]);
            
            return false;
        }
    }

    /**
     * Membuat notifikasi ketika pesanan dibatalkan
     */
    public function createOrderCancelledNotification($order)
    {
        try {
            if (!Auth::check() && !$order->user_id) {
                return false;
            }
            
            $userId = Auth::check() ? Auth::id() : $order->user_id;
            
            $notification = NotificationService::orderCancelled($userId, $order);
            
            Log::info('Notifikasi pesanan dibatalkan dibuat', [
                'user_id' => $userId,
                'order_id' => $order->id,
                'notification_id' => $notification->id
            ]);
            
            // Trigger event untuk memperbarui komponen Livewire
            $this->triggerNotificationEvent();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error membuat notifikasi pesanan dibatalkan', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null
            ]);
            
            return false;
        }
    }
    
    /**
     * Membuat notifikasi ketika pesanan sedang diproses
     */
    public function createOrderProcessingNotification($order)
    {
        try {
            if (!Auth::check() && !$order->user_id) {
                return false;
            }
            
            $userId = Auth::check() ? Auth::id() : $order->user_id;
            
            $notification = NotificationService::orderProcessing($userId, $order);
            
            Log::info('Notifikasi pesanan diproses dibuat', [
                'user_id' => $userId,
                'order_id' => $order->id,
                'notification_id' => $notification->id
            ]);
            
            // Trigger event untuk memperbarui komponen Livewire
            $this->triggerNotificationEvent();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error membuat notifikasi pesanan diproses', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null
            ]);
            
            return false;
        }
    }
    
    /**
     * Membuat notifikasi ketika pesanan dikirim
     */
    public function createOrderShippedNotification($order)
    {
        try {
            if (!Auth::check() && !$order->user_id) {
                return false;
            }
            
            $userId = Auth::check() ? Auth::id() : $order->user_id;
            
            $notification = NotificationService::orderShipped($userId, $order);
            
            Log::info('Notifikasi pesanan dikirim dibuat', [
                'user_id' => $userId,
                'order_id' => $order->id,
                'notification_id' => $notification->id
            ]);
            
            // Trigger event untuk memperbarui komponen Livewire
            $this->triggerNotificationEvent();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error membuat notifikasi pesanan dikirim', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null
            ]);
            
            return false;
        }
    }

    /**
     * Menandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai sudah dibaca');
    }

    /**
     * Menandai satu notifikasi sebagai dibaca
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca');
    }

    /**
     * Mengambil data untuk komponen ajax notifikasi
     */
    public function getNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Menghapus satu notifikasi
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notifikasi telah dihapus');
    }
    
    /**
     * Menghapus semua notifikasi yang sudah dibaca
     */
    public function destroyRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->delete();
            
        return redirect()->back()->with('success', 'Semua notifikasi yang sudah dibaca telah dihapus');
    }

    /**
     * Trigger Livewire event untuk memperbarui daftar notifikasi
     */
    private function triggerNotificationEvent()
    {
        try {
            // Gunakan event system Laravel standar
            event('notification.created');
            
            // Pastikan event ini juga terekam dalam log
            Log::info('Notification event triggered using Laravel events');
        } catch (\Exception $e) {
            Log::error('Error triggering notification event', [
                'error' => $e->getMessage()
            ]);
        }
    }


}
