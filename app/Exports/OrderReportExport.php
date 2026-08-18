<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderReportExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected array $filters;
    private int $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query data based on active filters.
     */
    public function query(): Builder
    {
        $query = Order::with('product');

        // Date range filter
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        // Status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Search query (Order Code, Customer Name, WhatsApp/Phone)
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%')
                  ->orWhere('whatsapp_number', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Sheet headings.
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Order',
            'Tanggal',
            'Customer',
            'No. WhatsApp',
            'Email',
            'Produk',
            'Jumlah',
            'Harga',
            'Total',
            'Status',
            'Status Pembayaran',
            'Alamat',
            'Kota',
            'Kode Pos',
            'Catatan',
        ];
    }

    /**
     * Map each order to excel row.
     */
    public function map($order): array
    {
        $this->rowNumber++;

        $unitPrice = $order->quantity > 0 
            ? ((float) $order->total_price / $order->quantity) 
            : (float) ($order->product?->price ?? $order->total_price);

        $whatsapp = $order->whatsapp_number ?: $order->customer_phone ?: '-';
        $email = $order->customer_email ?: '-';
        $address = $order->shipping_address ?: '-';
        $city = $order->city ?: '-';
        $postalCode = $order->postal_code ?: '-';
        $notes = $order->notes ?: '-';

        return [
            $this->rowNumber,
            $order->order_code,
            $order->created_at->format('d/m/Y'),
            $order->customer_name,
            $whatsapp,
            $email,
            $order->product?->name ?? '-',
            (int) $order->quantity,
            (float) $unitPrice,
            (float) $order->total_price,
            $order->status_label,
            $order->payment_status_label,
            $address,
            $city,
            $postalCode,
            $notes,
        ];
    }

    /**
     * Set sheet title.
     */
    public function title(): string
    {
        return 'Laporan Pesanan';
    }

    /**
     * Format columns (Currency & text).
     */
    public function columnFormats(): array
    {
        return [
            'B' => '@', // Kode Order as Text
            'C' => 'DD/MM/YYYY',
            'E' => '@', // Phone as Text
            'H' => '#,##0', // Qty
            'I' => '"Rp "#,##0', // Harga
            'J' => '"Rp "#,##0', // Total
            'O' => '@', // Postal code as Text
        ];
    }

    /**
     * Apply styles to sheet.
     */
    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style header row (Row 1)
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A3525'], // Bewole brand brown
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => false,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Border and alignment for data cells if data exists
        if ($highestRow > 1) {
            $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align specific columns (No, Tanggal, Qty, Status, Payment Status, Kode Pos)
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O2:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
