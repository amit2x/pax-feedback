<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackZone extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_zones';

    protected $fillable = ['uuid', 'terminal_id', 'code', 'name', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function terminal()
    {
        return $this->belongsTo(FeedbackTerminal::class, 'terminal_id');
    }
}
