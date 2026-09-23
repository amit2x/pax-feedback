<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackQrCode extends Model
{
    // use HasFactory, HasUuids;
    use HasFactory;

    protected $table = 'feedback_qr_codes';

    protected $fillable = [
        'uuid', 'code', 'token',
        'token_hash', 'token_prefix', 'type', 'name', 'description',
        'airport_id', 'terminal_id', 'zone_id', 'location_id', 'service_id',
        'status', 'usage_count', 'last_used_at', 'expires_at',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'usage_count' => 'integer',
    ];

    protected $hidden = [
        'id', 'token_hash',
    ];

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

    public function location()
    {
        return $this->belongsTo(FeedbackLocation::class, 'location_id');
    }

    public function service()
    {
        return $this->belongsTo(FeedbackService::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function isUsable(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
