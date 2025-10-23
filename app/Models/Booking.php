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
        'midtrans_snap_token',
        'midtrans_order_id',
        'confirmed_at',
        'paid_at',
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
        'paid_at' => 'datetime'
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
}
