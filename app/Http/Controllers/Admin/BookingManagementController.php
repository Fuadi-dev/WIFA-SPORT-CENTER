<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingManagementController extends Controller
{
    public function getBooking(Request $request)
    {
        $perPage = $request->get('per_page', 10); 
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'project_name');
        $sortDir = $request->get('sort_dir', 'asc');

        $user = Auth::user();
        
        // Get projects based on user role
        if ($user->role === 'admin') {
            // Admin can see all projects
            $query = Booking::with(['user']);
        } 

        // Search by project name and description
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting data
        $query->orderBy($sortBy, $sortDir);

        // Pagination dengan parameter dinamis
        $bookings = $query->paginate($perPage)->withQueryString();

        return view('admin.pages.bookings.bookings', compact('bookings', 'search', 'sortBy', 'sortDir', 'perPage'));
    }
}
