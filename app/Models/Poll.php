<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $primaryKey = 'poll_id';
    public $timestamps = true;
    protected $table = 'polls';

    protected $fillable = [
        'group_id',
        'poll_question',
        'status'
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(MyGroup::class, 'group_id');
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_id', 'poll_id');
    }
} 