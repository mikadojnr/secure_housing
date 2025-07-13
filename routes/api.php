<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Property API routes
    Route::apiResource('properties', PropertyController::class);

    // Booking API routes
    Route::post('/bookings/{property}', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::get('/bookings', [BookingController::class, 'index']);

    // Verification API routes
    Route::post('/verification/identity/initiate', [VerificationController::class, 'initiateIdentityVerification']);
    Route::post('/verification/student/initiate', [VerificationController::class, 'initiateStudentVerification']);
    Route::post('/verification/callback', [VerificationController::class, 'callback'])->withoutMiddleware('auth:sanctum'); // Callback might not have auth
    Route::get('/verification/status', [VerificationController::class, 'status']);
});
