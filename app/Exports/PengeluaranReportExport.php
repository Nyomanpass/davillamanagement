<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

// Import class dari PhpSpreadsheet untuk styling
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class PengeluaranReportExport implements 
    FromCollection,
    WithEvents,
    ShouldAutoSize
{
    protected $data;
    protected $villaName;
    protected $listJenisPengeluaran;

    // Bagian 1: Constructor
    public function __construct(Collection $data, string $villaName, array $listJenisPengeluaran)
    {
        $this->data = $data;
        $this->villaName = $villaName;
        $this->listJenisPengeluaran = $listJenisPengeluaran;
    }
    
    // Bagian 2: Collection (Data dan Baris Total)
    public function collection()
    {
        $listJenisPengeluaran = $this->listJenisPengeluaran;
        
        $report = new Collection();

        // 1. Baris 1: Judul Utama
        $report->push(['LAPORAN PENGELUARAN VILLA']);

        // 2. Baris 2: Nama Villa
        $report->push(['Villa: ' . $this->villaName]);
        
        // 3. Baris 3: Baris Kosong
        $report->push(['']); 

        // 4. Baris 4: Header Kolom (A, B, C, D, E)
        $report->push(['No.', 'Tanggal', 'Jenis Pengeluaran', 'Nominal (Rp)', 'Keterangan']);

        // 5. Baris 5 ke bawah: Data Pengeluaran
        $totalNominal = 0;
        foreach ($this->data as $index => $item) {
            $totalNominal += $item->nominal;
            $report->push([
                $index + 1,
                $item->tanggal->format('d/m/Y'), 
                $listJenisPengeluaran[$item->jenis_pengeluaran] ?? $item->jenis_pengeluaran,
                $item->nominal, // Nominal (Kolom D)
                $item->keterangan ?? '-', // Keterangan (Kolom E)
            ]);
        }
        
        // 6. Baris Total: Teks TOTAL PENGELUARAN di Kolom C, Nominal di Kolom D
        // Array: ['A', 'B', 'C (TEKS TOTAL)', 'D (NOMINAL)', 'E']
        $report->push(['TOTAL PENGELUARAN', '', '', $totalNominal, '']);

        return $report;
    }
    
    // Bagian 3: Register Events (Styling dan Merge)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                
                $sheet = $event->sheet;
                $dataRowCount = $this->data->count();
                
                $headerRow = 4;
                $firstDataRow = 5;
                $lastDataRow = 4 + $dataRowCount;
                $totalRow = $lastDataRow + 1; // Baris untuk Total Nominal

                // --- 1 & 2. JUDUL & NAMA VILLA ---
                $sheet->mergeCells('A1:E1'); 
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                
                // --- 2. HEADER KOLOM ---
                $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // --- 3. BORDER DATA ---
                $sheet->getStyle('A' . $firstDataRow . ':E' . $lastDataRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // --- 4. PERBAIKAN TOTAL ROW ---
                
                // FIX: Merge Kolom A, B, dan C untuk TEKS TOTAL
                $sheet->mergeCells('A' . $totalRow . ':C' . $totalRow); 
                
                // Styling (A:E) untuk seluruh baris total
                $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']], // Pastikan teks hitam
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAE3E3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // Alignment teks total harus rata kanan (karena sudah di-merge ke sel A)
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // --- 5. FORMAT RUPIAH DAN ALIGNMENT ---
                
                // Format Rupiah untuk kolom D (Data dan Total)
                $sheet->getStyle('D' . $firstDataRow . ':D' . $totalRow)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');
                
                // Rata kanan untuk kolom Nominal (D)
                $sheet->getStyle('D' . $firstDataRow . ':D' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Rata tengah untuk kolom No. (A)
                $sheet->getStyle('A' . $firstDataRow . ':A' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- 6. ATUR LEBAR KOLOM ---
                $sheet->getColumnDimension('A')->setWidth(5);   
                $sheet->getColumnDimension('B')->setWidth(15);  
                $sheet->getColumnDimension('C')->setWidth(30);  
                $sheet->getColumnDimension('D')->setWidth(20);  
                $sheet->getColumnDimension('E')->setWidth(40); 
            },
        ];
    }
}