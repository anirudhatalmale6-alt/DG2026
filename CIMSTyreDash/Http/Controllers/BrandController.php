<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Brand;

class BrandController extends Controller
{
    /**
     * List all brands with pagination.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Brand::withCount('products');

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', $term)
                  ->orWhere('code', 'LIKE', $term)
                  ->orWhere('country', 'LIKE', $term);
            });
        }

        $brands = $query->ordered()->paginate(25)->withQueryString();

        return view('cimstyredash::brands.index', compact('brands'));
    }

    /**
     * Show the form to create a new brand.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cimstyredash::brands.create');
    }

    /**
     * Validate and save a new brand.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20|unique:cims_tyredash_brands,code',
            'logo_url'    => 'nullable|url|max:500',
            'country'     => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'sometimes|boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Brand::create($validated);

        return redirect()
            ->route('cimstyredash.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Show the form to edit an existing brand.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        return view('cimstyredash::brands.edit', compact('brand'));
    }

    /**
     * Validate and update an existing brand.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20|unique:cims_tyredash_brands,code,' . $brand->id,
            'logo_url'    => 'nullable|url|max:500',
            'country'     => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'sometimes|boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $brand->update($validated);

        return redirect()
            ->route('cimstyredash.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Soft delete a brand.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        // Prevent deletion if brand has active products
        if ($brand->products()->where('is_active', true)->exists()) {
            return redirect()
                ->route('cimstyredash.brands.index')
                ->with('error', 'Cannot delete brand with active products. Deactivate or reassign products first.');
        }

        $brand->delete();

        return redirect()
            ->route('cimstyredash.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    /**
     * Activate a brand.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function activate($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['is_active' => true]);

        return redirect()
            ->route('cimstyredash.brands.index')
            ->with('success', 'Brand activated successfully.');
    }

    /**
     * Deactivate a brand.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function deactivate($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['is_active' => false]);

        return redirect()
            ->route('cimstyredash.brands.index')
            ->with('success', 'Brand deactivated successfully.');
    }
}
