<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardPublikController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\PenimbanganController;

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

});
