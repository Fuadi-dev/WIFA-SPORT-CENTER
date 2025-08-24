<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FutsalBooking extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'payment',
    ];
}
