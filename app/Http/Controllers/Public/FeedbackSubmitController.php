<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Services\FeedbackSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FeedbackSubmitController extends Controller
{
    public function __construct(private FeedbackSubmissionService $service) {}

    public function submit(StoreFeedbackRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Inject server-side session values. NEVER trust the client for these.
        $validated['submission_uuid'] = session('feedback.submission_uuid');
        $validated['qr_token'] = session('feedback.qr_token');
        $validated['language_code'] = session('feedback.language_code', 'en');

        if (! $validated['submission_uuid']) {
            return response()->json([
                'message' => 'Session expired. Please start again.',
            ], 419);
        }

        $feedback = $this->service->submit($validated);

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
}
