<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanReportExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected float $totalPendapatan;
    protected float $totalPengeluaran;
    protected string $villaName;
    protected string $periode;
    protected float $servicePercentage;
    protected float $feePercentage;

    public function __construct(
        float $totalPendapatan,
        float $totalPengeluaran,
        string $villaName,
        string $periode,
        float $servicePercentage,
        float $feePercentage
    ) {
        $this->totalPendapatan = $totalPendapatan;
        $this->totalPengeluaran = $totalPengeluaran;
        $this->villaName = $villaName;
        $this->periode = $periode;
        $this->servicePercentage = $servicePercentage;
        $this->feePercentage = $feePercentage;
    }

    public function collection()
    {
        $pendapatanBersih = $this->totalPendapatan - $this->totalPengeluaran;
        $serviceNominal = $pendapatanBersih * ($this->servicePercentage / 100);
        $pendapatanKotor = $pendapatanBersih - $serviceNominal;
        $feeNominal = $pendapatanKotor * ($this->feePercentage / 100);
        $pendapatanOwner = $pendapatanKotor - $feeNominal;

        $rows = [
            ['LAPORAN KEUANGAN VILLA'],
            ['Villa: ' . $this->villaName],
            ['Periode: ' . $this->periode],
            [''],
            ['Keterangan', 'Nominal (Rp)'],
            ['Total Pendapatan', $this->totalPendapatan],
            ['Total Pengeluaran', $this->totalPengeluaran],
            ['Pendapatan Bersih', $pendapatanBersih],
            ["Service ({$this->servicePercentage}%)", $serviceNominal],
            ['Pendapatan Kotor', $pendapatanKotor],
            ["Fee Manajemen ({$this->feePercentage}%)", $feeNominal],
            ['Pendapatan Owner', $pendapatanOwner],
        ];

        return collect($rows);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Judul
                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                ]);

                // Header
                $sheet->getStyle('A5:B5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFC107'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Border data
                $sheet->getStyle('A6:B' . $sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Rata kanan nominal & format rupiah
                $sheet->getStyle('B6:B' . $sheet->getHighestRow())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('B6:B' . $sheet->getHighestRow())
                    ->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');
            }
        ];
    }
}
