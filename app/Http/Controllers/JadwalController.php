<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Sport;
use App\Models\Event;
use App\Models\SportPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // Show schedules for Futsal, Volleyball, and Badminton courts
        $allowedSports = ['Futsal', 'Voli', 'Badminton'];
        $courts = Court::with('sport')
            ->whereHas('sport', function($query) use ($allowedSports) {
                $query->whereIn('name', $allowedSports);
            })
            ->active()
            ->orderBy('id')
            ->get();
        
        // Get selected date (default to today in Jakarta timezone)
        $selectedDate = $request->get('date', now()->setTimezone('Asia/Jakarta')->format('Y-m-d'));
        
        // If this is an AJAX request, return time slot data
        if ($request->ajax()) {
            $selectedCourtId = $request->get('court_id');
            $selectedCourt = Court::with('sport')->findOrFail($selectedCourtId);
            
            $timeSlots = $this->getTimeSlotsForDate($selectedCourt, $selectedDate);
            
            return response()->json([
                'success' => true,
                'timeSlots' => $timeSlots,
                'court' => $selectedCourt
            ]);
        }
        
        // Get selected court (default to first court)
        $selectedCourtId = $request->get('court', $courts->first()->id ?? 1);
        $selectedCourt = Court::with('sport')->findOrFail($selectedCourtId);
        
        // Get time slots for the selected date and court
        $timeSlots = $this->getTimeSlotsForDate($selectedCourt, $selectedDate);
        
        return view('users.jadwal.index', compact('courts', 'selectedCourt', 'timeSlots', 'selectedDate'));
    }
    
    private function getTimeSlotsForDate($court, $date)
    {
        $carbonDate = Carbon::parse($date);
        $isWeekend = SportPrice::isWeekend($carbonDate); // Jumat-Minggu = Weekend
        $timeSlots = [];
        
        // Check if there's an event on this date
        $event = $court->getEventForDate($date);
        
        // If there's an event, show all time slots as blocked with event info
        if ($event) {
            for ($hour = 8; $hour < 24; $hour++) {
                $startTime = sprintf('%02d:00', $hour);
                $endTime = sprintf('%02d:00', $hour + 1);
                
                $timeSlots[] = [
                    'time' => $startTime . ' - ' . $endTime,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'price' => 0,
                    'price_category' => 'event',
                    'price_label' => 'Event',
                    'is_available' => false,
                    'is_booked' => false,
                    'is_event' => true,
                    'event_info' => [
                        'title' => $event->title,
                        'event_code' => $event->event_code,
                        'status' => $event->status,
                        'sport_name' => $event->sport->name,
                        'court_name' => $event->court->name,
                        'description' => $event->description
                    ],
                    'booking_info' => null,
                    'date' => $date,
                    'court_id' => $court->id
                ];
            }
            return $timeSlots;
        }

        /**
         * Get time slots with pricing for a specific date
         * Operating hours: 08:00 - 24:00
         * Price categories: Morning (08-12), Afternoon (12-18), Evening (18-24)
         * Weekend multiplier: 1.5x for Saturday & Sunday
         */
        for ($hour = 8; $hour < 24; $hour++) {
            $startTime = sprintf('%02d:00', $hour);
            $endTime = sprintf('%02d:00', $hour + 1);
            
            // Get price based on time, day, and sport from database
            $priceInfo = $this->getPriceForTimeSlot($court->sport, $hour, $isWeekend);
            
            // Check if this time slot is available
            $isBooked = $this->isTimeSlotBooked($court->id, $date, $startTime, $endTime);
            $bookingInfo = $this->getBookingInfo($court->id, $date, $startTime, $endTime);
            
            $timeSlots[] = [
                'time' => $startTime . ' - ' . $endTime,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $priceInfo['price'],
                'price_category' => $priceInfo['category'],
                'price_label' => $priceInfo['label'],
                'is_available' => !$isBooked,
                'is_booked' => $isBooked,
                'is_event' => false,
                'event_info' => null,
                'booking_info' => $bookingInfo,
                'date' => $date,
                'court_id' => $court->id
            ];
        }
        
        return $timeSlots;
    }
    
    private function getPriceForTimeSlot($sport, $hour, $isWeekend = false)
    {
        // Determine time slot based on hour
        $timeSlot = '';
        $label = '';
        
        if ($hour >= 8 && $hour < 12) {
            $timeSlot = SportPrice::SLOT_MORNING;
            $label = 'Pagi';
        } elseif ($hour >= 12 && $hour < 18) {
            $timeSlot = SportPrice::SLOT_AFTERNOON;
            $label = 'Siang';
        } else {
            $timeSlot = SportPrice::SLOT_EVENING;
            $label = 'Malam';
        }
        
        // Get price from database
        $sportPrice = SportPrice::where('sport_id', $sport->id)
            ->where('time_slot', $timeSlot)
            ->where('is_active', true)
            ->first();
        
        $price = 0;
        if ($sportPrice) {
            $price = $isWeekend ? $sportPrice->weekend_price : $sportPrice->weekday_price;
        } else {
            // Fallback to sport's default price_per_hour if no specific pricing exists
            $price = $sport->price_per_hour ?? 0;
        }
        
        // Add weekend label
        if ($isWeekend) {
            $label .= ' (Weekend)';
        }
        
        return [
            'price' => (float) $price,
            'category' => $timeSlot,
            'label' => $label
        ];
    }
    
    private function isTimeSlotBooked($courtId, $date, $startTime, $endTime)
    {
        $court = Court::find($courtId);
        if (!$court) {
            return false;
        }
        
        // Use the Court model's availability check which includes conflict logic
        return !$court->isAvailable($date, $startTime, $endTime);
    }
    
    private function getBookingInfo($courtId, $date, $startTime, $endTime)
    {
        $court = Court::find($courtId);
        if (!$court) {
            return null;
        }
        
        // Get all conflicting courts for this court
        $conflictingCourts = $court->getConflictingCourts();
        
        // Find booking in any of the conflicting courts
        $booking = Booking::whereIn('court_id', $conflictingCourts)
            ->where('booking_date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->whereIn('status', ['confirmed', 'paid', 'pending_payment'])
            ->with(['user', 'court.sport'])
            ->first();
            
        if ($booking) {
            return [
                'team_name' => $booking->team_name,
                'user_name' => $booking->user->name ?? 'N/A',
                'notes' => $booking->notes,
                'status' => $booking->status,
                'total_amount' => $booking->total_amount,
                'court_name' => $booking->court->name ?? 'N/A',
                'sport_name' => $booking->court->sport->name ?? 'N/A'
            ];
        }
        
        return null;
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
    
    private function formatPrice($price)
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
