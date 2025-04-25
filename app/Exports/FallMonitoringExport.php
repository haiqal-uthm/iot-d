<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FallMonitoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $vibrationLogs;

    public function __construct($vibrationLogs)
    {
        $this->vibrationLogs = $vibrationLogs;
    }

    public function collection()
    {
        return $this->vibrationLogs;
    }

    public function headings(): array
    {
        return [
            'Timestamp',
            'Farm Name',
            'Device ID',
            'Total Falls'
        ];
    }

    public function map($log): array
    {
        return [
            $log->timestamp->format('Y-m-d H:i:s'),
            $log->orchard->orchardName ?? 'N/A',
            $log->device_id,
            $log->fall_count
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}