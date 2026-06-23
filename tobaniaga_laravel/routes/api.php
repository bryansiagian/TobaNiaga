<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TobaNiaga
|--------------------------------------------------------------------------
| Catatan: route di bawah ini disiapkan sebagai kerangka modul Auth versi API
| (misalnya untuk konsumsi mobile app di masa depan via Sanctum). Untuk saat
| ini web app utama menggunakan session-based auth dari routes/web.php.
*/

Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user()->load('roles');
        })->name('api.user');

        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    });
});
