<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Laravel\Sanctum\HasApiTokens;

class PasswordReset extends Eloquent
{
    use HasApiTokens, HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'password_resets';

    protected $fillable = ['email','otp','expires_at'];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
