<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use App\Models\Villa; // Pastikan Anda mengimpor model Villa jika diperlukan
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; // Pastikan ini di-import
use App\Exports\PendapatanReportExport; // Pastikan class yang baru di-import
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini di-import
use App\Exports\PengeluaranReportExport;
use App\Exports\LaporanReportExport;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Exports\ManagementFeeExport;


class ExportController extends Controller
{
    /**
     * Fungsi pembantu untuk mengambil data berdasarkan filter dari request.
     */
    private function getFilteredDataFromRequest(Request $request)
    {
        // 1. Tambahkan 'category' di dalam with()
        $query = Pendapatan::with(['villa', 'category'])
            ->where('villa_id', $request->villa_id);

        // 2. Filter Bulan & Tahun
        if ($request->bulan && $request->bulan !== '') {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun && $request->tahun !== '') {
            $query->whereYear('tanggal', $request->tahun);
        }

        // 3. Filter Range Tanggal
        if ($request->start) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->end) {
            $query->whereDate('tanggal', '<=', $request->end);
        }

        // 4. BARU: Filter berdasarkan Kategori
        // Sesuai dengan parameter 'category' yang kita kirim dari Livewire
        if ($request->category && $request->category !== '') {
            $query->where('category_id', $request->category);
        }
        
        return $query->latest()->get();
    }

   private function getFilteredPengeluaranDataFromRequest(Request $request)
{
    $query = \App\Models\Pengeluaran::query();

    // Filter Villa (Wajib)
    $query->where('villa_id', $request->villa_id);

    // FIX: Tambahkan filter kategori di sini
    if ($request->has('category') && $request->category != '') {
        $query->where('category_id', $request->category);
    }

    // Filter Bulan & Tahun
    if ($request->has('bulan') && $request->bulan != '') {
        $query->whereMonth('tanggal', $request->bulan);
    }
    if ($request->has('tahun') && $request->tahun != '') {
        $query->whereYear('tanggal', $request->tahun);
    }

    // Filter Range Tanggal
    if ($request->has('start') && $request->start != '') {
        $query->whereDate('tanggal', '>=', $request->start);
    }
    if ($request->has('end') && $request->end != '') {
        $query->whereDate('tanggal', '<=', $request->end);
    }

    return $query->latest()->get();
}


public function pendapatanExcel(Request $request)
{
    $data = $this->getFilteredDataFromRequest($request);

    if ($data->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
    }
    
    $villa = Villa::find($request->villa_id);
    $villaName = $villa->nama_villa ?? "Semua Villa";

    $categoryName = "Semua Kategori";
    if ($request->category) {
        $cat = \App\Models\Category::find($request->category);
        $categoryName = $cat ? $cat->name : "Semua Kategori";
    }

    $listMetodePembayaran = ['transfer' => 'Transfer Bank', 'cash' => 'Tunai (Cash)'];

    $filename = 'Laporan_Pendapatan_' . str_replace(' ', '_', $villaName) . '_' . now()->format('YmdHis') . '.xlsx';

    // Kirim $request->all() sebagai parameter terakhir (filterParams)
    return Excel::download(
        new PendapatanReportExport($data, $villaName, $listMetodePembayaran, $categoryName, $request->all()), 
        $filename
    );
}
public function pengeluaranExcel(Request $request)
{
    $data = $this->getFilteredPengeluaranDataFromRequest($request)->load('category');

    if ($data->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data pengeluaran untuk diekspor.');
    }
    
    $villa = Villa::find($request->villa_id);
    $villaName = $villa->nama_villa ?? "Semua Villa";
    
    $categoryName = 'Semua Kategori';
    if ($request->category) {
        $cat = \App\Models\Category::find($request->category);
        $categoryName = $cat ? $cat->name : 'Semua Kategori';
    }

    $filename = 'Laporan_Pengeluaran_' . str_replace(' ', '_', $villaName) . '_' . now()->format('Ymd_His') . '.xlsx';

    // Sertakan $request->all() untuk parameter filterParams
    return Excel::download(
        new PengeluaranReportExport($data, $villaName, $categoryName, $request->all()), 
        $filename
    );
}

