<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Customer;

class CustomerController extends Controller
{
    /**
     * List all customers with search and pagination.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Customer::withCount(['vehicles', 'quotes', 'jobCards']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('customer_type')) {
            $query->ofType($request->input('customer_type'));
        }

        if ($request->input('active_only') === '1') {
            $query->active();
        }

        $customers     = $query->latest()->paginate(25)->withQueryString();
        $customerTypes = Customer::getCustomerTypes();

        return view('cimstyredash::customers.index', compact('customers', 'customerTypes'));
    }

    /**
     * Show the form to create a new customer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customerTypes = Customer::getCustomerTypes();

        return view('cimstyredash::customers.create', compact('customerTypes'));
    }

    /**
     * Validate and save a new customer.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'company_name'   => 'nullable|string|max:200',
            'email'          => 'nullable|email|max:200',
            'phone'          => 'nullable|string|max:30',
            'cell'           => 'nullable|string|max:30',
            'vat_number'     => 'nullable|string|max:30',
            'debtor_account' => 'nullable|string|max:30',
            'customer_type'  => 'required|string|in:' . implode(',', Customer::getCustomerTypes()),
            'credit_limit'   => 'nullable|numeric|min:0',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'province'       => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'notes'          => 'nullable|string|max:2000',
            'is_active'      => 'sometimes|boolean',
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['balance']      = 0;
        $validated['created_by']   = auth()->id();

        $customer = Customer::create($validated);

        return redirect()
            ->route('cimstyredash.customers.show', $customer->id)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Show customer details including vehicles, quote history, and job card history.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $customer = Customer::with([
            'vehicles' => function ($q) {
                $q->latest();
            },
        ])->findOrFail($id);

        // Quote history (paginated separately)
        $quotes = $customer->quotes()
            ->with('branch')
            ->latest('quote_date')
            ->paginate(10, ['*'], 'quotes_page');

        // Job card history (paginated separately)
        $jobCards = $customer->jobCards()
            ->with(['branch', 'vehicle'])
            ->latest('job_date')
            ->paginate(10, ['*'], 'jobcards_page');

        return view('cimstyredash::customers.show', compact('customer', 'quotes', 'jobCards'));
    }

    /**
     * Show the form to edit an existing customer.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $customer      = Customer::findOrFail($id);
        $customerTypes = Customer::getCustomerTypes();

        return view('cimstyredash::customers.edit', compact('customer', 'customerTypes'));
    }

    /**
     * Validate and update an existing customer.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'company_name'   => 'nullable|string|max:200',
            'email'          => 'nullable|email|max:200',
            'phone'          => 'nullable|string|max:30',
            'cell'           => 'nullable|string|max:30',
            'vat_number'     => 'nullable|string|max:30',
            'debtor_account' => 'nullable|string|max:30',
            'customer_type'  => 'required|string|in:' . implode(',', Customer::getCustomerTypes()),
            'credit_limit'   => 'nullable|numeric|min:0',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'province'       => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'notes'          => 'nullable|string|max:2000',
            'is_active'      => 'sometimes|boolean',
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['credit_limit'] = $validated['credit_limit'] ?? $customer->credit_limit;

        $customer->update($validated);

        return redirect()
            ->route('cimstyredash.customers.show', $customer->id)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Soft delete a customer.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        // Prevent deletion if customer has active quotes or job cards
        $activeQuotes = $customer->quotes()
            ->whereIn('status', ['draft', 'sent', 'accepted'])
            ->count();

        $activeJobCards = $customer->jobCards()
            ->whereIn('status', ['open', 'in_progress', 'awaiting_parts'])
            ->count();

        if ($activeQuotes > 0 || $activeJobCards > 0) {
            return redirect()
                ->route('cimstyredash.customers.show', $customer->id)
                ->with('error', "Cannot delete customer with active quotes ({$activeQuotes}) or job cards ({$activeJobCards}).");
        }

        $customer->delete();

        return redirect()
            ->route('cimstyredash.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
