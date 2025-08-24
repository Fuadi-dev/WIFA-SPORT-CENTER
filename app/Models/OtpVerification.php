<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'password',
        'otp_code',
        'expires_at',
        'is_verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isValid($code)
    {
        return $this->otp_code === $code && !$this->isExpired() && !$this->is_verified;
    }
}
