<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Date Added',
            'Durian Type',
            'Weight (kg)',
            'Transaction Type',
            'Storage Location',
            'Remarks'
        ];
    }

    public function map($transaction): array
    {
        $typeLabels = [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment'
        ];

        return [
            $transaction->created_at->format('Y-m-d H:i:s'),
            $transaction->durian->name ?? 'N/A',
            $transaction->quantity,
            $typeLabels[$transaction->type] ?? $transaction->type,
            $transaction->storage->name ?? 'N/A',
            $transaction->remarks ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}