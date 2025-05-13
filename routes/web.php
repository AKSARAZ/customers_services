<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
*/

// Home route - Redirect to dashboard if logged in, otherwise to login page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard'); // Redirect to dashboard if logged in
    }
    return redirect()->route('login'); // Redirect to login page if not logged in
});

// Dashboard Route - Only accessible for authenticated and verified users
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes - Protected by auth middleware
Route::prefix('admin')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ServiceController::class, 'dashboard'])->name('admin.dashboard');
    
    // Export and Print Routes
    Route::get('/services/export', [ServiceController::class, 'exportData'])->name('admin.services.exportData');
    Route::get('/services/print-report', [ServiceController::class, 'printReport'])->name('admin.services.print-report');
    
    // Service Routes
    Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('admin.services.show');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::get('/admin/laporan', [ServiceController::class, 'generateReport'])->name('admin.services.report');
    
    // Additional Service Routes
    Route::patch('/services/{id}/status', [ServiceController::class, 'updateStatus'])
        ->name('admin.services.updateStatus');
});

// Profile Routes - Profile related actions are protected by auth middleware
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
