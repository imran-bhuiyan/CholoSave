<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyGroup extends Model
{
    use HasFactory;

    protected $primaryKey = 'group_id';
    public $timestamps = true;
    protected $table = 'my_group';

    protected $fillable = [
        'group_name',
        'description',
        'members',
        'group_admin_id',
        'dps_type',
        'time_period',
        'amount',
        'start_date',
        'bKash',
        'Rocket',
        'Nagad',
        'goal_amount',
        'emergency_fund'
    ];

    protected $casts = [
        'start_date' => 'date',
        'amount' => 'decimal:2',
        'emergency_fund' => 'decimal:2',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'group_admin_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_membership', 'group_id', 'user_id')
            ->withPivot('status', 'is_admin', 'leave_request', 'join_date', 'join_request_date', 'time_period_remaining');
    }

    public function memberships()
    {
        return $this->hasMany(GroupMembership::class, 'group_id', 'group_id');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class, 'group_id', 'group_id');
    }

    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function mySavings()
    {
        return $this->hasMany(MySavings::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'target_group_id');
    }

    public function paymentOtps()
    {
        return $this->hasMany(PaymentOtp::class);
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionInfo::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function payments()
    {
        return $this->hasMany(GroupPayment::class, 'group_id');
    }

    public function paymentSchedule()
    {
        return $this->hasMany(GroupPaymentSchedule::class, 'group_id');
    }

    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('is_admin', true)
            ->exists();
    }
} 