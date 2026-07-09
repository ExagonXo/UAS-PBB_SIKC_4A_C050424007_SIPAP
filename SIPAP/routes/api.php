<?php

use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PengembalianController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notifikasi}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken']);

    // Alat Routes
    Route::get('/alats', [AlatController::class, 'index']);
    Route::get('/alats/{alat}', [AlatController::class, 'show']);
    
    // Peminjaman Routes
    Route::get('/peminjamans', [PeminjamanController::class, 'index']);
    Route::post('/peminjamans', [PeminjamanController::class, 'store']);

    // Pengembalian Route (accessible to user to submit return, and admin to verify return)
    Route::post('/pengembalians', [PengembalianController::class, 'store']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        // Alat CRUD
        Route::post('/alats', [AlatController::class, 'store']);
        Route::put('/alats/{alat}', [AlatController::class, 'update']);
        Route::delete('/alats/{alat}', [AlatController::class, 'destroy']);

        // Peminjaman Approval
        Route::patch('/peminjamans/{peminjaman}/status', [PeminjamanController::class, 'updateStatus']);
    });
});
