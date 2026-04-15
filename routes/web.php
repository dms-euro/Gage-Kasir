<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfilPerusahaanController;
use App\Http\Controllers\UssersController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginShow'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('registerShow');
    Route::post('/register', [AuthController::class, 'registerProses'])->name('registerProses');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::get('/pelanggan/{id}/produksi', [PelangganController::class, 'produksi'])->name('pelanggan.produksi');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::get('/produksi/kategori', [ProduksiController::class, 'kategoriIndex'])->name('kategori.index');
    Route::post('/produksi/kategori', [ProduksiController::class, 'kategoriStore'])->name('kategori.store');
    Route::put('/produksi/kategori/{id}', [ProduksiController::class, 'kategoriUpdate'])->name('kategori.update');
    Route::delete('/produksi/kategori/{id}', [ProduksiController::class, 'kategoriDestroy'])->name('kategori.destroy');
    Route::get('/produksi', [ProduksiController::class, 'index'])->name('produksi.index');
    Route::get('/produksi/create', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::post('/produksi', [ProduksiController::class, 'store'])->name('produksi.store');
    Route::post('/produksi/finalisasi', [ProduksiController::class, 'finalisasi'])->name('produksi.finalisasi');
    Route::get('/produksi/detail/{id}/edit', [ProduksiController::class, 'editDetail'])->name('produksi.detail.edit');
    Route::put('/produksi/detail/{id}', [ProduksiController::class, 'updateDetail'])->name('produksi.detail.update');
    Route::delete('/produksi/detail/{id}', [ProduksiController::class, 'destroyDetail'])->name('produksi.detail.destroy');
    Route::get('/produksi/invoice/{id_produksi}', [ProduksiController::class, 'invoice'])->name('produksi.invoice');
    Route::delete('/produksi/{id}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
    Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
    Route::get('/piutang/{id_produksi}', [PiutangController::class, 'show'])->name('piutang.show');
    Route::post('/piutang/{id_produksi}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
    Route::get('/profile-perusahaan', [ProfilPerusahaanController::class, 'index'])->name('profile-perusahaan.index');
    Route::put('/profile-perusahaan', [ProfilPerusahaanController::class, 'update'])->name('profile-perusahaan.update');
    Route::get('/users', [UssersController::class, 'index'])->name('user.index');
    Route::post('/users', [UssersController::class, 'store'])->name('user.store');
    Route::put('/users/{id}', [UssersController::class, 'update'])->name('user.update');
    Route::delete('/users/{id}', [UssersController::class, 'destroy'])->name('user.destroy');
    Route::get('/me', [UssersController::class, 'me'])->name('me.index');
    Route::put('/me/profile', [UssersController::class, 'updateProfile'])->name('me.updateProfile');
    Route::put('/me/password', [UssersController::class, 'updatePassword'])->name('me.updatePassword');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
