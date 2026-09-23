<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackLocation extends Model
{
    // use HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_locations';

    protected $fillable = [
        'uuid',
        'airport_id', 'terminal_id', 'zone_id', 'service_id',
        'code', 'name', 'description', 'checkpoint_label',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function airport()
    {
        return $this->belongsTo(FeedbackAirport::class, 'airport_id');
    }

    public function terminal()
    {
        return $this->belongsTo(FeedbackTerminal::class, 'terminal_id');
    }

    public function zone()
    {
        return $this->belongsTo(FeedbackZone::class, 'zone_id');
    }

    public function service()
    {
        return $this->belongsTo(FeedbackService::class, 'service_id');
    }
}
