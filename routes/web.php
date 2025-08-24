<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebugAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/',[MainController::class, 'index'])->name('home');
Route::get('/test-oauth', function() {
    return view('test-oauth');
})->name('test.oauth');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postRegister'])->name('register.post');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// OTP Routes
Route::get('/otp/verify/{id}', [AuthController::class, 'showOtpForm'])->name('otp.verify');
Route::post('/otp/verify/{id}', [AuthController::class, 'verifyOtp'])->name('otp.verify.post');
Route::post('/otp/resend/{id}', [AuthController::class, 'resendOtp'])->name('otp.resend');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    });
    Route::get('booking', [BookingController::class, 'olahraga'])->name('booking.index');
});