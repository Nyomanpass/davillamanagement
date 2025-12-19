<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PendapatanReportExport implements 
    FromCollection,
    WithEvents,
    ShouldAutoSize
{
    protected $data;
    protected $villaName;
    protected $listMetodePembayaran;
    protected $categoryName;
    protected $filterParams; // Tambahkan properti untuk menangkap filter

    public function __construct(Collection $data, string $villaName, array $listMetodePembayaran, string $categoryName, array $filterParams)
    {
        $this->data = $data;
        $this->villaName = $villaName;
        $this->listMetodePembayaran = $listMetodePembayaran;
        $this->categoryName = $categoryName;
        $this->filterParams = $filterParams;
    }
    
    public function collection()
    {
        $listMetodePembayaran = $this->listMetodePembayaran;
        $report = new Collection();

        // --- Logika Penentuan Teks Periode (Sama seperti PDF) ---
        $start = $this->filterParams['start'] ?? null;
        $end = $this->filterParams['end'] ?? null;
        $bulan = (isset($this->filterParams['bulan']) && $this->filterParams['bulan'] !== '') ? (int)$this->filterParams['bulan'] : null;
        $tahun = (isset($this->filterParams['tahun']) && $this->filterParams['tahun'] !== '') ? (int)$this->filterParams['tahun'] : null;

        if ($start && $end) {
            $periodeTeks = Carbon::parse($start)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end)->translatedFormat('d M Y');
        } elseif ($bulan && $tahun) {
            $periodeTeks = Carbon::create()->month($bulan)->translatedFormat('F') . ' ' . $tahun;
        } elseif ($tahun) {
            $periodeTeks = 'Tahun ' . $tahun;
        } else {
            $periodeTeks = 'Semua Data';
        }

        // 1. Header Judul & Info
        $report->push(['LAPORAN PENDAPATAN VILLA']);
        $report->push(['Villa', ': ' . $this->villaName]);
        $report->push(['Kategori', ': ' . $this->categoryName]);
        $report->push(['Periode', ': ' . $periodeTeks]); // Tambahkan baris periode di sini
        $report->push(['Tanggal Cetak', ': ' . now()->translatedFormat('d F Y H:i')]);
        $report->push(['']); 

        // 2. Header Kolom (Baris ke-7)
        $report->push([
            'No.', 
            'Tanggal', 
            'Kategori', 
            'Detail Item/Booking', 
            'Qty / Nights', 
            'Harga Satuan (Rp)', 
            'Total Nominal (Rp)', 
            'Metode'
        ]);

        // 3. Looping Data
        $totalNominal = 0;
        foreach ($this->data as $index => $item) {
            $totalNominal += $item->nominal;
            $isRoom = str_contains(strtolower($item->category->name ?? ''), 'room');
            
            if ($isRoom) {
                $checkIn = Carbon::parse($item->check_in)->format('d/m');
                $checkOut = Carbon::parse($item->check_out)->format('d/m');
                $detail = "Booking Room ({$checkIn} - {$checkOut})";
                $qtyText = $item->nights . " Malam";
                $hargaSatuan = $item->nominal / ($item->nights > 0 ? $item->nights : 1);
            } else {
                $detail = $item->item_name ?? '-';
                $qtyText = $item->qty . " x";
                $hargaSatuan = $item->harga_satuan ?? ($item->nominal / ($item->qty > 0 ? $item->qty : 1));
            }

            $report->push([
                $index + 1,
                Carbon::parse($item->tanggal)->format('d/m/Y'),
                $item->category->name ?? '-',
                $detail,
                $qtyText,
                $hargaSatuan,
                $item->nominal,
                $listMetodePembayaran[$item->metode_pembayaran] ?? $item->metode_pembayaran,
            ]);
        }
        
        // 4. Baris Total
        $report->push(['', '', '', '', '', 'TOTAL PENDAPATAN', $totalNominal, '']);

        return $report;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $dataRowCount = $this->data->count();
                
                $headerRow = 7; // Header bergeser ke baris 7 karena ada baris periode
                $firstDataRow = 8;
                $lastDataRow = 7 + $dataRowCount;
                $totalRow = $lastDataRow + 1;

                // Style Judul Utama
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                
                // Style Bold untuk Label Info (Villa, Kategori, Periode, dll)
                $sheet->getStyle('A2:A5')->getFont()->setBold(true);

                // Style Header Tabel (Warna Amber)
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB45309']], 
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                
                $sheet->getStyle('A' . $firstDataRow . ':H' . $lastDataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Format Rupiah & Alignment
                $sheet->getStyle('F' . $firstDataRow . ':G' . $totalRow)->getNumberFormat()->setFormatCode('"Rp"#,##0_-');
                $sheet->getStyle('A' . $firstDataRow . ':B' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . $firstDataRow . ':E' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Style Total
                $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE68A');

                // Lebar Kolom
                $sheet->getColumnDimension('A')->setWidth(5);   
                $sheet->getColumnDimension('D')->setWidth(35);  
            },
        ];
    }
}