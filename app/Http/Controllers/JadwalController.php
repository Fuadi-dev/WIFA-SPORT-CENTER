<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Sport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // Get all courts
        $courts = Court::with('sport')->active()->orderBy('id')->get();
        
        // Get selected court (default to first court)
        $selectedCourtId = $request->get('court', $courts->first()->id ?? 1);
        $selectedCourt = Court::with('sport')->findOrFail($selectedCourtId);
        
        // Get date filter (default to today)
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $startDate = Carbon::parse($selectedDate);
        $endDate = $startDate->copy()->addDays(6); // Show 7 days
        
        // Generate time slots for the selected court
        $schedules = $this->generateScheduleData($selectedCourt, $startDate, $endDate);
        
        return view('jadwal.index', compact('courts', 'selectedCourt', 'schedules', 'selectedDate'));
    }
    
    private function generateScheduleData($court, $startDate, $endDate)
    {
        $schedules = [];
        $operatingHours = range(6, 23); // 06:00 to 23:00
        
        // Get existing bookings for this court in the date range
        $existingBookings = Booking::where('court_id', $court->id)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->whereIn('status', ['confirmed', 'paid', 'pending_payment'])
            ->with('user')
            ->get();
        
        // Debug: Log the bookings found
        Log::info('Bookings found for court ' . $court->id, [
            'count' => $existingBookings->count(),
            'bookings' => $existingBookings->map(function($b) {
                return [
                    'id' => $b->id,
                    'date' => $b->booking_date,
                    'start' => $b->start_time,
                    'end' => $b->end_time,
                    'team' => $b->team_name
                ];
            })
        ]);
        
        // Generate schedule for each day
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            
            // Filter bookings for this specific date
            $dayBookings = $existingBookings->filter(function($booking) use ($dateStr) {
                // Handle different date formats
                $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                return $bookingDate === $dateStr;
            });
            
            foreach ($operatingHours as $hour) {
                $startTime = sprintf('%02d:00', $hour);
                $endTime = sprintf('%02d:00', $hour + 1);
                
                // Check if this slot is booked
                $booking = $dayBookings->first(function($booking) use ($startTime, $endTime) {
                    // Extract time from datetime fields
                    $bookingStartTime = Carbon::parse($booking->start_time)->format('H:i');
                    $bookingEndTime = Carbon::parse($booking->end_time)->format('H:i');
                    
                    // Check if this hour slot overlaps with the booking
                    return ($bookingStartTime <= $startTime && $bookingEndTime > $startTime) ||
                           ($bookingStartTime < $endTime && $bookingEndTime >= $endTime) ||
                           ($bookingStartTime >= $startTime && $bookingEndTime <= $endTime);
                });
                
                // Determine status
                $status = 'available';
                $teamName = null;
                $bookingDetails = null;
                
                if ($booking) {
                    $status = 'booked';
                    $teamName = $booking->team_name;
                    $bookingDetails = $booking;
                }
                
                // Check if it's a holiday (simplified - you can enhance this)
                $isLibur = $this->isHoliday($date, $startTime);
                if ($isLibur) {
                    $status = 'libur';
                    $teamName = 'LIBUR';
                }
                
                $schedules[] = (object) [
                    'date' => $date->copy(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'team_name' => $teamName,
                    'status' => $status,
                    'is_today' => $date->isToday(),
                    'booking_details' => $bookingDetails,
                    'price_category' => $this->getPriceCategoryByHour($hour)
                ];
            }
        }
        
        return collect($schedules);
    }
    
    private function isHoliday($date, $time)
    {
        // Simple holiday logic - you can enhance this
        // For now, let's make some random holidays for demo
        $holidays = [
            '2025-08-31', // Example holiday
            '2025-09-01', // Example holiday
        ];
        
        return in_array($date->format('Y-m-d'), $holidays);
    }
    
    private function getPriceCategoryByHour($hour)
    {
        if ($hour >= 6 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 18) {
            return 'afternoon';
        } else {
            return 'evening';
        }
    }
}
