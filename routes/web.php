<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportStepController;
use App\Http\Controllers\ImportDocumentController;
use App\Http\Controllers\ImportCostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clients', ClientController::class);
    Route::resource('imports', ImportController::class);
    Route::resource('imports.steps', ImportStepController::class)->shallow();

    Route::get('documents/{document}/download', [ImportDocumentController::class, 'download'])
        ->name('documents.download');
    Route::resource('documents', ImportDocumentController::class)
        ->only(['edit', 'update']);

    Route::get('imports/{import}/costs/create', [ImportCostController::class, 'create'])
        ->name('imports.costs.create');
    Route::post('imports/{import}/costs', [ImportCostController::class, 'store'])
        ->name('imports.costs.store');

    Route::resource('costs', ImportCostController::class)
        ->only(['edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
