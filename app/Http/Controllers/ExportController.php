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


class ExportController extends Controller
{
    /**
     * Fungsi pembantu untuk mengambil data berdasarkan filter dari request.
     */
    private function getFilteredDataFromRequest(Request $request)
    {
        // Pastikan Anda memvalidasi input jika perlu, tapi fokus pada query
        
        $query = Pendapatan::with('villa')
            ->where('villa_id', $request->villa_id); // Filter wajib berdasarkan villa aktif

        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->start) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->end) {
            $query->whereDate('tanggal', '<=', $request->end);
        }
        
        return $query->latest()->get();
    }

    private function getFilteredPengeluaranDataFromRequest(Request $request)
    {
        $query = Pengeluaran::with('villa')
            ->where('villa_id', $request->villa_id);

        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->start) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->end) {
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
        
        // --- 1. Ambil Nama Villa ---
        $villaId = $request->villa_id;
        $villa = Villa::find($villaId);
        $villaName = $villa->nama_villa ?? "Semua Villa";

        $listJenisPendapatan = [ 
                'sewa' => 'Sewa Villa / Akomodasi',
                'makanan' => 'Penjualan Makanan',
                'minuman' => 'Penjualan Minuman',
                'laundry' => 'Layanan Laundry',
                // ... (Lengkapi yang lain)
                'lainnya' => 'Pendapatan Lain-Lain',
            ];
            $listMetodePembayaran = [
                'transfer' => 'Transfer Bank',
                'cash' => 'Tunai (Cash)',
            ];


        $filename = 'laporan_pendapatan_' . str_replace(' ', '_', $villaName) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // --- MENGGUNAKAN MAATWEBSITE EXCEL ---
        return Excel::download(
            new PendapatanReportExport($data, $villaName, $listJenisPendapatan, $listMetodePembayaran), 
            $filename
        );
    }

    public function pengeluaranExcel(Request $request)
    {
        // Gunakan helper function Pengeluaran
        $data = $this->getFilteredPengeluaranDataFromRequest($request);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pengeluaran untuk diekspor.');
        }
        
        $villaId = $request->villa_id;
        $villa = Villa::find($villaId);
        $villaName = $villa->nama_villa ?? "Semua Villa";

        // Daftar Jenis Pengeluaran (HARUS SAMA DENGAN DI LIVEWIRE)
        $listJenisPengeluaran = [
            'gaji' => 'Gaji Karyawan',
            'operasional' => 'Biaya Operasional',
            'marketing' => 'Biaya Marketing',
            'listrik' => 'Biaya Listrik/Air',
            'makanan' => 'Belanja Makanan/Bahan',
            'lainnya' => 'Pengeluaran Lain-Lain',
        ];


        $filename = 'laporan_pengeluaran_' . str_replace(' ', '_', $villaName) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // --- MENGGUNAKAN EXPORT CLASS UNTUK PENGELUARAN ---
        return Excel::download(
            new PengeluaranReportExport($data, $villaName, $listJenisPengeluaran), 
            $filename
        );
    }

    public function pengeluaranPdf(Request $request)
    {
        // 1. Ambil data Pengeluaran yang sudah difilter
        // Asumsi: Anda sudah memiliki method getFilteredDataFromRequest yang bisa menerima parameter filter
        $data = $this->getFilteredPengeluaranDataFromRequest($request, Pengeluaran::class); // Pastikan Model Pengeluaran yang dipakai

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }
        
        // 2. Ambil Nama Villa
        $villaId = $request->villa_id;
        $villa = Villa::find($villaId);
        $villaName = $villa->nama_villa ?? "Villa ID: {$villaId} (Tidak Ditemukan)";
        
        // 3. Mendefinisikan list mapping untuk Pengeluaran
        // Catatan: Pengeluaran umumnya hanya punya Jenis Pengeluaran, tidak ada Metode Pembayaran
        $listJenisPengeluaran = [
            'gaji' => 'Gaji Karyawan',
            'operasional' => 'Biaya Operasional',
            'marketing' => 'Biaya Marketing',
            'listrik' => 'Biaya Listrik/Air',
            'makanan' => 'Belanja Makanan/Bahan',
            'lainnya' => 'Pengeluaran Lain-Lain',
        ];

        // 4. Load View PDF dan Kirim Data
        $pdf = Pdf::loadView('exports.pengeluaran_pdf', [ // Ganti ke view pengeluaran_pdf
            'dataPengeluaran' => $data, // Ganti nama variabel data
            'filterParams' => $request->all(), 
            'listJenisPengeluaran' => $listJenisPengeluaran,
            'villaName' => $villaName,
        ])
        ->setPaper('a4', 'landscape'); // Format Landscape biasanya lebih baik untuk laporan tabel lebar

        // 5. Download File
        $filename = 'pengeluaran_filtered_' . str_replace(' ', '_', $villaName) . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename); 
    }

    /**
     * Export data ke format PDF.
     */
    public function pendapatanPdf(Request $request)
    {
        // ... (Logika getFilteredDataFromRequest) ...
        $data = $this->getFilteredDataFromRequest($request);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }
        
        // --- AMBIL NAMA VILLA BERDASARKAN ID YANG ADA DI REQUEST ---
        $villaId = $request->villa_id;
        $villa = Villa::find($villaId);
        $villaName = $villa->nama_villa ?? "Villa ID: {$villaId} (Tidak Ditemukan)";
        
        // PENTING: Mendefinisikan list mapping...
        $listJenisPendapatan = [ /* ... LENGKAPI DAFTAR INI ... */ ];
        $listMetodePembayaran = [ /* ... LENGKAPI DAFTAR INI ... */ ];

        // --- KIRIM NAMA VILLA KE VIEW ---
        $pdf = Pdf::loadView('exports.pendapatan_pdf', [
            'dataPendapatan' => $data,
            'filterParams' => $request->all(), 
            'listJenisPendapatan' => $listJenisPendapatan,
            'listMetodePembayaran' => $listMetodePembayaran,
            'villaName' => $villaName, // <-- Variabel BARU
        ]);

        // ... (Logika download) ...
        $filename = 'pendapatan_filtered_' . str_replace(' ', '_', $villaName) . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
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