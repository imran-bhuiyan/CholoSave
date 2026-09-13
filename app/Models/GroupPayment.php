<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
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