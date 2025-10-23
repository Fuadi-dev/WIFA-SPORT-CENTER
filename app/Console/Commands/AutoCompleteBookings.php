<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCompleteBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete confirmed bookings after end time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for bookings to complete...');
        
        $now = Carbon::now();
        
        // Get all confirmed bookings
        $bookings = Booking::where('status', 'confirmed')
            ->get();
        
        if ($bookings->isEmpty()) {
            $this->info('✅ No confirmed bookings found');
            return Command::SUCCESS;
        }
        
        $completedCount = 0;
        
        foreach ($bookings as $booking) {
            // Parse booking end datetime properly
            // end_time is cast as datetime:H:i, so we need to extract just the time
            $endTimeStr = $booking->end_time instanceof Carbon 
                ? $booking->end_time->format('H:i:s') 
                : $booking->end_time;
            
            $bookingEndDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $endTimeStr);
            
            // Complete if current time has passed the end time
            if ($now->greaterThan($bookingEndDateTime)) {
                $booking->status = 'completed';
                $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                    . "Auto-completed: Booking selesai pada " . $bookingEndDateTime->format('Y-m-d H:i:s') 
                    . ". Status diubah ke completed pada: " . $now->format('Y-m-d H:i:s');
                $booking->save();
                
                $completedCount++;
                
                $this->line("✅ Completed: {$booking->booking_code} - {$booking->team_name}");
                $this->line("   End time: {$bookingEndDateTime->format('Y-m-d H:i')} | Completed at: {$now->format('Y-m-d H:i')}");
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("Total bookings completed: {$completedCount}");
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
