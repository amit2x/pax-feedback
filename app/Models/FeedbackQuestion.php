<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackQuestion extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_questions';

    protected $fillable = ['uuid',
        'category_id', 'subcategory_id', 'type',
        'question_en', 'question_hi', 'question_bn',
        'is_required', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(FeedbackQuestionOption::class, 'question_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
