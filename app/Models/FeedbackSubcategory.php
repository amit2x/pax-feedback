<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackSubcategory extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_subcategories';

    protected $fillable = ['uuid',
        'category_id', 'code', 'name_en', 'name_hi', 'name_bn',
        'default_department_id', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(FeedbackCategory::class, 'category_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $field = "name_{$locale}";

        return $this->{$field} ?: $this->name_en;
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}
