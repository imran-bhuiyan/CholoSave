<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    use HasFactory;

    protected $table = 'loan_request';  // Specify the correct table name

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'group_id',
        'reason',
        'amount',
        'status',
        'return_time',
        'approve_date',
        'repayment_amount',
        'payment_method',
        'transaction_id',
        'payment_time',
        'repayment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'repayment_amount' => 'decimal:2',
        'return_time' => 'date',
        'approve_date' => 'date',
        'payment_time' => 'datetime',
        'repayment_date' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id');
    }
} 