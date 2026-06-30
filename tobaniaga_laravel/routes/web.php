<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KategoriUmkmController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\CustomerKeranjangController;
use App\Http\Controllers\Customer\CustomerCheckoutController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\CustomerPesananController;
use App\Http\Controllers\Sales\SalesDashboardController;
use App\Http\Controllers\Sales\SalesPendapatanController;
use App\Http\Controllers\Sales\SalesPesananController;
use App\Http\Controllers\Sales\SalesLacakController;
use App\Http\Controllers\Courier\CourierController;
use App\Http\Controllers\Customer\AlamatCustomerController;
use App\Http\Controllers\Sales\SalesPromoController;
use App\Http\Controllers\Admin\AdminPromoController;
use App\Http\Controllers\Sales\SalesRekeningController;
use App\Http\Controllers\Sales\SalesPencairanController;
use App\Http\Controllers\Admin\AdminPencairanController;
use App\Http\Controllers\Admin\AdminOngkirController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\Courier\CourierDokumenController;
use App\Http\Controllers\Sales\SalesDokumenController;
use App\Http\Controllers\Admin\AdminVerifikasiDokumenController;
use App\Http\Controllers\Courier\CourierRekeningController;
use App\Http\Controllers\Courier\CourierPencairanController;
use App\Http\Controllers\Courier\CourierPendapatanController;
use App\Http\Controllers\Admin\AdminPencairanKurirController;
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

    $kategoriProduk = \App\Models\KategoriProduk::withCount('produk')->get();

    return view('welcome', compact('produkTerbaru', 'kategoriProduk'));
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

    Route::get('/sales/dashboard', [SalesDashboardController::class, 'index'])
        ->name('sales.dashboard')
        ->middleware('role:sales');

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

        // Kelola Promo
        Route::get('/promo',                    [AdminPromoController::class, 'index'])->name('promo.index');
        Route::post('/promo',                   [AdminPromoController::class, 'store'])->name('promo.store');
        Route::put('/promo/{promo}',            [AdminPromoController::class, 'update'])->name('promo.update');
        Route::delete('/promo/{promo}',         [AdminPromoController::class, 'destroy'])->name('promo.destroy');
        Route::patch('/promo/{promo}/toggle',   [AdminPromoController::class, 'toggle'])->name('promo.toggle');

        // Pencairan Dana
        Route::get('/pencairan',                  [AdminPencairanController::class, 'index'])->name('pencairan.index');
        Route::get('/pencairan/{pencairanDana}',  [AdminPencairanController::class, 'show'])->name('pencairan.show');
        Route::post('/pencairan/{pencairanDana}/proses', [AdminPencairanController::class, 'proses'])->name('pencairan.proses');
        Route::post('/pencairan/{pencairanDana}/selesai', [AdminPencairanController::class, 'selesai'])->name('pencairan.selesai');
        Route::post('/pencairan/{pencairanDana}/tolak',   [AdminPencairanController::class, 'tolak'])->name('pencairan.tolak');

        Route::get('/pencairan-kurir',                    [AdminPencairanKurirController::class, 'index'])->name('pencairan-kurir.index');
        Route::get('/pencairan-kurir/{pencairanDanaKurir}', [AdminPencairanKurirController::class, 'show'])->name('pencairan-kurir.show');
        Route::post('/pencairan-kurir/{pencairanDanaKurir}/proses',  [AdminPencairanKurirController::class, 'proses'])->name('pencairan-kurir.proses');
        Route::post('/pencairan-kurir/{pencairanDanaKurir}/selesai', [AdminPencairanKurirController::class, 'selesai'])->name('pencairan-kurir.selesai');
        Route::post('/pencairan-kurir/{pencairanDanaKurir}/tolak',   [AdminPencairanKurirController::class, 'tolak'])->name('pencairan-kurir.tolak');

        // Ongkos Kirim
        Route::get('/ongkir',                [AdminOngkirController::class, 'index'])->name('ongkir.index');
        Route::post('/ongkir',               [AdminOngkirController::class, 'store'])->name('ongkir.store');
        Route::put('/ongkir/{ongkosKirimTrayek}', [AdminOngkirController::class, 'update'])->name('ongkir.update');
        Route::delete('/ongkir/{ongkosKirimTrayek}', [AdminOngkirController::class, 'destroy'])->name('ongkir.destroy');
        Route::patch('/ongkir/{ongkosKirimTrayek}/toggle', [AdminOngkirController::class, 'toggle'])->name('ongkir.toggle');

        // Verifikasi Dokumen
        Route::get('/verifikasi-dokumen',                    [AdminVerifikasiDokumenController::class, 'index'])->name('verifikasi.dokumen.index');
        Route::get('/verifikasi-dokumen/{user}',             [AdminVerifikasiDokumenController::class, 'show'])->name('verifikasi.dokumen.show');
        Route::post('/verifikasi-dokumen/{user}/approve',    [AdminVerifikasiDokumenController::class, 'approve'])->name('verifikasi.dokumen.approve');
        Route::post('/verifikasi-dokumen/{user}/reject',     [AdminVerifikasiDokumenController::class, 'reject'])->name('verifikasi.dokumen.reject');
    });

    Route::middleware(['auth', 'role:sales'])->prefix('sales')->name('sales.')->group(function () {

        // Pendapatan
        Route::get('/pendapatan', [SalesPendapatanController::class, 'index'])->name('pendapatan.index')->middleware('role:sales');

        // Pesanan
        Route::get('/pesanan', [SalesPesananController::class, 'index'])->name('pesanan.index');
        Route::patch('/pesanan/{pesanan}', [SalesPesananController::class, 'update'])->name('pesanan.update');
        Route::post('/pesanan/{pesanan}/approve', [SalesPesananController::class, 'approve'])->name('pesanan.approve');

        // Produk
        Route::get('/produk', [App\Http\Controllers\Sales\SalesProdukController::class, 'index'])->name('produk.index');
        Route::post('/produk', [App\Http\Controllers\Sales\SalesProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{produk}', [App\Http\Controllers\Sales\SalesProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [App\Http\Controllers\Sales\SalesProdukController::class, 'destroy'])->name('produk.destroy');
        Route::delete('/produk/foto/{foto}', [App\Http\Controllers\Sales\SalesProdukController::class, 'destroyFoto'])->name('produk.foto.destroy');

        // Profil
        Route::get('/profil', [App\Http\Controllers\Sales\SalesProfilController::class, 'index'])->name('profil.index');
        Route::put('/profil', [App\Http\Controllers\Sales\SalesProfilController::class, 'update'])->name('profil.update');

        // Lacak
        Route::get('/lacak/{pesanan}', [SalesLacakController::class, 'show'])->name('lacak.show');

        // Promo
        Route::get('/promo',                [SalesPromoController::class, 'index'])->name('promo.index');
        Route::post('/promo',               [SalesPromoController::class, 'store'])->name('promo.store');
        Route::put('/promo/{promo}',        [SalesPromoController::class, 'update'])->name('promo.update');
        Route::delete('/promo/{promo}',     [SalesPromoController::class, 'destroy'])->name('promo.destroy');
        Route::patch('/promo/{promo}/toggle', [SalesPromoController::class, 'toggle'])->name('promo.toggle');

        // Rekening Bank
        Route::get('/rekening',                  [SalesRekeningController::class, 'index'])->name('rekening.index');
        Route::post('/rekening',                 [SalesRekeningController::class, 'store'])->name('rekening.store');
        Route::put('/rekening/{rekeningBank}',   [SalesRekeningController::class, 'update'])->name('rekening.update');
        Route::delete('/rekening/{rekeningBank}',[SalesRekeningController::class, 'destroy'])->name('rekening.destroy');
        Route::patch('/rekening/{rekeningBank}/utama', [SalesRekeningController::class, 'setUtama'])->name('rekening.utama');

        // Pencairan Dana
        Route::get('/pencairan',         [SalesPencairanController::class, 'index'])->name('pencairan.index');
        Route::post('/pencairan',        [SalesPencairanController::class, 'store'])->name('pencairan.store');

        Route::get('/dokumen',  [SalesDokumenController::class, 'form'])->name('dokumen');
        Route::post('/dokumen', [SalesDokumenController::class, 'store'])->name('dokumen.store');

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

        // Resume setelah input alamat pertama kali
        Route::get('/checkout/resume', [CustomerCheckoutController::class, 'resume'])->name('checkout.resume');

        // Payment
        Route::get('/payment/{pesanan}',         [CustomerPaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{pesanan}/charge', [CustomerPaymentController::class, 'charge'])->name('payment.charge');
        Route::get('/payment/{pesanan}/status', [CustomerPaymentController::class, 'status'])->name('payment.status');

        // Riwayat
        Route::get('/pesanan/riwayat', [CustomerPesananController::class, 'riwayat'])->name('pesanan.riwayat');
        Route::get('/pesanan/{pesanan}', [CustomerPesananController::class, 'show'])->name('pesanan.show');

        // Alamat
        Route::post('/alamat', [App\Http\Controllers\Customer\AlamatCustomerController::class, 'store'])->name('alamat.store');
        Route::delete('/alamat/{alamat}', [App\Http\Controllers\Customer\AlamatCustomerController::class, 'destroy'])->name('alamat.destroy');

        // Lacak Pesanan
        Route::get('/pesanan/{pesanan}/lacak', [CustomerPesananController::class, 'lacak'])->name('pesanan.lacak');

        // Promo
        Route::post('/checkout/apply-promo', [CustomerCheckoutController::class, 'applyPromo'])->name('checkout.apply-promo');

        Route::get('/checkout/resume', [CustomerCheckoutController::class, 'resume'])->name('checkout.resume');
        Route::post('/checkout/hitung-ongkir', [CustomerCheckoutController::class, 'hitungOngkir'])->name('checkout.hitung-ongkir');

    });

    Route::middleware(['auth', 'role:courier'])->prefix('courier')->name('courier.')->group(function () {
        Route::get('/dashboard',                              [CourierController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengiriman',                             [CourierController::class, 'pengirimanIndex'])->name('pengiriman.index');
        Route::post('/pengiriman/{pengiriman}/claim',         [CourierController::class, 'claim'])->name('pengiriman.claim');
        Route::patch('/pengiriman/{pengiriman}/status',       [CourierController::class, 'updateStatus'])->name('pengiriman.status');

        Route::get('/dokumen',  [CourierDokumenController::class, 'form'])->name('dokumen');
        Route::post('/dokumen', [CourierDokumenController::class, 'store'])->name('dokumen.store');

        Route::get('/rekening',                       [CourierRekeningController::class, 'index'])->name('rekening.index');
        Route::post('/rekening',                      [CourierRekeningController::class, 'store'])->name('rekening.store');
        Route::put('/rekening/{rekeningBankKurir}',   [CourierRekeningController::class, 'update'])->name('rekening.update');
        Route::delete('/rekening/{rekeningBankKurir}',[CourierRekeningController::class, 'destroy'])->name('rekening.destroy');
        Route::patch('/rekening/{rekeningBankKurir}/utama', [CourierRekeningController::class, 'setUtama'])->name('rekening.utama');

        Route::get('/pencairan',  [CourierPencairanController::class, 'index'])->name('pencairan.index');
        Route::post('/pencairan', [CourierPencairanController::class, 'store'])->name('pencairan.store');

        Route::get('/pendapatan', [CourierPendapatanController::class, 'index'])->name('pendapatan.index');
    });

    Route::prefix('daftar')->name('daftar.')->group(function () {
        Route::get('/sales',  [PendaftaranController::class, 'formSales'])->name('sales');
        Route::post('/sales', [PendaftaranController::class, 'storeSales'])->name('sales.store');

        Route::get('/kurir',  [PendaftaranController::class, 'formKurir'])->name('kurir');
        Route::post('/kurir', [PendaftaranController::class, 'storeKurir'])->name('kurir.store');
    });

    Route::get('/dokumen/{user}/{tipe}', [PendaftaranController::class, 'lihatDokumen'])
        ->name('dokumen.lihat')
        ->where('tipe', 'ktp|kk');
});

// Webhook — di luar grup auth
Route::post('/midtrans/callback', [CustomerPaymentController::class, 'callback'])
    ->name('midtrans.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
