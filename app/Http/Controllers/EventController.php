<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of active events.
     */
    public function index(Request $request)
    {
        $query = Event::with(['sport', 'court'])
            ->whereIn('status', ['open_registration', 'ongoing'])
            ->upcoming();

        // Filter by sport
        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(9);
        $sports = \App\Models\Sport::all();

        return view('events.index', compact('events', 'sports'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['sport', 'court', 'registrations' => function($query) {
            $query->where('status', '!=', 'cancelled');
        }]);

        // Check if current user already registered
        $userRegistration = null;
        if (Auth::check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', Auth::id())
                ->where('status', '!=', 'cancelled')
                ->first();
        }

        return view('events.show', compact('event', 'userRegistration'));
    }

    /**
     * Show the registration form for the specified event.
     */
    public function register(Event $event)
    {
        // Check if registration is still open
        if (!$event->is_registration_open) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Pendaftaran untuk event ini sudah ditutup');
        }

        // Check if user already registered
        if (Auth::check()) {
            $existingRegistration = $event->registrations()
                ->where('user_id', Auth::id())
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingRegistration) {
                return redirect()->route('events.show', $event)
                    ->with('error', 'Anda sudah terdaftar untuk event ini');
            }
        }

        return view('events.register', compact('event'));
    }

    /**
     * Store a new event registration.
     */
    public function storeRegistration(Request $request, Event $event)
    {
        // Check if registration is still open
        if (!$event->is_registration_open) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Pendaftaran untuk event ini sudah ditutup');
        }

        // Check if user already registered
        $existingRegistration = $event->registrations()
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingRegistration) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Anda sudah terdaftar untuk event ini');
        }

        $request->validate([
            'team_name' => 'required|string|max:255',
            'team_members' => 'required|array|min:1',
            'team_members.*.name' => 'required|string|max:255',
            'team_members.*.position' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'additional_info' => 'nullable|string',
        ]);

        // Create registration
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'team_name' => $request->team_name,
            'team_members' => $request->team_members,
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'additional_info' => $request->additional_info,
            'status' => 'pending',
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', "Pendaftaran berhasil! Kode pendaftaran: {$registration->registration_code}");
    }

    /**
     * Show user's registrations.
     */
    public function myRegistrations()
    {
        $registrations = EventRegistration::with(['event.sport', 'event.court'])
            ->where('user_id', Auth::id())
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('events.my-registrations', compact('registrations'));
    }

    /**
     * Cancel a registration.
     */
    public function cancelRegistration(EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if event hasn't started yet
        if ($registration->event->event_date <= now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Tidak dapat membatalkan pendaftaran, event sudah dimulai');
        }

        $registration->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Pendaftaran berhasil dibatalkan');
    }
}
