<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'otp_expires_at',
    ];

    protected $dates = [
        'otp_expires_at',
    ];

    /**
     * Check if the OTP is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->otp_expires_at);
    }
}
