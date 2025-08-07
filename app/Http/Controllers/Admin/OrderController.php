<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman daftar pesanan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * Menampilkan detail pesanan.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Mengubah status pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        
        $order->status = $request->status;
        
        // Jika status dibatalkan, ubah juga payment status
        if ($request->status === 'cancelled') {
            $order->payment_status = 'cancelled';
        }
        
        $order->save();
        
        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
    
    /**
     * Generate dan download invoice pesanan khusus admin
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        
        // Pastikan pesanan sudah dibayar
        if ($order->payment_status !== 'paid') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah dibayar.');
        }
        
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('user.checkout.invoice', compact('order'));
        
        return $pdf->stream('Invoice-'.$order->order_number.'.pdf');
    }
} 