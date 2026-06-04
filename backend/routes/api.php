<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Public\MenuController;
use App\Http\Controllers\Api\Public\ReservationController as PublicReservationController;
use App\Http\Controllers\Api\ReservationController as AdminReservationController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Support\Facades\Route;

// ==========================================
Route::get('/public/categories', [MenuController::class, 'categories']);
Route::get('/public/products', [MenuController::class, 'products']);
Route::post('/public/reservations', [PublicReservationController::class, 'store']);
Route::get('/public/tables/available', [PublicReservationController::class, 'checkAvailability']);

// Route Public (Tidak perlu token)
Route::post('/auth/login', [AuthController::class, 'login']);

// Route Protected (Wajib bawa token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // -- GRUP ADMIN & KASIR --
    Route::middleware('role:admin|kasir')->group(function () {
        Route::get('reservations', [AdminReservationController::class, 'index']);
        Route::get('reservations/{reservation}', [AdminReservationController::class, 'show']);
        Route::patch('reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus']);
        // POS Kasir
        Route::post('pos/vouchers/check', [PosController::class, 'checkVoucher']);
        Route::post('pos/transactions', [PosController::class, 'store']);
    });

    // -- GRUP KITCHEN (Akses: Admin & Kitchen) --
    Route::middleware('role:admin|kitchen')->group(function () {
        Route::get('kitchen/orders', [KitchenController::class, 'index']);
        Route::patch('kitchen/orders/{item}/status', [KitchenController::class, 'updateStatus']);
    });

    // -- GRUP BAR (Akses: Admin & Bar) --
    Route::middleware('role:admin|bar')->group(function () {
        Route::get('bar/orders', [BarController::class, 'index']);
        Route::patch('bar/orders/{item}/status', [BarController::class, 'updateStatus']);
    });

    // Hanya user dengan role 'admin' yang bisa mengakses rute di bawah ini
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('tables', TableController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('vouchers', VoucherController::class);
    });
});
