<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Models\Asset;

// Controller
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\Technician\JobController as TechJobController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

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

        // Dashboard Utama
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
    });

    // =========================================================================
    // 5. UTILITY & API (Internal)
    // =========================================================================
    Route::get('/scan-asset/{uuid}', function ($uuid) {
        $asset = Asset::with('location', 'category')->where('uuid', $uuid)->first();

        if (!$asset) {
            return response()->json(['error' => 'Aset tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'id' => $asset->id,
            'name' => $asset->name,
            'serial_number' => $asset->serial_number ?? '-',
            'location' => $asset->location->name ?? '-',
            'image' => $asset->image_path
        ]);
    })->name('api.asset.scan');
});

require __DIR__ . '/auth.php';
