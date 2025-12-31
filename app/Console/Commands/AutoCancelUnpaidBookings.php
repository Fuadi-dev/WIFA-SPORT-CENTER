<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Automatically cancel pending_confirmation bookings less than 1 hour before booking time (except last minute bookings)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for pending bookings to auto-cancel...');
        
        $now = Carbon::now();
        $oneHourFromNow = $now->copy()->addHour();
        
        $cancelledCount = 0;
        
        // === CANCEL BOOKING CASH YANG MASIH PENDING_CONFIRMATION ===
        // Kriteria:
        // - Status pending_confirmation
        // - Payment method cash
        // - Bukan last minute booking (is_last_minute_booking = false)
        // - Kurang dari 1 jam sebelum waktu booking
        
        $pendingBookings = Booking::where('status', 'pending_confirmation')
            ->where('payment_method', 'cash')
            ->where('is_last_minute_booking', false)
            ->where(function ($query) use ($now) {
                // Booking date sudah lewat
                $query->where('booking_date', '<', $now->toDateString())
                    // Atau booking hari ini
                    ->orWhere('booking_date', $now->toDateString());
            })
            ->get();
        
        $this->info("📋 Found {$pendingBookings->count()} pending cash bookings to check");
        
        foreach ($pendingBookings as $booking) {
            // Parse booking start time
            $startTimeStr = $booking->start_time instanceof Carbon 
                ? $booking->start_time->format('H:i:s') 
                : $booking->start_time;
            
            $bookingStartDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $startTimeStr);
            
            // Cancel jika kurang dari 1 jam sebelum waktu booking atau sudah lewat
            if ($oneHourFromNow->greaterThanOrEqualTo($bookingStartDateTime)) {
                $oldStatus = $booking->status;
                $booking->status = 'cancelled';
                $booking->auto_cancelled_at = $now;
                $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                    . "Auto-cancelled: Booking tidak dikonfirmasi oleh admin hingga 1 jam sebelum waktu booking. " 
                    . "Waktu booking: {$bookingStartDateTime->format('Y-m-d H:i')}, Dibatalkan pada: {$now->format('Y-m-d H:i:s')}";
                $booking->save();
                
                $cancelledCount++;
                
                $this->line("❌ Cancelled: {$booking->booking_code} - {$booking->team_name}");
                $this->line("   Booking time: {$bookingStartDateTime->format('Y-m-d H:i')}");
                
                Log::info('Auto-cancelled booking (not confirmed by admin)', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'team_name' => $booking->team_name,
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'start_time' => $startTimeStr,
                    'is_last_minute_booking' => $booking->is_last_minute_booking,
                ]);
            } else {
                $minutesLeft = $now->diffInMinutes($bookingStartDateTime, false);
                $hoursLeft = floor($minutesLeft / 60);
                $minsLeft = $minutesLeft % 60;
                $this->line("⏳ Waiting: {$booking->booking_code} - {$hoursLeft}h {$minsLeft}m until cancellation deadline");
            }
        }
        
        // === CANCEL BOOKING MIDTRANS YANG BELUM DIBAYAR ===
        // Kriteria sama dengan cash:
        // - Status pending_payment
        // - Payment method midtrans
        // - Bukan last minute booking
        // - Kurang dari 1 jam sebelum waktu booking
        $pendingMidtransBookings = Booking::where('status', 'pending_payment')
            ->where('payment_method', 'midtrans')
            ->where('is_last_minute_booking', false)
            ->where(function ($query) use ($now) {
                $query->where('booking_date', '<', $now->toDateString())
                    ->orWhere('booking_date', $now->toDateString());
            })
            ->get();
        
        $this->info("💳 Found {$pendingMidtransBookings->count()} pending midtrans bookings to check");
        
        foreach ($pendingMidtransBookings as $booking) {
            $startTimeStr = $booking->start_time instanceof Carbon 
                ? $booking->start_time->format('H:i:s') 
                : $booking->start_time;
            
            $bookingStartDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $startTimeStr);
            
            // Cancel jika kurang dari 1 jam sebelum waktu booking atau sudah lewat
            if ($oneHourFromNow->greaterThanOrEqualTo($bookingStartDateTime)) {
                $oldStatus = $booking->status;
                $booking->status = 'cancelled';
                $booking->auto_cancelled_at = $now;
                $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') 
                    . "Auto-cancelled: Pembayaran Midtrans tidak diselesaikan hingga 1 jam sebelum waktu booking. " 
                    . "Waktu booking: {$bookingStartDateTime->format('Y-m-d H:i')}, Dibatalkan pada: {$now->format('Y-m-d H:i:s')}";
                $booking->save();
                
                $cancelledCount++;
                
                $this->line("❌ Cancelled (Midtrans): {$booking->booking_code} - {$booking->team_name}");
                $this->line("   Booking time: {$bookingStartDateTime->format('Y-m-d H:i')}");
                
                Log::info('Auto-cancelled booking (midtrans not paid)', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'team_name' => $booking->team_name,
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'start_time' => $startTimeStr,
                    'is_last_minute_booking' => $booking->is_last_minute_booking,
                ]);
            } else {
                $minutesLeft = $now->diffInMinutes($bookingStartDateTime, false);
                $hoursLeft = floor($minutesLeft / 60);
                $minsLeft = $minutesLeft % 60;
                $this->line("⏳ Waiting (Midtrans): {$booking->booking_code} - {$hoursLeft}h {$minsLeft}m until cancellation deadline");
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("Total bookings cancelled: {$cancelledCount}");
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
