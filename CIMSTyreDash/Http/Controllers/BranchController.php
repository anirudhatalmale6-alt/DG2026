<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Branch;

class BranchController extends Controller
{
    /**
     * List all branches with pagination.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Branch::withCount(['stockRecords', 'quotes', 'jobCards']);

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', $term)
                  ->orWhere('code', 'LIKE', $term)
                  ->orWhere('city', 'LIKE', $term)
                  ->orWhere('manager_name', 'LIKE', $term);
            });
        }

        $branches = $query->orderBy('name')->paginate(25)->withQueryString();

        return view('cimstyredash::branches.index', compact('branches'));
    }

    /**
     * Show the form to create a new branch.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cimstyredash::branches.create');
    }

    /**
     * Validate and save a new branch.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:20|unique:cims_tyredash_branches,code',
            'address'      => 'nullable|string|max:500',
            'city'         => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:200',
            'manager_name' => 'nullable|string|max:100',
            'is_active'    => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Branch::create($validated);

        return redirect()
            ->route('cimstyredash.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form to edit an existing branch.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        return view('cimstyredash::branches.edit', compact('branch'));
    }

    /**
     * Validate and update an existing branch.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:20|unique:cims_tyredash_branches,code,' . $branch->id,
            'address'      => 'nullable|string|max:500',
            'city'         => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:200',
            'manager_name' => 'nullable|string|max:100',
            'is_active'    => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()
            ->route('cimstyredash.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Soft delete a branch.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $branch = Branch::withCount(['stockRecords', 'quotes', 'jobCards'])->findOrFail($id);

        // Prevent deletion if branch has related data
        if ($branch->stock_records_count > 0 || $branch->quotes_count > 0 || $branch->job_cards_count > 0) {
            return redirect()
                ->route('cimstyredash.branches.index')
                ->with('error', 'Cannot delete branch with existing stock records, quotes, or job cards. Deactivate it instead.');
        }

        $branch->delete();

        return redirect()
            ->route('cimstyredash.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    /**
     * Activate a branch.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function activate($id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => true]);

        return redirect()
            ->route('cimstyredash.branches.index')
            ->with('success', 'Branch activated successfully.');
    }

    /**
     * Deactivate a branch.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function deactivate($id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => false]);

        return redirect()
            ->route('cimstyredash.branches.index')
            ->with('success', 'Branch deactivated successfully.');
    }
}
