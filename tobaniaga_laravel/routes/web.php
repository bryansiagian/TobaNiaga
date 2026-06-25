<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KategoriUmkmController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\CustomerKeranjangController;
use App\Http\Controllers\Customer\CustomerCheckoutController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\CustomerPesananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TobaNiaga
|--------------------------------------------------------------------------
*/

// ── Landing Page ───────────────────────────────────────────
Route::get('/', function () {
    $produkTerbaru = \App\Models\Produk::with(['fotoProduk', 'umkm', 'kategori'])
        ->latest()
        ->take(10)
        ->get();
    return view('welcome', compact('produkTerbaru'));
})->name('welcome');

// Halaman katalog produk publik (customer & guest) — dengan filter
Route::get('/produk', [App\Http\Controllers\ProdukPublikController::class, 'index'])->name('produk.index');
Route::get('/produk/{slug}', [App\Http\Controllers\ProdukPublikController::class, 'detail'])->name('produk.detail');

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
        Route::get('/umkm', [AdminController::class, 'umkmIndex'])->name('umkm.index');
        Route::get('/umkm/pending',       [AdminController::class, 'umkmPending'])->name('umkm.pending');
        Route::get('/umkm/{umkm}',        [AdminController::class, 'umkmDetail'])->name('umkm.detail');
        Route::post('/umkm/{umkm}/approve', [AdminController::class, 'umkmApprove'])->name('umkm.approve');
        Route::post('/umkm/{umkm}/reject',  [AdminController::class, 'umkmReject'])->name('umkm.reject');
        Route::get('/umkm-rejected', [AdminController::class, 'umkmRejected'])->name('umkm.rejected');
        Route::post('/umkm-rejected/{umkm}/reactivate', [AdminController::class, 'umkmReactivate'])->name('umkm.reactivate');

        // Kelola User
        Route::get('/users',              [AdminController::class, 'users'])->name('users.index');
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
        Route::post('/users/{user}/aktivasi', [AdminController::class, 'aktivasiUser'])->name('users.aktivasi');
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Kelola Kategori Produk
        Route::get('/kategori-produk', [App\Http\Controllers\Admin\AdminKategoriProdukController::class, 'index'])->name('kategori-produk.index');
        Route::post('/kategori-produk', [App\Http\Controllers\Admin\AdminKategoriProdukController::class, 'store'])->name('kategori-produk.store');
        Route::put('/kategori-produk/{kategoriProduk}', [App\Http\Controllers\Admin\AdminKategoriProdukController::class, 'update'])->name('kategori-produk.update');
        Route::delete('/kategori-produk/{kategoriProduk}', [App\Http\Controllers\Admin\AdminKategoriProdukController::class, 'destroy'])->name('kategori-produk.destroy');
    });

    Route::middleware(['auth', 'role:sales'])->prefix('sales')->name('sales.')->group(function () {

        // Produk
        Route::get('/produk', [App\Http\Controllers\Sales\SalesProdukController::class, 'index'])->name('produk.index');
        Route::post('/produk', [App\Http\Controllers\Sales\SalesProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{produk}', [App\Http\Controllers\Sales\SalesProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [App\Http\Controllers\Sales\SalesProdukController::class, 'destroy'])->name('produk.destroy');
        Route::delete('/produk/foto/{foto}', [App\Http\Controllers\Sales\SalesProdukController::class, 'destroyFoto'])->name('produk.foto.destroy');

        // Profil
        Route::get('/profil', [App\Http\Controllers\Sales\SalesProfilController::class, 'index'])->name('profil.index');
        Route::put('/profil', [App\Http\Controllers\Sales\SalesProfilController::class, 'update'])->name('profil.update');


    });

    Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

        // Keranjang
        Route::get('/keranjang',              [CustomerKeranjangController::class, 'index'])->name('keranjang.index');
        Route::post('/keranjang',             [CustomerKeranjangController::class, 'store'])->name('keranjang.store');
        Route::put('/keranjang/{keranjang}',  [CustomerKeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{keranjang}',[CustomerKeranjangController::class, 'destroy'])->name('keranjang.destroy');

        // Checkout
        Route::post('/checkout',       [CustomerCheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout/store', [CustomerCheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout', fn() => redirect()->route('customer.keranjang.index'))->name('checkout.index');

        // Payment
        Route::get('/payment/{pesanan}',         [CustomerPaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{pesanan}/charge', [CustomerPaymentController::class, 'charge'])->name('payment.charge');
        Route::get('/payment/{pesanan}/status', [CustomerPaymentController::class, 'status'])->name('payment.status');

        // Riwayat
        Route::get('/pesanan/riwayat', [CustomerPesananController::class, 'riwayat'])->name('pesanan.riwayat');
        Route::get('/pesanan/{pesanan}', [CustomerPesananController::class, 'show'])->name('pesanan.show');

    });
});

// Webhook — di luar grup auth
Route::post('/midtrans/callback', [CustomerPaymentController::class, 'callback'])
    ->name('midtrans.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
