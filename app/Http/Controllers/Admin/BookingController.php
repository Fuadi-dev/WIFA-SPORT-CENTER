<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Sport;
use App\Models\Court;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        return view('admin.bookings.index', compact('bookings', 'stats'));
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
    
    public function export(Request $request)
    {
        // This method can be implemented later for CSV/Excel export
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}