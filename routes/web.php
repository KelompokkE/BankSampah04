<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardPublikController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;

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
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
        ->name('admin.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});