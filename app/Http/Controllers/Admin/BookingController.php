<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Sport;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'sport', 'court']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('team_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sport', function ($sportQuery) use ($search) {
                      $sportQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('court', function ($courtQuery) use ($search) {
                      $courtQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }
        
        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'booking_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $bookings = $query->paginate($perPage)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending_payment')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'today' => Booking::whereDate('booking_date', Carbon::today())->count(),
            'this_week' => Booking::whereBetween('booking_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'revenue_today' => Booking::whereDate('booking_date', Carbon::today())
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'revenue_month' => Booking::whereMonth('booking_date', Carbon::now()->month)
                ->whereYear('booking_date', Carbon::now()->year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
        ];
        
        // Get sports and courts for modal
        $sports = Sport::all();
        $courts = Court::all();
        
        return view('admin.bookings.index', compact('bookings', 'stats', 'sports', 'courts'));
    }
    
    public function show(Booking $booking)
    {
        $booking->load(['user', 'sport', 'court']);
        
        return view('admin.bookings.show', compact('booking'));
    }
    
    public function destroy(Booking $booking)
    {
        // Check if booking can be deleted (hasn't passed the date and time)
        $bookingDate = Carbon::parse($booking->booking_date);
        $bookingDateTime = $bookingDate->setTimeFromTimeString($booking->start_time);
        
        if (!$bookingDateTime->isFuture()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus booking yang sudah melewati waktu');
        }
        
        $bookingCode = $booking->booking_code;
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')
            ->with('success', "Booking {$bookingCode} berhasil dihapus");
    }

    public function confirmBooking(Booking $booking)
    {
        // Check if booking is in pending_confirmation status
        if ($booking->status !== 'pending_confirmation') {
            return redirect()->back()->with('error', 'Hanya booking dengan status pending confirmation yang dapat dikonfirmasi');
        }
        
        $oldStatus = $booking->status;
        $booking->status = 'confirmed';
        $booking->confirmed_at = now();
        $booking->save();
        
        return redirect()->route('admin.bookings.index')
            ->with('success', "Booking {$booking->booking_code} berhasil dikonfirmasi!");
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending_payment,confirmed,paid,cancelled,completed',
        ]);

        $oldStatus = $booking->status;
        $booking->status = $request->status;

        // Update timestamps based on status
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $booking->confirmed_at = now();
        } elseif ($request->status === 'paid' && $oldStatus !== 'paid') {
            $booking->paid_at = now();
        }

        $booking->save();

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', "Status booking berhasil diubah dari '{$oldStatus}' menjadi '{$request->status}'");
    }
    
    public function storeManualBooking(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'user_name' => 'required_without:user_id|string|max:255',
            'user_phone' => 'required_without:user_id|string|max:20',
            'user_email' => 'required_without:user_id|email|max:255',
            'team_name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Get or create user
            if ($request->user_id) {
                $user = User::findOrFail($request->user_id);
            } else {
                // Create guest user for manual booking
                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'phone_number' => $request->user_phone,
                    'password' => bcrypt('guest123'), // Default password for guest users
                    'role' => 'user',
                    'date_of_birth' => '1990-01-01', // Default date
                    'gender' => 'other', // Default gender
                    'email_verified_at' => now(),
                ]);
            }

            // Calculate duration and price
            $startTime = Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = Carbon::createFromFormat('H:i', $request->end_time);
            $duration = $endTime->diffInHours($startTime);

            $sport = Sport::findOrFail($request->sport_id);
            $court = Court::findOrFail($request->court_id);

            // Check if date is weekend
            $bookingDate = Carbon::parse($request->date);
            $isWeekend = in_array($bookingDate->dayOfWeek, [0, 5, 6]); // Friday, Saturday, Sunday

            $pricePerHour = $isWeekend ? $court->weekend_price : $court->weekday_price;
            $totalPrice = $pricePerHour * $duration;

            // Generate unique booking code
            $bookingCode = Str::random(10);

            // Create booking
            $booking = Booking::create([
                'booking_code' => 'WIFA-' . $bookingCode,
                'user_id' => $user->id,
                'sport_id' => $request->sport_id,
                'court_id' => $request->court_id,
                'slug' => $bookingCode,
                'team_name' => $request->team_name,
                'contact_person' => $request->contact_person ?: $user->name,
                'booking_date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $duration,
                'price_per_hour' => $pricePerHour,
                'total_price' => $totalPrice,
                'payment_method' => 'cash',
                'status' => 'confirmed', // Manual booking langsung confirmed
                'notes' => $request->notes,
                'admin_notes' => 'Booking manual oleh admin: ' . Auth::user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking manual berhasil dibuat',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(Request $request)
    {
        $query = User::where('role', 'user')
                    ->select('id', 'name', 'email', 'phone_number as phone');
        
        // Real-time search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('name')
                      ->limit(20) // Limit hasil untuk performa
                      ->get();
        
        return response()->json([
            'users' => $users
        ]);
    }

    public function getCourtsBySport(Sport $sport)
    {
        $courts = Court::where('sport_id', $sport->id)->get();
        
        return response()->json([
            'courts' => $courts
        ]);
    }

    public function export(Request $request)
    {
        // This method can be implemented later for CSV/Excel export
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}