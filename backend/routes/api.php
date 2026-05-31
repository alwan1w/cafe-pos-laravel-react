<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

// Route Public (Tidak perlu token)
Route::post('/auth/login', [AuthController::class, 'login']);

// Route Protected (Wajib bawa token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Hanya user dengan role 'admin' yang bisa mengakses rute di bawah ini
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('products', ProductController::class);

        // Tambahkan route tables di sini
        Route::apiResource('tables', TableController::class);
    });
});
