<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'amount',
        'date',
        'status'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 