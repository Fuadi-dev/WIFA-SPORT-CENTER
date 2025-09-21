<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_code',
        'event_id',
        'user_id',
        'team_name',
        'team_members',
        'contact_person',
        'contact_phone',
        'contact_email',
        'additional_info',
        'status',
        'registration_fee_paid',
        'registered_at',
        'confirmed_at',
    ];

    protected $casts = [
        'team_members' => 'array',
        'registration_fee_paid' => 'decimal:2',
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Boot the model and set up registration code generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (empty($registration->registration_code)) {
                $registration->registration_code = self::generateRegistrationCode();
            }

            if (empty($registration->registered_at)) {
                $registration->registered_at = now();
            }
        });
    }

    /**
     * Generate unique registration code
     */
    public static function generateRegistrationCode()
    {
        $lastRegistration = self::where('registration_code', 'like', 'REG-EVT-%')
            ->orderBy('registration_code', 'desc')
            ->first();

        if ($lastRegistration) {
            $lastNumber = (int) substr($lastRegistration->registration_code, 8);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'REG-EVT-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Attributes
     */
    public function getTeamMembersCountAttribute()
    {
        return is_array($this->team_members) ? count($this->team_members) : 0;
    }

    public function getIsConfirmedAttribute()
    {
        return $this->status === 'confirmed';
    }

    public function getIsPaidAttribute()
    {
        return $this->registration_fee_paid >= $this->event->registration_fee;
    }
}
