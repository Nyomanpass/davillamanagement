<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanReportExport implements FromCollection, WithEvents, ShouldAutoSize
{
    // Properti sesuai logika baru
    protected $totalPendapatan, $totalPengeluaran, $villaName, $periode, $servicePercentage, $feePercentage;
    protected $pKhusus, $exKhusus, $marginKhusus, $serviceNominal, $pUmum, $exUmum, $pendapatanKotor, $feeNominal, $pendapatanOwner;

    public function __construct(
        $totalPendapatan, $totalPengeluaran, $villaName, $periode, $servicePercentage, $feePercentage,
        $serviceNominal, $feeNominal, $pendapatanKotor, $pKhusus = 0, $exKhusus = 0, $pUmum = 0, $exUmum = 0
    ) {
        $this->totalPendapatan = $totalPendapatan;
        $this->totalPengeluaran = $totalPengeluaran;
        $this->villaName = $villaName;
        $this->periode = $periode;
        $this->servicePercentage = (float)$servicePercentage;
        $this->feePercentage = (float)$feePercentage;
        
        // Hasil perhitungan dari Controller
        $this->serviceNominal = $serviceNominal;
        $this->feeNominal = $feeNominal;
        $this->pendapatanKotor = $pendapatanKotor;
        $this->pKhusus = $pKhusus;
        $this->exKhusus = $exKhusus;
        $this->pUmum = $pUmum;
        $this->exUmum = $exUmum;
        $this->marginKhusus = $pKhusus - $exKhusus;
        $this->pendapatanOwner = $pendapatanKotor - $feeNominal;
    }

    public function collection()
    {
        return collect([
            ['LAPORAN REKAPITULASI KEUANGAN VILLA'],
            ['Villa: ' . $this->villaName],
            ['Periode: ' . $this->periode],
            [''],
            ['Keterangan', 'Nominal (Rp)'],
            ['A. KATEGORI KHUSUS', ''],
            ['Total Pendapatan Khusus', $this->pKhusus],
            ['Total Pengeluaran Khusus', -$this->exKhusus],
            ['Margin Kategori Khusus', $this->marginKhusus],
            ["Potongan Service Karyawan ({$this->servicePercentage}%)", -$this->serviceNominal],
            [''],
            ['B. KATEGORI UMUM', ''],
            ['Total Pendapatan Umum', $this->pUmum],
            ['Total Pengeluaran Umum', -$this->exUmum],
            [''],
            ['C. RINGKASAN AKHIR', ''],
            ['Total Pendapatan Kotor (A+B - Service)', $this->pendapatanKotor],
            ["Fee Manajemen ({$this->feePercentage}%)", -$this->feeNominal],
            ['TOTAL HASIL BERSIH OWNER', $this->pendapatanOwner],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                // Styling Judul
                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                
                // Styling Header Tabel (Baris 5)
                $sheet->getStyle('A5:B5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '333333']],
                ]);

                // Format Ribuan (Rp) untuk semua angka di kolom B
                $sheet->getStyle('B7:B' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

                // Warna Hijau untuk Pendapatan & Margin & Owner
                // Baris: 7 (P.Khusus), 9 (Margin), 13 (P.Umum), 17 (P.Kotor), 19 (Owner)
                foreach([7, 9, 13, 17, 19] as $row) {
                    $sheet->getStyle('B'.$row)->getFont()->getColor()->setRGB('2B8A3E');
                    $sheet->getStyle('B'.$row)->getFont()->setBold(true);
                }

                // Warna Merah untuk Pengeluaran & Potongan
                // Baris: 8 (Ex.Khusus), 10 (Service), 14 (Ex.Umum), 18 (Fee)
                foreach([8, 10, 14, 18] as $row) {
                    $sheet->getStyle('B'.$row)->getFont()->getColor()->setRGB('C92A2A');
                }

                // Highlight Baris Owner (Terakhir)
                $sheet->getStyle('A'.$lastRow.':B'.$lastRow)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EBFBEE']],
                    'font' => ['bold' => true, 'size' => 12],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2B8A3E']]]
                ]);

                // Border untuk seluruh tabel
                $sheet->getStyle('A5:B' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}