<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'email',
        'otp_hash',
        'expiry'
    ];

    protected $casts = [
        'expiry' => 'datetime',
    ];
} 