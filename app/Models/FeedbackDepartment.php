<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackDepartment extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_departments';

    protected $fillable = [
        'uuid', 'airport_id',
        'code', 'name', 'email', 'contact_person', 'contact_phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function categories()
    {
        return $this->hasMany(FeedbackCategory::class, 'default_department_id');
    }
}
