<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    // Menggunakan query agar lebih hemat memory untuk data ribuan baris
    public function query()
    {
        return Transaction::query()
            ->where('status', 'completed')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->orderBy('created_at', 'asc');
    }

    // Mapping kolom ke baris Excel
    public function map($transaction): array
    {
        return [
            $transaction->created_at->format('Y-m-d H:i:s'),
            $transaction->invoice,
            $transaction->customer_name,
            $transaction->table_number ?? '-',
            $transaction->payment_method,
            $transaction->subtotal,
            $transaction->discount_amount,
            $transaction->tax_amount,
            $transaction->grand_total,
        ];
    }

    // Header untuk kolom Excel
    public function headings(): array
    {
        return [
            'Tanggal & Jam',
            'No Invoice',
            'Nama Customer',
            'Meja',
            'Metode Pembayaran',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Pajak 11% (Rp)',
            'Grand Total (Rp)',
        ];
    }
}
