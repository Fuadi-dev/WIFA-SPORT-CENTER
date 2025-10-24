<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinancialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $bookingBySport;
    protected $topCourts;
    protected $stats;
    protected $period;

    public function __construct($bookingBySport, $topCourts, $stats, $period)
    {
        $this->bookingBySport = $bookingBySport;
        $this->topCourts = $topCourts;
        $this->stats = $stats;
        $this->period = $period;
    }

    public function collection()
    {
        // Combine sport and court data
        $data = collect();
        
        // Add sport data
        foreach ($this->bookingBySport as $sport) {
            $data->push([
                'type' => 'Olahraga',
                'name' => $sport->name,
                'count' => $sport->count,
                'revenue' => $sport->revenue,
            ]);
        }
        
        // Add court data
        foreach ($this->topCourts as $court) {
            $data->push([
                'type' => 'Lapangan',
                'name' => $court->name,
                'count' => $court->booking_count,
                'revenue' => $court->revenue,
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Nama',
            'Jumlah Booking',
            'Total Pendapatan',
            'Persentase',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $percentage = 0;
        if ($this->stats['booking_revenue'] > 0) {
            $percentage = ($item['revenue'] / $this->stats['booking_revenue']) * 100;
        }

        return [
            $no,
            $item['type'],
            $item['name'],
            $item['count'],
            $item['revenue'],
            number_format($percentage, 1) . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row style
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F59E0B'], // Amber color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows style
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align for specific columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Currency format for Total Pendapatan
        $sheet->getStyle('E2:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 15,  // Kategori
            'C' => 30,  // Nama
            'D' => 18,  // Jumlah Booking
            'E' => 20,  // Total Pendapatan
            'F' => 15,  // Persentase
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                
                // Add summary section
                $summaryRow = $lastRow + 2;
                
                // Summary title
                $event->sheet->getDelegate()->setCellValue('A' . $summaryRow, 'RINGKASAN KEUANGAN');
                $event->sheet->getDelegate()->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
                $event->sheet->getDelegate()->getStyle('A' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB'],
                    ],
                ]);

                // Summary data
                $summaryData = [
                    ['Total Pendapatan:', 'Rp ' . number_format($this->stats['booking_revenue'], 0, ',', '.')],
                    ['Total Booking:', $this->stats['booking_count']],
                    ['Rata-rata per Booking:', 'Rp ' . number_format($this->stats['booking_count'] > 0 ? $this->stats['booking_revenue'] / $this->stats['booking_count'] : 0, 0, ',', '.')],
                ];

                $currentRow = $summaryRow + 1;
                foreach ($summaryData as $data) {
                    $event->sheet->getDelegate()->setCellValue('A' . $currentRow, $data[0]);
                    $event->sheet->getDelegate()->setCellValue('B' . $currentRow, $data[1]);
                    $event->sheet->getDelegate()->getStyle('A' . $currentRow)->getFont()->setBold(true);
                    $currentRow++;
                }

                // Add period info
                $periodRow = $currentRow + 1;
                $event->sheet->getDelegate()->setCellValue('A' . $periodRow, 'PERIODE LAPORAN');
                $event->sheet->getDelegate()->mergeCells('A' . $periodRow . ':B' . $periodRow);
                $event->sheet->getDelegate()->getStyle('A' . $periodRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB'],
                    ],
                ]);

                $periodRow++;
                $event->sheet->getDelegate()->setCellValue('A' . $periodRow, 'Tanggal Mulai:');
                $event->sheet->getDelegate()->setCellValue('B' . $periodRow, $this->period['start']);
                $event->sheet->getDelegate()->getStyle('A' . $periodRow)->getFont()->setBold(true);
                
                $periodRow++;
                $event->sheet->getDelegate()->setCellValue('A' . $periodRow, 'Tanggal Akhir:');
                $event->sheet->getDelegate()->setCellValue('B' . $periodRow, $this->period['end']);
                $event->sheet->getDelegate()->getStyle('A' . $periodRow)->getFont()->setBold(true);

                // Freeze first row
                $event->sheet->getDelegate()->freezePane('A2');

                // Auto-filter
                $event->sheet->getDelegate()->setAutoFilter('A1:F' . $lastRow);
            },
        ];
    }
}
