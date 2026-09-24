<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FeedbackSubmissionService
{
    public function __construct(
        private ReferenceNumberService $references,
        private QrTokenService $qrTokens,
        private AttachmentService $attachments,
    ) {}

    /**
     * Idempotent submission.
     *
     * The same submission_uuid from two rapid requests returns the SAME
     * Feedback row. Different submission_uuids create distinct rows, even
     * if the rest of the payload is identical.
     */
    public function submit(array $data): Feedback
    {
        // --- Fast path: same submission_uuid already persisted ---
        $existing = Feedback::where('submission_uuid', $data['submission_uuid'])->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($data) {
            // Re-check inside the transaction to handle race conditions
            $existing = Feedback::where('submission_uuid', $data['submission_uuid'])->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            // Resolve QR (if any) — never trust client-supplied location
            $qr = null;
            if (! empty($data['qr_token'])) {
                $qr = $this->qrTokens->resolve($data['qr_token']);
                if (! $qr || ! $qr->isUsable()) {
                    abort(422, 'Invalid or expired QR code.');
                }
            }

            // Create the Feedback row
            $feedback = Feedback::create([
                'uuid' => (string) Str::uuid(),
                'reference_no' => $this->references->next(),
                'submission_uuid' => $data['submission_uuid'],
                'qr_code_id' => $qr?->id,
                'airport_id' => $qr?->airport_id,
                'terminal_id' => $qr?->terminal_id,
                'zone_id' => $qr?->zone_id,
                'location_id' => $qr?->location_id,
                'service_id' => $qr?->service_id,
                'language_code' => $data['language_code'] ?? 'en',
                'feedback_type' => $data['feedback_type'],
                'overall_rating' => $data['overall_rating'],
                'category_id' => $data['category_id'] ?? null,
                'subcategory_id' => $data['subcategory_id'] ?? null,
                'comment' => $data['comment'] ?? null,
                'is_anonymous' => (bool) ($data['is_anonymous'] ?? true),
                'name' => ! empty($data['is_anonymous']) ? null : ($data['name'] ?? null),
                'mobile' => ! empty($data['is_anonymous']) ? null : ($data['mobile'] ?? null),
                'email' => ! empty($data['is_anonymous']) ? null : ($data['email'] ?? null),
                'preferred_contact_method' => ! empty($data['is_anonymous'])
                    ? 'none'
                    : ($data['preferred_contact_method'] ?? 'none'),
                'flight_number' => $data['flight_number'] ?? null,
                'travel_date' => $data['travel_date'] ?? null,
                'status' => 'submitted',
                'priority' => 'medium',
                'submission_source' => $qr ? 'qr' : 'generic',
                'submitted_at' => now(),
            ]);

            // Status history
            FeedbackStatusHistory::create([
                'feedback_id' => $feedback->id,
                'from_status' => null,
                'to_status' => 'submitted',
                'changed_by' => null,
                'note' => 'Submitted by passenger',
            ]);

            // Attachments — fail the whole submission if any fails
            if (! empty($data['voice']) && $data['voice'] instanceof \Illuminate\Http\UploadedFile) {
                $this->attachments->store($feedback, $data['voice'], 'voice');
            }

            if (! empty($data['photos']) && is_array($data['photos'])) {
                foreach ($data['photos'] as $photo) {
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        $this->attachments->store($feedback, $photo, 'photo');
                    }
                }
            }

            // QR usage counter
            if ($qr) {
                $qr->increment('usage_count');
                $qr->update(['last_used_at' => now()]);
            }

            return $feedback;
        });
    }
}
