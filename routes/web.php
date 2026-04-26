<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DendaController;

// Route untuk Login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login_process'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grup route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Route untuk halaman beranda
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    // Route untuk CRUD Buku
    Route::get('/buku', [BukuController::class, 'index'])->name('buku');
    Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
    Route::get('/buku/{id}/edit', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');

    // Route untuk CRUD Peminjam
    Route::get('/peminjam', [PeminjamController::class, 'index'])->name('peminjam');
    Route::post('/peminjam', [PeminjamController::class, 'store'])->name('peminjam.store');
    Route::get('/peminjam/{id}/edit', [PeminjamController::class, 'edit'])->name('peminjam.edit');
    Route::put('/peminjam/{id}', [PeminjamController::class, 'update'])->name('peminjam.update');
    Route::delete('/peminjam/{id}', [PeminjamController::class, 'destroy'])->name('peminjam.destroy');

    // Route untuk Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::post('/transaksi/perpanjang/{id}', [TransaksiController::class, 'perpanjang'])->name('transaksi.perpanjang');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])
        ->name('transaksi.destroy')
        ->where('id', '[0-9]+');

    // Route untuk Denda
    Route::resource('denda', DendaController::class);
    Route::get('/denda/create/{peminjaman}', [DendaController::class, 'create'])->name('denda.create');
    Route::post('/denda/store', [DendaController::class, 'store'])->name('denda.store');
    Route::get('/denda/{denda}/edit', [DendaController::class, 'edit'])->name('denda.edit');
    Route::put('/denda/{denda}', [DendaController::class, 'update'])->name('denda.update');
    Route::delete('/denda/{denda}', [DendaController::class, 'destroy'])->name('denda.destroy');
    Route::post('/denda/{denda}/update-status', [DendaController::class, 'updateStatus'])->name('denda.updateStatus');

    // Route untuk Peminjaman
    Route::post('/transaksi/{id}/kembali', [TransaksiController::class, 'prosesKembali'])->name('transaksi.kembali');

    // Tambahkan route untuk testing keterlambatan
    Route::get('/test-keterlambatan/{id}', [TransaksiController::class, 'testKeterlambatan'])
         ->name('test.keterlambatan');

    // Tambahkan route untuk generate PDF
    Route::get('/transaksi/report', [TransaksiController::class, 'generateReport'])->name('transaksi.report');
});

