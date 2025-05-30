<?php

use App\Http\Controllers\InvestorController;
use App\Http\Controllers\SamplingController;
use App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\CageController;
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

    // FeedTypeController
    Route::get('feed-types', [FeedTypeController::class, 'index'])->name('feed-types.index');
    Route::get('feed-types/list', [FeedTypeController::class, 'list'])->name('feed-types.list');
    Route::post('feed-types', [FeedTypeController::class, 'store'])->name('feed-types.store');
    Route::put('feed-types/{feedType}', [FeedTypeController::class, 'update'])->name('feed-types.update');
    Route::delete('feed-types/{feedType}', [FeedTypeController::class, 'destroy'])->name('feed-types.destroy');
    Route::post('feed-types/{id}/restore', [FeedTypeController::class, 'restore'])->name('feed-types.restore');

    // CageController
    Route::get('cages', [CageController::class, 'index'])->name('cages.index');
    Route::get('cages/list', [CageController::class, 'list'])->name('cages.list');
    Route::post('cages', [CageController::class, 'store'])->name('cages.store');
    Route::put('cages/{cage}', [CageController::class, 'update'])->name('cages.update');
    Route::delete('cages/{cage}', [CageController::class, 'destroy'])->name('cages.destroy');
    Route::get('cages/{cage}/view', [CageController::class, 'show'])->name('cages.view');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
