<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Sport;
use App\Models\Court;
use App\Exports\BookingsExport;
use App\Exports\FinancialExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'court.sport'])
            ->orderBy('booking_date', 'desc');
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by sport
        if ($request->filled('sport_id')) {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            });
        }
        
        // Filter by court
        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }
        
        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        $bookings = $query->paginate(20);
        
        // Get statistics
        $stats = $this->getBookingStats($request);
        
        // Get sports and courts for filters
        $sports = Sport::all();
        $courts = Court::with('sport')->get();
        
        return view('admin.report.bookings', compact('bookings', 'stats', 'sports', 'courts'));
    }
    
    private function getBookingStats($request)
    {
        $query = Booking::query();
        
        // Apply same filters as main query
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('sport_id')) {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            });
        }
        
        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        return [
            'total_bookings' => (clone $query)->count(),
            'total_revenue' => (clone $query)->whereIn('status', ['paid', 'completed'])->sum('total_price'),
            'pending_payment' => (clone $query)->where('status', 'pending_payment')->count(),
            'paid_bookings' => (clone $query)->where('status', 'paid')->count(),
            'completed_bookings' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_bookings' => (clone $query)->where('status', 'cancelled')->count(),
            'confirmed_bookings' => (clone $query)->where('status', 'confirmed')->count(),
            'pending_confirmation' => (clone $query)->where('status', 'pending_confirmation')->count(),
        ];
    }
    
    public function bookingsByMonth(Request $request)
    {
        $year = $request->get('year', now()->year);
        
        $bookings = Booking::selectRaw('MONTH(booking_date) as month, COUNT(*) as count, SUM(total_price) as revenue')
            ->whereYear('booking_date', $year)
            ->whereIn('status', ['paid', 'completed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        
        $revenue = Booking::selectRaw('MONTH(booking_date) as month, SUM(total_price) as revenue')
            ->whereYear('booking_date', $year)
            ->whereIn('status', ['paid', 'completed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();
        
        // Fill missing months with 0
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => $i,
                'month_name' => Carbon::create()->month($i)->format('F'),
                'bookings' => $bookings[$i] ?? 0,
                'revenue' => $revenue[$i] ?? 0,
            ];
        }
        
        return response()->json($data);
    }
    
    public function bookingsBySport(Request $request)
    {
        $stats = Booking::join('courts', 'bookings.court_id', '=', 'courts.id')
            ->join('sports', 'courts.sport_id', '=', 'sports.id')
            ->select('sports.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(bookings.total_price) as revenue'))
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->groupBy('sports.id', 'sports.name')
            ->orderBy('count', 'desc')
            ->get();
        
        return response()->json($stats);
    }
    
    public function exportBookings(Request $request)
    {
        $query = Booking::with(['user', 'court.sport'])
            ->orderBy('booking_date', 'desc');
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('sport_id')) {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            });
        }
        
        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        $bookings = $query->get();
        
        // Get statistics for summary
        $stats = $this->getBookingStats($request);
        
        // Get filter info
        $filters = [];
        if ($request->filled('start_date')) {
            $filters['Tanggal Mulai'] = Carbon::parse($request->start_date)->format('d/m/Y');
        }
        if ($request->filled('end_date')) {
            $filters['Tanggal Akhir'] = Carbon::parse($request->end_date)->format('d/m/Y');
        }
        if ($request->filled('status')) {
            $filters['Status'] = ucfirst(str_replace('_', ' ', $request->status));
        }
        if ($request->filled('sport_id')) {
            $sport = Sport::find($request->sport_id);
            if ($sport) {
                $filters['Olahraga'] = $sport->name;
            }
        }
        if ($request->filled('court_id')) {
            $court = Court::find($request->court_id);
            if ($court) {
                $filters['Lapangan'] = $court->name;
            }
        }
        if ($request->filled('payment_method')) {
            $paymentMethods = [
                'midtrans' => 'Midtrans',
                'cash' => 'Tunai',
            ];
            $filters['Metode Pembayaran'] = $paymentMethods[$request->payment_method] ?? $request->payment_method;
        }
        
        $filename = 'laporan-booking-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new BookingsExport($bookings, $stats, $filters), $filename);
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        // Default date range: current month
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Booking Revenue
        $bookingRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_price');

        $bookingCount = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'completed'])
            ->count();

        // Revenue by Sport (Booking)
        $bookingBySport = Booking::join('courts', 'bookings.court_id', '=', 'courts.id')
            ->join('sports', 'courts.sport_id', '=', 'sports.id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->select(
                'sports.name',
                DB::raw('COUNT(bookings.id) as count'),
                DB::raw('SUM(bookings.total_price) as revenue')
            )
            ->groupBy('sports.id', 'sports.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Monthly comparison (current year)
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create(now()->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create(now()->year, $month, 1)->endOfMonth();

            $bookingMonthRevenue = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total_price');

            $monthlyData[] = [
                'month' => $month,
                'month_name' => $monthStart->format('F'),
                'booking_revenue' => $bookingMonthRevenue,
            ];
        }

        // Top 5 Courts by Revenue
        $topCourts = Booking::join('courts', 'bookings.court_id', '=', 'courts.id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->select(
                'courts.name',
                'courts.id',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_price) as revenue')
            )
            ->groupBy('courts.id', 'courts.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'booking_revenue' => $bookingRevenue,
            'booking_count' => $bookingCount,
        ];

        return view('admin.report.financial', compact(
            'stats',
            'bookingBySport',
            'monthlyData',
            'topCourts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export Financial Report
     */
    public function exportFinancial(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Booking Revenue
        $bookingRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_price');

        $bookingCount = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'completed'])
            ->count();

        // Revenue by Sport (Booking)
        $bookingBySport = Booking::join('courts', 'bookings.court_id', '=', 'courts.id')
            ->join('sports', 'courts.sport_id', '=', 'sports.id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->select(
                'sports.name',
                DB::raw('COUNT(bookings.id) as count'),
                DB::raw('SUM(bookings.total_price) as revenue')
            )
            ->groupBy('sports.id', 'sports.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Top 5 Courts by Revenue
        $topCourts = Booking::join('courts', 'bookings.court_id', '=', 'courts.id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->select(
                'courts.name',
                'courts.id',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_price) as revenue')
            )
            ->groupBy('courts.id', 'courts.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'booking_revenue' => $bookingRevenue,
            'booking_count' => $bookingCount,
        ];

        $period = [
            'start' => $startDate->format('d/m/Y'),
            'end' => $endDate->format('d/m/Y'),
        ];

        $filename = 'laporan-keuangan-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new FinancialExport($bookingBySport, $topCourts, $stats, $period), $filename);
    }
}
