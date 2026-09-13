<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'description',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_us';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    // Status constants for easier use
    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';

    /**
     * Scope a query to only include pending contacts.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include completed contacts.
     */
    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    /**
     * Get the human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }
}
