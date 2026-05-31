<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Route Public (Tidak perlu token)
Route::post('/auth/login', [AuthController::class, 'login']);

// Route Protected (Wajib bawa token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Nanti route master data (Admin), POS (Kasir), dll. akan diletakkan di dalam sini
});
