<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackTerminal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_terminals';

    protected $fillable = [
        'uuid',
        'airport_id',
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function airport()
    {
        return $this->belongsTo(FeedbackAirport::class, 'airport_id');
    }

    public function zones()
    {
        return $this->hasMany(FeedbackZone::class, 'terminal_id');
    }

    public function locations()
    {
        return $this->hasMany(FeedbackLocation::class, 'terminal_id');
    }
}
