<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Court;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        // Auto-update event statuses before showing the list
        Event::updateAllEventStatuses();
        
        $query = Event::with(['sport', 'court']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by sport
        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->orderBy('event_date', 'desc')->paginate(10);
        $sports = Sport::all();

        return view('admin.events.index', compact('events', 'sports'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $sports = Sport::all();
        $courts = Court::all();
        $minDate = Event::getMinEventDate();

        // If AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'sports' => $sports,
                'courts' => $courts,
                'minDate' => $minDate
            ]);
        }

        // For direct access, redirect to index with modal instruction
        return redirect()->route('admin.events.index')->with('openModal', 'create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sport_id' => 'required|exists:sports,id',
            'court_id' => 'required|exists:courts,id',
            'event_date' => 'required|date|after:' . Event::getMinEventDate(),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'registration_fee' => 'required|numeric|min:0',
            'max_teams' => 'required|integer|min:2|max:64',
            'registration_deadline' => 'required|date|before:event_date',
            'requirements' => 'nullable|string',
            'prize_info' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'event_date.after' => 'Tanggal event harus lebih dari 32 hari dari sekarang.',
        ]);

        try {
            // Check if court is available on the event date
            $this->checkCourtAvailability($request->court_id, $request->event_date);

            $eventData = $request->except(['poster']);

            // Handle poster upload
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('events/posters', 'public');
                $eventData['poster'] = $posterPath;
            }

            $slug = Str::slug($request->title);

            $event = Event::create($eventData + ['slug' => $slug]);

            // If AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Event '{$event->title}' berhasil dibuat dengan kode {$event->event_code}",
                    'event' => $event
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', "Event '{$event->title}' berhasil dibuat dengan kode {$event->event_code}");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        // Auto-update status before showing event details
        $event->checkAndUpdateStatus();
        
        $event->load(['sport', 'court', 'registrations.user']);
        
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        // Auto-update status before editing
        $event->checkAndUpdateStatus();
        
        $sports = Sport::all();
        $courts = Court::where('sport_id', $event->sport_id)->get();
        $minDate = Event::getMinEventDate();

        // If AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'sport_id' => $event->sport_id,
                    'court_id' => $event->court_id,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'start_time' => is_string($event->start_time) 
                        ? (strlen($event->start_time) === 8 ? substr($event->start_time, 0, 5) : $event->start_time)
                        : $event->start_time->format('H:i'),
                    'end_time' => is_string($event->end_time) 
                        ? (strlen($event->end_time) === 8 ? substr($event->end_time, 0, 5) : $event->end_time)
                        : $event->end_time->format('H:i'),
                    'registration_fee' => $event->registration_fee,
                    'max_teams' => $event->max_teams,
                    'registration_deadline' => $event->registration_deadline->format('Y-m-d'),
                    'requirements' => $event->requirements,
                    'prize_info' => $event->prize_info,
                    'status' => $event->status,
                    'poster' => $event->poster,
                    'poster_url' => $event->poster_url
                ],
                'courts' => $courts,
                'sports' => $sports,
                'minDate' => $minDate
            ]);
        }

        return view('admin.events.edit', compact('event', 'sports', 'courts', 'minDate'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sport_id' => 'required|exists:sports,id',
            'court_id' => 'required|exists:courts,id',
            'event_date' => 'required|date|after:' . Event::getMinEventDate(),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'registration_fee' => 'required|numeric|min:0',
            'max_teams' => 'required|integer|min:2|max:64',
            'registration_deadline' => 'required|date|before:event_date',
            'requirements' => 'nullable|string',
            'prize_info' => 'nullable|string',
            'status' => 'required|in:draft,open_registration,registration_closed,ongoing,completed,cancelled',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'event_date.after' => 'Tanggal event harus lebih dari 32 hari dari sekarang.',
        ]);

        try {
            // Check if court is available on the event date (if court or date changed)
            if ($event->court_id != $request->court_id || $event->event_date != $request->event_date) {
                $this->checkCourtAvailability($request->court_id, $request->event_date, $event->id);
            }

            $eventData = $request->except(['poster']);

            // Handle poster upload
            if ($request->hasFile('poster')) {
                // Delete old poster
                if ($event->poster) {
                    Storage::disk('public')->delete($event->poster);
                }
                
                $posterPath = $request->file('poster')->store('events/posters', 'public');
                $eventData['poster'] = $posterPath;
            }

            $event->update($eventData);

            // If AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Event '{$event->title}' berhasil diperbarui",
                    'event' => $event
                ]);
            }

            return redirect()->route('admin.events.show', $event)
                ->with('success', "Event '{$event->title}' berhasil diperbarui");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        // Check if event has registrations
        if ($event->registrations()->count() > 0) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Tidak dapat menghapus event yang sudah memiliki pendaftar');
        }

        // Delete poster if exists
        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }

        $eventTitle = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$eventTitle}' berhasil dihapus");
    }

    /**
     * Check court availability for the event date
     */
    private function checkCourtAvailability($courtId, $eventDate, $excludeEventId = null)
    {
        // Check if court has any bookings on the event date
        $existingBookings = \App\Models\Booking::where('court_id', $courtId)
            ->where('booking_date', $eventDate)
            ->count();

        if ($existingBookings > 0) {
            throw new \Exception('Lapangan sudah memiliki booking pada tanggal tersebut');
        }

        // Check if court has any events on the same date
        $query = Event::where('court_id', $courtId)
            ->where('event_date', $eventDate);
            
        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        $existingEvents = $query->count();

        if ($existingEvents > 0) {
            throw new \Exception('Lapangan sudah memiliki event lain pada tanggal tersebut');
        }
    }

    /**
     * Show event registrations
     */
    public function registrations(Event $event)
    {
        $registrations = $event->registrations()
            ->with(['user'])
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('admin.events.registrations', compact('event', 'registrations'));
    }

    /**
     * Update registration status
     */
    public function updateRegistrationStatus(Request $request, Event $event, EventRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $oldStatus = $registration->status;
        $registration->status = $request->status;

        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $registration->confirmed_at = now();
        }

        $registration->save();

        return redirect()->back()
            ->with('success', "Status pendaftaran berhasil diubah dari '{$oldStatus}' menjadi '{$request->status}'");
    }

    /**
     * Get courts by sport (AJAX endpoint)
     */
    public function getCourtsBySport($sportId)
    {
        $courts = Court::where('sport_id', $sportId)->get(['id', 'name']);
        
        return response()->json([
            'success' => true,
            'courts' => $courts
        ]);
    }

    /**
     * Get event detail for modal (AJAX endpoint)
     */
    public function getEventDetail(Event $event)
    {
        // Auto-update status before showing
        $event->checkAndUpdateStatus();
        
        $event->load(['sport', 'court', 'registrations']);
        
        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'event_code' => $event->event_code,
                'title' => $event->title,
                'description' => $event->description,
                'sport_name' => $event->sport->name,
                'court_name' => $event->court->name,
                'event_date_formatted' => $event->event_date->format('d F Y'),
                'start_time' => is_string($event->start_time) 
                    ? (strlen($event->start_time) === 8 ? substr($event->start_time, 0, 5) : $event->start_time)
                    : $event->start_time->format('H:i'),
                'end_time' => is_string($event->end_time) 
                    ? (strlen($event->end_time) === 8 ? substr($event->end_time, 0, 5) : $event->end_time)
                    : $event->end_time->format('H:i'),
                'registration_deadline_formatted' => $event->registration_deadline->format('d F Y'),
                'registration_fee' => $event->registration_fee,
                'registration_fee_formatted' => number_format($event->registration_fee, 0, ',', '.'),
                'max_teams' => $event->max_teams,
                'registered_teams_count' => $event->registered_teams_count,
                'available_slots' => $event->available_slots,
                'requirements' => $event->requirements,
                'prize_info' => $event->prize_info,
                'status' => $event->status,
                'poster' => $event->poster,
                'poster_url' => $event->poster_url
            ]
        ]);
    }

    /**
     * Get event registrations for modal (AJAX endpoint)
     */
    public function getEventRegistrations(Event $event)
    {
        // Auto-update status before showing
        $event->checkAndUpdateStatus();
        
        $registrations = $event->registrations()
            ->with(['user'])
            ->orderBy('registered_at', 'desc')
            ->get()
            ->map(function($reg) {
                return [
                    'id' => $reg->id,
                    'registration_code' => $reg->registration_code,
                    'team_name' => $reg->team_name,
                    'user_name' => $reg->user->name,
                    'contact_person' => $reg->contact_person,
                    'contact_phone' => $reg->contact_phone,
                    'contact_email' => $reg->contact_email,
                    'team_members_count' => is_array($reg->team_members) ? count($reg->team_members) : 0,
                    'status' => $reg->status,
                    'registered_at_formatted' => $reg->registered_at->format('d M Y H:i'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'max_teams' => $event->max_teams,
                'registered_teams_count' => $event->registered_teams_count,
            ],
            'registrations' => $registrations
        ]);
    }
}
