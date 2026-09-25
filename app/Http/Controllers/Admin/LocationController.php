<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackAirport;
use App\Models\FeedbackLocation;
use App\Models\FeedbackService;
use App\Models\FeedbackTerminal;
use App\Models\FeedbackZone;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    // public function index(): View
    // {
    //     $locations = FeedbackLocation::with(['airport', 'terminal', 'zone', 'service'])
    //         ->orderBy('sort_order')
    //         ->orderBy('name')
    //         ->get();

    //     return view('admin.locations.index', compact('locations'));
    // }

    public function index(): View
    {
        return view('admin.locations.index', [
            'locations' => FeedbackLocation::with(['airport', 'terminal', 'zone', 'service'])
                ->orderBy('code')->get(),
            'airports' => FeedbackAirport::orderBy('name')->get(),
            'terminals' => FeedbackTerminal::orderBy('name')->get(),
            'zones' => FeedbackZone::orderBy('name')->get(),
            'services' => FeedbackService::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.locations.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLocation($request);

        $location = FeedbackLocation::create([
            'uuid' => (string) Str::uuid(),
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('location.created', $location, [], $location->toArray());

        return redirect()->route('admin.locations.index')
            ->with('status', 'Location created successfully.');
    }

    public function edit(FeedbackLocation $location): View
    {
        return view('admin.locations.edit', array_merge(
            $this->formData(),
            compact('location')
        ));
    }

    public function update(Request $request, FeedbackLocation $location): RedirectResponse
    {
        $validated = $this->validateLocation($request, $location->id);
        $old = $location->toArray();

        $location->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('location.updated', $location, $old, $location->fresh()->toArray());

        return redirect()->route('admin.locations.index')
            ->with('status', 'Location updated successfully.');
    }

    public function destroy(FeedbackLocation $location): RedirectResponse
    {
        if ($location->qrCodes()->exists()) {
            return back()->withErrors([
                'location' => 'Cannot delete: this location has QR codes assigned. Disable or reassign them first.',
            ]);
        }

        $this->audit->log('location.deleted', $location, $location->toArray(), []);
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('status', 'Location deleted.');
    }

    private function formData(): array
    {
        return [
            'airports' => FeedbackAirport::orderBy('name')->get(),
            'terminals' => FeedbackTerminal::orderBy('sort_order')->get(),
            'zones' => FeedbackZone::orderBy('sort_order')->get(),
            'services' => FeedbackService::orderBy('name')->get(),
        ];
    }

    private function validateLocation(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'airport_id' => ['required', 'integer', 'exists:feedback_airports,id'],
            'terminal_id' => ['nullable', 'integer', 'exists:feedback_terminals,id'],
            'zone_id' => ['nullable', 'integer', 'exists:feedback_zones,id'],
            'service_id' => ['nullable', 'integer', 'exists:feedback_services,id'],
            'code' => [
                'required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('feedback_locations', 'code')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'checkpoint_label' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'between:0,9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
