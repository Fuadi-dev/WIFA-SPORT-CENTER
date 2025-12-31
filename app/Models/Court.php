<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sport_id',
        'type',
        'physical_location',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isAvailable($date, $startTime, $endTime)
    {
        // Logic untuk availability check berdasarkan jenis court
        $conflictingCourts = $this->getConflictingCourts();
        
        // Check for any event conflicts first (events block entire day)
        $eventConflict = Event::whereIn('court_id', $conflictingCourts)
            ->where('event_date', $date)
            ->whereIn('status', ['open_registration', 'registration_closed', 'ongoing'])
            ->exists();
            
        if ($eventConflict) {
            return false; // Court not available due to event
        }
        
        // Check for any booking conflicts in courts that would conflict with this booking
        return !Booking::whereIn('court_id', $conflictingCourts)
            ->where('booking_date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                // Check for any TRUE overlap between existing bookings and requested time
                $query->where(function($q) use ($startTime, $endTime) {
                    // Existing booking overlaps with requested time if:
                    // 1. Existing booking starts before requested ends AND
                    // 2. Existing booking ends after requested starts
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->whereIn('status', ['confirmed', 'paid', 'pending_payment'])
            ->exists();
    }

    /**
     * Get courts that would conflict with this court's booking
     */
    public function getConflictingCourts()
    {
        if (!$this->physical_location) {
            return [$this->id];
        }
        
        // Rules:
        // 1. Voli conflicts with ALL courts in same location (Badminton A + B)
        // 2. Badminton A only conflicts with Voli (not with Badminton B)
        // 3. Badminton B only conflicts with Voli (not with Badminton A)
        // 4. Futsal conflicts with Basket and vice versa
        
        $sportName = $this->sport->name;
        
        if ($sportName === 'Voli') {
            // Voli needs entire court, conflicts with ALL courts in same location
            return Court::where('physical_location', $this->physical_location)
                ->pluck('id')
                ->toArray();
        }
        
        if ($sportName === 'Badminton') {
            // Badminton only conflicts with Voli (not with other Badminton courts)
            $conflictingIds = [$this->id]; // Always include self
            
            // Add Voli court if it's in same location
            $voliCourt = Court::where('physical_location', $this->physical_location)
                ->whereHas('sport', function($q) {
                    $q->where('name', 'Voli');
                })
                ->first();
                
            if ($voliCourt) {
                $conflictingIds[] = $voliCourt->id;
            }
            
            return $conflictingIds;
        }
        
        if ($sportName === 'Futsal') {
            // Futsal conflicts with Basket
            return Court::where('physical_location', $this->physical_location)
                ->pluck('id')
                ->toArray();
        }
        
        if ($sportName === 'Basket') {
            // Basket conflicts with Futsal
            return Court::where('physical_location', $this->physical_location)
                ->pluck('id')
                ->toArray();
        }
        
        // Default: only check this court
        return [$this->id];
    }

    /**
     * Get event information for a specific date
     */
    public function getEventForDate($date)
    {
        $conflictingCourts = $this->getConflictingCourts();
        
        $event = Event::whereIn('court_id', $conflictingCourts)
            ->where('event_date', $date)
            ->whereIn('status', ['open_registration', 'registration_closed', 'ongoing'])
            ->with(['sport', 'court'])
            ->first();
        
        // Auto-update event status if found
        if ($event) {
            $event->checkAndUpdateStatus();
            
            // Re-check if event is still active after status update
            if (!in_array($event->status, ['open_registration', 'registration_closed', 'ongoing'])) {
                return null; // Event is no longer active
            }
        }
        
        return $event;
    }

    /**
     * Check if court has an event on specific date
     */
    public function hasEventOnDate($date)
    {
        return $this->getEventForDate($date) !== null;
    }

    /**
     * Get all courts that share the same physical location
     */
    public function getSharedCourts()
    {
        if (!$this->physical_location) {
            return collect([$this]);
        }
        
        $sportName = $this->sport->name;
        
        if ($sportName === 'Badminton') {
            // Badminton courts share with Voli, but not with each other for display purposes
            return Court::where('physical_location', $this->physical_location)
                ->whereHas('sport', function($q) {
                    $q->where('name', 'Voli');
                })
                ->with('sport')
                ->get();
        }
        
        // For other sports, show all courts in same location except self
        return Court::where('physical_location', $this->physical_location)
            ->where('id', '!=', $this->id)
            ->with('sport')
            ->get();
    }

    /**
     * Get price for a specific date and time
     * Delegates to Sport model which has the price settings
     * 
     * @param string|Carbon $date
     * @param string $time Format H:i or H:i:s
     * @return float
     */
    public function getPriceForDateTime($date, string $time): float
    {
        return $this->sport->getPriceForDateTime($date, $time);
    }

    /**
     * Calculate total price for booking
     * Delegates to Sport model
     * 
     * @param string|Carbon $date
     * @param string $startTime
     * @param string $endTime
     * @return float
     */
    public function calculateBookingPrice($date, string $startTime, string $endTime): float
    {
        return $this->sport->calculateBookingPrice($date, $startTime, $endTime);
    }
}
