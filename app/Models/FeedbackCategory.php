<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackCategory extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_categories';

    protected $fillable = [
        'uuid', 'code', 'name_en', 'name_hi', 'name_bn', 'icon',
        'default_department_id', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subcategories()
    {
        return $this->hasMany(FeedbackSubcategory::class, 'category_id');
    }

    public function defaultDepartment()
    {
        return $this->belongsTo(FeedbackDepartment::class, 'default_department_id');
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
