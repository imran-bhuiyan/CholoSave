<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    protected $table = 'leaderboard';

    protected $fillable = [
        'group_id',
        'points'
    ];

    protected $casts = [
        'points' => 'decimal:2'
    ];

    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }
} 