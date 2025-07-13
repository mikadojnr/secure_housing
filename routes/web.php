<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Livewire\PropertySearch;
use App\Livewire\VerificationCenter;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/how-it-works', function () {
    return view('how-it-works');
})->name('how-it-works');

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Property routes
    Route::get('/properties', PropertySearch::class)->name('properties.index');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show')->where('property', '[0-9]+');
    Route::post('/properties/{property}/favorite', [PropertyController::class, 'toggleFavorite'])->name('properties.favorite')->where('property', '[0-9]+');

    // Landlord routes - using custom middleware
    Route::middleware(['check.user.type:landlord'])->group(function () {
        Route::get('/my-properties', [PropertyController::class, 'myProperties'])->name('landlord.properties');
        // Route::get('/list-property', [PropertyController::class, 'create'])->name('properties.create');
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit')->where('property', '[0-9]+');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update')->where('property', '[0-9]+');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy')->where('property', '[0-9]+');
    });



    // Verification routes
    Route::get('/verification', VerificationCenter::class)->name('verification.center');
    Route::get('/verification/success', [VerificationController::class, 'success'])->name('verification.success');
    Route::get('/verification/error', [VerificationController::class, 'error'])->name('verification.error');
    Route::post('/verification/identity', [VerificationController::class, 'initiateIdentityVerification'])->name('verification.identity');
    Route::post('/verification/student', [VerificationController::class, 'initiateStudentVerification'])->middleware('check.user.type:student')->name('verification.student');

    // Verification routes




    // Booking routes
    Route::get('/properties/{property}/book', [BookingController::class, 'create'])->name('properties.book')->where('property', '[0-9]+');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::put('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'processPayment'])->name('bookings.payment');

    // Message routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Review routes
    Route::post('/properties/{property}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Profile routes
    Route::put('/profile/update-additional', [ProfileController::class, 'updateAdditional'])->name('profile.update-additional');

    // // Admin routes
    // Route::middleware(['check.user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
    //     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    //     Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    //     Route::get('/properties', [AdminController::class, 'properties'])->name('admin.properties');
    //     Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    //     Route::get('/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
    //     Route::put('/verifications/{verification}/approve', [AdminController::class, 'approveVerification'])->name('admin.verifications.approve');
    //     Route::put('/verifications/{verification}/reject', [AdminController::class, 'rejectVerification'])->name('admin.verifications.reject');
    //     Route::put('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    // });


    // Admin routes
    Route::middleware(['check.user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/properties', [AdminController::class, 'properties'])->name('properties');
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
        Route::get('/verifications', [AdminController::class, 'verifications'])->name('verifications');
        Route::put('/verifications/{verification}/approve', [AdminController::class, 'approveVerification'])->name('verifications.approve');
        Route::put('/verifications/{verification}/reject', [AdminController::class, 'rejectVerification'])->name('verifications.reject');
        Route::put('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    });
});

// Public verification callback
Route::post('/verification/callback', [VerificationController::class, 'callback'])->name('verification.callback');

Route::get('/debug-user', function () {
    return auth()->check() ? auth()->user()->profile->user_type : 'guest';
});
