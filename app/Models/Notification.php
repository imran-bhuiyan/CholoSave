<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    public $timestamps = true;

    protected $fillable = [
        'target_user_id',
        'target_group_id',
        'title',
        'message',
        'status',
        'type'
    ];

    // Relationships
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function targetGroup()
    {
        return $this->belongsTo(MyGroup::class, 'target_group_id');
    }
} 