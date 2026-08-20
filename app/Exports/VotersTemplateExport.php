<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VotersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Isi contoh data di template.
     */
    public function array(): array
    {
        return [
            ['Ahmad Subagyo'],
            ['Siti Rahayu'],
            ['Budi Santoso'],
        ];
    }

    /**
     * Header kolom (hanya "nama").
     */
    public function headings(): array
    {
        return ['nama'];
    }

    /**
     * Styling header dan baris contoh.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => 'FF059669'], // emerald-600
                ],
            ],
        ];
    }

    /**
     * Lebar kolom.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
        ];
    }
}
