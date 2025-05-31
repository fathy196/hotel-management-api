<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.verify');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('jwt.verify');
    Route::get('/me', [AuthController::class, 'me'])->middleware('jwt.verify');
});

Route::prefix('rooms')->group(function () {
    // Public routes
    Route::get('/', [RoomController::class, 'index']);
    Route::get('/trashed', [RoomController::class, 'trashed'])->middleware(['jwt.verify', 'permission:manage rooms']);
    Route::get('/available', [RoomController::class, 'available']);
    Route::get('/{room}', [RoomController::class, 'show']);

    // Protected routes (require JWT auth + manage rooms permission)
    Route::middleware(['jwt.verify', 'permission:manage rooms'])->group(function () {
        Route::post('/', [RoomController::class, 'store']);
        Route::put('/{room}', [RoomController::class, 'update']);
        Route::delete('/{room}', [RoomController::class, 'destroy']);
        Route::post('/{id}/restore', [RoomController::class, 'restore']);
    });
});
Route::middleware(['jwt.verify'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);

});