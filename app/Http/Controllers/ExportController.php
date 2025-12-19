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
    $villa = Villa::findOrFail($request->villa_id);

    $queryPendapatan = DB::table('pendapatan')->where('villa_id', $villa->id);
    $queryPengeluaran = DB::table('pengeluarans')->where('villa_id', $villa->id);

    if ($request->tahun) {
        $queryPendapatan->whereYear('tanggal', $request->tahun);
        $queryPengeluaran->whereYear('tanggal', $request->tahun);
    }

    // Hanya filter bulan kalau ada
    if ($request->bulan) {
        $queryPendapatan->whereMonth('tanggal', $request->bulan);
        $queryPengeluaran->whereMonth('tanggal', $request->bulan);
    }

    $totalPendapatan = (float) $queryPendapatan->sum('nominal');
    $totalPengeluaran = (float) $queryPengeluaran->sum('nominal');

    $servicePercentage = $villa->service_karyawan;
    $feePercentage = $villa->fee_manajemen;

   $periode = $request->bulan
        ? $request->bulan . '-' . $request->tahun // gunakan "-" bukan "/"
        : $request->tahun;

    $filename = 'laporan_' . str_replace(' ', '_', $periode) . '.xlsx';

    return Excel::download(
        new LaporanReportExport(
            $totalPendapatan,
            $totalPengeluaran,
            $villa->nama_villa,
            $periode, 
            $servicePercentage,
            $feePercentage
        ),
        $filename
    );
}


public function laporanPdf(Request $request)
{
    $villa = Villa::findOrFail($request->villa_id);

    $queryPendapatan = DB::table('pendapatan')->where('villa_id', $villa->id);
    $queryPengeluaran = DB::table('pengeluarans')->where('villa_id', $villa->id);

    if ($request->tahun) {
        $queryPendapatan->whereYear('tanggal', $request->tahun);
        $queryPengeluaran->whereYear('tanggal', $request->tahun);
    }
    if ($request->bulan) {
        $queryPendapatan->whereMonth('tanggal', $request->bulan);
        $queryPengeluaran->whereMonth('tanggal', $request->bulan);
    }

    $totalPendapatan = (float) $queryPendapatan->sum('nominal');
    $totalPengeluaran = (float) $queryPengeluaran->sum('nominal');

    $pendapatanBersih = $totalPendapatan - $totalPengeluaran;
    $servicePercentage = $villa->service_karyawan;
    $serviceNominal = $pendapatanBersih * ($servicePercentage / 100);
    $pendapatanKotor = $pendapatanBersih - $serviceNominal;
    $feePercentage = $villa->fee_manajemen;
    $feeNominal = $pendapatanKotor * ($feePercentage / 100);
    $pendapatanOwner = $pendapatanKotor - $feeNominal;

    $periode = $request->bulan
        ? $request->bulan . '_' . $request->tahun // <-- ganti / jadi _
        : $request->tahun;

    $pdf = Pdf::loadView('exports.laporan_pdf', [
        'villaName' => $villa->nama_villa,
        'periode' => $periode,
        'totalPendapatan' => $totalPendapatan,
        'totalPengeluaran' => $totalPengeluaran,
        'pendapatanBersih' => $pendapatanBersih,
        'servicePercentage' => $servicePercentage,
        'serviceNominal' => $serviceNominal,
        'pendapatanKotor' => $pendapatanKotor,
        'feePercentage' => $feePercentage,
        'feeNominal' => $feeNominal,
        'pendapatanOwner' => $pendapatanOwner,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('laporan_keuangan_' . $periode . '.pdf');
}






}