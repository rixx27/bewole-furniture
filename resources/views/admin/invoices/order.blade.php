<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo h1 {
            font-size: 24px;
            color: #7c3aed;
            margin: 0;
        }
        .logo p {
            font-size: 11px;
            color: #888;
            margin: 5px 0 0;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 18px;
            color: #333;
            margin: 0 0 5px;
        }
        .invoice-info p {
            font-size: 11px;
            color: #888;
            margin: 2px 0;
        }
        .invoice-info .code {
            font-size: 14px;
            font-weight: bold;
            color: #7c3aed;
            letter-spacing: 1px;
        }
        .customer-section {
            margin-bottom: 30px;
        }
        .customer-section h3 {
            font-size: 13px;
            color: #7c3aed;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .customer-details {
            width: 100%;
        }
        .customer-details td {
            font-size: 12px;
            padding: 3px 0;
            vertical-align: top;
        }
        .customer-details td:first-child {
            width: 100px;
            color: #888;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items thead th {
            background-color: #7c3aed;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.items tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        table.items tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-section {
            border-top: 2px solid #7c3aed;
            padding-top: 15px;
            margin-bottom: 30px;
        }
        .total-section table {
            width: 100%;
        }
        .total-section td {
            padding: 5px 0;
            font-size: 12px;
        }
        .total-section td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .total-section .grand-total td {
            font-size: 16px;
            font-weight: bold;
            color: #7c3aed;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .status-section {
            margin-bottom: 30px;
        }
        .status-section table {
            width: 100%;
        }
        .status-section td {
            padding: 5px 0;
            font-size: 12px;
        }
        .status-section td:first-child {
            width: 150px;
            color: #888;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>BEWOLE FURNITURE</h1>
            <p>Furniture Custom & Interior</p>
        </div>
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <p class="code">{{ $order->order_code }}</p>
            <p>Tanggal: {{ $order->created_at->format('d F Y') }}</p>
        </div>
    </div>

    <div class="customer-section">
        <h3>Informasi Pelanggan</h3>
        <table class="customer-details">
            <tr>
                <td>Nama</td>
                <td><strong>{{ $order->customer_name }}</strong></td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td>{{ $order->customer_phone }}</td>
            </tr>
            @if ($order->customer_email)
            <tr>
                <td>Email</td>
                <td>{{ $order->customer_email }}</td>
            </tr>
            @endif
            <tr>
                <td>Alamat</td>
                <td>{{ $order->shipping_address }}</td>
            </tr>
            <tr>
                <td>Kota</td>
                <td>{{ $order->city }}{{ $order->postal_code ? ' - ' . $order->postal_code : '' }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->product?->name ?? 'Produk tidak tersedia' }}</td>
                <td style="text-align: center;">{{ $order->quantity }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->product?->price ?? 0, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Pengiriman</td>
                <td>Rp 0</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="status-section">
        <h3 style="font-size: 13px; color: #7c3aed; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 1px;">Informasi Status</h3>
        <table>
            <tr>
                <td>Status Pesanan</td>
                <td><strong>{{ $order->status_label }}</strong></td>
            </tr>
            <tr>
                <td>Status Pembayaran</td>
                <td><strong>{{ $order->payment_status_label }}</strong></td>
            </tr>
            <tr>
                <td>Metode Pengiriman</td>
                <td>{{ $order->shipping_method_label }}</td>
            </tr>
            @if ($order->courier)
            <tr>
                <td>Kurir</td>
                <td>{{ $order->courier }}</td>
            </tr>
            @endif
            @if ($order->tracking_number)
            <tr>
                <td>No. Resi</td>
                <td>{{ $order->tracking_number }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p><strong>BEWOLE FURNITURE</strong></p>
        <p>{{ config('app.url') }}</p>
        <p>Terima kasih atas kepercayaan Anda!</p>
        <p style="margin-top: 5px;">Invoice dicetak pada {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
