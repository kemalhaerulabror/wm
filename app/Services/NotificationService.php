<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Buat notifikasi baru
     *
     * @param int $userId ID pengguna yang akan menerima notifikasi
     * @param string $title Judul notifikasi
     * @param string $message Pesan notifikasi
     * @param string $type Tipe notifikasi (info, success, warning, error)
     * @param string|null $relatedModelType Tipe model terkait (Order, Product, dll)
     * @param int|null $relatedModelId ID model terkait
     * @param string|null $link URL yang terkait dengan notifikasi
     * @return Notification
     */
    public static function create($userId, $title, $message, $type = 'info', $relatedModelType = null, $relatedModelId = null, $link = null)
    {
        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'related_model_type' => $relatedModelType,
                'related_model_id' => $relatedModelId,
                'link' => $link,
                'is_read' => false,
            ]);
            
            \Illuminate\Support\Facades\Log::info('Notifikasi dibuat via service', [
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'title' => $title
            ]);
            
            return $notification;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error dalam membuat notifikasi', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'title' => $title
            ]);
            
            throw $e;
        }
    }

    /**
     * Buat notifikasi untuk pesanan berhasil
     *
     * @param int $userId ID pengguna
     * @param \App\Models\Order $order Pesanan yang berhasil
     * @return Notification
     */
    public static function orderSuccess($userId, $order)
    {
        return self::create(
            $userId,
            'Pembayaran Berhasil',
            'Pembayaran untuk pesanan ' . $order->order_number . ' telah berhasil.',
            'success',
            'Order',
            $order->id,
            route('checkout.success', $order->id)
        );
    }

    /**
     * Buat notifikasi untuk pesanan dibatalkan
     *
     * @param int $userId ID pengguna
     * @param \App\Models\Order $order Pesanan yang dibatalkan
     * @return Notification
     */
    public static function orderCancelled($userId, $order)
    {
        return self::create(
            $userId,
            'Pesanan Dibatalkan',
            'Pesanan ' . $order->order_number . ' telah dibatalkan.',
            'error',
            'Order',
            $order->id,
            route('checkout.success', $order->id)
        );
    }

    /**
     * Buat notifikasi untuk pesanan diproses
     *
     * @param int $userId ID pengguna
     * @param \App\Models\Order $order Pesanan yang diproses
     * @return Notification
     */
    public static function orderProcessing($userId, $order)
    {
        return self::create(
            $userId,
            'Pesanan Diproses',
            'Pesanan ' . $order->order_number . ' sedang diproses.',
            'info',
            'Order',
            $order->id,
            route('checkout.success', $order->id)
        );
    }
    
    /**
     * Buat notifikasi untuk pesanan dikirim
     *
     * @param int $userId ID pengguna
     * @param \App\Models\Order $order Pesanan yang dikirim
     * @return Notification
     */
    public static function orderShipped($userId, $order)
    {
        return self::create(
            $userId,
            'Pesanan Dikirim',
            'Pesanan ' . $order->order_number . ' telah dikirim.',
            'info',
            'Order',
            $order->id,
            route('checkout.success', $order->id)
        );
    }
}
