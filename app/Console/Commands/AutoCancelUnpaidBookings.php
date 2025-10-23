<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCancelUnpaidBookings extends Command
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
    protected $description = 'Automatically cancel unpaid/unconfirmed bookings 15 minutes before start time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for unpaid/unconfirmed bookings...');
        
        $now = Carbon::now();
        $fifteenMinutesLater = $now->copy()->addMinutes(15);
        
        // Get all bookings with pending payment or pending confirmation status
        $bookings = Booking::whereIn('status', ['pending_payment', 'pending_confirmation'])
            ->get();
        
        if ($bookings->isEmpty()) {
            $this->info('✅ No pending bookings found');
            return Command::SUCCESS;
        }
        
        $cancelledCount = 0;
        
        foreach ($bookings as $booking) {
            // Parse booking datetime properly
            // start_time is cast as datetime:H:i, so we need to extract just the time
            $startTimeStr = $booking->start_time instanceof Carbon 
                ? $booking->start_time->format('H:i:s') 
                : $booking->start_time;
            
            $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $startTimeStr);
            $timeDiff = $now->diffInMinutes($bookingDateTime, false);
            
            // Cancel if 15 minutes or less before start time (or already passed)
            if ($timeDiff <= 15) {
                $oldStatus = $booking->status;
                $booking->status = 'cancelled';
                $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                    . "Auto-cancelled: {$oldStatus} tidak dikonfirmasi/dibayar 15 menit sebelum waktu mulai. Dibatalkan pada: " . $now->format('Y-m-d H:i:s');
                $booking->save();
                
                $cancelledCount++;
                
                $this->line("❌ Cancelled: {$booking->booking_code} - {$booking->team_name} (Status: {$oldStatus})");
                $this->line("   Booking time: {$bookingDateTime->format('Y-m-d H:i')} | Time diff: {$timeDiff} minutes");
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("Total bookings cancelled: {$cancelledCount}");
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
