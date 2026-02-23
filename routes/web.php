<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Models\Asset;
use App\Http\Middleware\IsAdmin;

// Controller
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\Technician\JobController as TechJobController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\SparepartController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SearchController;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'technician' => redirect()->route('technician.dashboard'),
            default => redirect()->route('staff.dashboard'),
        };
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // =========================================================================
    // 1. PROFILE MANAGEMENT
    // =========================================================================
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // =========================================================================
    // 2. STAFF / PELAPOR AREA
    // =========================================================================
    Route::prefix('staff')->name('staff.')->group(function () {

        // Dashboard Utama
        Route::get('/dashboard', [StaffTicketController::class, 'index'])
            ->name('dashboard');

        // Ticket Management
        Route::controller(StaffTicketController::class)->group(function () {

            // Fitur Lapor (Create)
            Route::get('/lapor', 'create')->name('tickets.create');
            Route::post('/lapor', 'store')->name('tickets.store');

            // Fitur Riwayat (History)
            Route::get('/riwayat', 'history')->name('tickets.history');

            // Detail Tiket
            Route::get('/tickets/{ticket}', 'show')->name('tickets.show');
        });
    });

    // =========================================================================
    // 3. TECHNICIAN
    // =========================================================================
    Route::prefix('technician')->name('technician.')->group(function () {

        // Dashboard Utama
        Route::get('/dashboard', [TechJobController::class, 'index'])
            ->name('dashboard');

        // Halaman Detail Tugas
        Route::get('/job/{id}', [TechJobController::class, 'show'])->name('job.show');

        // Fitur Update/Selesai
        Route::patch('/job/{id}', [TechJobController::class, 'update'])->name('job.update');
    });

    // =========================================================================
    // 4. ADMIN
    // =========================================================================
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::middleware([IsAdmin::class])->group(function () {

            // A. DASHBOARD
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            // RUTE BARU: Ekspor RCA ke PDF
            Route::get('/dashboard/export-rca', [AdminDashboardController::class, 'exportRca'])
                ->name('dashboard.exportRca');

            // SEARCH & NOTIFICATIONS
            Route::get('/search', [SearchController::class, 'search'])->name('search');
            Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('notifications');

            // B. MASTER DATA
            // management aset
            Route::resource('assets', AssetController::class);
            Route::get('/assets/{id}/print-qr', [AssetController::class, 'printQr'])
                ->name('assets.print_qr');

            //management sparepart
            Route::resource('spareparts', SparepartController::class);

            // C. OPERASIONAL (Monitoring Tiket)
            Route::resource('tickets', AdminTicketController::class)
                ->only(['index', 'show', 'update']);
            Route::get('/tickets/{id}/print', [AdminTicketController::class, 'print'])
                ->name('tickets.print');
            Route::post('/tickets/{id}/add-sparepart', [AdminTicketController::class, 'addSparepart'])
                ->name('tickets.add-sparepart');
            Route::delete('/tickets/{ticket}/remove-sparepart/{sparepart}', [AdminTicketController::class, 'removeSparepart'])
                ->name('tickets.remove-sparepart');

            // D. PERENCANAAN (Jadwal Maintenance Rutin)
            Route::resource('maintenance', MaintenanceController::class)
                ->only(['index', 'store', 'destroy']);

            // E. REPORTING (Laporan Tiket)
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

            // F. USER MANAGEMENT
            Route::resource('users', UserController::class)->only(['index']);
        });
    });

    Route::get('/scan/{uuid}', function ($uuid) {
        return "Scan Berhasil: " . $uuid;
    })->name('scan.index');

    // =========================================================================
    // 5. UTILITY & API (Internal)
    // =========================================================================
});

Route::get('/scan-asset/{identifier}', [AssetController::class, 'scanAssetJson'])
    ->name('scan.asset.json');

require __DIR__ . '/auth.php';
