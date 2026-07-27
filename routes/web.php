<?php

use App\Http\Controllers\BenchmarkController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/benchmarks');

Route::get('/methodology', [\App\Http\Controllers\MethodologyController::class, 'index'])->name('methodology.index');

Route::get('/benchmarks', [BenchmarkController::class, 'index'])->name('benchmarks.index');
Route::get('/benchmarks/history', [BenchmarkController::class, 'history'])->name('benchmarks.history');
Route::get('/benchmarks/create', [BenchmarkController::class, 'create'])->name('benchmarks.create');
Route::post('/benchmarks', [BenchmarkController::class, 'store'])->name('benchmarks.store');
Route::get('/benchmarks/charts', [BenchmarkController::class, 'charts'])->name('benchmarks.charts');
Route::get('/benchmarks/{benchmark}', [BenchmarkController::class, 'show'])->name('benchmarks.show');
Route::delete('/benchmarks/{benchmark}', [BenchmarkController::class, 'destroy'])->name('benchmarks.destroy');
