<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login; // Component Login berada di sub-folder 'Auth'
use App\Livewire\Master\Dashboard as MasterDashboard;
use App\Livewire\Villa\Dashboard as VillaDashboard;

use App\Livewire\Master\Pendapatan;
use App\Livewire\Master\Pengeluaran;
use App\Livewire\Master\KelolaVilla;
use App\Livewire\Master\Laporan;
use App\Livewire\Master\HistoryUser;
use App\Livewire\Master\CreateVilla;
use App\Livewire\Master\CreateEmployee;
use App\Livewire\Master\EditVilla; 
use App\Http\Controllers\ExportController;
use App\Livewire\Master\ManagementFeeReport;


// =======================
// GUEST (Belum login)
// =======================
Route::middleware('guest')->group(function () {
   // Route Livewire Invokable Component
   Route::get('/login', Login::class)->name('login');
});

// =======================
// AUTH (Sudah login)
// =======================
Route::middleware('auth')->group(function () {

    // Logout: Menggunakan method 'logout' di dalam Livewire Component 'Login'
    Route::get('/logout', [Login::class, 'logout'])->name('logout');

    // =======================
    // ROLE: MASTER
    // =======================
    Route::middleware('role:master')->group(function () {
       // Route Livewire Invokable Component
       Route::get('/master/dashboard', MasterDashboard::class)->name('master.dashboard');
       Route::get('/master/karyawan/create', CreateEmployee::class)->name('master.create.employee');
       Route::get('/master/pendapatan', Pendapatan::class)->name('master.pendapatan');
       Route::get('/master/pengeluaran', Pengeluaran::class)->name('master.pengeluaran');
       Route::get('/master/kelola-villa', KelolaVilla::class)->name('master.kelola.villa'); 
       Route::get('/master/kelola-villa/create', CreateVilla::class)->name('master.create.villa');
       Route::get('/villa/{villa}/edit', EditVilla::class)->name('master.edit.villa');
       Route::get('/master/laporan', Laporan::class)->name('master.laporan');
       Route::get('/master/history-user', HistoryUser::class)->name('master.history.user');
       Route::get('/export/pendapatan/excel', [ExportController::class, 'pendapatanExcel'])->name('export.pendapatan.excel');
       Route::get('/export/pendapatan/pdf', [ExportController::class, 'pendapatanPdf'])->name('export.pendapatan.pdf');
       Route::get('/export/pengeluaran/excel', [ExportController::class, 'pengeluaranExcel'])->name('export.pengeluaran.excel');
       Route::get('/export/pengeluaran/pdf', [ExportController::class, 'pengeluaranPdf'])->name('export.pengeluaran.pdf');
       Route::get('/laporan/excel', [ExportController::class, 'laporanExcel'])->name('laporan.excel');
       Route::get('/laporan/pdf', [ExportController::class, 'laporanPdf'])->name('laporan.pdf');
       Route::get('/master/laporan/fee-manajemen', ManagementFeeReport::class)->name('master.report.fee-management'); 
    });

    // =======================
    // ROLE: OWNER & STAF
    // =======================
    Route::middleware('role:owner,staf')->group(function () {
       // Route Livewire Invokable Component
       Route::get('/villa/dashboard', VillaDashboard::class)->name('villa.dashboard');
    });
});