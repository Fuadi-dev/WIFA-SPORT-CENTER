<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;
use App\Services\WhatsAppService;

class BookingController extends Controller
{

    // Step 1: Choose Sport
    public function index()
    {
        $sports = Sport::active()->get();
        return view('users.booking.choose-sport', compact('sports'));
    }

    // Step 2: Choose Court (for badminton) and Date/Time
    public function showCourt(Sport $sport)
    {
        $courts = $sport->courts()->active()->get();
        
        // For sports other than badminton, redirect directly to schedule
        if ($sport->name !== 'Badminton') {
            $court = $courts->first();
            return redirect()->route('booking.schedule', ['sport' => $sport->id, 'court' => $court->id]);
        }   
        return view('users.booking.choose-court', compact('sport', 'courts'));
    }

    // Step 3: Choose Schedule
    public function showSchedule(Request $request, $sportId, $courtId)
    {
        $sport = Sport::findOrFail($sportId);
        $court = Court::findOrFail($courtId);
        
        // Get the next 30 days
        $dates = collect();
        for ($i = 0; $i < 30; $i++) {
            $dates->push(Carbon::now()->addDays($i));
        }
        
        // Operating hours (8 AM to 12 AM - 24:00) based on price list
        $timeSlots = [];
        for ($hour = 8; $hour < 24; $hour++) {
            // Get both weekday and weekend prices
            $weekdayPrice = $this->getPriceByHour($hour, $sport, Carbon::parse('2025-09-01')); // Monday = weekday
            $weekendPrice = $this->getPriceByHour($hour, $sport, Carbon::parse('2025-08-29')); // Friday = weekend
            
            $timeSlots[] = [
                'start' => sprintf('%02d:00', $hour),
                'end' => sprintf('%02d:00', $hour + 1),
                'display' => sprintf('%02d:00-%02d:00', $hour, $hour + 1),
                'price' => $weekdayPrice, // Default to weekday price for display
                'weekday_price' => $weekdayPrice,
                'weekend_price' => $weekendPrice,
                'price_category' => $this->getPriceCategoryByHour($hour)
            ];
        }
        
        return view('users.booking.choose-schedule', compact('sport', 'court', 'dates', 'timeSlots'));
    }

    // Get price based on hour and sport
    private function getPriceByHour($hour, $sport = null, $date = null)
    {
        // Determine if it's weekend (Friday, Saturday, Sunday)
        $isWeekend = false;
        if ($date) {
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
        }
        
        // Get sport name if sport object is provided
        $sportName = $sport ? strtolower($sport->name) : 'futsal';
        
        // Price based on time slots and sport
        $price = 0;
        if ($hour >= 8 && $hour < 12) {
            // 08:00 - 12:00
            switch ($sportName) {
                case 'futsal':
                    $price = $isWeekend ? 65000 : 60000;
                    break;
                case 'badminton':
                    $price = $isWeekend ? 35000 : 30000;
                    break;
                case 'voli':
                case 'volleyball':
                    $price = $isWeekend ? 55000 : 50000;
                    break;
                default:
                    $price = $isWeekend ? 65000 : 60000; // Default to futsal prices
            }
        } elseif ($hour >= 12 && $hour < 18) {
            // 12:00 - 18:00
            switch ($sportName) {
                case 'futsal':
                    $price = $isWeekend ? 85000 : 80000;
                    break;
                case 'badminton':
                    $price = $isWeekend ? 40000 : 35000;
                    break;
                case 'voli':
                case 'volleyball':
                    $price = $isWeekend ? 65000 : 60000;
                    break;
                default:
                    $price = $isWeekend ? 85000 : 80000; // Default to futsal prices
            }
        } elseif ($hour >= 18 && $hour < 24) {
            // 18:00 - 00:00
            switch ($sportName) {
                case 'futsal':
                    $price = $isWeekend ? 105000 : 100000;
                    break;
                case 'badminton':
                    $price = $isWeekend ? 45000 : 40000;
                    break;
                case 'voli':
                case 'volleyball':
                    $price = $isWeekend ? 75000 : 70000;
                    break;
                default:
                    $price = $isWeekend ? 105000 : 100000; // Default to futsal prices
            }
        }
        
        return $price;
    }

