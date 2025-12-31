<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code',
        'user_id',
        'sport_id',
        'court_id',
        'slug',
        'booking_date',
        'start_time',
        'end_time',
        'team_name',
        'notes',
        'payment_method',
        'total_price',
        'status',
        'is_last_minute_booking',
        'midtrans_snap_token',
        'midtrans_order_id',
        'confirmed_at',
        'paid_at',
        'auto_cancelled_at',
        'promo_code_id',
        'auto_promo_id',
        'discount_amount',
        'original_price'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'auto_cancelled_at' => 'datetime',
        'is_last_minute_booking' => 'boolean',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = $booking->generateBookingCode();
            }
            if (empty($booking->slug)) {
                $booking->slug = $booking->generateSlug();
            }
        });
    }

    private function generateBookingCode()
    {
        $lastBooking = static::latest('id')->first();
        $nextNumber = $lastBooking ? $lastBooking->id + 1 : 1;
        
        return 'WIFA-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateSlug()
    {
        $lastBooking = static::latest('id')->first();
        $nextNumber = $lastBooking ? $lastBooking->id + 1 : 1;
        
        // Generate slug format: wifa-booking-001-timestamp
        $timestamp = now()->format('Ymd');
        return 'wifa-booking-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '-' . $timestamp;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function autoPromo()
    {
        return $this->belongsTo(AutoPromo::class);
    }

    /**
     * Scope untuk booking yang pending confirmation dan harus di-auto cancel
     * Kriteria:
     * - Status pending_confirmation
     * - Payment method cash
     * - Bukan last minute booking
     * - Kurang dari 1 jam sebelum waktu booking
     */
    public function scopeEligibleForAutoCancel($query)
    {
        $oneHourFromNow = Carbon::now()->addHour();
        
        return $query->where('status', 'pending_confirmation')
            ->where('payment_method', 'cash')
            ->where('is_last_minute_booking', false)
            ->where(function ($q) use ($oneHourFromNow) {
                // Booking date sudah lewat
                $q->where('booking_date', '<', Carbon::today())
                    // Atau booking hari ini tapi start_time kurang dari 1 jam dari sekarang
                    ->orWhere(function ($q2) use ($oneHourFromNow) {
                        $q2->where('booking_date', Carbon::today())
                            ->whereRaw("CONCAT(booking_date, ' ', start_time) <= ?", [$oneHourFromNow->format('Y-m-d H:i:s')]);
                    });
            });
    }

    /**
     * Auto cancel booking ini
     */
    public function autoCancel(): bool
    {
        $this->status = 'cancelled';
        $this->auto_cancelled_at = Carbon::now();
        return $this->save();
    }

    /**
     * Cek apakah booking ini eligible untuk di-auto cancel
     */
    public function isEligibleForAutoCancel(): bool
    {
        if ($this->status !== 'pending_confirmation') {
            return false;
        }
        
        if ($this->payment_method !== 'cash') {
            return false;
        }
        
        if ($this->is_last_minute_booking) {
            return false;
        }
        
        $bookingDateTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s'));
        $oneHourFromNow = Carbon::now()->addHour();
        
        return $bookingDateTime->lte($oneHourFromNow);
    }

    /**
     * Get formatted status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending_payment' => 'Menunggu Pembayaran',
            'pending_confirmation' => 'Menunggu Konfirmasi Admin',
            'confirmed' => 'Terkonfirmasi',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Cek apakah booking dibatalkan otomatis oleh sistem
     */
    public function wasAutoCancelled(): bool
    {
        return $this->status === 'cancelled' && $this->auto_cancelled_at !== null;
    }
}
