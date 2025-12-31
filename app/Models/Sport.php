<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'price_per_hour',
        'is_active'
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relationship to SportPrice (harga per time slot)
     */
    public function prices()
    {
        return $this->hasMany(SportPrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get price for a specific date and time
     * 
     * @param string|Carbon $date
     * @param string $time Format H:i or H:i:s
     * @return float
     */
    public function getPriceForDateTime($date, string $time): float
    {
        $timeSlot = SportPrice::getTimeSlotFromTime($time);
        
        $sportPrice = $this->prices()
            ->where('time_slot', $timeSlot)
            ->where('is_active', true)
            ->first();
        
        if ($sportPrice) {
            return $sportPrice->getPriceForDate($date);
        }
        
        // Fallback ke price_per_hour jika tidak ada setting harga
        return (float) $this->price_per_hour;
    }

    /**
     * Get SportPrice model for a specific time
     * 
     * @param string $time Format H:i or H:i:s
     * @return SportPrice|null
     */
    public function getPriceSettingForTime(string $time): ?SportPrice
    {
        $timeSlot = SportPrice::getTimeSlotFromTime($time);
        
        return $this->prices()
            ->where('time_slot', $timeSlot)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Calculate total price for booking
     * 
     * @param string|Carbon $date
     * @param string $startTime
     * @param string $endTime
     * @return float
     */
    public function calculateBookingPrice($date, string $startTime, string $endTime): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        // Handle overnight (e.g., 22:00 - 00:00)
        if ($end <= $start) {
            $end->addDay();
        }
        
        $totalPrice = 0;
        $current = $start->copy();
        
        // Calculate price per hour based on time slot
        while ($current < $end) {
            $hourPrice = $this->getPriceForDateTime($date, $current->format('H:i'));
            $totalPrice += $hourPrice;
            $current->addHour();
        }
        
        return $totalPrice;
    }

    /**
     * Get all prices grouped by time slot for display
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPriceList()
    {
        return $this->prices()
            ->where('is_active', true)
            ->orderByRaw("FIELD(time_slot, 'morning', 'afternoon', 'evening')")
            ->get();
    }
}
