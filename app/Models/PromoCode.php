<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'start_date',
        'expiry_date',
        'usage_limit',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2'
    ];

    /**
     * Check if promo code is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        // Check if started
        if ($now->lt($this->start_date)) {
            return false;
        }

        // Check if expired
        if ($this->expiry_date && $now->gt($this->expiry_date)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->bookings()->count() >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return $amount * ($this->value / 100);
        }

        // For 'fixed' or 'nominal' type - return the discount value or amount (whichever is smaller)
        return min($this->value, $amount);
    }

    /**
     * Get remaining usage
     */
    public function getRemainingUsageAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->bookings()->count());
    }

    /**
     * Relationship with bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'promo_code_id');
    }
}
