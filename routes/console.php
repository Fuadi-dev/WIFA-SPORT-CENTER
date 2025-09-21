<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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
