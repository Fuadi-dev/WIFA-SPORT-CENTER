<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\Court;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;

class BookingController extends Controller
{

    // Step 1: Choose Sport
    public function index()
    {
        $sports = Sport::active()->get();
        return view('booking.choose-sport', compact('sports'));
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
        
        return view('booking.choose-court', compact('sport', 'courts'));
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
        
        // Operating hours (6 AM to 12 AM - 24:00)
        $timeSlots = [];
        for ($hour = 6; $hour < 24; $hour++) {
            $price = $this->getPriceByHour($hour);
            $timeSlots[] = [
                'start' => sprintf('%02d:00', $hour),
                'end' => sprintf('%02d:00', $hour + 1),
                'display' => sprintf('%02d:00-%02d:00', $hour, $hour + 1),
                'price' => $price,
                'price_category' => $this->getPriceCategoryByHour($hour)
            ];
        }
        
        return view('booking.choose-schedule', compact('sport', 'court', 'dates', 'timeSlots'));
    }

    // Get price based on hour
    private function getPriceByHour($hour)
    {
        if ($hour >= 6 && $hour < 12) {
            return 60000; // 06:00 - 12:00 = Rp 60.000
        } elseif ($hour >= 12 && $hour < 18) {
            return 80000; // 12:00 - 18:00 = Rp 80.000
        } else {
            return 100000; // 18:00 - 24:00 = Rp 100.000
        }
    }

    // Get price category name
    private function getPriceCategoryByHour($hour)
    {
        if ($hour >= 6 && $hour < 12) {
            return 'morning'; // Pagi
        } elseif ($hour >= 12 && $hour < 18) {
            return 'afternoon'; // Siang
        } else {
            return 'evening'; // Malam
        }
    }

    // Calculate total price for time range
    private function calculateTotalPrice($startTime, $endTime)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $totalPrice = 0;
        
        $currentHour = $start->hour;
        while ($currentHour < $end->hour) {
            $totalPrice += $this->getPriceByHour($currentHour);
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
        $isAvailable = $court->isAvailable($date, $startTime, $endTime);
        
        return response()->json(['available' => $isAvailable]);
    }

    // Get price for time range (AJAX)
    public function getPriceForTimeRange(Request $request)
    {
        $startTime = $request->start_time;
        $duration = $request->duration;
        
        $start = Carbon::parse($startTime);
        $end = $start->copy()->addHours($duration);
        $endTime = $end->format('H:i');
        
        $totalPrice = $this->calculateTotalPrice($startTime, $endTime);
        
        // Get price breakdown by hour
        $priceBreakdown = [];
        $currentHour = $start->hour;
        for ($i = 0; $i < $duration; $i++) {
            $hourPrice = $this->getPriceByHour($currentHour);
            $category = $this->getPriceCategoryByHour($currentHour);
            $priceBreakdown[] = [
                'hour' => sprintf('%02d:00-%02d:00', $currentHour, $currentHour + 1),
                'price' => $hourPrice,
                'category' => $category
            ];
            $currentHour++;
        }
        
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
        
        // Calculate duration and price with time-based pricing
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $duration = $start->diffInHours($end);
        $totalPrice = $this->calculateTotalPrice($startTime, $endTime);
        
        return view('booking.booking-form', compact('sport', 'court', 'date', 'startTime', 'endTime', 'duration', 'totalPrice'));
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
        if (!$court->isAvailable($request->date, $request->start_time, $request->end_time)) {
            return back()->withErrors(['error' => 'Slot waktu sudah tidak tersedia.']);
        }

        // Calculate price with time-based pricing
        $totalPrice = $this->calculateTotalPrice($request->start_time, $request->end_time);

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

        return redirect()->route('booking.confirmation', $booking->id)
                        ->with('success', 'Pemesanan berhasil dibuat!');
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

        return view('booking.confirmation', compact('booking'));
    }

    // User's bookings
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
                          ->with(['sport', 'court'])
                          ->orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->paginate(10);

        return view('booking.my-bookings', compact('bookings'));
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

    // Legacy method for old route
    public function olahraga()
    {
        return redirect()->route('booking.index');
    }
}
