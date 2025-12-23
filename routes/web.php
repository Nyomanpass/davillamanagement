<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login; // Component Login berada di sub-folder 'Auth'
use App\Livewire\Master\Dashboard as MasterDashboard;

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
use App\Livewire\Master\CreateUser;
use App\Livewire\Master\ManageUsers;
use App\Livewire\CategorySettings;
use App\Livewire\Master\VillaFeeHistoryManager;
use App\Livewire\Auth\VerifyPin;
use App\Livewire\Report\OccupancyReport;
use App\Livewire\Master\ProfileSettings;

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
    Route::get('/verify-pin', VerifyPin::class)->name('verify.pin');

    // =======================
    // ROLE: MASTER
    // =======================
   Route::middleware('pin.verified')->group(function () {
      Route::middleware('role:master,staf_master')->group(function () {
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
         Route::get('/master/akun/kelola/create-user', CreateUser::class)->name('master.manageakun.create-user');
         Route::get('/master/akun/kelola', ManageUsers::class)->name('master.users.manage');
         Route::get('/master/settings/categories', CategorySettings::class)->name('master.settings.categories');
         Route::get('/master/kelola-villa/{villaId}/settings', VillaFeeHistoryManager::class)->name('master.villa.settings');
         Route::get('/master/laporan/occupancy-report', OccupancyReport::class)->name('master.report.occupancy');
         Route::get('/export/management-fee-excel', [ExportController::class, 'managementFeeExcel'])->name('management.fee.excel');
         Route::get('/export/management-fee-pdf', [ExportController::class, 'managementFeePdf'])->name('management.fee.pdf');
         Route::get('/master/profile', ProfileSettings::class)->name('master.profile');
      });

    // =======================
    // ROLE: OWNER & STAF
    // =======================
    Route::middleware('role:owner,staf')->group(function () {
       // Route Livewire Invokable Component
       Route::get('/villa/dashboard', MasterDashboard::class)->name('villa.dashboard');
       Route::get('/villa/pendapatan', Pendapatan::class)->name('villa.pendapatan');
       Route::get('/villa/pengeluaran', Pengeluaran::class)->name('villa.pengeluaran');
       Route::get('/villa/laporan', Laporan::class)->name('villa.laporan');
       Route::get('/villa/laporan/occupancy-report', OccupancyReport::class)->name('villa.report.occupancy');
       Route::get('/export/pendapatan/excel', [ExportController::class, 'pendapatanExcel'])->name('export.pendapatan.excel');
       Route::get('/export/pendapatan/pdf', [ExportController::class, 'pendapatanPdf'])->name('export.pendapatan.pdf');
       Route::get('/export/pengeluaran/excel', [ExportController::class, 'pengeluaranExcel'])->name('export.pengeluaran.excel');
       Route::get('/export/pengeluaran/pdf', [ExportController::class, 'pengeluaranPdf'])->name('export.pengeluaran.pdf');
       Route::get('/laporan/excel', [ExportController::class, 'laporanExcel'])->name('laporan.excel');
       Route::get('/laporan/pdf', [ExportController::class, 'laporanPdf'])->name('laporan.pdf');
    });
   }); // End Middleware pin.verified
});