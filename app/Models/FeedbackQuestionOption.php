<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackQuestionOption extends Model
{
    // use HasUuids;
    use HasFactory;

    protected $table = 'feedback_question_options';

    protected $fillable = ['uuid',
        'question_id', 'value', 'label_en', 'label_hi', 'label_bn',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function question()
    {
        return $this->belongsTo(FeedbackQuestion::class, 'question_id');
    }
}
