<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackAirport;
use App\Models\FeedbackLocation;
use App\Models\FeedbackQrCode;
use App\Models\FeedbackService;
use App\Models\FeedbackTerminal;
use App\Models\FeedbackZone;
use App\Services\AuditLogger;
use App\Services\QrImageService;
use App\Services\QrTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class QrCodeController extends Controller
{
    public function __construct(
        private AuditLogger $audit,
        private QrTokenService $tokens,
        private QrImageService $qrImage,
    ) {}

    // ================================================================
    // LIST
    // ================================================================
    public function index(): View
    {
        return view('admin.qr.index', [
            'qrCodes' => FeedbackQrCode::with(['location', 'terminal', 'service'])
                ->latest()
                ->get(),
        ]);
    }

    // ================================================================
    // CREATE FORM
    // ================================================================
    public function create(): View
    {

        return view('admin.qr.create', [
            'airports' => FeedbackAirport::orderBy('name')->get(),
            'terminals' => FeedbackTerminal::orderBy('name')->get(),
            'zones' => FeedbackZone::orderBy('name')->get(),
            'locations' => FeedbackLocation::orderBy('name')->get(),
            'services' => FeedbackService::orderBy('name')->get(),
        ]);
    }

    // ================================================================
    // STORE
    // ================================================================
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:location,generic'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'airport_id' => ['nullable', 'exists:feedback_airports,id'],
            'terminal_id' => ['nullable', 'exists:feedback_terminals,id'],
            'zone_id' => ['nullable', 'exists:feedback_zones,id'],
            'location_id' => ['nullable', 'exists:feedback_locations,id'],
            'service_id' => ['nullable', 'exists:feedback_services,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        if ($validated['type'] === 'location' && empty($validated['location_id'])) {
            return back()->withErrors([
                'location_id' => 'Location is required for location-type QR codes.',
            ]);
        }

        $token = $this->tokens->generate();

        $qr = FeedbackQrCode::create([
            'uuid' => (string) Str::uuid(),
            'token_hash' => $token['hash'],
            'token_encrypted' => $token['plain'],   // cast encrypts on save
            'token_prefix' => $token['prefix'],
            'type' => $validated['type'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'airport_id' => $validated['airport_id'] ?? null,
            'terminal_id' => $validated['terminal_id'] ?? null,
            'zone_id' => $validated['zone_id'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'service_id' => $validated['service_id'] ?? null,
            'status' => 'active',
            'expires_at' => $validated['expires_at'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $this->audit->log('qr.created', $qr, [], [
            'name' => $qr->name,
            'type' => $qr->type,
        ]);

        return redirect()
            ->route('admin.qr.show', $qr->uuid)
            ->with('status', 'QR code created. Preview and download it below.');
    }

    // ================================================================
    // SHOW DETAIL + INLINE PREVIEW
    // ================================================================
    public function show(FeedbackQrCode $qrCode): View
    {
        $qrCode->load(['location', 'terminal', 'service']);

        $plain = $qrCode->plainToken();
        $feedbackUrl = null;
        $inlineImage = null;
        $hasToken = $plain !== null;

        if ($hasToken) {
            $feedbackUrl = $this->tokens->publicUrl($plain);

            // Build an inline data URI — no HTTP round-trip, no CSP issues.
            try {
                $inlineImage = $this->qrImage->dataUri(
                    $feedbackUrl,
                    400,
                    $this->defaultLogoPath()
                );
            } catch (Throwable $e) {
                Log::error('QR inline preview generation failed', [
                    'qr_uuid' => $qrCode->uuid,
                    'error' => $e->getMessage(),
                ]);
                $inlineImage = null;
            }
        }

        return view('admin.qr.show', [
            'qrCode' => $qrCode,
            'feedbackUrl' => $feedbackUrl,
            'inlineImage' => $inlineImage,
            'hasToken' => $hasToken,
        ]);
    }

    // ================================================================
    // INLINE PNG (for <img src="...">)
    // ================================================================
    public function image(Request $request, FeedbackQrCode $qrCode): Response
    {
        $plain = $qrCode->plainToken();
        abort_if($plain === null, 404, 'Plaintext token unavailable.');

        $size = (int) $request->integer('size', 400);
        $size = max(200, min($size, 1200));

        $withLogo = $request->boolean('logo', true);
        $logoPath = $withLogo ? $this->defaultLogoPath() : null;

        $png = $this->qrImage->png(
            $this->tokens->publicUrl($plain),
            $size,
            $logoPath
        );

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Length' => (string) strlen($png),
            'Cache-Control' => 'private, max-age=60',
        ]);
    }

    // ================================================================
    // DOWNLOAD PNG
    // ================================================================
    public function download(Request $request, FeedbackQrCode $qrCode): Response|RedirectResponse
    {
        $plain = $qrCode->plainToken();

        if ($plain === null) {
            return back()->withErrors([
                'qr' => 'This QR code has no stored token. '
                      .'Regenerate it to enable downloads.',
            ]);
        }

        $size = (int) $request->integer('size', 800);
        $size = max(200, min($size, 2000));

        $withLogo = $request->boolean('logo', true);
        $logoPath = $withLogo ? $this->defaultLogoPath() : null;

        try {
            $png = $this->qrImage->png(
                $this->tokens->publicUrl($plain),
                $size,
                $logoPath
            );
        } catch (Throwable $e) {
            Log::error('QR download failed', [
                'qr_uuid' => $qrCode->uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'qr' => 'Could not generate the QR image. Check server logs.',
            ]);
        }

        $filename = 'qr-'.Str::slug($qrCode->name).'-'.$size.'px.png';

        $this->audit->log('qr.downloaded', $qrCode, [], [
            'size' => $size,
            'with_logo' => $withLogo,
        ]);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Length' => (string) strlen($png),
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    // ================================================================
    // REGENERATE
    // ================================================================
    public function regenerate(FeedbackQrCode $qrCode): RedirectResponse
    {
        $token = $this->tokens->generate();

        $qrCode->update([
            'token_hash' => $token['hash'],
            'token_encrypted' => $token['plain'],   // cast encrypts on save
            'token_prefix' => $token['prefix'],
            'status' => 'active',
            'updated_by' => auth()->id(),
        ]);

        $this->audit->log('qr.regenerated', $qrCode);

        return redirect()
            ->route('admin.qr.show', $qrCode->uuid)
            ->with('status', 'QR code regenerated. The old printed code will no longer work.');
    }

    // ================================================================
    // STATUS
    // ================================================================
    public function updateStatus(Request $request, FeedbackQrCode $qrCode): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive,expired,compromised'],
        ]);

        $old = $qrCode->status;

        $qrCode->update([
            'status' => $validated['status'],
            'updated_by' => auth()->id(),
        ]);

        $this->audit->log('qr.status_changed', $qrCode,
            ['status' => $old],
            ['status' => $validated['status']]
        );

        return back()->with('status', 'QR status updated.');
    }

    // ================================================================
    // DELETE
    // ================================================================
    public function destroy(FeedbackQrCode $qrCode): RedirectResponse
    {
        $this->audit->log('qr.deleted', $qrCode);
        $qrCode->delete();

        return back()->with('status', 'QR code deleted.');
    }

    // ================================================================
    // HELPERS
    // ================================================================
    private function defaultLogoPath(): ?string
    {
        $path = public_path('images/aai-logo.png');

        return is_readable($path) ? $path : null;
    }
}
