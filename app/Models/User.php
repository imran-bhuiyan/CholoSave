<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'profile_picture',
        'otp',
        'otp_expiry',
        'email_verified_at',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expiry'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expiry' => 'datetime',
            'role' => 'string'
        ];

    /**
     * Get the groups that the user belongs to.
     */
    public function groups()
    {
        return $this->belongsToMany(MyGroup::class, 'group_membership', 'user_id', 'group_id')
            ->withPivot('status', 'is_admin', 'leave_request', 'join_date', 'join_request_date', 'time_period_remaining');
    }

    /**
     * Get the groups where the user is an admin.
     */
    public function adminGroups()
    {
        return $this->hasMany(MyGroup::class, 'group_admin_id');
    }

    /**
     * Get the user's savings records.
     */
    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    /**
     * Get the user's personal savings records.
     */
    public function mySavings()
    {
        return $this->hasMany(MySavings::class);
    }

    /**
     * Get the user's loan requests.
     */
    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }

    /**
     * Get the user's loan repayments.
     */
    public function loanRepayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }

    /**
     * Get the user's withdrawal requests.
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the user's messages.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'target_user_id');
    }

    /**
     * Get the user's payment OTPs.
     */
    public function paymentOtps()
    {
        return $this->hasMany(PaymentOtp::class);
    }

    /**
     * Get the user's poll votes.
     */
    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Get the user's transactions.
     */
    public function transactions()
    {
        return $this->hasMany(TransactionInfo::class);
    }
}
