<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule event status updates
Schedule::command('events:update-statuses')
    ->dailyAt('01:00')
    ->description('Auto-update event statuses based on dates')
    ->appendOutputTo(storage_path('logs/event-status-updates.log'));

// Additional schedule for more frequent checks during event days
Schedule::command('events:update-statuses')
    ->hourly()
    ->between('08:00', '23:00')
    ->description('Hourly event status check during active hours')
    ->appendOutputTo(storage_path('logs/event-status-updates.log'));

// Cleanup soft deleted records older than 30 days
Schedule::command('cleanup:soft-deleted --days=30')
    ->dailyAt('02:00')
    ->description('Permanently delete soft deleted records older than 30 days')
    ->appendOutputTo(storage_path('logs/cleanup-soft-deleted.log'));

// Auto-cancel unpaid/unconfirmed bookings 15 minutes before start time
Schedule::command('bookings:auto-cancel')
    ->everyFiveMinutes()
    ->description('Auto-cancel unpaid/unconfirmed bookings 15 minutes before start time')
    ->appendOutputTo(storage_path('logs/auto-cancel-bookings.log'));

// Auto-complete confirmed bookings after end time has passed
Schedule::command('bookings:auto-complete')
    ->everyTenMinutes()
    ->description('Auto-complete confirmed bookings after end time')
    ->appendOutputTo(storage_path('logs/auto-complete-bookings.log'));
