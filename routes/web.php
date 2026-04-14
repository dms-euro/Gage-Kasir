<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProduksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginShow'])->name('loginShow');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('registerShow');
    Route::post('/register', [AuthController::class, 'registerProses'])->name('registerProses');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::get('/produksi/kategori', [ProduksiController::class, 'kategoriIndex'])->name('kategori.index');
    Route::post('/produksi/kategori', [ProduksiController::class, 'kategoriStore'])->name('kategori.store');
    Route::put('/produksi/kategori/{id}', [ProduksiController::class, 'kategoriUpdate'])->name('kategori.update');
    Route::delete('/produksi/kategori/{id}', [ProduksiController::class, 'kategoriDestroy'])->name('kategori.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
