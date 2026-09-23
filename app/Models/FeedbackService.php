<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackService extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_services';

    protected $fillable = ['uuid', 'code', 'name', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
