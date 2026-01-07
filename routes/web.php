<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Models\Asset;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // 1. Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Dashboard
    Route::get('/dashboard', [TicketController::class, 'index'])->name('dashboard');

    // 3. Fitur Lapor
    Route::get('/lapor', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/lapor', [TicketController::class, 'store'])->name('tickets.store');
});

// API scan QR
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
        'image' => $asset->image_path // jika ada
    ]);
})->name('api.asset.scan');

require __DIR__ . '/auth.php';
