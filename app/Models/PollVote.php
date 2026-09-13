<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $primaryKey = 'vote_id';
    public $timestamps = true;
    protected $table = 'polls_vote';

    protected $fillable = [
        'poll_id',
        'user_id',
        'vote_option'
    ];

    // Relationships
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 