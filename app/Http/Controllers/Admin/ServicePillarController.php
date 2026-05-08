<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServicePillarRequest;
use App\Models\ServicePillar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ServicePillarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $servicePillars = ServicePillar::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.service-pillars.index', compact('servicePillars'));
    }

    public function create()
    {
        return view('admin.service-pillars.create');
    }

    public function store(ServicePillarRequest $request)
    {
        $validated = $this->normalizePayload($request->validated());

        ServicePillar::create($validated);
        $this->clearSiteCache(['service_pillars']);

        Alert::success('Success', 'Service pillar created successfully.');

        return redirect()->route('admin.service-pillars.index');
    }

    public function show(ServicePillar $servicePillar)
    {
        return redirect()->route('admin.service-pillars.edit', $servicePillar);
    }

    public function edit(ServicePillar $servicePillar)
    {
        return view('admin.service-pillars.edit', compact('servicePillar'));
    }

    public function update(ServicePillarRequest $request, ServicePillar $servicePillar)
    {
        $validated = $this->normalizePayload($request->validated(), $servicePillar);

        $servicePillar->update($validated);
        $this->clearSiteCache(['service_pillars']);

        Alert::success('Success', 'Service pillar updated successfully.');

        return redirect()->route('admin.service-pillars.index');
    }

    public function destroy(ServicePillar $servicePillar)
    {
        $servicePillar->delete();
        $this->clearSiteCache(['service_pillars']);

        Alert::success('Success', 'Service pillar removed successfully.');

        return back();
    }

    public function toggleStatus(ServicePillar $servicePillar)
    {
        $newStatus = $servicePillar->status === 'active' ? 'inactive' : 'active';

        $servicePillar->update(['status' => $newStatus]);
        $this->clearSiteCache(['service_pillars']);

        Alert::success('Success', "Service pillar marked as {$newStatus}.");

        return back();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizePayload(array $validated, ?ServicePillar $servicePillar = null): array
    {
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['bullets'] = collect($validated['bullets'] ?? [])
            ->filter(fn (?string $bullet): bool => filled($bullet))
            ->values()
            ->all();

        if ($servicePillar && $validated['slug'] === $servicePillar->slug) {
            return $validated;
        }

        $baseSlug = $validated['slug'];
        $slug = $baseSlug;
        $counter = 2;

        while (ServicePillar::where('slug', $slug)
            ->when($servicePillar, fn ($query) => $query->whereKeyNot($servicePillar->id))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['slug'] = $slug;

        return $validated;
    }
}
