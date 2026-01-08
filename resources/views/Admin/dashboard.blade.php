use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// ... Staff & Technician Routes sudah ada di atas ...

// =========================================================================
// 3. ADMIN (MANAGEMENT)
// =========================================================================
Route::middleware(['auth', /* 'role:admin' <-- Nanti aktifkan middleware ini */])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');

    // Nanti tambah route manage users, assets, dll disini
    });