<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan otomatis booking cash yang masih pending_confirmation kurang dari 1 jam sebelum waktu booking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for pending bookings to auto-cancel...');
        
        $bookingsToCancel = Booking::eligibleForAutoCancel()->get();
        
        if ($bookingsToCancel->isEmpty()) {
            $this->info('No bookings to cancel.');
            return Command::SUCCESS;
        }
        
        $cancelledCount = 0;
        
        foreach ($bookingsToCancel as $booking) {
            try {
                $booking->autoCancel();
                $cancelledCount++;
                
                $this->line("Cancelled booking: {$booking->booking_code} - {$booking->team_name} ({$booking->booking_date->format('Y-m-d')} {$booking->start_time->format('H:i')})");
                
                Log::info('Auto-cancelled booking', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'team_name' => $booking->team_name,
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'start_time' => $booking->start_time->format('H:i'),
                    'is_last_minute_booking' => $booking->is_last_minute_booking,
                ]);
                
            } catch (\Exception $e) {
                $this->error("Failed to cancel booking {$booking->booking_code}: {$e->getMessage()}");
                
                Log::error('Failed to auto-cancel booking', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info("Successfully cancelled {$cancelledCount} booking(s).");
        
        return Command::SUCCESS;
    }
}
