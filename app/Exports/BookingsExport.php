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

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $bookings;
    protected $stats;
    protected $filters;

    public function __construct($bookings, $stats, $filters)
    {
        $this->bookings = $bookings;
        $this->stats = $stats;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->bookings;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Booking',
            'Tanggal',
            'Waktu Mulai',
            'Waktu Selesai',
            'Nama Pelanggan',
            'Email',
            'Telepon',
            'Olahraga',
            'Lapangan',
            'Metode Pembayaran',
            'Total Harga',
            'Status',
            'Dibuat Pada'
        ];
    }

    public function map($booking): array
    {
        static $no = 0;
        $no++;

        $paymentMethod = '-';
        if ($booking->payment_method === 'midtrans') {
            $paymentMethod = 'Midtrans';
        } elseif ($booking->payment_method === 'cash') {
            $paymentMethod = 'Tunai';
        }

        $status = '';
        if ($booking->status === 'paid') {
            $status = 'Dibayar';
        } elseif ($booking->status === 'confirmed') {
            $status = 'Dikonfirmasi';
        } elseif ($booking->status === 'completed') {
            $status = 'Selesai';
        } elseif ($booking->status === 'pending_payment') {
            $status = 'Menunggu Pembayaran';
        } elseif ($booking->status === 'pending_confirmation') {
            $status = 'Menunggu Konfirmasi';
        } elseif ($booking->status === 'cancelled') {
            $status = 'Dibatalkan';
        } else {
            $status = ucfirst($booking->status);
        }

        return [
            $no,
            $booking->booking_code,
            $booking->booking_date->format('d/m/Y'),
            substr($booking->start_time, 0, 5),
            substr($booking->end_time, 0, 5),
            $booking->user->name,
            $booking->user->email,
            $booking->user->phone_number ?? '-',
            $booking->court->sport->name,
            $booking->court->name,
            $paymentMethod,
            $booking->total_price,
            $status,
            $booking->created_at->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row style
        $sheet->getStyle('A1:N1')->applyFromArray([
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
        $sheet->getStyle('A2:N' . $lastRow)->applyFromArray([
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
        $sheet->getStyle('C2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:M' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Currency format for Total Harga
        $sheet->getStyle('L2:L' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Kode Booking
            'C' => 12,  // Tanggal
            'D' => 12,  // Waktu Mulai
            'E' => 12,  // Waktu Selesai
            'F' => 25,  // Nama Pelanggan
            'G' => 30,  // Email
            'H' => 15,  // Telepon
            'I' => 15,  // Olahraga
            'J' => 20,  // Lapangan
            'K' => 20,  // Metode Pembayaran
            'L' => 15,  // Total Harga
            'M' => 20,  // Status
            'N' => 18,  // Dibuat Pada
        ];
    }

    public function title(): string
    {
        return 'Laporan Booking';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                
                // Add summary section
                $summaryRow = $lastRow + 2;
                
                // Summary title
                $event->sheet->getDelegate()->setCellValue('A' . $summaryRow, 'RINGKASAN');
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
                    ['Total Booking:', $this->bookings->count()],
                    ['Menunggu Konfirmasi:', $this->stats['pending_confirmation']],
                    ['Booking Selesai:', $this->stats['completed_bookings']],
                    ['Booking Dibayar:', $this->stats['paid_bookings']],
                ];

                $currentRow = $summaryRow + 1;
                foreach ($summaryData as $data) {
                    $event->sheet->getDelegate()->setCellValue('A' . $currentRow, $data[0]);
                    $event->sheet->getDelegate()->setCellValue('B' . $currentRow, $data[1]);
                    $event->sheet->getDelegate()->getStyle('A' . $currentRow)->getFont()->setBold(true);
                    $currentRow++;
                }

                // Add filter info if exists
                if (!empty($this->filters)) {
                    $filterRow = $currentRow + 1;
                    $event->sheet->getDelegate()->setCellValue('A' . $filterRow, 'FILTER YANG DITERAPKAN');
                    $event->sheet->getDelegate()->mergeCells('A' . $filterRow . ':B' . $filterRow);
                    $event->sheet->getDelegate()->getStyle('A' . $filterRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E5E7EB'],
                        ],
                    ]);

                    $filterRow++;
                    foreach ($this->filters as $key => $value) {
                        if (!empty($value)) {
                            $event->sheet->getDelegate()->setCellValue('A' . $filterRow, ucfirst(str_replace('_', ' ', $key)) . ':');
                            $event->sheet->getDelegate()->setCellValue('B' . $filterRow, $value);
                            $event->sheet->getDelegate()->getStyle('A' . $filterRow)->getFont()->setBold(true);
                            $filterRow++;
                        }
                    }
                }

                // Freeze first row
                $event->sheet->getDelegate()->freezePane('A2');

                // Auto-filter
                $event->sheet->getDelegate()->setAutoFilter('A1:N' . $lastRow);
            },
        ];
    }
}
