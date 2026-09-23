<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeedbackSubmissionService
{
    public function __construct(
        private ReferenceNumberService $references,
        private QrTokenService $qrTokens,
        private AttachmentService $attachments,
    ) {}

    public function submit(array $data): Feedback
    {
        // Idempotency: same submission_uuid → return existing row.
        $existing = Feedback::where('submission_uuid', $data['submission_uuid'])->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($data) {
            $qr = null;
            if (! empty($data['qr_token'])) {
                $qr = $this->qrTokens->resolve($data['qr_token']);
                if (! $qr || ! $qr->isUsable()) {
                    abort(422, 'Invalid or expired QR code.');
                }
            }

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

            FeedbackStatusHistory::create([
                'feedback_id' => $feedback->id,
                'from_status' => null,
                'to_status' => 'submitted',
                'changed_by' => null,
                'note' => 'Submitted by passenger',
            ]);

            if (! empty($data['voice'])) {
                $this->attachments->store($feedback, $data['voice'], 'voice');
            }

            foreach ($data['photos'] ?? [] as $photo) {
                $this->attachments->store($feedback, $photo, 'photo');
            }

            if ($qr) {
                $qr->increment('usage_count');
                $qr->update(['last_used_at' => now()]);
            }

            return $feedback;
        });
    }
}
