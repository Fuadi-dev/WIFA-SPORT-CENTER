<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AutoPromo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'schedule_type',
        'specific_date',
        'days_of_week',
        'start_time',
        'end_time',
        'valid_from',
        'valid_until',
        'min_transaction',
        'max_discount',
        'is_active'
    ];

    protected $casts = [
        'specific_date' => 'date',
        'days_of_week' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'max_discount' => 'decimal:2'
    ];

    /**
     * Check if auto promo is applicable for given date and time
     */
    public function isApplicable(Carbon $dateTime, float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check date validity (only if valid_from is set)
        if ($this->valid_from && $dateTime->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $dateTime->gt($this->valid_until)) {
            return false;
        }

        // Check minimum transaction
        if ($this->min_transaction && $amount < $this->min_transaction) {
            return false;
        }

        // Check schedule type
        switch ($this->schedule_type) {
            case 'specific_date':
                if (!$this->specific_date || !$dateTime->isSameDay($this->specific_date)) {
                    return false;
                }
                break;

            case 'day_of_week':
                if (!$this->days_of_week || !in_array($dateTime->dayOfWeek, $this->days_of_week)) {
                    return false;
                }
                break;

            case 'daily':
                // Always applicable for daily
                break;
        }

        // Check time range
        $time = $dateTime->format('H:i:s');
        if ($time < $this->start_time || $time > $this->end_time) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = $amount * ($this->discount_value / 100);
            
            // Apply max discount limit for percentage type
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            // For 'fixed' or 'nominal' type - return the discount value or amount (whichever is smaller)
            $discount = min($this->discount_value, $amount);
        }

        return $discount;
    }

    /**
     * Get days of week as readable string
     */
    public function getDaysOfWeekStringAttribute(): string
    {
        if (!$this->days_of_week) {
            return '-';
        }

        $daysMap = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];

        return implode(', ', array_map(fn($day) => $daysMap[$day] ?? $day, $this->days_of_week));
    }
}
