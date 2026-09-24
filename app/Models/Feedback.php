<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    // use HasFactory, HasUuids, SoftDeletes;
    use HasFactory, SoftDeletes;

    protected $table = 'feedback';

    protected $fillable = [
        'uuid',
        'reference_no', 'submission_uuid',
        'airport_id', 'terminal_id', 'zone_id', 'location_id', 'service_id', 'qr_code_id',
        'language_code', 'feedback_type', 'overall_rating',
        'category_id', 'subcategory_id', 'department_id',
        'comment', 'is_anonymous',
        'name', 'mobile', 'email', 'preferred_contact_method',
        'flight_number', 'travel_date',
        'status', 'priority', 'submission_source', 'submitted_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'travel_date' => 'date',
        'submitted_at' => 'datetime',
        'analyzed_at' => 'datetime',
        'incident_created_at' => 'datetime',
    ];

    protected $hidden = [
        'id', 'uuid', 'submission_uuid', 'deleted_at',
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

    public function qrCode()
    {
        return $this->belongsTo(FeedbackQrCode::class, 'qr_code_id');
    }

    public function category()
    {
        return $this->belongsTo(FeedbackCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(FeedbackSubcategory::class, 'subcategory_id');
    }

    public function department()
    {
        return $this->belongsTo(FeedbackDepartment::class, 'department_id');
    }

    public function attachments()
    {
        return $this->hasMany(FeedbackAttachment::class);
    }

    public function answers()
    {
        return $this->hasMany(FeedbackAnswer::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(FeedbackStatusHistory::class);
    }

    public function assignments()
    {
        return $this->hasMany(FeedbackAssignment::class);
    }

    public function followups()
    {
        return $this->hasMany(FeedbackFollowup::class);
    }

    public function scopePendingAnalysis($query)
    {
        return $query->where('analysis_status', 'pending');
    }
}
