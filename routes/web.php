<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Admin\BookingController as ManagementBookingController;

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
        //Route for Owner
        Route::middleware(['role:owner'])->prefix('owner')->group(function () {
            Route::get('/dashboard', [OwnerDashboardController::class, 'dashboard'])->name('owner.dashboard');
        });
        //Route for Admin
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            
            // Admin Booking Management
            Route::prefix('bookings')->name('admin.bookings.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
                Route::get('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
                Route::patch('/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('updateStatus');
                Route::delete('/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('destroy');
                Route::get('/export/data', [App\Http\Controllers\Admin\BookingController::class, 'export'])->name('export');
            });
            
            // Admin Event Management
            Route::prefix('events')->name('admin.events.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Admin\EventController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\EventController::class, 'store'])->name('store');
                Route::get('/courts-by-sport/{sport}', [App\Http\Controllers\Admin\EventController::class, 'getCourtsBySport'])->name('courts-by-sport');
                Route::get('/{event}', [App\Http\Controllers\Admin\EventController::class, 'show'])->name('show');
                Route::get('/{event}/edit', [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('edit');
                Route::patch('/{event}', [App\Http\Controllers\Admin\EventController::class, 'update'])->name('update');
                Route::delete('/{event}', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('destroy');
                Route::get('/{event}/registrations', [App\Http\Controllers\Admin\EventController::class, 'registrations'])->name('registrations');
                Route::patch('/{event}/registrations/{registration}/status', [App\Http\Controllers\Admin\EventController::class, 'updateRegistrationStatus'])->name('updateRegistrationStatus');
            });
            
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
        
        // User Event Routes
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [App\Http\Controllers\EventController::class, 'index'])->name('index');
            Route::get('/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('show');
            Route::get('/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('register');
            Route::post('/{event}/register', [App\Http\Controllers\EventController::class, 'storeRegistration'])->name('storeRegistration');
            Route::get('/my/registrations', [App\Http\Controllers\EventController::class, 'myRegistrations'])->name('myRegistrations');
            Route::patch('/registrations/{registration}/cancel', [App\Http\Controllers\EventController::class, 'cancelRegistration'])->name('cancelRegistration');
        });
    });
});