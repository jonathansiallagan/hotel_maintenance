<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

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

require __DIR__.'/auth.php';
