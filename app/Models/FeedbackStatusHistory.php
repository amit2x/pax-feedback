<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackStatusHistory extends Model
{
    protected $table = 'feedback_status_histories';

    protected $fillable = [
        'feedback_id', 'from_status', 'to_status', 'changed_by', 'note',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
