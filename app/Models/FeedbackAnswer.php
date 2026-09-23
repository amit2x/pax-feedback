<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackAnswer extends Model
{
    // use HasUuids;
    use HasFactory;

    protected $table = 'feedback_answers';

    protected $fillable = [
        'uuid', 'feedback_id', 'question_id', 'question_option_id', 'answer_text',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function question()
    {
        return $this->belongsTo(FeedbackQuestion::class);
    }

    public function questionOption()
    {
        return $this->belongsTo(FeedbackQuestionOption::class);
    }
}
