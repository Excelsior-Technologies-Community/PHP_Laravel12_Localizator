<?php

use App\Http\Controllers\LocalizatorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [LocalizatorController::class, 'index']);
    Route::get('/translations/{lang}', [LocalizatorController::class, 'translations']);
    Route::post('/translations/save/{lang}', [LocalizatorController::class, 'saveTranslations']);
    Route::get('/export/{lang}', [LocalizatorController::class, 'export']);
    Route::get('/history/{lang}', [LocalizatorController::class, 'history']);
    Route::post('/rollback/{lang}/{historyId}', [LocalizatorController::class, 'rollback']);
    Route::get('/search/{lang}', [LocalizatorController::class, 'search']);
    Route::get('/preview/{lang}', [LocalizatorController::class, 'preview']);

    // Admin only routes
    Route::middleware('isAdmin')->group(function () {
        Route::post('/language', [LocalizatorController::class, 'storeLanguage']);
        Route::post('/admin/access/grant', [LocalizatorController::class, 'grantAccess']);
        Route::delete('/admin/access/revoke/{id}', [LocalizatorController::class, 'revokeAccess']);
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
