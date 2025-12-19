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

class PengeluaranReportExport implements 
    FromCollection,
    WithEvents,
    ShouldAutoSize
{
    protected $data;
    protected $villaName;
    protected $categoryName;
    protected $filterParams; // Menangkap filter untuk periode

    public function __construct(Collection $data, string $villaName, string $categoryName, array $filterParams)
    {
        $this->data = $data;
        $this->villaName = $villaName;
        $this->categoryName = $categoryName;
        $this->filterParams = $filterParams;
    }
    
    public function collection()
    {
        $report = new Collection();

        // --- Logika Penentuan Teks Periode (Sesuai PDF) ---
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

        // 1. Header Judul & Detail Laporan
        $report->push(['LAPORAN PENGELUARAN VILLA']);
        $report->push(['Villa', ': ' . $this->villaName]);
        $report->push(['Kategori', ': ' . $this->categoryName]);
        $report->push(['Periode', ': ' . $periodeTeks]);
        $report->push(['Tanggal Cetak', ': ' . now()->translatedFormat('d F Y H:i')]);
        $report->push(['']); 

        // 2. Header Kolom (Baris ke-7)
        $report->push(['No.', 'Tanggal', 'Kategori', 'Nama Pengeluaran', 'Qty', 'Harga Satuan (Rp)', 'Total Nominal (Rp)', 'Metode', 'Keterangan']);

        // 3. Data
        $totalNominal = 0;
        foreach ($this->data as $index => $item) {
            $totalNominal += $item->nominal;
            $report->push([
                $index + 1,
                $item->tanggal->format('d/m/Y'), 
                $item->category->name ?? '-',
                $item->nama_pengeluaran,
                (float)$item->qty . ' ' . $item->satuan,
                $item->harga_satuan,
                $item->nominal,
                strtoupper($item->metode_pembayaran),
                $item->keterangan ?? '-',
            ]);
        }
        
        // 4. Baris Total
        $report->push(['TOTAL PENGELUARAN', '', '', '', '', '', $totalNominal, '', '']);

        return $report;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = $this->data->count();
                $headerRow = 7; 
                $firstDataRow = 8;
                $lastDataRow = 7 + $rowCount;
                $totalRow = $lastDataRow + 1;

                // Styling Judul & Info
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2:A5')->getFont()->setBold(true);

                // Header Kolom Styling (Warna Slate/Biru Gelap)
                $sheet->getStyle('A'.$headerRow.':I'.$headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Border & Alignment Data
                $sheet->getStyle('A'.$firstDataRow.':I'.$lastDataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A'.$firstDataRow.':B'.$lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E'.$firstDataRow.':E'.$lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H'.$firstDataRow.':H'.$lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Format Rupiah (F & G)
                $sheet->getStyle('F'.$firstDataRow.':G'.$totalRow)->getNumberFormat()->setFormatCode('"Rp"#,##0_-');

                // Styling Total Row
                $sheet->mergeCells('A'.$totalRow.':F'.$totalRow);
                $sheet->getStyle('A'.$totalRow.':I'.$totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF1F5F9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Set Lebar Kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('D')->setWidth(35);
                $sheet->getColumnDimension('I')->setWidth(30);
            },
        ];
    }
}