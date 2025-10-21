<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Sport;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        return [
            'total_bookings' => (clone $query)->count(),
            'total_revenue' => (clone $query)->whereIn('status', ['paid', 'completed'])->sum('total_price'),
            'pending_payment' => (clone $query)->where('status', 'pending_payment')->count(),
            'paid_bookings' => (clone $query)->where('status', 'paid')->count(),
            'completed_bookings' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_bookings' => (clone $query)->where('status', 'cancelled')->count(),
            'confirmed_bookings' => (clone $query)->where('status', 'confirmed')->count(),
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
        
        $bookings = $query->get();
        
        $filename = 'laporan-booking-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Kode Booking',
                'Tanggal',
                'Waktu Mulai',
                'Waktu Selesai',
                'Nama Pelanggan',
                'Email',
                'Telepon',
                'Olahraga',
                'Lapangan',
                'Total Harga',
                'Status',
                'Dibuat Pada'
            ]);
            
            // Data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_code,
                    $booking->booking_date->format('Y-m-d'),
                    $booking->start_time,
                    $booking->end_time,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->user->phone ?? '-',
                    $booking->court->sport->name,
                    $booking->court->name,
                    'Rp ' . number_format($booking->total_price, 0, ',', '.'),
                    $booking->status,
                    $booking->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
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

        // Event Revenue
        $eventRevenue = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->whereBetween('events.event_date', [$startDate, $endDate])
            ->where('event_registrations.status', '!=', 'cancelled')
            ->sum('event_registrations.registration_fee_paid');

        $eventRegistrations = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->whereBetween('events.event_date', [$startDate, $endDate])
            ->where('event_registrations.status', '!=', 'cancelled')
            ->count();

        // Total Revenue
        $totalRevenue = $bookingRevenue + $eventRevenue;

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

        // Revenue by Sport (Event)
        $eventBySport = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('sports', 'events.sport_id', '=', 'sports.id')
            ->whereBetween('events.event_date', [$startDate, $endDate])
            ->where('event_registrations.status', '!=', 'cancelled')
            ->select(
                'sports.name',
                DB::raw('COUNT(event_registrations.id) as count'),
                DB::raw('SUM(event_registrations.registration_fee_paid) as revenue')
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

            $eventMonthRevenue = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->whereBetween('events.event_date', [$monthStart, $monthEnd])
                ->where('event_registrations.status', '!=', 'cancelled')
                ->sum('event_registrations.registration_fee_paid');

            $monthlyData[] = [
                'month' => $month,
                'month_name' => $monthStart->format('F'),
                'booking_revenue' => $bookingMonthRevenue,
                'event_revenue' => $eventMonthRevenue,
                'total_revenue' => $bookingMonthRevenue + $eventMonthRevenue,
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

        // Top 5 Events by Revenue
        $topEvents = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->whereBetween('events.event_date', [$startDate, $endDate])
            ->where('event_registrations.status', '!=', 'cancelled')
            ->select(
                'events.title',
                'events.id',
                DB::raw('COUNT(event_registrations.id) as registration_count'),
                DB::raw('SUM(event_registrations.registration_fee_paid) as revenue')
            )
            ->groupBy('events.id', 'events.title')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_revenue' => $totalRevenue,
            'booking_revenue' => $bookingRevenue,
            'event_revenue' => $eventRevenue,
            'booking_count' => $bookingCount,
            'event_registrations' => $eventRegistrations,
        ];

        return view('admin.report.financial', compact(
            'stats',
            'bookingBySport',
            'eventBySport',
            'monthlyData',
            'topCourts',
            'topEvents',
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

        // Get all bookings
        $bookings = Booking::with(['user', 'court.sport'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'completed'])
            ->orderBy('booking_date', 'desc')
            ->get();

        // Get all event registrations
        $eventRegistrations = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->join('sports', 'events.sport_id', '=', 'sports.id')
            ->whereBetween('events.event_date', [$startDate, $endDate])
            ->where('event_registrations.status', '!=', 'cancelled')
            ->select(
                'event_registrations.*',
                'events.title as event_title',
                'events.event_date',
                'users.name as user_name',
                'users.email as user_email',
                'sports.name as sport_name'
            )
            ->orderBy('events.event_date', 'desc')
            ->get();

        $filename = 'laporan-keuangan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($bookings, $eventRegistrations, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title
            fputcsv($file, ['LAPORAN KEUANGAN WIFA SPORT CENTER']);
            fputcsv($file, ['Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')]);
            fputcsv($file, ['']);
            
            // Booking Section
            fputcsv($file, ['=== PENDAPATAN BOOKING ===']);
            fputcsv($file, [
                'Kode Booking',
                'Tanggal',
                'Pelanggan',
                'Olahraga',
                'Lapangan',
                'Waktu',
                'Total',
                'Status'
            ]);
            
            $totalBooking = 0;
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_code,
                    $booking->booking_date->format('Y-m-d'),
                    $booking->user->name,
                    $booking->court->sport->name,
                    $booking->court->name,
                    substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5),
                    $booking->total_price,
                    $booking->status
                ]);
                $totalBooking += $booking->total_price;
            }
            
            fputcsv($file, ['', '', '', '', '', 'TOTAL BOOKING:', 'Rp ' . number_format($totalBooking, 0, ',', '.')]);
            fputcsv($file, ['']);
            
            // Event Section
            fputcsv($file, ['=== PENDAPATAN EVENT ===']);
            fputcsv($file, [
                'Kode Registrasi',
                'Event',
                'Tanggal Event',
                'Tim',
                'Contact Person',
                'Olahraga',
                'Biaya Pendaftaran',
                'Status'
            ]);
            
            $totalEvent = 0;
            foreach ($eventRegistrations as $reg) {
                fputcsv($file, [
                    $reg->registration_code,
                    $reg->event_title,
                    Carbon::parse($reg->event_date)->format('Y-m-d'),
                    $reg->team_name,
                    $reg->contact_person,
                    $reg->sport_name,
                    $reg->registration_fee_paid,
                    $reg->status
                ]);
                $totalEvent += $reg->registration_fee_paid;
            }
            
            fputcsv($file, ['', '', '', '', '', 'TOTAL EVENT:', 'Rp ' . number_format($totalEvent, 0, ',', '.')]);
            fputcsv($file, ['']);
            
            // Summary
            fputcsv($file, ['=== RINGKASAN ===']);
            fputcsv($file, ['Total Pendapatan Booking:', 'Rp ' . number_format($totalBooking, 0, ',', '.')]);
            fputcsv($file, ['Total Pendapatan Event:', 'Rp ' . number_format($totalEvent, 0, ',', '.')]);
            fputcsv($file, ['TOTAL PENDAPATAN:', 'Rp ' . number_format($totalBooking + $totalEvent, 0, ',', '.')]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
