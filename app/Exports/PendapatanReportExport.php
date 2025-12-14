<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Untuk mengatur lebar kolom otomatis
use Carbon\Carbon;

// Import class dari PhpSpreadsheet untuk styling
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class PendapatanReportExport implements 
    FromCollection,
    WithEvents,
    ShouldAutoSize // Implementasi ini membantu kerapian
{
    protected $data;
    protected $villaName;
    protected $listJenisPendapatan;
    protected $listMetodePembayaran;

    public function __construct(Collection $data, string $villaName, array $listJenisPendapatan, array $listMetodePembayaran)
    {
        $this->data = $data;
        $this->villaName = $villaName;
        $this->listJenisPendapatan = $listJenisPendapatan;
        $this->listMetodePembayaran = $listMetodePembayaran;
    }
    
    // Kita membuat seluruh laporan (Judul, Header, Data, Total) sebagai satu Collection
    public function collection()
    {
        $listJenisPendapatan = $this->listJenisPendapatan;
        $listMetodePembayaran = $this->listMetodePembayaran;
        
        $report = new Collection();

        // 1. Baris 1: Judul Utama
        $report->push(['LAPORAN PENDAPATAN VILLA']);

        // 2. Baris 2: Nama Villa
        $report->push(['Villa: ' . $this->villaName]);
        
        // 3. Baris 3: Baris Kosong
        $report->push(['']); 

        // 4. Baris 4: Header Kolom
        $report->push(['No.', 'Tanggal', 'Jenis Pendapatan', 'Metode Pembayaran', 'Nominal (Rp)']);

        // 5. Baris 5 ke bawah: Data Pendapatan
        $totalNominal = 0;
        foreach ($this->data as $index => $item) {
            $totalNominal += $item->nominal;
            $report->push([
                $index + 1,
                Carbon::parse($item->tanggal)->format('d/m/Y'),
                $listJenisPendapatan[$item->jenis_pendapatan] ?? $item->jenis_pendapatan,
                $listMetodePembayaran[$item->metode_pembayaran] ?? $item->metode_pembayaran,
                $item->nominal, // Nominal dalam bentuk angka
            ]);
        }
        
        // 6. Baris Total
        $report->push(['', '', '', 'TOTAL PENDAPATAN', $totalNominal]);

        return $report;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                
                $sheet = $event->sheet;
                $dataRowCount = $this->data->count();
                
                // BARIS PENTING:
                $headerRow = 4; // Baris untuk Header Kolom
                $firstDataRow = 5; // Baris pertama data
                $lastDataRow = 4 + $dataRowCount; // Baris terakhir data
                $totalRow = $lastDataRow + 1; // Baris untuk Total

                // --- 1. JUDUL UTAMA (A1) & NAMA VILLA (A2) ---
                
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF108465']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                
                // --- 2. HEADER KOLOM (A4:E4) ---
                $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF108465']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // --- 3. BORDER DATA (A5:E[lastDataRow]) ---
                $sheet->getStyle('A' . $firstDataRow . ':E' . $lastDataRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // --- 4. TOTAL ROW (A[totalRow]:E[totalRow]) ---
                $sheet->getStyle('D' . $totalRow . ':E' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1E7DD']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                // --- 5. FORMAT RUPIAH DAN ALIGNMENT ---
                
                // Format Rupiah untuk kolom E (Data dan Total)
                $sheet->getStyle('E' . $firstDataRow . ':E' . $totalRow)->getNumberFormat()->setFormatCode('"Rp"#,##0.00_-');
                
                // Rata kanan untuk kolom Nominal (E)
                $sheet->getStyle('E' . $firstDataRow . ':E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Rata tengah untuk kolom No. (A)
                $sheet->getStyle('A' . $firstDataRow . ':A' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- 6. ATUR LEBAR KOLOM (Custom) ---
                $sheet->getColumnDimension('A')->setWidth(5);   
                $sheet->getColumnDimension('B')->setWidth(15);  
                $sheet->getColumnDimension('C')->setWidth(35);  
                $sheet->getColumnDimension('D')->setWidth(25);  
                $sheet->getColumnDimension('E')->setWidth(20);  
            },
        ];
    }
}