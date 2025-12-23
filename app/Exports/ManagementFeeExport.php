<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ManagementFeeExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $reports;
    protected $summary;
    protected $periode;

    public function __construct($reports, $summary, $periode)
    {
        $this->reports = collect($reports);
        $this->summary = $summary;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN FEE MANAJEMEN'],
            ['Periode: ' . $this->periode],
            [''],
            ['Villa', 'Pendapatan Kotor', 'Fee (%)', 'Nominal Fee (Rp)', 'Service Karyawan (Rp)']
        ];
    }

    public function map($report): array
    {
        return [
            $report['name'],
            $report['laba_kotor'],
            $report['fee_percent'],
            $report['fee_amount'],
            $report['service_amount'],
        ];
    }

  public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                // 1. Styling Judul & Header (Tetap sama)
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4:E4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
                ]);

                // 2. Format Rupiah untuk Data Body (Kolom B, D, dan E)
                $sheet->getStyle('B5:B' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle('D5:E' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');

                // 3. Tambahkan Baris TOTAL di paling bawah
                $sheet->append([
                    'TOTAL KESELURUHAN',
                    $this->summary['total_revenue'],
                    '', // Kolom Fee % dikosongkan
                    $this->summary['total_fee'],
                    '' // Kolom Service dikosongkan atau bisa diisi total service jika ada
                ]);
                
                $totalRow = $sheet->getHighestRow();

                // 4. Styling Khusus Baris TOTAL (Bold & Background Abu-abu)
                $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                ]);

                // 5. Format Rupiah KHUSUS Baris TOTAL agar ada simbol Rp-nya
                $sheet->getStyle('B' . $totalRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');

                // 6. Border untuk seluruh tabel
                $sheet->getStyle('A4:E' . $totalRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}