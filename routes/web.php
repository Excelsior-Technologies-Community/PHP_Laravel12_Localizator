<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocalizatorController;

Route::get('/', [LocalizatorController::class, 'index']);
Route::post('/language', [LocalizatorController::class, 'storeLanguage']);
Route::get('/translations/{lang}', [LocalizatorController::class, 'translations']);
Route::post('/translations/save/{lang}', [LocalizatorController::class, 'saveTranslations']);
Route::get('/export/{lang}', [LocalizatorController::class, 'export']);

