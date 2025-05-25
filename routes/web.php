<?php

use App\Http\Controllers\InvestorController;
use App\Http\Controllers\SamplingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // InvestoreContoller
    Route::get('investors', [InvestorController::class, 'index'])->name('investors.index');
    Route::get('investors/list', [InvestorController::class, 'list'])->name('investors.list');
    Route::post('investors', [InvestorController::class, 'store'])->name('investors.store');
    Route::put('investors/{investor}', [InvestorController::class, 'update'])->name('investors.update');
    Route::delete('investors/{investor}', [InvestorController::class, 'destroy'])->name('investors.destroy');
    Route::get('investors/select', [InvestorController::class, 'select'])->name('investors.select');

    // SamplingController
    Route::get('samplings', [SamplingController::class, 'index'])->name('samplings.index');
    Route::get('samplings/list', [SamplingController::class, 'list'])->name('samplings.list');
    Route::post('samplings', [SamplingController::class, 'store'])->name('samplings.store');
    Route::put('samplings/{sampling}', [SamplingController::class, 'update'])->name('samplings.update');
    Route::delete('samplings/{sampling}', [SamplingController::class, 'destroy'])->name('samplings.destroy');
    Route::get('samplings/report', [SamplingController::class, 'report'])->name('samplings.report');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
