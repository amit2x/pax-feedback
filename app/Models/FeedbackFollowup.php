<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackFollowup extends Model
{
    protected $table = 'feedback_followups';

    protected $fillable = [
        'feedback_id', 'user_id', 'channel', 'notes', 'contacted_at',
    ];

    protected $casts = ['contacted_at' => 'datetime'];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
