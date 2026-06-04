<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Public\MenuController;
use App\Http\Controllers\Api\Public\ReservationController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ==========================================
Route::get('/public/categories', [MenuController::class, 'categories']);
Route::get('/public/products', [MenuController::class, 'products']);
Route::post('/public/reservations', [ReservationController::class, 'store']);
Route::get('/public/tables/available', [ReservationController::class, 'checkAvailability']);

// Route Public (Tidak perlu token)
Route::post('/auth/login', [AuthController::class, 'login']);

// Route Protected (Wajib bawa token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // -- GRUP ADMIN & KASIR --
    Route::middleware('role:admin|kasir')->group(function () {
        Route::get('reservations', [ReservationController::class, 'index']);
        Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
        Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);
    });

    // Hanya user dengan role 'admin' yang bisa mengakses rute di bawah ini
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('tables', TableController::class);
        Route::apiResource('users', UserController::class);
    });
});
