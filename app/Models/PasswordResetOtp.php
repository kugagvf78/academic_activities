<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'is_used'];
    
    public $timestamps = false;
    
    protected $dates = ['created_at', 'expires_at'];

    public function isValid()
    {
        return !$this->is_used && $this->expires_at > now();
    }

    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }

    public static function clearExpired()
    {
        static::where('expires_at', '<', now())->delete();
    }
}