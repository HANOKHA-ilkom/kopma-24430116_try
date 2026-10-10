<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ShuController;
use App\Http\Controllers\LaporanController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Application Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Anggota (Members)
    Route::resource('members', MemberController::class);

    // Simpanan
    Route::get('/simpanans', [SimpananController::class, 'index'])->name('simpanans.index');
    Route::post('/simpanans/transaksi', [SimpananController::class, 'storeTransaksi'])->name('simpanans.transaksi');

    // Pinjaman & Angsuran
    Route::resource('pinjamans', PinjamanController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/pinjamans/{id}/status', [PinjamanController::class, 'updateStatus'])->name('pinjamans.status');
    Route::post('/pinjamans/angsuran/{angsuranId}/bayar', [PinjamanController::class, 'bayarAngsuran'])->name('pinjamans.bayar');

    // Toko POS (Point of Sale Kasir)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/struk/{id}', [PosController::class, 'struk'])->name('pos.struk');

    // Produk & Stok
    Route::get('/produks', [ProdukController::class, 'index'])->name('produks.index');
    Route::post('/produks', [ProdukController::class, 'store'])->name('produks.store');
    Route::put('/produks/{id}', [ProdukController::class, 'update'])->name('produks.update');
    Route::post('/produks/{id}/stok', [ProdukController::class, 'updateStok'])->name('produks.stok');

    // Pembagian SHU
    Route::get('/shu', [ShuController::class, 'index'])->name('shu.index');
    Route::post('/shu/generate', [ShuController::class, 'generate'])->name('shu.generate');
    Route::post('/shu/cairkan/{id}', [ShuController::class, 'cairkan'])->name('shu.cairkan');

    // Laporan Keuangan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});