public function pengeluaranPdf(Request $request)
{
    // 1. Ambil data dengan filter kategori yang sudah diperbaiki
    $data = $this->getFilteredPengeluaranDataFromRequest($request)->load('category');

    if ($data->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
    }
    
    // 2. Data Villa & Nama Kategori untuk Judul
    $villa = Villa::find($request->villa_id);
    $villaName = $villa->nama_villa ?? "Semua Villa";
    
    $categoryName = 'Semua Kategori';
    if ($request->category) {
        $cat = \App\Models\Category::find($request->category);
        $categoryName = $cat ? $cat->name : 'Semua Kategori';
    }

    // 3. Load View PDF - DISESUAIKAN AGAR SAMA DENGAN PENDAPATAN
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pengeluaran_pdf', [
        'dataPengeluaran' => $data,
        'villaName' => $villaName,
        'categoryName' => $categoryName,
        // UBAH dateRange menjadi filterParams agar terbaca oleh Blade
        'filterParams' => [
            'start' => $request->start,
            'end'   => $request->end,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]
    ])
    ->setPaper('a4', 'landscape');

    $filename = 'Laporan_Pengeluaran_' . str_replace(' ', '_', $villaName) . '_' . now()->format('Ymd_His') . '.pdf';
    return $pdf->download($filename); 
}

    /**
     * Export data ke format PDF.
     */
    public function pendapatanPdf(Request $request)
{
    $data = $this->getFilteredDataFromRequest($request);

    if ($data->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
    }
    
    $villa = Villa::find($request->villa_id);
    $villaName = $villa->nama_villa ?? "Villa Tidak Ditemukan";

    $categoryName = "Semua Kategori";
    if ($request->category) {
        $cat = \App\Models\Category::find($request->category);
        $categoryName = $cat ? $cat->name : "Semua Kategori";
    }

    $listMetodePembayaran = [
        'transfer' => 'Transfer Bank',
        'cash'     => 'Tunai (Cash)',
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pendapatan_pdf', [
        'dataPendapatan' => $data,
        'filterParams' => $request->all(), 
        'listMetodePembayaran' => $listMetodePembayaran,
        'villaName' => $villaName,
        'categoryName' => $categoryName,
    ])->setPaper('a4', 'landscape'); // Menggunakan Landscape agar kolom tidak sesak

    $filename = 'Laporan_Pendapatan_' . str_replace(' ', '_', $villaName) . '_' . now()->format('YmdHis') . '.pdf';
    return $pdf->download($filename); 
}

public function laporanExcel(Request $request)
{
    // 1. Ambil Data Villa & Kategori Khusus
    $villa = Villa::with('specialCategories')->findOrFail($request->villa_id);
    $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();

    // 2. Inisialisasi Query (Gunakan clone agar query dasar tidak tercampur)
    $pKhususQuery = \App\Models\Pendapatan::where('villa_id', $villa->id)->whereIn('category_id', $specialCategoryIds);
    $exKhususQuery = \App\Models\Pengeluaran::where('villa_id', $villa->id)->whereIn('category_id', $specialCategoryIds);
    $pUmumQuery = \App\Models\Pendapatan::where('villa_id', $villa->id)->whereNotIn('category_id', $specialCategoryIds);
    $exUmumQuery = \App\Models\Pengeluaran::where('villa_id', $villa->id)->whereNotIn('category_id', $specialCategoryIds);

    // 3. Filter Tahun & Bulan
    if ($request->tahun) {
        foreach ([$pKhususQuery, $exKhususQuery, $pUmumQuery, $exUmumQuery] as $q) {
            $q->whereYear('tanggal', $request->tahun);
        }
    }
    if ($request->bulan) {
        foreach ([$pKhususQuery, $exKhususQuery, $pUmumQuery, $exUmumQuery] as $q) {
            $q->whereMonth('tanggal', $request->bulan);
        }
    }

    // 4. Eksekusi Sum Nominal
    $pKhusus = (float) $pKhususQuery->sum('nominal');
    $exKhusus = (float) $exKhususQuery->sum('nominal');
    $pUmum = (float) $pUmumQuery->sum('nominal');
    $exUmum = (float) $exUmumQuery->sum('nominal');

    // 5. Ambil Persentase (Sesuai logika dashboard Anda)
    $servicePercentage = $villa->service_karyawan;
    $feePercentage = $villa->fee_manajemen;

    // 6. LOGIKA PERHITUNGAN (Harus sama dengan PDF & Dashboard)
    $marginKhusus = $pKhusus - $exKhusus;
    $serviceKaryawanNominal = $marginKhusus > 0 ? $marginKhusus * ($servicePercentage / 100) : 0;
    
    // Pendapatan Kotor = (Margin Khusus - Service) + (Pendapatan Umum - Pengeluaran Umum)
    $pendapatanKotor = ($marginKhusus - $serviceKaryawanNominal) + ($pUmum - $exUmum);
    $feeManajemenNominal = $pendapatanKotor * ($feePercentage / 100);
    
    $totalPendapatan = $pKhusus + $pUmum;
    $totalPengeluaran = $exKhusus + $exUmum;

    // 7. Penamaan File
   $periodeName = $request->bulan 
    ? \Carbon\Carbon::createFromDate($request->tahun, (int)$request->bulan, 1)->translatedFormat('F-Y')
    : $request->tahun;
    $filename = 'laporan_excel_' . str_replace(' ', '_', $periodeName) . '.xlsx';

    // 8. Export (PASTIKAN SEMUA PARAMETER TERISI)
    return \Maatwebsite\Excel\Facades\Excel::download(
        new LaporanReportExport(
            $totalPendapatan,        // 1
            $totalPengeluaran,       // 2
            $villa->nama_villa,      // 3
            $periodeName,            // 4
            $servicePercentage,      // 5
            $feePercentage,          // 6
            $serviceKaryawanNominal, // 7
            $feeManajemenNominal,    // 8
            $pendapatanKotor,        // 9
            $pKhusus,                // 10 (Tambahan)
            $exKhusus,               // 11 (Tambahan)
            $pUmum,                  // 12 (Tambahan)
            $exUmum                  // 13 (Tambahan)
        ),
        $filename
    );
}


