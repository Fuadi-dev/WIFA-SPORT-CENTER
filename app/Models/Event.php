<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_code',
        'title',
        'slug',
        'description',
        'poster',
        'sport_id',
        'court_id',
        'event_date',
        'start_time',
        'end_time',
        'registration_fee',
        'max_teams',
        'registration_deadline',
        'status',
        'requirements',
        'prize_info',
    ];

    protected $casts = [
        'event_date' => 'date',
        'registration_deadline' => 'date',
        'registration_fee' => 'decimal:2',
    ];

    /**
     * Boot the model and set up event code generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_code)) {
                $event->event_code = self::generateEventCode();
            }
        });
    }

    /**
     * Generate unique event code
     */
    public static function generateEventCode()
    {
        $lastEvent = self::where('event_code', 'like', 'EVT-%')
            ->orderBy('event_code', 'desc')
            ->first();

        if ($lastEvent) {
            $lastNumber = (int) substr($lastEvent->event_code, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'EVT-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if event date is valid (>32 days from now)
     */
    public static function isValidEventDate($date)
    {
        $eventDate = Carbon::parse($date);
        $minDate = Carbon::now()->addDays(32);
        
        return $eventDate->gte($minDate);
    }

    /**
     * Get minimum allowed event date
     */
    public static function getMinEventDate()
    {
        return Carbon::now()->addDays(32)->format('Y-m-d');
    }

    /**
     * Relationships
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open_registration', 'ongoing']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', Carbon::now()->format('Y-m-d'));
    }

    /**
     * Attributes
     */
    public function getRegisteredTeamsCountAttribute()
    {
        return $this->registrations()->where('status', '!=', 'cancelled')->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_teams - $this->registered_teams_count;
    }

    public function getIsRegistrationOpenAttribute()
    {
        return $this->status === 'open_registration' 
            && $this->registration_deadline >= Carbon::now()->format('Y-m-d')
            && $this->available_slots > 0;
    }

    public function getPosterUrlAttribute()
    {
        return $this->poster ? asset('storage/' . $this->poster) : null;
    }

    /**
     * Auto-update event status based on current date and registration deadline
     */
    public function updateStatus($dryRun = false)
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        
        $eventDate = Carbon::parse($this->event_date)->format('Y-m-d');
        $registrationDeadline = Carbon::parse($this->registration_deadline)->format('Y-m-d');
        
        $oldStatus = $this->status;
        
        // Logic untuk mengupdate status:
        // 1. Jika event sudah selesai (melewati tanggal event + end_time)
        if ($today > $eventDate || ($today === $eventDate && $currentTime > $this->end_time)) {
            $this->status = 'completed';
        }
        // 2. Jika event sedang berlangsung (hari H event dan waktu >= start_time)
        elseif ($today === $eventDate && $currentTime >= $this->start_time) {
            $this->status = 'ongoing';
        }
        // 3. Jika registration deadline sudah lewat tapi event belum dimulai
        elseif ($today > $registrationDeadline || 
                ($today === $registrationDeadline && $currentTime >= '23:59:59')) {
            if ($this->status === 'open_registration') {
                $this->status = 'registration_closed';
            }
        }
        // 4. Jika masih dalam periode registrasi dan status masih draft
        elseif ($this->status === 'draft' && $today <= $registrationDeadline) {
            // Bisa tetap draft atau bisa auto-aktifkan ke open_registration
            // Untuk sekarang kita biarkan manual activation
        }
        
        // Save hanya jika status berubah dan bukan dry-run
        if ($oldStatus !== $this->status && !$dryRun) {
            $this->save();
        }
        
        return [
            'updated' => $oldStatus !== $this->status,
            'old_status' => $oldStatus,
            'new_status' => $this->status,
            'event_title' => $this->title
        ];
    }

    /**
     * Static method untuk update semua event
     */
    public static function updateAllEventStatuses()
    {
        $updatedEvents = [];
        
        // Ambil semua event yang berpotensi perlu diupdate
        $events = self::whereIn('status', [
            'draft',
            'open_registration',
            'registration_closed',
            'ongoing'
        ])->get();
        
        foreach ($events as $event) {
            $result = $event->updateStatus();
            if ($result['updated']) {
                $updatedEvents[] = $result;
            }
        }
        
        return $updatedEvents;
    }

    /**
     * Check and update status for real-time operations
     * Digunakan saat event diakses melalui web interface
     */
    public function checkAndUpdateStatus()
    {
        // Hanya update jika status masih bisa berubah
        if (in_array($this->status, ['draft', 'open_registration', 'registration_closed', 'ongoing'])) {
            return $this->updateStatus();
        }
        
        return ['updated' => false, 'status' => $this->status];
    }

    /**
     * Scope untuk event yang perlu diupdate statusnya
     */
    public function scopeNeedsStatusUpdate($query)
    {
        $today = Carbon::now()->format('Y-m-d');
        
        return $query->where(function($q) use ($today) {
            $q->where('status', 'open_registration')
              ->where('registration_deadline', '<', $today)
              ->orWhere('status', 'registration_closed')
              ->where('event_date', '<=', $today)
              ->orWhere('status', 'ongoing')
              ->where('event_date', '<', $today);
        });
    }
}
