<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickCountController;
use App\Http\Controllers\RealCountController;
use App\Http\Controllers\RekapQuickController;
use App\Http\Controllers\RekapRealController;
use App\Http\Controllers\SupporterController;
use App\Http\Controllers\TpsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoterController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Semua route wajib login
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.live-data');

    // Quick Count (Fitur Hitung Cepat TPS)
    Route::get('/quickcount', [QuickCountController::class, 'index'])->name('quickcount.index');
    Route::put('/quickcount/{tp}', [QuickCountController::class, 'update'])->name('quickcount.update');
    Route::delete('/quickcount/{tp}/reset', [QuickCountController::class, 'reset'])->name('quickcount.reset');

    // Rekap Quick Count vs Pendukung
    Route::get('/rekap-quick', [RekapQuickController::class, 'index'])->name('rekap-quick.index');
    Route::get('/rekap-quick/print', [RekapQuickController::class, 'print'])->name('rekap-quick.print');

    // Input Hasil Real Pemilihan tiap TPS (Real Count)
    Route::get('/realcount', [RealCountController::class, 'index'])->name('realcount.index');
    Route::put('/realcount/{tp}', [RealCountController::class, 'update'])->name('realcount.update');
    Route::delete('/realcount/{tp}/reset', [RealCountController::class, 'reset'])->name('realcount.reset');

    // Rekap Hasil Pemilih Real vs Data Pendukung tiap TPS
    Route::get('/rekap-real', [RekapRealController::class, 'index'])->name('rekap-real.index');
    Route::get('/rekap-real/print', [RekapRealController::class, 'print'])->name('rekap-real.print');

    // Manajemen Pemilih (CRUD Lengkap)
    Route::get('voters/template', [VoterController::class, 'downloadTemplate'])->name('voters.template');
    Route::post('voters/import', [VoterController::class, 'import'])->name('voters.import');
    Route::post('voters/bulk-supporter', [VoterController::class, 'bulkSupporter'])->name('voters.bulk-supporter');
    Route::post('voters/bulk-delete', [VoterController::class, 'bulkDelete'])->name('voters.bulk-delete');
    Route::resource('voters', VoterController::class)->except(['show']);
    Route::post('voters/{voter}/toggle', [VoterController::class, 'toggleSupporter'])->name('voters.toggle');

    // Khusus Pendukung
    Route::get('/supporters', [SupporterController::class, 'index'])->name('supporters.index');

    // Laporan & Rekapitulasi Data Pemilih
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');

    // ── Khusus Administrator (Admin Only) ────────────────────────────────────
    Route::middleware('admin')->group(function () {
        // Manajemen TPS (CRUD)
        Route::resource('tps', TpsController::class)->except(['show']);

        // Manajemen Pengguna / Akun Saksi TPS (CRUD)
        Route::resource('users', UserController::class)->except(['show']);

        // Master Data / Setting Calon Lurah (Main & Lawan)
        Route::resource('candidates', CandidateController::class)->except(['show', 'create', 'edit']);
    });

    // Profile Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
