<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AssemblyScanner;
use App\Livewire\AssemblyMonitor;
use App\Http\Controllers\AssemblyReportController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta protegida (o pública si prefieres) para el escaneo
Route::get('/scan/{assembly}', AssemblyScanner::class)->name('scan');
Route::get('/monitor/{assembly}', AssemblyMonitor::class)->name('monitor');
Route::get('/assemblies/{assembly}/report', [AssemblyReportController::class, 'download'])->name('assemblies.report');
Route::get('/fines/{fine}/receipt', [\App\Http\Controllers\ReceiptController::class, 'download'])->name('fines.receipt');
Route::get('/report/finance', [\App\Http\Controllers\FinancePDFController::class, 'download'])->name('report.finance');
Route::get('/citizens/{citizen}/card', [\App\Http\Controllers\CitizenController::class, 'card'])->name('citizen.card');
