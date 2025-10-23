<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookingController as ManagementBookingController;
use App\Http\Controllers\Admin\EventController as ManagementEventController;
use App\Http\Controllers\Admin\UserController as ManagementUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\AutoPromoController;
use App\Http\Controllers\EventController;

Route::get('/',[MainController::class, 'index'])->name('home');

// Public routes
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postRegister'])->name('register.post');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// WhatsApp Setup Routes (after OAuth)
Route::middleware(['auth'])->group(function () {
    Route::get('/whatsapp-setup', [AuthController::class, 'whatsappSetup'])->name('whatsapp.setup');
    Route::post('/whatsapp-setup', [AuthController::class, 'whatsappSetupPost'])->name('whatsapp.setup.post');
});

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
        //Route for Admin
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            
            // Admin Booking Management
            Route::prefix('bookings')->name('admin.bookings.')->group(function () {
                Route::get('/', [ManagementBookingController::class, 'index'])->name('index');
                Route::post('/manual', [ManagementBookingController::class, 'storeManualBooking'])->name('manual.store');
                Route::get('/users', [ManagementBookingController::class, 'getUsers'])->name('users');
                Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');
                Route::get('/courts-by-sport/{sport}', [ManagementBookingController::class, 'getCourtsBySport'])->name('courts-by-sport');
                Route::get('/{booking:slug}', [ManagementBookingController::class, 'show'])->name('show');
                Route::patch('/{booking:slug}/confirm', [ManagementBookingController::class, 'confirmBooking'])->name('confirm');
                Route::patch('/{booking:slug}/status', [ManagementBookingController::class, 'updateStatus'])->name('updateStatus');
                Route::delete('/{booking:slug}', [ManagementBookingController::class, 'destroy'])->name('destroy');
                Route::get('/export/data', [ManagementBookingController::class, 'export'])->name('export');
            });
            
            // Admin Event Management
            Route::prefix('events')->name('admin.events.')->group(function () {
                Route::get('/', [ManagementEventController::class, 'index'])->name('index');
                Route::get('/create', [ManagementEventController::class, 'create'])->name('create');
                Route::post('/', [ManagementEventController::class, 'store'])->name('store');
                Route::get('/courts-by-sport/{sport}', [ManagementEventController::class, 'getCourtsBySport'])->name('courts-by-sport');
                Route::get('/{event:id}', [ManagementEventController::class, 'show'])->name('show');
                Route::get('/{event:id}/edit', [ManagementEventController::class, 'edit'])->name('edit');
                Route::get('/{event:id}/detail', [ManagementEventController::class, 'getEventDetail'])->name('detail');
                Route::get('/{event:id}/registrations-list', [ManagementEventController::class, 'getEventRegistrations'])->name('registrations-list');
                Route::patch('/{event:id}', [ManagementEventController::class, 'update'])->name('update');
                Route::delete('/{event:id}', [ManagementEventController::class, 'destroy'])->name('destroy');
                Route::get('/{event:id}/registrations', [ManagementEventController::class, 'registrations'])->name('registrations');
                Route::patch('/{event:id}/registrations/{registration}/status', [ManagementEventController::class, 'updateRegistrationStatus'])->name('updateRegistrationStatus');
            });
            
            // Admin User Management
            Route::prefix('users')->name('admin.users.')->group(function () {
                Route::get('/', [ManagementUserController::class, 'index'])->name('index');
                Route::get('/create', [ManagementUserController::class, 'create'])->name('create');
                Route::post('/', [ManagementUserController::class, 'store'])->name('store');
                Route::get('/{user}', [ManagementUserController::class, 'show'])->name('show');
                Route::get('/{user}/detail', [ManagementUserController::class, 'getUserDetail'])->name('detail');
                Route::delete('/{user}', [ManagementUserController::class, 'destroy'])->name('destroy');
                Route::patch('/{user}/toggle-status', [ManagementUserController::class, 'toggleStatus'])->name('toggleStatus');
            });
            
            // Admin Reports
            Route::prefix('reports')->name('admin.reports.')->group(function () {
                Route::get('/bookings', [ReportController::class, 'bookings'])->name('bookings');
                Route::get('/bookings/export', [ReportController::class, 'exportBookings'])->name('bookings.export');
                Route::get('/bookings/by-month', [ReportController::class, 'bookingsByMonth'])->name('bookings.by-month');
                Route::get('/bookings/by-sport', [ReportController::class, 'bookingsBySport'])->name('bookings.by-sport');
                Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
                Route::get('/financial/export', [ReportController::class, 'exportFinancial'])->name('financial.export');
            });
            
            // Admin Promo Code Management
            Route::prefix('promo/codes')->name('admin.promo.codes.')->group(function () {
                Route::get('/', [PromoCodeController::class, 'index'])->name('index');
                Route::post('/', [PromoCodeController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [PromoCodeController::class, 'edit'])->name('edit');
                Route::patch('/{id}', [PromoCodeController::class, 'update'])->name('update');
                Route::delete('/{id}', [PromoCodeController::class, 'destroy'])->name('destroy');
                Route::patch('/{id}/toggle-status', [PromoCodeController::class, 'toggleStatus'])->name('toggleStatus');
            });
            
            // Admin Auto Promo Management
            Route::prefix('promo/auto')->name('admin.promo.auto.')->group(function () {
                Route::get('/', [AutoPromoController::class, 'index'])->name('index');
                Route::post('/', [AutoPromoController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [AutoPromoController::class, 'edit'])->name('edit');
                Route::patch('/{id}', [AutoPromoController::class, 'update'])->name('update');
                Route::delete('/{id}', [AutoPromoController::class, 'destroy'])->name('destroy');
                Route::patch('/{id}/toggle-status', [AutoPromoController::class, 'toggleStatus'])->name('toggleStatus');
            });
            
        });
        
        // Booking Routes
        Route::prefix('booking')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('booking.index');
            Route::get('/sport/{sport:slug}', [BookingController::class, 'showCourt'])->name('booking.court');
            Route::get('/schedule/{sport:slug}/{court:slug}', [BookingController::class, 'showSchedule'])->name('booking.schedule');
            Route::get('/form', [BookingController::class, 'showBookingForm'])->name('booking.form');
            Route::post('/store', [BookingController::class, 'store'])->name('booking.store');
            Route::get('/confirmation/{booking:slug}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
            Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
            Route::post('/get-price', [BookingController::class, 'getPriceForTimeRange'])->name('booking.get-price');
            
            // Midtrans payment routes
            Route::get('/payment-status/{booking:slug}', [BookingController::class, 'checkPaymentStatus'])->name('booking.payment-status');
            
            // Legacy route
            Route::get('/olahraga', [BookingController::class, 'olahraga']);
        });
        
        // Promo API Routes
        Route::post('/api/validate-promo-code', [BookingController::class, 'validatePromoCode'])->name('api.validate-promo-code');
        
        // User bookings
        Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');
        
        // User Event Routes (requires auth)
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/{event:slug}/register', [EventController::class, 'register'])->name('register');
            Route::post('/{event:slug}/register', [EventController::class, 'storeRegistration'])->name('storeRegistration');
            Route::get('/my/registrations', [EventController::class, 'myRegistrations'])->name('myRegistrations');
            Route::patch('/registrations/{registration}/cancel', [EventController::class, 'cancelRegistration'])->name('cancelRegistration');
        });
    });
});