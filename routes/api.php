<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Property management
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{property}', [PropertyController::class, 'update']);
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);

    // Verification
    Route::post('/verification/identity', [VerificationController::class, 'initiateIdentityVerification']);
    Route::post('/verification/student', [VerificationController::class, 'initiateStudentVerification']);
    Route::get('/verification/status', [VerificationController::class, 'status']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Messages
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{user}', [MessageController::class, 'conversation']);
    Route::put('/messages/{message}/read', [MessageController::class, 'markAsRead']);
});

// Webhook routes (no auth required)
Route::post('/verification/callback', [VerificationController::class, 'callback']);
