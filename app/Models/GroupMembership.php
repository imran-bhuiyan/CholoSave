<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMembership extends Model
{
    use HasFactory;

    protected $primaryKey = 'membership_id';
    public $timestamps = true;
    protected $table = 'group_membership';

    protected $fillable = [
        'group_id',
        'user_id',
        'status',
        'is_admin',
        'leave_request',
        'join_date',
        'join_request_date',
        'time_period_remaining'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'join_date' => 'date',
        'join_request_date' => 'date',
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 