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
}
