<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Service;

class ServiceController extends Controller
{
    /**
     * List all services with pagination.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', $term)
                  ->orWhere('code', 'LIKE', $term)
                  ->orWhere('description', 'LIKE', $term);
            });
        }

        $services = $query->ordered()->paginate(25)->withQueryString();

        return view('cimstyredash::services.index', compact('services'));
    }

    /**
     * Show the form to create a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cimstyredash::services.create');
    }

    /**
     * Validate and save a new service.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'code'           => 'required|string|max:20|unique:cims_tyredash_services,code',
            'description'    => 'nullable|string|max:500',
            'price'          => 'required|numeric|min:0',
            'price_per_tyre' => 'sometimes|boolean',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $validated['is_active']      = $request->boolean('is_active', true);
        $validated['price_per_tyre'] = $request->boolean('price_per_tyre', false);
        $validated['sort_order']     = $validated['sort_order'] ?? 0;

        Service::create($validated);

        return redirect()
            ->route('cimstyredash.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show the form to edit an existing service.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);

        return view('cimstyredash::services.edit', compact('service'));
    }

    /**
     * Validate and update an existing service.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'code'           => 'required|string|max:20|unique:cims_tyredash_services,code,' . $service->id,
            'description'    => 'nullable|string|max:500',
            'price'          => 'required|numeric|min:0',
            'price_per_tyre' => 'sometimes|boolean',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $validated['is_active']      = $request->boolean('is_active', true);
        $validated['price_per_tyre'] = $request->boolean('price_per_tyre', false);
        $validated['sort_order']     = $validated['sort_order'] ?? 0;

        $service->update($validated);

        return redirect()
            ->route('cimstyredash.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Soft delete a service.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()
            ->route('cimstyredash.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Activate a service.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function activate($id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => true]);

        return redirect()
            ->route('cimstyredash.services.index')
            ->with('success', 'Service activated successfully.');
    }

    /**
     * Deactivate a service.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function deactivate($id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => false]);

        return redirect()
            ->route('cimstyredash.services.index')
            ->with('success', 'Service deactivated successfully.');
    }
}
