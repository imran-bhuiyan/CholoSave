<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $primaryKey = 'investment_id';

    protected $fillable = [
        'group_id',
        'amount',
        'investment_type',
        'ex_profit',
        'ex_return_date',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'ex_profit' => 'double',
        'ex_return_date' => 'date'
    ];

    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }

    public function returns()
    {
        return $this->hasMany(InvestmentReturn::class, 'investment_id');
    }
} 