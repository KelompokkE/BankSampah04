<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardPublikController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\PenimbanganController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\LaporanKegiatanController;

// Dashboard publik
Route::get('/', [DashboardPublikController::class, 'index'])
    ->name('dashboard.publik');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.proses');

// Halaman khusus pengelola
Route::middleware('auth')->group(function () {

    // Dashboard admin
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
        ->name('admin.dashboard');

    // Pengelolaan jenis sampah
    Route::get('/admin/jenis-sampah', [JenisSampahController::class, 'index'])
        ->name('admin.jenis-sampah.index');

    Route::post('/admin/jenis-sampah', [JenisSampahController::class, 'store'])
        ->name('admin.jenis-sampah.store');

    Route::put('/admin/jenis-sampah/{jenisSampah}', [JenisSampahController::class, 'update'])
        ->name('admin.jenis-sampah.update');

    Route::patch('/admin/jenis-sampah/{jenisSampah}/status', [JenisSampahController::class, 'toggleStatus'])
        ->name('admin.jenis-sampah.status');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Halaman sementara untuk uji coba penimbangan
    Route::view('/admin/penimbangan/tes', 'admin.penimbangan-tes')
        ->name('admin.penimbangan.tes');

    // Pengelolaan data penimbangan
    Route::get('/admin/penimbangan', [PenimbanganController::class, 'index'])
        ->name('admin.penimbangan.index');

    Route::post('/admin/penimbangan', [PenimbanganController::class, 'store'])
        ->name('admin.penimbangan.store');

    Route::put('/admin/penimbangan/{penimbangan}', [PenimbanganController::class, 'update'])
        ->name('admin.penimbangan.update');

    Route::delete('/admin/penimbangan/{penimbangan}', [PenimbanganController::class, 'destroy'])
        ->name('admin.penimbangan.destroy');

    // Pengelolaan data nasabah
    Route::get('/admin/nasabah', [NasabahController::class, 'index'])
        ->name('admin.nasabah.index');

    Route::post('/admin/nasabah', [NasabahController::class, 'store'])
        ->name('admin.nasabah.store');

    // Mengubah data nasabah
Route::put('/admin/nasabah/{nasabah}', [NasabahController::class, 'update'])
    ->name('admin.nasabah.update');

// Menghapus data nasabah
Route::delete('/admin/nasabah/{nasabah}', [NasabahController::class, 'destroy'])
    ->name('admin.nasabah.destroy');

    // Pengelolaan riwayat pengumpulan sampah
Route::get('/admin/pengumpulan', [PengumpulanController::class, 'index'])
    ->name('admin.pengumpulan.index');

Route::post('/admin/pengumpulan', [PengumpulanController::class, 'store'])
    ->name('admin.pengumpulan.store');

    // Riwayat pengumpulan per nasabah
Route::get('/admin/nasabah/{nasabah}/riwayat', [PengumpulanController::class, 'riwayatNasabah'])
    ->name('admin.nasabah.riwayat');


    // Pengelolaan laporan kegiatan
    Route::get('/admin/laporan-kegiatan', [LaporanKegiatanController::class, 'index'])
        ->name('admin.laporan-kegiatan.index');

    Route::get('/admin/laporan-kegiatan/create', [LaporanKegiatanController::class, 'create'])
        ->name('admin.laporan-kegiatan.create');

    Route::post('/admin/laporan-kegiatan', [LaporanKegiatanController::class, 'store'])
        ->name('admin.laporan-kegiatan.store');

    Route::get('/admin/laporan-kegiatan/{id}', [LaporanKegiatanController::class, 'show'])
        ->name('admin.laporan-kegiatan.show');

    Route::get('/admin/laporan-kegiatan/{id}/edit', [LaporanKegiatanController::class, 'edit'])
        ->name('admin.laporan-kegiatan.edit');

    Route::put('/admin/laporan-kegiatan/{id}', [LaporanKegiatanController::class, 'update'])
        ->name('admin.laporan-kegiatan.update');

    Route::delete('/admin/laporan-kegiatan/{id}', [LaporanKegiatanController::class, 'destroy'])
        ->name('admin.laporan-kegiatan.destroy');


});