public function laporanPdf(Request $request)
{
    // 1. Ambil Data Villa & Kategori Khusus (sama seperti di Livewire)
    $villa = Villa::with('specialCategories')->findOrFail($request->villa_id);
    $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();

    // 2. Inisialisasi Query menggunakan Eloquent Model
    $pKhususQuery = \App\Models\Pendapatan::where('villa_id', $villa->id)->whereIn('category_id', $specialCategoryIds);
    $exKhususQuery = \App\Models\Pengeluaran::where('villa_id', $villa->id)->whereIn('category_id', $specialCategoryIds);
    
    $pUmumQuery = \App\Models\Pendapatan::where('villa_id', $villa->id)->whereNotIn('category_id', $specialCategoryIds);
    $exUmumQuery = \App\Models\Pengeluaran::where('villa_id', $villa->id)->whereNotIn('category_id', $specialCategoryIds);

    // 3. Filter Tahun & Bulan
    if ($request->tahun) {
        foreach ([$pKhususQuery, $exKhususQuery, $pUmumQuery, $exUmumQuery] as $q) {
            $q->whereYear('tanggal', $request->tahun);
        }
    }

    if ($request->bulan) {
        foreach ([$pKhususQuery, $exKhususQuery, $pUmumQuery, $exUmumQuery] as $q) {
            $q->whereMonth('tanggal', $request->bulan);
        }
    }

    // 4. Ambil Nominal
    $pKhusus = (float) $pKhususQuery->sum('nominal');
    $exKhusus = (float) $exKhususQuery->sum('nominal');
    $pUmum = (float) $pUmumQuery->sum('nominal');
    $exUmum = (float) $exUmumQuery->sum('nominal');

    // 5. Logika History Fee (Opsional tapi disarankan agar akurat)
    // Mencari fee yang berlaku pada periode tersebut
    $filterDate = $request->bulan 
        ? Carbon::createFromDate($request->tahun, $request->bulan, 1)->format('Y-m-d')
        : Carbon::createFromDate($request->tahun, 1, 1)->format('Y-m-d');

    $history = \App\Models\VillaFeeHistory::where('villa_id', $villa->id)
        ->where('mulai_berlaku', '<=', $filterDate)
        ->orderBy('mulai_berlaku', 'desc')
        ->first();

    $servicePercentage = $history ? $history->service_karyawan : $villa->service_karyawan;
    $feePercentage = $history ? $history->fee_manajemen : $villa->fee_manajemen;

    // 6. LOGIKA PERHITUNGAN BARU (Persis Dashboard)
    $marginKhusus = $pKhusus - $exKhusus;
    
    // Service Karyawan HANYA dari Margin Khusus (jika positif)
    $serviceNominal = $marginKhusus > 0 ? $marginKhusus * ($servicePercentage / 100) : 0;
    
    // Pendapatan Kotor = (Margin Khusus - Service) + Margin Umum
    $pendapatanKotor = ($marginKhusus - $serviceNominal) + ($pUmum - $exUmum);
    
    // Fee Manajemen dari Pendapatan Kotor
    $feeNominal = $pendapatanKotor * ($feePercentage / 100);
    
    $pendapatanOwner = $pendapatanKotor - $feeNominal;

    // 7. Format Periode untuk Nama File
    $periode = $request->bulan
        ? Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F_Y')
        : $request->tahun;

    // 8. Load View PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.laporan_pdf', [
        'villaName'         => $villa->nama_villa,
        'periode'           => str_replace('_', ' ', $periode),
        'totalPendapatan'   => $pKhusus + $pUmum,
        'totalPengeluaran'  => $exKhusus + $exUmum,
        'pKhusus'           => $pKhusus,
        'exKhusus'          => $exKhusus,
        'marginKhusus'      => $marginKhusus,
        'pUmum'             => $pUmum,
        'exUmum'            => $exUmum,
        'servicePercentage' => $servicePercentage,
        'serviceNominal'    => $serviceNominal,
        'pendapatanKotor'   => $pendapatanKotor,
        'feePercentage'     => $feePercentage,
        'feeNominal'        => $feeNominal,
        'pendapatanOwner'   => $pendapatanOwner,
    ])->setPaper('a4', 'portrait'); // Biasanya laporan keuangan lebih rapi portrait

    return $pdf->download('laporan_keuangan_' . $periode . '.pdf');
}

