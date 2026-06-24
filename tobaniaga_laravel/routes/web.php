<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KategoriUmkmController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TobaNiaga
|--------------------------------------------------------------------------
*/

// ── Landing Page ───────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ── Guest Routes (belum login) ─────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.store');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/forgot-password', function () {
        return view('auth.login'); // placeholder
    })->name('password.request');
});

// ── OTP Routes (tidak butuh auth/guest — email belum verified) ─
Route::get('/verify-otp',      [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/verify-otp',     [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/resend-otp',     [AuthController::class, 'resendOtp'])->name('otp.resend');

// ── Authenticated Routes ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard customer — redirect ke welcome
    Route::get('/customer/dashboard', function () {
        return redirect()->route('welcome');
    })->name('customer.dashboard')->middleware('role:customer');

    // Dashboard Sales
    Route::get('/sales/dashboard', function () {
        return view('sales.dashboard');
    })->name('sales.dashboard')->middleware('role:sales');

    // Dashboard Courier
    Route::get('/courier/dashboard', function () {
        return view('courier.dashboard');
    })->name('courier.dashboard')->middleware('role:courier');

    // ── Admin Routes ───────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard',          [AdminController::class, 'dashboard'])->name('dashboard');

        // Kelola Kategori UMKM
        Route::get('/kategori-umkm', [KategoriUmkmController::class, 'index'])->name('kategori-umkm.index');
        Route::post('/kategori-umkm', [KategoriUmkmController::class, 'store'])->name('kategori-umkm.store');
        Route::put('/kategori-umkm/{kategori_umkm}', [KategoriUmkmController::class, 'update'])->name('kategori-umkm.update');
        Route::delete('/kategori-umkm/{kategori_umkm}', [KategoriUmkmController::class, 'destroy'])->name('kategori-umkm.destroy');

        // Kelola UMKM
        Route::get('/umkm/pending',       [AdminController::class, 'umkmPending'])->name('umkm.pending');
        Route::get('/umkm/{umkm}',        [AdminController::class, 'umkmDetail'])->name('umkm.detail');
        Route::post('/umkm/{umkm}/approve', [AdminController::class, 'umkmApprove'])->name('umkm.approve');
        Route::post('/umkm/{umkm}/reject',  [AdminController::class, 'umkmReject'])->name('umkm.reject');

        // Kelola User
        Route::get('/users',              [AdminController::class, 'users'])->name('users.index');
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    });
});
