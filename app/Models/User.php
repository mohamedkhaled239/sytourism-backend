<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'phone',
        'country',
        'user_type',
        'is_approved',
        'approved_at',
        'password',
        'email_verification_code',
        'email_verification_code_expires',
        'email_verified_at',
        'last_login_at',
        'notifications_enabled',
        'fcm_token',
        'onesignal_player_id',
        'reset_password_token',
        'reset_password_expires',
        'login_otp',
        'login_otp_expires'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'reset_password_token',
        'login_otp'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'email_verification_code_expires' => 'datetime',
        'last_login_at' => 'datetime',
        'reset_password_expires' => 'datetime',
        'login_otp_expires' => 'datetime',
        'notifications_enabled' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function favoriteEvents()
    {
        return $this->belongsToMany(Event::class, 'user_favorite_events');
    }

    public function favoriteLocations()
    {
        return $this->belongsToMany(Location::class, 'user_favorite_locations');
    }

    public function isInvestor()
    {
        return $this->user_type === 'investor';
    }

    public function isApprovedInvestor()
    {
        return $this->isInvestor() && $this->is_approved;
    }

    public function isTourist()
    {
        return $this->user_type === 'tourist';
    }
}
