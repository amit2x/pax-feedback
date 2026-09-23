<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackAssignment extends Model
{
    protected $table = 'feedback_assignments';

    protected $fillable = [
        'feedback_id', 'department_id', 'assigned_to', 'assigned_by',
        'note', 'assigned_at', 'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function department()
    {
        return $this->belongsTo(FeedbackDepartment::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
