<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HarvestReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $harvests;

    public function __construct($harvests)
    {
        $this->harvests = $harvests;
    }

    public function collection()
    {
        return $this->harvests;
    }

    public function headings(): array
    {
        return [
            'Date of Harvest',
            'Farmer Name',
            'Farm Name',
            'Durian Type',
            'Quantity Harvested',
            'Total Weight (kg)',
            'Remarks'
        ];
    }

    public function map($harvest): array
    {
        return [
            $harvest->harvest_date->format('Y-m-d'),
            $harvest->farmer->user->name ?? 'N/A',
            $harvest->farmer->farm_name ?? 'N/A',
            $harvest->orchard->orchardName ?? 'N/A',
            $harvest->durian->name ?? $harvest->durian_type ?? 'N/A',
            $harvest->total_harvested,
            $harvest->estimated_weight,
            $harvest->remarks ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}