public function managementFeeExcel(Request $request)
{
    // 1. Inisialisasi Data Dasar
    $selectedYear = $request->tahun ?? now()->year;
    $selectedMonth = $request->bulan; // Bisa null jika tahunan
    $filterVilla = $request->villa_id; // Bisa null jika semua villa

    $queryVillas = Villa::with('specialCategories')->orderBy('nama_villa');
    if ($filterVilla) {
        $queryVillas->where('id', $filterVilla);
    }
    $villas = $queryVillas->get();

    $reports = [];
    $totalNetRevenueGlobal = 0;
    $totalFeeManagementGlobal = 0;

    // 2. Tentukan Rentang Bulan
    $monthsToProcess = $selectedMonth ? [(int)$selectedMonth] : range(1, 12);

    foreach ($villas as $villa) {
        $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();
        
        $villaTotalLabaKotor = 0;
        $villaTotalFeeAmount = 0;
        $villaTotalServiceAmount = 0;
        $usedFeesInPeriod = [];

        foreach ($monthsToProcess as $month) {
            $currentStart = Carbon::createFromDate($selectedYear, $month, 1)->startOfMonth();
            $currentEnd = $currentStart->copy()->endOfMonth();

            // Ambil Fee dari History atau Default Villa
            $history = \App\Models\VillaFeeHistory::where('villa_id', $villa->id)
                ->where('mulai_berlaku', '<=', $currentStart->format('Y-m-d'))
                ->orderBy('mulai_berlaku', 'desc')
                ->first();

            $feeServicePercent = $history ? $history->service_karyawan : $villa->service_karyawan;
            $feeManajPercent = $history ? $history->fee_manajemen : $villa->fee_manajemen;
            
            $usedFeesInPeriod[] = (float)$feeManajPercent;

            // Query Data
            $pKhusus = Pendapatan::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereIn('category_id', $specialCategoryIds)->sum('nominal');
            $exKhusus = Pengeluaran::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereIn('category_id', $specialCategoryIds)->sum('nominal');
            $pUmum = Pendapatan::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereNotIn('category_id', $specialCategoryIds)->sum('nominal');
            $exUmum = Pengeluaran::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereNotIn('category_id', $specialCategoryIds)->sum('nominal');

            // Hitung
            $marginKhusus = $pKhusus - $exKhusus;
            $sNominal = $marginKhusus > 0 ? $marginKhusus * ($feeServicePercent / 100) : 0;
            $lKotor = ($marginKhusus - $sNominal) + ($pUmum - $exUmum);
            $fNominal = $lKotor * ($feeManajPercent / 100);

            $villaTotalLabaKotor += $lKotor;
            $villaTotalFeeAmount += $fNominal;
            $villaTotalServiceAmount += $sNominal;
        }

        $uniqueFees = array_unique($usedFeesInPeriod);
        $displayFee = count($uniqueFees) > 1 ? 'Mixed' : head($uniqueFees);

        $reports[] = [
            'name' => $villa->nama_villa,
            'laba_kotor' => $villaTotalLabaKotor,
            'fee_percent' => $displayFee,
            'fee_amount' => $villaTotalFeeAmount,
            'service_amount' => $villaTotalServiceAmount,
        ];

        $totalNetRevenueGlobal += $villaTotalLabaKotor;
        $totalFeeManagementGlobal += $villaTotalFeeAmount;
    }

    // 3. Metadata untuk Excel
    $summary = [
        'total_revenue' => $totalNetRevenueGlobal,
        'total_fee' => $totalFeeManagementGlobal,
    ];

    $periode = $selectedMonth 
        ? Carbon::createFromDate($selectedYear, (int)$selectedMonth, 1)->translatedFormat('F Y')
        : "Tahun " . $selectedYear;

    $filename = 'Laporan_Fee_Manajemen_' . str_replace(' ', '_', $periode) . '.xlsx';

    return Excel::download(new ManagementFeeExport($reports, $summary, $periode), $filename);
}


