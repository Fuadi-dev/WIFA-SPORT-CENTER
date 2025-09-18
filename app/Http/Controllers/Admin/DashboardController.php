<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Court;
use App\Models\Sport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current date ranges
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        
        // Basic statistics
        $stats = [
            'total_bookings' => Booking::count(),
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_courts' => Court::count(),
            'total_sports' => Sport::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
        ];
        
        // Today's statistics
        $todayStats = [
            'bookings' => Booking::whereDate('date', $today)->count(),
            'revenue' => Booking::whereDate('date', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'new_users' => User::whereDate('created_at', $today)->count(),
        ];
        
        // This month statistics
        $monthStats = [
            'bookings' => Booking::whereDate('date', '>=', $thisMonth)->count(),
            'revenue' => Booking::whereDate('date', '>=', $thisMonth)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'new_users' => User::whereDate('created_at', '>=', $thisMonth)->count(),
        ];
        
        // Recent bookings (last 5)
        $recentBookings = Booking::with(['user', 'court', 'sport'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Most popular sports
        $popularSports = DB::table('bookings')
            ->join('sports', 'bookings.sport_id', '=', 'sports.id')
            ->select('sports.name', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('sports.id', 'sports.name')
            ->orderBy('booking_count', 'desc')
            ->limit(5)
            ->get();
        
        // Court utilization
        $courtUtilization = DB::table('bookings')
            ->join('courts', 'bookings.court_id', '=', 'courts.id')
            ->select('courts.name', DB::raw('COUNT(*) as booking_count'))
            ->where('bookings.status', '!=', 'cancelled')
            ->groupBy('courts.id', 'courts.name')
            ->orderBy('booking_count', 'desc')
            ->get();
        
        // Revenue by month (last 6 months)
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Booking::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');
            
            $revenueData[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }
        
        // Booking trends (last 7 days)
        $bookingTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Booking::whereDate('created_at', $date)->count();
            
            $bookingTrends[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }
        
        return view('admin.dashboard', compact(
            'stats',
            'todayStats', 
            'monthStats',
            'recentBookings',
            'popularSports',
            'courtUtilization',
            'revenueData',
            'bookingTrends'
        ));
    }
    
    public function dashboard()
    {
        return $this->index();
    }
}
