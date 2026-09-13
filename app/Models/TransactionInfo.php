<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionInfo extends Model
{
    use HasFactory;

    protected $table = 'transaction_info';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'group_id',
        'amount',
        'transaction_id',
        'payment_method',
        'payment_time'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }
} 