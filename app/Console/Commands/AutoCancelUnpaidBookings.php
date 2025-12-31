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
    protected $description = 'Automatically cancel unpaid/unconfirmed bookings (H-1 for future bookings, at start time for today bookings)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for unpaid/unconfirmed bookings...');
        
        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay()->startOfDay(); // Besok jam 00:00:00
        
        // === CANCEL BOOKING BESOK (H-1) ===
        // Get all bookings with pending payment or pending confirmation status
        // where booking date is tomorrow (H-1)
        $tomorrowBookings = Booking::whereIn('status', ['pending_payment', 'pending_confirmation'])
            ->whereDate('booking_date', $tomorrow->toDateString())
            ->get();
        
        $this->info("📅 Found {$tomorrowBookings->count()} pending bookings for tomorrow (H-1)");
        
        $cancelledCount = 0;
        
        foreach ($tomorrowBookings as $booking) {
            $oldStatus = $booking->status;
            $booking->status = 'cancelled';
            $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                . "Auto-cancelled: {$oldStatus} tidak dikonfirmasi/dibayar hingga H-1 (1 hari sebelum booking). Dibatalkan pada: " . $now->format('Y-m-d H:i:s');
            $booking->save();
            
            $cancelledCount++;
            
            $this->line("❌ Cancelled: {$booking->booking_code} - {$booking->team_name} (Status: {$oldStatus})");
            $this->line("   Booking date: {$booking->booking_date->format('Y-m-d')} | Cancelled at: {$now->format('Y-m-d H:i')}");
        }
        
        // === CANCEL BOOKING HARI INI (H-0) - DEADLINE KETIKA WAKTU MULAI SUDAH LEWAT ===
        $today = $now->copy()->startOfDay();
        
        $todayBookings = Booking::whereIn('status', ['pending_payment', 'pending_confirmation'])
            ->whereDate('booking_date', $today->toDateString())
            ->get();
        
        $this->info("⏰ Found {$todayBookings->count()} pending bookings for today (H-0)");
        
        foreach ($todayBookings as $booking) {
            // Parse booking start time
            $startTimeStr = $booking->start_time instanceof Carbon 
                ? $booking->start_time->format('H:i:s') 
                : $booking->start_time;
            
            $bookingStartDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $startTimeStr);
            
            // Cancel jika waktu mulai booking sudah lewat
            if ($now->greaterThanOrEqualTo($bookingStartDateTime)) {
                $oldStatus = $booking->status;
                $booking->status = 'cancelled';
                $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                    . "Auto-cancelled: {$oldStatus} booking hari ini tidak dikonfirmasi/dibayar hingga waktu mulai booking. " 
                    . "Waktu mulai: {$bookingStartDateTime->format('H:i')}, Dibatalkan pada: {$now->format('Y-m-d H:i:s')}";
                $booking->save();
                
                $cancelledCount++;
                
                $this->line("❌ Cancelled: {$booking->booking_code} - {$booking->team_name} (Status: {$oldStatus})");
                $this->line("   Booking start: {$bookingStartDateTime->format('Y-m-d H:i')} (passed)");
            } else {
                $minutesLeft = $now->diffInMinutes($bookingStartDateTime, false);
                $this->line("⏳ Waiting: {$booking->booking_code} - {$minutesLeft} minutes until start time");
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("Total bookings cancelled: {$cancelledCount}");
        $this->info("  - H-1 cancellations: " . $tomorrowBookings->count());
        $this->info("  - H-0 cancellations: " . ($cancelledCount - $tomorrowBookings->count()));
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
