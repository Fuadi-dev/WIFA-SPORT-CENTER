<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
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
            ->withCount(['registrations' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->whereIn('status', ['open_registration', 'registration_closed', 'ongoing'])
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

        return view('users.events.index', compact('events', 'sports'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['sport', 'court', 'registrations' => function($query) {
            $query->where('status', '!=', 'cancelled');
        }])->loadCount(['registrations' => function($query) {
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

        return view('users.events.show', compact('event', 'userRegistration'));
    }

    /**
     * Show the registration form for the specified event.
     */
    public function register(Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar event.');
        }

        // Check if registration is still open
        if (Carbon::now()->isAfter($event->registration_deadline)) {
            return redirect()->route('events.show', $event)->with('error', 'Pendaftaran sudah ditutup.');
        }

        // Check if user already registered
        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRegistration) {
            return redirect()->route('events.show', $event)->with('error', 'Anda sudah terdaftar untuk event ini.');
        }

        return view('users.events.register', compact('event'));
    }

    /**
     * Store a new event registration.
     */
    public function storeRegistration(Request $request, Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar event.');
        }

        // Check if registration is still open
        if (Carbon::now()->isAfter($event->registration_deadline)) {
            return redirect()->route('events.show', $event)->with('error', 'Pendaftaran sudah ditutup.');
        }

        // Check if user already registered
        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRegistration) {
            return redirect()->route('events.show', $event)->with('error', 'Anda sudah terdaftar untuk event ini.');
        }

        // Validate request
        $request->validate([
            'team_name' => 'required|string|max:255',
            'team_members' => 'required|array|min:1',
            'team_members.*.name' => 'required|string|max:255',
            'team_members.*.position' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'additional_info' => 'nullable|string|max:1000',
            'agree_terms' => 'required|accepted',
        ], [
            'team_name.required' => 'Nama tim harus diisi.',
            'team_members.required' => 'Minimal satu anggota tim harus diisi.',
            'team_members.*.name.required' => 'Nama anggota tim harus diisi.',
            'contact_person.required' => 'Nama penanggung jawab harus diisi.',
            'contact_phone.required' => 'Nomor HP harus diisi.',
            'contact_email.required' => 'Email harus diisi.',
            'contact_email.email' => 'Format email tidak valid.',
            'agree_terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        try {
            // Create event registration
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'team_name' => $request->team_name,
                'team_members' => json_encode($request->team_members),
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'additional_info' => $request->additional_info,
                'registration_status' => 'pending',
                'registered_at' => now(),
            ]);

            return redirect()->route('events.show', $event)->with('success', 'Pendaftaran berhasil! Tim Anda telah terdaftar untuk event ini.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.')->withInput();
        }
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

        return view('users.events.my-registrations', compact('registrations'));
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
