<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TobaNiaga
|--------------------------------------------------------------------------
*/

// ============ LANDING PAGE ============
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ============ GUEST ROUTES (belum login) ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    // Catatan: route password.request (lupa kata sandi) dirujuk dari halaman login,
    // akan diimplementasikan pada modul Auth selanjutnya (forgot/reset password).
    Route::get('/forgot-password', function () {
        return view('auth.login'); // placeholder sementara, ganti saat modul reset password dibuat
    })->name('password.request');
});

// ============ AUTHENTICATED ROUTES (sudah login) ============
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard per role — placeholder, akan dikembangkan di modul berikutnya
    Route::get('/customer/dashboard', function () {
        return 'Dashboard Customer — TobaNiaga';
    })->name('customer.dashboard')->middleware('role:customer');

    Route::get('/sales/dashboard', function () {
        return 'Dashboard Sales — TobaNiaga';
    })->name('operator.dashboard')->middleware('role:sales');

    Route::get('/admin/dashboard', function () {
        return 'Dashboard Admin — TobaNiaga';
    })->name('admin.dashboard')->middleware('role:admin');

    Route::get('/courier/dashboard', function () {
        return 'Dashboard Kurir — TobaNiaga';
    })->name('courier.dashboard')->middleware('role:courier');
});
