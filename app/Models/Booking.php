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
        'paid_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = $booking->generateBookingCode();
            }
        });
    }

    private function generateBookingCode()
    {
        $lastBooking = static::latest('id')->first();
        $nextNumber = $lastBooking ? $lastBooking->id + 1 : 1;
        
        return 'WIFA-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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
}
