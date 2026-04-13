<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginShow'])->name('loginShow');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('registerShow');
    Route::post('/register', [AuthController::class, 'registerProses'])->name('registerProses');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
