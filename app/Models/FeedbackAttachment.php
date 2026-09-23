<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackAttachment extends Model
{
    // use HasUuids;
    use HasFactory;

    protected $table = 'feedback_attachments';

    protected $fillable = [
        'uuid',
        'feedback_id', 'type',
        'original_name', 'stored_name', 'mime_type', 'extension',
        'size', 'storage_disk', 'storage_path', 'checksum',
        'duration_seconds', 'scan_status', 'processing_status',
    ];

    protected $casts = [
        'size' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
