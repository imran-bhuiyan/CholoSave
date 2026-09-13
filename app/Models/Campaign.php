<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'deadline',
        'status',
        'bKash',
        'Rocket',
        'Nagad',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function latestActive()
    {
        return self::active()->orderBy('created_at', 'desc')->first();
    }
} 