    // Get price category name
    private function getPriceCategoryByHour($hour)
    {
        if ($hour >= 8 && $hour < 12) {
            return 'morning'; // Pagi
        } elseif ($hour >= 12 && $hour < 18) {
            return 'afternoon'; // Siang
        } else {
            return 'evening'; // Malam
        }
    }

    // Calculate total price for time range
    private function calculateTotalPrice($startTime, $endTime, $sport = null, $date = null)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $totalPrice = 0;
        
        $currentHour = $start->hour;
        while ($currentHour < $end->hour) {
            $totalPrice += $this->getPriceByHour($currentHour, $sport, $date);
            $currentHour++;
        }
        
        return $totalPrice;
    }

        // Check availability (AJAX)
    public function checkAvailability(Request $request)
    {
        $courtId = $request->court_id;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        $court = Court::findOrFail($courtId);
        
        // Check for events first - this should block all booking
        $event = $court->getEventForDate($date);
        if ($event) {
            return response()->json([
                'available' => false,
                'reason' => 'event',
                'message' => 'Event: ' . $event->title,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'event_code' => $event->event_code,
                    'status' => $event->status,
                    'sport_name' => $event->sport->name,
                    'court_name' => $event->court->name
                ]
            ]);
        }
        
        $isAvailable = $court->isAvailable($date, $startTime, $endTime);
        
        return response()->json([
            'available' => $isAvailable,
            'reason' => $isAvailable ? 'available' : 'booking_conflict',
            'message' => $isAvailable ? 'Tersedia' : 'Sudah Dibooking'
        ]);
    }

    // Get price for time range (AJAX)
    public function getPriceForTimeRange(Request $request)
    {
        $startTime = $request->start_time;
        $duration = $request->duration;
        $sportId = $request->sport_id;
        $date = $request->date;
        
        $sport = $sportId ? Sport::find($sportId) : null;
        
        // Debug weekend detection
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
        Log::info('Price calculation debug', [
            'date' => $date,
            'dayOfWeek' => $dayOfWeek,
            'dayName' => Carbon::parse($date)->format('l'),
            'isWeekend' => $isWeekend,
            'sport' => $sport ? $sport->name : 'null',
            'startTime' => $startTime,
            'duration' => $duration
        ]);
        
        $start = Carbon::parse($startTime);
        $end = $start->copy()->addHours($duration);
        $endTime = $end->format('H:i');
        
        $totalPrice = $this->calculateTotalPrice($startTime, $endTime, $sport, $date);
        
        // Get price breakdown by hour
        $priceBreakdown = [];
        $currentHour = $start->hour;
        for ($i = 0; $i < $duration; $i++) {
            $hourPrice = $this->getPriceByHour($currentHour, $sport, $date);
            $category = $this->getPriceCategoryByHour($currentHour);
            $priceBreakdown[] = [
                'hour' => sprintf('%02d:00-%02d:00', $currentHour, $currentHour + 1),
                'price' => $hourPrice,
                'category' => $category
            ];
            $currentHour++;
        }
        
        Log::info('Price calculation result', [
            'totalPrice' => $totalPrice,
            'priceBreakdown' => $priceBreakdown
        ]);
        
        return response()->json([
            'total_price' => $totalPrice,
            'end_time' => $endTime,
            'price_breakdown' => $priceBreakdown
        ]);
    }

        // Step 4: Fill Booking Details
    public function showBookingForm(Request $request)
    {
        $sport = Sport::findOrFail($request->sport_id);
        $court = Court::findOrFail($request->court_id);
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        // Validate that all required parameters are present
        if (!$date || !$startTime || !$endTime) {
            return redirect()->route('booking.schedule', ['sport' => $sport->id, 'court' => $court->id])
                           ->with('error', 'Data booking tidak lengkap. Silakan pilih ulang jadwal.');
        }
        
        // Check for events first
        $event = $court->getEventForDate($date);
        if ($event) {
            return redirect()->route('booking.schedule', ['sport' => $sport->id, 'court' => $court->id])
                           ->with('error', "Lapangan tidak tersedia pada tanggal tersebut karena ada event: {$event->title} ({$event->event_code})");
        }
        
        // IMPORTANT: Double check availability for the entire time range before showing form
        if (!$court->isAvailable($date, $startTime, $endTime)) {
            return redirect()->route('booking.schedule', ['sport' => $sport->id, 'court' => $court->id])
                           ->with('error', 'Slot waktu sudah tidak tersedia. Silakan pilih waktu lain.');
        }
        
        // Calculate duration and price with time-based pricing
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $duration = $start->diffInHours($end);
        $totalPrice = $this->calculateTotalPrice($startTime, $endTime, $sport, $date);
        
        return view('users.booking.booking-form', compact('sport', 'court', 'date', 'startTime', 'endTime', 'duration', 'totalPrice'));
    }

    // Step 5: Process Booking
    public function store(Request $request)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'team_name' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,midtrans',
            'notes' => 'nullable|string',
            'agree_terms' => 'required|accepted'
        ]);

        // Double check availability
        $court = Court::findOrFail($request->court_id);
        
        // Check for events first
        $event = $court->getEventForDate($request->date);
        if ($event) {
            return back()->withErrors(['error' => "Lapangan tidak tersedia pada tanggal tersebut karena ada event: {$event->title} ({$event->event_code})"]);
        }
        
        if (!$court->isAvailable($request->date, $request->start_time, $request->end_time)) {
            return back()->withErrors(['error' => 'Slot waktu sudah tidak tersedia.']);
        }

        // Calculate price with time-based pricing
        $sport = Sport::findOrFail($request->sport_id);
        $totalPrice = $this->calculateTotalPrice($request->start_time, $request->end_time, $sport, $request->date);

        // Determine initial status based on payment method
        $initialStatus = $request->payment_method === 'cash' ? 'confirmed' : 'pending_payment';
        $confirmedAt = $request->payment_method === 'cash' ? now() : null;

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'sport_id' => $request->sport_id,
            'court_id' => $request->court_id,
            'booking_date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'team_name' => $request->team_name,
            'notes' => $request->notes,
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
            'status' => $initialStatus,
            'confirmed_at' => $confirmedAt
        ]);

        // Handle Midtrans payment
        if ($request->payment_method === 'midtrans') {
            return $this->handleMidtransPayment($booking);
        }

        // Handle cash payment - generate WhatsApp URL
        $whatsappService = new WhatsAppService();
        $whatsappUrl = $whatsappService->generateBookingConfirmationUrl($booking);

        return redirect()->route('booking.confirmation', $booking->id)
                        ->with('success', 'Pemesanan berhasil dibuat!')
                        ->with('whatsapp_url', $whatsappUrl);
    }

    // Handle Midtrans Payment
    private function handleMidtransPayment(Booking $booking)
    {
        try {
            Log::info('Starting Midtrans payment processing', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'total_price' => $booking->total_price
            ]);
            
            $midtransService = new MidtransService();
            $snapToken = $midtransService->createTransaction($booking);
            
            Log::info('Snap token created successfully', [
                'booking_id' => $booking->id,
                'snap_token' => $snapToken
            ]);
            
            // Save the snap token to booking (redundant but safe)
            $booking->update(['midtrans_snap_token' => $snapToken]);
            
            return redirect()->route('booking.confirmation', $booking->id)
                            ->with('success', 'Pemesanan berhasil dibuat! Silakan lakukan pembayaran.')
                            ->with('snap_token', $snapToken);
        } catch (\Exception $e) {
            Log::error('Midtrans payment processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If Midtrans fails, update booking status to failed
            $booking->update(['status' => 'cancelled']);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan dalam memproses pembayaran: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    // Step 6: Pemesanan Confirmation
    public function confirmation(Booking $booking)
    {
        // Ensure user can only see their own pemesanan
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('users.booking.confirmation', compact('booking'));
    }

    // User's bookings
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
                          ->with(['sport', 'court'])
                          ->orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->paginate(10);

        return view('users.booking.my-bookings', compact('bookings'));
    }

    // Midtrans payment notification webhook
    public function midtransNotification(Request $request)
    {
        // Log incoming webhook for debugging
        Log::info('Midtrans Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $midtransService = new MidtransService();
            $result = $midtransService->handleNotification($request->all());
            
            Log::info('Midtrans Webhook Processed Successfully', ['result' => $result]);
            
            return response()->json(['status' => 'success', 'message' => 'Notification processed']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Check Midtrans payment status (for manual check)
    public function checkPaymentStatus(Booking $booking)
    {
        // Ensure user can only check their own booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $midtransService = new MidtransService();
            $status = $midtransService->getTransactionStatus($booking->midtrans_order_id);
            
            return response()->json([
                'status' => 'success',
                'payment_status' => $status,
                'booking_status' => $booking->fresh()->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
