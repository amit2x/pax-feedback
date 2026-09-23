<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackAirport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_airports';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'city',
        'country',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function terminals()
    {
        return $this->hasMany(FeedbackTerminal::class, 'airport_id');
    }

    public function locations()
    {
        return $this->hasMany(FeedbackLocation::class, 'airport_id');
    }
}
