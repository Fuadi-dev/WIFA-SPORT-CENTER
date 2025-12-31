<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SportPrice extends Model
{
    use HasFactory;

    // Time slot constants
    const SLOT_MORNING = 'morning';     // 08:00 - 12:00
    const SLOT_AFTERNOON = 'afternoon'; // 12:00 - 18:00
    const SLOT_EVENING = 'evening';     // 18:00 - 00:00

    protected $fillable = [
        'sport_id',
        'time_slot',
        'start_time',
        'end_time',
        'weekday_price',
        'weekend_price',
        'is_active'
    ];

    protected $casts = [
        'weekday_price' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Sport
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get time slot options
     */
    public static function getTimeSlots(): array
    {
        return [
            self::SLOT_MORNING => [
                'label' => 'Pagi (08:00 - 12:00)',
                'start' => '08:00',
                'end' => '12:00'
            ],
            self::SLOT_AFTERNOON => [
                'label' => 'Siang (12:00 - 18:00)',
                'start' => '12:00',
                'end' => '18:00'
            ],
            self::SLOT_EVENING => [
                'label' => 'Malam (18:00 - 00:00)',
                'start' => '18:00',
                'end' => '00:00'
            ],
        ];
    }

    /**
     * Determine time slot from a given time
     * 
     * @param string $time Format H:i or H:i:s
     * @return string|null
     */
    public static function getTimeSlotFromTime(string $time): ?string
    {
        $hour = (int) Carbon::parse($time)->format('H');
        
        if ($hour >= 8 && $hour < 12) {
            return self::SLOT_MORNING;
        } elseif ($hour >= 12 && $hour < 18) {
            return self::SLOT_AFTERNOON;
        } elseif ($hour >= 18 || $hour < 8) {
            return self::SLOT_EVENING;
        }
        
        return null;
    }

    /**
     * Check if a date is weekend (Jumat, Sabtu, Minggu)
     * Based on price list: Weekend = Jumat-Minggu
     * 
     * @param string|Carbon $date
     * @return bool
     */
    public static function isWeekend($date): bool
    {
        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek;
        
        // 0 = Minggu, 5 = Jumat, 6 = Sabtu
        return in_array($dayOfWeek, [0, 5, 6]);
    }

    /**
     * Get price based on date (weekday or weekend)
     * 
     * @param string|Carbon $date
     * @return float
     */
    public function getPriceForDate($date): float
    {
        return self::isWeekend($date) 
            ? (float) $this->weekend_price 
            : (float) $this->weekday_price;
    }

    /**
     * Get formatted price for display
     * 
     * @param string|Carbon $date
     * @return string
     */
    public function getFormattedPriceForDate($date): string
    {
        return 'Rp ' . number_format($this->getPriceForDate($date), 0, ',', '.');
    }

    /**
     * Get formatted weekday price
     */
    public function getFormattedWeekdayPrice(): string
    {
        return 'Rp ' . number_format($this->weekday_price, 0, ',', '.');
    }

    /**
     * Get formatted weekend price
     */
    public function getFormattedWeekendPrice(): string
    {
        return 'Rp ' . number_format($this->weekend_price, 0, ',', '.');
    }

    /**
     * Get time slot label
     */
    public function getTimeSlotLabel(): string
    {
        $slots = self::getTimeSlots();
        return $slots[$this->time_slot]['label'] ?? $this->time_slot;
    }

    /**
     * Scope for active prices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
