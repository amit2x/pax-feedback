<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Services\FeedbackSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FeedbackSubmitController extends Controller
{
    public function __construct(private FeedbackSubmissionService $service) {}

    public function submit(StoreFeedbackRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Attach session-derived values (never trust the client for the QR token)
        $validated['qr_token'] = session('feedback.qr_token');
        $validated['language_code'] = session('feedback.language_code', 'en');

        try {
            $feedback = $this->service->submit($validated);
        } catch (\Throwable $e) {
            Log::error('Feedback submission failed', [
                'submission_uuid' => $validated['submission_uuid'] ?? null,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'We could not save your feedback. Please try again.',
            ], 500);
        }

        // Clear wizard session so the next submission starts clean
        $this->clearWizardSession();

        return response()->json([
            'ok' => true,
            'reference' => $feedback->reference_no,
            'redirect' => route('feedback.thank-you', ['reference' => $feedback->reference_no]),
        ]);
    }

    public function thankYou(string $reference): View
    {
        return view('feedback.thank-you', [
            'reference' => $reference,
        ]);
    }

    private function clearWizardSession(): void
    {
        // Remove only our own namespace, leaving the admin session (if any) untouched
        session()->forget([
            'feedback.qr_token',
            'feedback.submission_uuid',
            'feedback.language_code',
            'feedback.category_id',
            'feedback.subcategory_id',
        ]);
    }
}
