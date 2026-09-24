<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FeedbackCategory;
use App\Models\FeedbackSubcategory;
use App\Services\QrTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackWizardController extends Controller
{
    public function __construct(private QrTokenService $qrTokens) {}

    public function welcome()
    {
        return view('feedback.welcome', [
            'qr' => null,
            'location' => null,
        ]);
    }

    public function fromQr(string $token)
    {
        $qr = $this->qrTokens->resolve($token);
        abort_unless($qr && $qr->isUsable(), 404);

        session(['feedback.qr_token' => $token]);

        return view('feedback.welcome', [
            'qr' => $qr,
            'location' => $qr->location?->load(['terminal', 'zone', 'service']),
        ]);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'language_code' => ['required', 'in:en,hi,bn'],
        ]);

        session(['feedback.language_code' => $validated['language_code']]);

        app()->setLocale($validated['language_code']);

        return redirect()->route('feedback.step', ['step' => 'rating']);
    }

    /* public function fromQr(string $token)
    {
        $qr = $this->qrTokens->resolve($token);
        abort_unless($qr && $qr->isUsable(), 404);

        session(['feedback.qr_token' => $token]);
        session(['feedback.submission_uuid' => (string) Str::uuid()]);

        return view('feedback.welcome', [
            'qr' => $qr,
            'location' => $qr->location?->load(['terminal', 'zone', 'service']),
        ]);
    } */

  /*   public function start(Request $request)
    {
        $validated = $request->validate([
            'language_code' => ['required', 'in:en,hi,bn'],
        ]);

        session(['feedback.language_code' => $validated['language_code']]);

        if (! session()->has('feedback.submission_uuid')) {
            session(['feedback.submission_uuid' => (string) Str::uuid()]);
        }

        app()->setLocale($validated['language_code']);

        return redirect()->route('feedback.step', ['step' => 'rating']);
    } */

    public function step(Request $request, string $step)
    {
        $allowed = ['rating', 'type', 'category', 'comment', 'voice', 'photo', 'contact'];
        abort_unless(in_array($step, $allowed, true), 404);

        if (! session()->has('feedback.language_code')) {
            return redirect()->route('feedback.welcome');
        }

        app()->setLocale(session('feedback.language_code', 'en'));

        $categories = FeedbackCategory::query()->active()->get();
        $qr = null;
        if (session()->has('feedback.qr_token')) {
            $qr = $this->qrTokens->resolve(session('feedback.qr_token'));
        }

        return view('feedback.steps.rating', [
            'categories' => $categories,
            'qr' => $qr,
        ]);
    }

    public function subcategories(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:feedback_categories,id'],
        ]);

        $subs = FeedbackSubcategory::query()
            ->where('category_id', $validated['category_id'])
            ->active()
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->localizedName(),
            ]);

        return response()->json($subs);
    }
}
