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
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:feedback_locations,code'],
            'name' => ['required', 'string', 'max:120'],
            'airport_id' => ['required', 'exists:feedback_airports,id'],
            'terminal_id' => ['nullable', 'exists:feedback_terminals,id'],
            'zone_id' => ['nullable', 'exists:feedback_zones,id'],
            'service_id' => ['nullable', 'exists:feedback_services,id'],
            'checkpoint_label' => ['nullable', 'string', 'max:60'],
        ]);

        $loc = FeedbackLocation::create([
            'uuid' => (string) Str::uuid(),
            ...$validated,
            'is_active' => true,
        ]);

        $this->audit->log('location.created', $loc, [], $validated);

        return back()->with('status', 'Location created.');
    }

    public function update(Request $request, FeedbackLocation $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'checkpoint_label' => ['nullable', 'string', 'max:60'],
            'is_active' => ['boolean'],
        ]);

        $location->update($validated);
        $this->audit->log('location.updated', $location, [], $validated);

        return back()->with('status', 'Location updated.');
    }

    public function destroy(FeedbackLocation $location): RedirectResponse
    {
        $this->audit->log('location.deleted', $location);
        $location->delete();

        return back()->with('status', 'Location deleted.');
    }
}
