<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2d241e;
            margin: 0;
            padding: 30px;
        }
        .header {
            border-bottom: 2.5px solid #5c3d1e;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo h1 {
            font-size: 22px;
            color: #5c3d1e;
            margin: 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .logo p {
            font-size: 10px;
            color: #7d6b5c;
            margin: 3px 0 0;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 16px;
            color: #5c3d1e;
            margin: 0 0 4px;
        }
        .invoice-info p {
            font-size: 10px;
            color: #7d6b5c;
            margin: 2px 0;
        }
        .invoice-info .code {
            font-size: 12px;
            font-weight: bold;
            color: #8b5e34;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #5c3d1e;
            margin: 0 0 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e8dfd8;
            padding-bottom: 4px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .details-table {
            width: 100%;
        }
        .details-table td {
            font-size: 10.5px;
            padding: 2px 0;
            vertical-align: top;
        }
        .details-table td.label {
            width: 90px;
            color: #7d6b5c;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items thead th {
            background-color: #5c3d1e;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.items tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e8dfd8;
            font-size: 10.5px;
            vertical-align: top;
        }
        table.items tbody tr:nth-child(even) {
            background-color: #faf7f4;
        }
        .item-title {
            font-weight: bold;
            color: #2d241e;
        }
        .item-meta {
            font-size: 9.5px;
            color: #7d6b5c;
            margin-top: 2px;
            line-height: 1.3;
        }
        .total-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .total-container td {
            vertical-align: top;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 4px 0;
            font-size: 10.5px;
        }
        .total-table td.amount {
            text-align: right;
            font-weight: bold;
        }
        .total-table .grand-total td {
            font-size: 13px;
            font-weight: bold;
            color: #5c3d1e;
            padding-top: 8px;
            border-top: 2px solid #5c3d1e;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            background-color: #f3ece6;
            color: #5c3d1e;
            border: 1px solid #d9c8bc;
        }
        .footer {
            border-top: 1px solid #e8dfd8;
            padding-top: 12px;
            text-align: center;
            font-size: 9.5px;
            color: #7d6b5c;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">
                        <h1>BEWOLE FURNITURE</h1>
                        <p>Furniture Custom, Ukir & Interior Jepara</p>
                    </div>
                </td>
                <td>
                    <div class="invoice-info">
                        <h2>INVOICE PEMBELIAN</h2>
                        <p class="code">#{{ $order->order_code }}</p>
                        <p>Tanggal: {{ $order->created_at->format('d F Y H:i') }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td style="padding-right: 15px;">
                <div class="section-title">Informasi Pelanggan</div>
                <table class="details-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td><strong>{{ $order->customer_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">WhatsApp</td>
                        <td>{{ $order->customer_phone }}</td>
                    </tr>
                    @if ($order->customer_email)
                    <tr>
                        <td class="label">Email</td>
                        <td>{{ $order->customer_email }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Alamat</td>
                        <td>{{ $order->shipping_address }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kota/Kab.</td>
                        <td>{{ $order->city }}{{ $order->postal_code ? ' (' . $order->postal_code . ')' : '' }}</td>
                    </tr>
                </table>
            </td>
            <td style="padding-left: 15px;">
                <div class="section-title">Status & Pengiriman</div>
                <table class="details-table">
                    <tr>
                        <td class="label">Status Pesanan</td>
                        <td><span class="status-badge">{{ $order->status_label }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Pembayaran</td>
                        <td><span class="status-badge">{{ $order->payment_status_label }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Metode Bayar</td>
                        <td>{{ $order->payment_method_label }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pengiriman</td>
                        <td>{{ $order->shipping_method_label }}</td>
                    </tr>
                    @if ($order->courier)
                    <tr>
                        <td class="label">Kurir/Kargo</td>
                        <td>{{ $order->courier }}</td>
                    </tr>
                    @endif
                    @if ($order->tracking_number)
                    <tr>
                        <td class="label">No. Resi</td>
                        <td><strong>{{ $order->tracking_number }}</strong></td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Item Pesanan</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 50%;">Produk & Spesifikasi</th>
                <th style="text-align: center; width: 10%;">Qty</th>
                <th style="text-align: right; width: 20%;">Harga Satuan</th>
                <th style="text-align: right; width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if ($order->items && $order->items->count() > 0)
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <div class="item-title">{{ $item->product?->name ?? 'Produk' }}</div>
                            <div class="item-meta">
                                @if ($item->meubel_type)
                                    <span>• Jenis: {{ $item->meubel_type_label }}</span>
                                @endif
                                @if ($item->seat_material_name)
                                    <br><span>• Dudukan: {{ $item->seat_material_name }}</span>
                                @endif
                                @if ($item->packing_material_name)
                                    <br><span>• Packing: {{ $item->packing_material_name }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <div class="item-title">{{ $order->product?->name ?? 'Produk Bewole' }}</div>
                        <div class="item-meta">
                            @if ($order->meubel_type)
                                <span>• Jenis: {{ $order->meubel_type_label }}</span>
                            @endif
                            @if ($order->packing_type)
                                <br><span>• Packing: {{ $order->packing_type_label }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $order->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($order->product?->price ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="total-container">
        <tr>
            <td style="width: 55%; padding-right: 20px;">
                @if ($order->notes)
                    <div class="section-title">Catatan Khusus</div>
                    <p style="font-size: 10px; color: #4b3d33; line-height: 1.4; margin: 0; white-space: pre-line;">
                        {{ $order->notes }}
                    </p>
                @endif
            </td>
            <td style="width: 45%;">
                <table class="total-table">
                    <tr>
                        <td>Subtotal Produk</td>
                        <td class="amount">Rp {{ number_format(($order->total_price - ($order->customization_fee ?? 0) - ($order->packing_fee ?? 0)), 0, ',', '.') }}</td>
                    </tr>
                    @if ($order->customization_fee > 0)
                    <tr>
                        <td>Biaya Custom Dudukan</td>
                        <td class="amount">Rp {{ number_format($order->customization_fee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if ($order->packing_fee > 0)
                    <tr>
                        <td>Biaya Packing</td>
                        <td class="amount">Rp {{ number_format($order->packing_fee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Biaya Pengiriman</td>
                        <td class="amount">Rp 0</td>
                    </tr>
                    <tr class="grand-total">
                        <td>Grand Total</td>
                        <td class="amount">{{ $order->formatted_total_price }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>BEWOLE JEPARA FURNITURE</strong> — Jepara, Jawa Tengah, Indonesia</p>
        <p>Terima kasih telah mempercayakan kebutuhan furniture & interior Anda kepada kami!</p>
        <p style="margin-top: 4px; font-size: 8.5px; color: #a39589;">Dokumen ini dicetak otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
