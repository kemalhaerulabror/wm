<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 20px;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1f2937;
        }
        
        .invoice-subtitle {
            font-size: 14px;
            color: #4b5563;
            margin-top: 5px;
        }
        
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        
        .invoice-info {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .invoice-info-item {
            margin-bottom: 20px;
        }
        
        .invoice-info-item h3 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #1f2937;
        }
        
        .invoice-details, .customer-details {
            margin-bottom: 10px;
        }
        
        .label {
            font-weight: bold;
            color: #4b5563;
            width: 120px;
            display: inline-block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th {
            background-color: #f9fafb;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amounts {
            width: 300px;
            margin-left: auto;
        }
        
        .amounts td {
            padding: 5px;
            border: none;
        }
        
        .amounts .total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            color: #6b7280;
            font-size: 11px;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #22c55e;
            background-color: transparent;
            border-radius: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-header">
            <div class="invoice-title">WIPA MOTOR</div>
            <div class="invoice-subtitle">INVOICE PEMBELIAN</div>
        </div>
        
        <div class="invoice-info">
            <div class="invoice-info-item">
                <h3>DETAIL INVOICE</h3>
                <div class="invoice-details">
                    <p><span class="label">No. Invoice:</span> {{ $order->order_number }}</p>
                    <p><span class="label">Tanggal:</span> {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
                    <p><span class="label">Status Bayar:</span> {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</p>
                    <p><span class="label">Metode Bayar:</span> {{ $order->payment_method ?? 'Pembayaran Digital' }}</p>
                </div>
            </div>
            
            <div class="invoice-info-item">
                <h3>DETAIL PEMBELI</h3>
                <div class="customer-details">
                    @if($order->user)
                    <p><span class="label">Nama:</span> {{ $order->user->name }}</p>
                    <p><span class="label">Email:</span> {{ $order->user->email }}</p>
                    <p><span class="label">Telepon:</span> {{ $order->user->phone ?? '-' }}</p>
                    @elseif($order->customer_name)
                        <p><span class="label">Nama:</span> {{ $order->customer_name }}</p>
                        <p><span class="label">Email:</span> {{ $order->customer_email ?? '-' }}</p>
                        <p><span class="label">Telepon:</span> {{ $order->customer_phone ?? '-' }}</p>
                    @else
                        <p><span class="label">Nama:</span> Pelanggan</p>
                        <p><span class="label">Email:</span> -</p>
                        <p><span class="label">Telepon:</span> -</p>
                    @endif
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 45%">Produk</th>
                    <th style="width: 15%" class="text-center">Harga</th>
                    <th style="width: 10%" class="text-center">Jumlah</th>
                    <th style="width: 25%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <table class="amounts">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ 'Rp ' . number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Total:</td>
                <td class="text-right">{{ 'Rp ' . number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div class="footer">
            <p>Terima kasih telah berbelanja di Wipa Motor</p>
            <p>Jika Anda memiliki pertanyaan tentang invoice ini, silakan hubungi kami di support@wipamotor.com</p>
            <p>&copy; {{ date('Y') }} Wipa Motor. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html> 