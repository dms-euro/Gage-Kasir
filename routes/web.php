<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\AbsensiAdminController;
use App\Http\Controllers\Admin\AbsensiConfigController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisPelangganController;
use App\Http\Controllers\KasBukuController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfilPerusahaanController;
use App\Http\Controllers\UssersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginShow'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('registerShow');
    Route::post('/register', [AuthController::class, 'registerProses'])->name('registerProses');
    Route::get('/invoice', [ProduksiController::class, 'invoicePublic'])->name('invoice.public');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::get('/pelanggan/{id}/produksi', [PelangganController::class, 'produksi'])->name('pelanggan.produksi');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::post('/pelanggan/{id}/multi-tagihan', [PelangganController::class, 'multiTagihan'])->name('pelanggan.multi-tagihan');
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
    Route::get('/produksi/cetak/{id_produksi}', [ProduksiController::class, 'cetakNota'])->name('produksi.cetak');
    Route::get('/produksi/cetak-pdf/{id_produksi}', [ProduksiController::class, 'cetakNotaPdf'])->name('produksi.cetak-pdf');
    Route::get('/produksi/export-pdf', [ProduksiController::class, 'exportPdf'])->name('produksi.export-pdf');
    Route::delete('/produksi/{id}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
    Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
    Route::get('/piutang/export-pdf', [PiutangController::class, 'exportPdf'])->name('piutang.export-pdf');
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
    Route::get('/kas-buku', [KasBukuController::class, 'index'])->name('kas.index');
    Route::post('/kas-buku', [KasBukuController::class, 'store'])->name('kas.store');
    Route::delete('/kas-buku/{id}', [KasBukuController::class, 'destroy'])->name('kas.destroy');
    Route::get('/kas-buku/export', [KasBukuController::class, 'export'])->name('kas.export');
    Route::get('/jenis-pelanggan', [JenisPelangganController::class, 'index'])->name('jenis-pelanggan.index');
    Route::post('/jenis-pelanggan', [JenisPelangganController::class, 'store'])->name('jenis-pelanggan.store');
    Route::put('/jenis-pelanggan/{id}', [JenisPelangganController::class, 'update'])->name('jenis-pelanggan.update');
    Route::delete('/jenis-pelanggan/{id}', [JenisPelangganController::class, 'destroy'])->name('jenis-pelanggan.destroy');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/admin/absensi/rekap', [AbsensiAdminController::class, 'rekap'])->name('absensi.rekap');
    Route::get('/admin/absensi/export', [AbsensiAdminController::class, 'exportPdf'])->name('absensi.export');
    Route::get('/config-absensi', [AbsensiConfigController::class, 'index'])->name('config-absensi.index');
    Route::put('/config-absensi', [AbsensiConfigController::class, 'update'])->name('config-absensi.update');
    Route::get('/test-foto', function () {

        $photo = 'absensi\69ed7bde7560b.jpg'; // ganti sesuai file kamu

        // ambil ukuran
        $size = Storage::disk('public')->size($photo);

        $sizeKB = $size / 1024;
        $sizeMB = $sizeKB / 1024;

        dd([
            'file' => $photo,
            'byte' => $size,
            'kb' => round($sizeKB, 2),
            'mb' => round($sizeMB, 2),
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
