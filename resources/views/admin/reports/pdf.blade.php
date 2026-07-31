<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a56db;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1a56db;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 2px 5px;
            font-size: 11px;
        }
        .info td:last-child {
            text-align: right;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data th {
            background-color: #1a56db;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table.data tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .footer table {
            width: 100%;
        }
        .footer td {
            font-size: 11px;
            padding: 3px 5px;
        }
        .footer td:last-child {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #e0e7ff; color: #3730a3; }
        .badge-ready_to_ship { background: #f3e8ff; color: #6b21a8; }
        .badge-shipped { background: #e0f2fe; color: #075985; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-2 { margin-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BEWOLE FURNITURE</h1>
        <p>Laporan Pesanan</p>
        @if ($startDate && $endDate)
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @elseif ($startDate)
            <p>Dari: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</p>
        @elseif ($endDate)
            <p>Sampai: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @else
            <p>Semua Periode</p>
        @endif
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Customer</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Grand Total</th>
                <th>Pengiriman</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->product?->name ?? '-' }}</td>
                    <td class="text-right">{{ $order->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>{{ $order->shipping_method_label }}</td>
                    <td>{{ $order->status_label }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        Tidak ada data pesanan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td><strong>Jumlah Pesanan:</strong> {{ $totalOrders }}</td>
                <td class="text-right"><strong>Total Pendapatan:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</td>
                <td class="text-right">Admin: {{ $adminName }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
