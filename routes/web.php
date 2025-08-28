<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebugAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/',[MainController::class, 'index'])->name('home');

// Public routes
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');

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

// Midtrans webhook (outside auth middleware)
Route::post('/midtrans/notification', [BookingController::class, 'midtransNotification'])->name('midtrans.notification');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['status:active'])->group(function (){
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        });
        
        // Booking Routes
        Route::prefix('booking')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('booking.index');
            Route::get('/sport/{sport}', [BookingController::class, 'showCourt'])->name('booking.court');
            Route::get('/schedule/{sport}/{court}', [BookingController::class, 'showSchedule'])->name('booking.schedule');
            Route::get('/form', [BookingController::class, 'showBookingForm'])->name('booking.form');
            Route::post('/store', [BookingController::class, 'store'])->name('booking.store');
            Route::get('/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
            Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
            Route::post('/get-price', [BookingController::class, 'getPriceForTimeRange'])->name('booking.get-price');
            
            // Midtrans payment routes
            Route::get('/payment-status/{booking}', [BookingController::class, 'checkPaymentStatus'])->name('booking.payment-status');
            
            // Legacy route
            Route::get('/olahraga', [BookingController::class, 'olahraga']);
        });
        
        // User bookings
        Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');
    });
});