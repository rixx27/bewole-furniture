<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Order::with('product');

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['shipping_method'])) {
            $query->where('shipping_method', $this->filters['shipping_method']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Order',
            'Customer',
            'Telepon',
            'Produk',
            'Qty',
            'Grand Total',
            'Metode Pengiriman',
            'Status',
            'Status Pembayaran',
            'Tanggal',
        ];
    }

    public function map($order): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $order->order_code,
            $order->customer_name,
            $order->customer_phone,
            $order->product?->name ?? '-',
            $order->quantity,
            (float) $order->total_price,
            $order->shipping_method_label,
            $order->status_label,
            $order->payment_status_label,
            $order->created_at->format('d/m/Y'),
        ];
    }

    public function title(): string
    {
        return 'Laporan Pesanan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
