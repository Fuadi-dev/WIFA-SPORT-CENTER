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
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
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
            'bookings' => Booking::whereDate('booking_date', $today)->count(),
            'revenue' => Booking::whereDate('booking_date', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'new_users' => User::whereDate('created_at', $today)->count(),
        ];
        
                // This week's statistics  
        $thisWeekStats = [
            'bookings' => Booking::whereBetween('booking_date', [$startOfWeek, $endOfWeek])->count(),
            'revenue' => Booking::whereBetween('booking_date', [$startOfWeek, $endOfWeek])
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'new_users' => User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
        ];
        
        // This month's statistics
        $monthStats = [
            'bookings' => Booking::whereMonth('booking_date', $thisMonth->month)
                ->whereYear('booking_date', $thisMonth->year)
                ->count(),
            'revenue' => Booking::whereMonth('booking_date', $thisMonth->month)
                ->whereYear('booking_date', $thisMonth->year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'new_users' => User::whereMonth('created_at', $thisMonth->month)
                ->whereYear('created_at', $thisMonth->year)
                ->count(),
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
            $revenue = Booking::whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
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
            'thisWeekStats',
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
