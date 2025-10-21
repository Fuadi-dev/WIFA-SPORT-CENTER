<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Event;
use App\Models\PromoCode;
use App\Models\AutoPromo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;

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
            return redirect()->route('booking.schedule', ['sport' => $sport->slug, 'court' => $court->slug]);
        }   
        return view('users.booking.choose-court', compact('sport', 'courts'));
    }

    // Step 3: Choose Schedule
    public function showSchedule(Request $request, Sport $sport, Court $court)
    {
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
                case 'basket':
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
                case 'basket':
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
                case 'basket':
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
        
        // Handle end time specially if it's "24:00" (midnight)
        if ($endTime === '24:00') {
            $endHour = 24;
        } else {
            $end = Carbon::parse($endTime);
            $endHour = $end->hour;
        }
        
        $totalPrice = 0;
        $currentHour = $start->hour;
        
        // Make sure we don't go beyond hour 24
        while ($currentHour < $endHour && $currentHour < 24) {
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
        
        // Check if the selected date-time is in the past
        $bookingDateTime = Carbon::parse($date . ' ' . $startTime);
        $now = Carbon::now();
        
        if ($bookingDateTime->isPast()) {
            return response()->json([
                'available' => false,
                'reason' => 'past_time',
                'message' => 'Waktu sudah lewat'
            ]);
        }
        
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
        try {
            $startTime = $request->start_time;
            $duration = (int) $request->duration;
            $sportId = $request->sport_id;
            $date = $request->date;
            
            // Validate input
            if (!$startTime || !$duration || !$date) {
                return response()->json([
                    'error' => 'Missing required parameters',
                    'total_price' => 0,
                    'end_time' => '00:00',
                    'price_breakdown' => []
                ], 400);
            }
            
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
            $startHour = $start->hour;
            
            // Calculate actual duration (don't exceed 24:00)
            $actualDuration = min($duration, 24 - $startHour);
            $endHour = $startHour + $actualDuration;
            
            // Format end time
            if ($endHour >= 24) {
                $endTime = '24:00';
            } else {
                $endTime = sprintf('%02d:00', $endHour);
            }
            
            Log::info('Duration calculation', [
                'startHour' => $startHour,
                'requestedDuration' => $duration,
                'actualDuration' => $actualDuration,
                'endHour' => $endHour,
                'endTime' => $endTime
            ]);
            
            // Calculate total price
            $totalPrice = 0;
            $priceBreakdown = [];
            
            for ($i = 0; $i < $actualDuration; $i++) {
                $currentHour = $startHour + $i;
                
                // Safety check
                if ($currentHour >= 24) {
                    Log::warning('Hour exceeded 24', ['currentHour' => $currentHour, 'i' => $i]);
                    break;
                }
                
                $nextHour = min($currentHour + 1, 24);
                $hourPrice = $this->getPriceByHour($currentHour, $sport, $date);
                $category = $this->getPriceCategoryByHour($currentHour);
                
                $totalPrice += $hourPrice;
                
                $priceBreakdown[] = [
                    'hour' => sprintf('%02d:00-%02d:00', $currentHour, $nextHour),
                    'price' => $hourPrice,
                    'category' => $category
                ];
            }
            
            Log::info('Price calculation result', [
                'totalPrice' => $totalPrice,
                'breakdownCount' => count($priceBreakdown),
                'actualDuration' => $actualDuration,
                'endTime' => $endTime
            ]);
            
            return response()->json([
                'total_price' => $totalPrice,
                'end_time' => $endTime,
                'price_breakdown' => $priceBreakdown,
                'debug' => [
                    'actualDuration' => $actualDuration,
                    'startHour' => $startHour,
                    'endHour' => $endHour
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getPriceForTimeRange', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal server error: ' . $e->getMessage(),
                'total_price' => 0,
                'end_time' => '00:00',
                'price_breakdown' => []
            ], 500);
        }
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
            return redirect()->route('booking.schedule', ['sport' => $sport->slug, 'court' => $court->slug])
                           ->with('error', 'Data booking tidak lengkap. Silakan pilih ulang jadwal.');
        }
        
        // Check for events first
        $event = $court->getEventForDate($date);
        if ($event) {
            return redirect()->route('booking.schedule', ['sport' => $sport->slug, 'court' => $court->slug])
                           ->with('error', "Lapangan tidak tersedia pada tanggal tersebut karena ada event: {$event->title} ({$event->event_code})");
        }
        
        // IMPORTANT: Double check availability for the entire time range before showing form
        if (!$court->isAvailable($date, $startTime, $endTime)) {
            return redirect()->route('booking.schedule', ['sport' => $sport->slug, 'court' => $court->slug])
                           ->with('error', 'Slot waktu sudah tidak tersedia. Silakan pilih waktu lain.');
        }
        
        // Calculate duration and price with time-based pricing
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $duration = $start->diffInHours($end);
        $totalPrice = $this->calculateTotalPrice($startTime, $endTime, $sport, $date);
        
        // Check for applicable auto promo
        $autoPromo = $this->findApplicableAutoPromo($date, $startTime, $totalPrice);
        
        return view('users.booking.booking-form', compact('sport', 'court', 'date', 'startTime', 'endTime', 'duration', 'totalPrice', 'autoPromo'));
    }
    
    // API: Validate Promo Code
    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'total_amount' => 'required|numeric'
        ]);
        
        $code = strtoupper($request->code);
        $promoCode = PromoCode::where('code', $code)->first();
        
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan'
            ]);
        }
        
        if (!$promoCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo sudah tidak berlaku atau telah mencapai batas penggunaan'
            ]);
        }
        
        $discount = $promoCode->calculateDiscount($request->total_amount);
        
        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil diterapkan',
            'promo' => [
                'code' => $promoCode->code,
                'type' => $promoCode->type, // 'percentage' or 'fixed'
                'value' => $promoCode->value,
                'description' => $promoCode->type === 'percentage' 
                    ? $promoCode->value . '% discount' 
                    : 'Rp ' . number_format($promoCode->value, 0, ',', '.') . ' discount'
            ],
            'discount' => $discount
        ]);
    }
    
    // Find applicable auto promo (return the one with highest discount)
    private function findApplicableAutoPromo($date, $time, $amount)
    {
        $dateTime = Carbon::parse($date . ' ' . $time);
        
        $autoPromos = AutoPromo::where('is_active', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_from')
                      ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', $date);
            })
            ->get();
        
        $applicablePromos = [];
        
        foreach ($autoPromos as $promo) {
            if ($promo->isApplicable($dateTime, $amount)) {
                $discount = $promo->calculateDiscount($amount);
                $applicablePromos[] = [
                    'promo' => $promo,
                    'discount' => $discount
                ];
            }
        }
        
        // If multiple promos are applicable, return the one with highest discount
        if (count($applicablePromos) > 0) {
            usort($applicablePromos, function($a, $b) {
                return $b['discount'] <=> $a['discount'];
            });
            
            return $applicablePromos[0]['promo'];
        }
        
        return null;
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
            'agree_terms' => 'required|accepted',
            'validated_promo_code' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0'
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
        $originalPrice = $this->calculateTotalPrice($request->start_time, $request->end_time, $sport, $request->date);
        
        // Process promo codes
        $promoCodeId = null;
        $autoPromoId = null;
        $discountAmount = 0;
        
        // Check if user applied a promo code
        if ($request->filled('validated_promo_code')) {
            $promoCode = PromoCode::where('code', strtoupper($request->validated_promo_code))->first();
            if ($promoCode && $promoCode->isValid()) {
                $promoCodeId = $promoCode->id;
                $discountAmount = $promoCode->calculateDiscount($originalPrice);
            }
        }
        
        // If no promo code, check for auto promo
        if (!$promoCodeId && $request->filled('auto_promo_detected')) {
            $autoPromo = $this->findApplicableAutoPromo($request->date, $request->start_time, $originalPrice);
            if ($autoPromo) {
                $autoPromoId = $autoPromo->id;
                $discountAmount = $autoPromo->calculateDiscount($originalPrice);
            }
        }
        
        // Calculate final price
        $totalPrice = $originalPrice - $discountAmount;

        // Determine initial status based on payment method
        $initialStatus = $request->payment_method === 'cash' ? 'confirmed' : 'pending_payment';
        $confirmedAt = $request->payment_method === 'cash' ? now() : null;
        $bookingCode = Str::random(10);

        // Create booking
        $booking = Booking::create([
            'booking_code' => $bookingCode,
            'user_id' => Auth::id(),
            'sport_id' => $request->sport_id,
            'court_id' => $request->court_id,
            'slug' => $bookingCode,
            'booking_date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'team_name' => $request->team_name,
            'notes' => $request->notes,
            'payment_method' => $request->payment_method,
            'promo_code_id' => $promoCodeId,
            'auto_promo_id' => $autoPromoId,
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
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

        return redirect()->route('booking.confirmation', $booking->slug)
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
            
            return redirect()->route('booking.confirmation', $booking->slug)
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
        
        // Load relationships
        $booking->load(['sport', 'court', 'promoCode', 'autoPromo']);

        return view('users.booking.confirmation', compact('booking'));
    }

    // User's bookings
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
                          ->with(['sport', 'court', 'promoCode', 'autoPromo'])
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