public function managementFeePdf(Request $request)
{
    // 1. Logika pengambilan data (Sama dengan Excel)
    $selectedYear = $request->tahun ?? now()->year;
    $selectedMonth = $request->bulan;
    $filterVilla = $request->villa_id;

    $queryVillas = Villa::with('specialCategories')->orderBy('nama_villa');
    if ($filterVilla) {
        $queryVillas->where('id', $filterVilla);
    }
    $villas = $queryVillas->get();

    $reports = [];
    $totalNetRevenueGlobal = 0;
    $totalFeeManagementGlobal = 0;
    $monthsToProcess = $selectedMonth ? [(int)$selectedMonth] : range(1, 12);

    foreach ($villas as $villa) {
        $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();
        $villaTotalLabaKotor = 0;
        $villaTotalFeeAmount = 0;

        foreach ($monthsToProcess as $month) {
            $currentStart = \Carbon\Carbon::createFromDate($selectedYear, $month, 1)->startOfMonth();
            $currentEnd = $currentStart->copy()->endOfMonth();

            $history = \App\Models\VillaFeeHistory::where('villa_id', $villa->id)
                ->where('mulai_berlaku', '<=', $currentStart->format('Y-m-d'))
                ->orderBy('mulai_berlaku', 'desc')->first();

            $feeServicePercent = $history ? $history->service_karyawan : $villa->service_karyawan;
            $feeManajPercent = $history ? $history->fee_manajemen : $villa->fee_manajemen;

            $pKhusus = Pendapatan::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereIn('category_id', $specialCategoryIds)->sum('nominal');
            $exKhusus = Pengeluaran::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereIn('category_id', $specialCategoryIds)->sum('nominal');
            $pUmum = Pendapatan::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereNotIn('category_id', $specialCategoryIds)->sum('nominal');
            $exUmum = Pengeluaran::where('villa_id', $villa->id)->whereBetween('tanggal', [$currentStart, $currentEnd])->whereNotIn('category_id', $specialCategoryIds)->sum('nominal');

            $marginKhusus = $pKhusus - $exKhusus;
            $sNominal = $marginKhusus > 0 ? $marginKhusus * ($feeServicePercent / 100) : 0;
            $lKotor = ($marginKhusus - $sNominal) + ($pUmum - $exUmum);
            
            $villaTotalLabaKotor += $lKotor;
            $villaTotalFeeAmount += ($lKotor * ($feeManajPercent / 100));
        }

        $reports[] = [
            'name' => $villa->nama_villa,
            'laba_kotor' => $villaTotalLabaKotor,
            'fee_percent' => $feeManajPercent, // Tambahkan baris ini
            'fee_amount' => $villaTotalFeeAmount,
        ];
        $totalNetRevenueGlobal += $villaTotalLabaKotor;
        $totalFeeManagementGlobal += $villaTotalFeeAmount;
    }

    $periode = $selectedMonth 
        ? \Carbon\Carbon::createFromDate($selectedYear, (int)$selectedMonth, 1)->translatedFormat('F Y')
        : "Tahun " . $selectedYear;

    // 2. Load View PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.management_fee_pdf', [
        'reports' => $reports,
        'totalRevenue' => $totalNetRevenueGlobal,
        'totalFee' => $totalFeeManagementGlobal,
        'periode' => $periode,
        'averagePercent' => $totalNetRevenueGlobal > 0 ? ($totalFeeManagementGlobal / $totalNetRevenueGlobal) * 100 : 0
    ])->setPaper('a4', 'portrait');

    return $pdf->download('Laporan_Fee_Manajemen_' . str_replace(' ', '_', $periode) . '.pdf');
}


}