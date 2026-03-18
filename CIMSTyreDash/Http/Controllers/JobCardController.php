<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CIMSTyreDash\Models\Branch;
use Modules\CIMSTyreDash\Models\Customer;
use Modules\CIMSTyreDash\Models\JobCard;
use Modules\CIMSTyreDash\Models\JobCardService;
use Modules\CIMSTyreDash\Models\JobCardTyre;
use Modules\CIMSTyreDash\Models\Product;
use Modules\CIMSTyreDash\Models\Quote;
use Modules\CIMSTyreDash\Models\Service;
use Modules\CIMSTyreDash\Models\Stock;
use Modules\CIMSTyreDash\Models\TyreDashSetting;
use Modules\CIMSTyreDash\Models\Vehicle;

class JobCardController extends Controller
{
    /**
     * List job cards with filters (status, branch, technician, date range).
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = JobCard::with(['customer', 'vehicle', 'branch']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }

        if ($request->filled('branch_id')) {
            $query->forBranch($request->input('branch_id'));
        }

        if ($request->filled('technician_name')) {
            $query->where('technician_name', 'LIKE', '%' . $request->input('technician_name') . '%');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->input('date_from'), $request->input('date_to'));
        } elseif ($request->filled('date_from')) {
            $query->where('job_date', '>=', $request->input('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->where('job_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('job_card_number', 'LIKE', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('first_name', 'LIKE', $term)
                         ->orWhere('last_name', 'LIKE', $term)
                         ->orWhere('company_name', 'LIKE', $term);
                  })
                  ->orWhereHas('vehicle', function ($vq) use ($term) {
                      $vq->where('registration', 'LIKE', $term);
                  });
            });
        }

        $jobCards  = $query->latest('job_date')->latest('id')->paginate(25)->withQueryString();
        $branches  = Branch::active()->orderBy('name')->get();
        $statuses  = JobCard::STATUSES;

        return view('cimstyredash::jobcards.index', compact('jobCards', 'branches', 'statuses'));
    }

    /**
     * View job card detail with tyre lines and services.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $jobCard = JobCard::with([
            'customer',
            'vehicle',
            'branch',
            'quote',
            'jobCardTyres.product.brand',
            'jobCardTyres.product.size',
            'jobCardServices.service',
        ])->findOrFail($id);

        $currencySymbol = TyreDashSetting::getCurrencySymbol();

        return view('cimstyredash::jobcards.show', compact('jobCard', 'currencySymbol'));
    }

    /**
     * Show form to create a job card directly (not from a quote).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->ordered()->get();
        $products = Product::active()->with(['brand', 'size'])->orderBy('model_name')->get();

        $jobCardNumber = JobCard::generateNextJobCardNumber();

        return view('cimstyredash::jobcards.create', compact(
            'branches',
            'services',
            'products',
            'jobCardNumber'
        ));
    }

    /**
     * Save a new job card.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'             => 'nullable|integer|exists:cims_tyredash_customers,id',
            'vehicle_id'              => 'nullable|integer|exists:cims_tyredash_vehicles,id',
            'branch_id'               => 'nullable|integer|exists:cims_tyredash_branches,id',
            'technician_name'         => 'nullable|string|max:100',
            'job_date'                => 'required|date',
            'odometer_in'             => 'nullable|integer|min:0',
            'vehicle_condition_notes' => 'nullable|string|max:2000',
            'work_notes'              => 'nullable|string|max:2000',
            // Tyre lines
            'tyres'                   => 'nullable|array',
            'tyres.*.product_id'      => 'required|integer|exists:cims_tyredash_products,id',
            'tyres.*.quantity'        => 'required|integer|min:1|max:20',
            'tyres.*.position'        => 'nullable|string|in:' . implode(',', JobCardTyre::POSITIONS),
            'tyres.*.unit_price'      => 'required|numeric|min:0',
            'tyres.*.serial_number_new' => 'nullable|string|max:50',
            'tyres.*.serial_number_old' => 'nullable|string|max:50',
            // Service lines
            'services'                => 'nullable|array',
            'services.*.service_id'   => 'required|integer|exists:cims_tyredash_services,id',
            'services.*.quantity'     => 'required|integer|min:1|max:100',
            'services.*.unit_price'   => 'required|numeric|min:0',
        ]);

        $jobCard = DB::transaction(function () use ($validated, $request) {
            // Create the job card
            $jobCard = JobCard::create([
                'job_card_number'         => JobCard::generateNextJobCardNumber(),
                'quote_id'                => null,
                'customer_id'             => $validated['customer_id'] ?? null,
                'vehicle_id'              => $validated['vehicle_id'] ?? null,
                'branch_id'               => $validated['branch_id'] ?? null,
                'technician_name'         => $validated['technician_name'] ?? null,
                'job_date'                => $validated['job_date'],
                'status'                  => JobCard::STATUS_OPEN,
                'odometer_in'             => $validated['odometer_in'] ?? null,
                'vehicle_condition_notes' => $validated['vehicle_condition_notes'] ?? null,
                'work_notes'              => $validated['work_notes'] ?? null,
                'total_amount'            => 0,
                'created_by'              => auth()->id(),
                'updated_by'              => auth()->id(),
            ]);

            // Create tyre lines
            if (!empty($validated['tyres'])) {
                foreach ($validated['tyres'] as $tyreData) {
                    $tyreLine = new JobCardTyre([
                        'job_card_id'       => $jobCard->id,
                        'product_id'        => $tyreData['product_id'],
                        'quantity'          => $tyreData['quantity'],
                        'position'          => $tyreData['position'] ?? null,
                        'serial_number_new' => $tyreData['serial_number_new'] ?? null,
                        'serial_number_old' => $tyreData['serial_number_old'] ?? null,
                        'unit_price'        => $tyreData['unit_price'],
                    ]);
                    $tyreLine->line_total = $tyreLine->calculateLineTotal();
                    $tyreLine->save();
                }
            }

            // Create service lines
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $serviceData) {
                    $svcLine = new JobCardService([
                        'job_card_id' => $jobCard->id,
                        'service_id'  => $serviceData['service_id'],
                        'quantity'    => $serviceData['quantity'],
                        'unit_price'  => $serviceData['unit_price'],
                        'completed'   => false,
                    ]);
                    $svcLine->line_total = $svcLine->calculateLineTotal();
                    $svcLine->save();
                }
            }

            // Recalculate total
            $jobCard->recalculateTotal();

            return $jobCard;
        });

        return redirect()
            ->route('cimstyredash.jobcards.show', $jobCard->id)
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' created successfully.');
    }

    /**
     * Show form to edit a job card.
     *
     * @param  int $id
     * @return \Illuminate\View\View|RedirectResponse
     */
    public function edit($id)
    {
        $jobCard = JobCard::with([
            'customer',
            'vehicle',
            'jobCardTyres.product.brand',
            'jobCardTyres.product.size',
            'jobCardServices.service',
        ])->findOrFail($id);

        if (!$jobCard->is_editable) {
            return redirect()
                ->route('cimstyredash.jobcards.show', $jobCard->id)
                ->with('error', 'This job card cannot be edited in its current status (' . $jobCard->status . ').');
        }

        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->ordered()->get();
        $products = Product::active()->with(['brand', 'size'])->orderBy('model_name')->get();

        return view('cimstyredash::jobcards.edit', compact(
            'jobCard',
            'branches',
            'services',
            'products'
        ));
    }

    /**
     * Update a job card.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $jobCard = JobCard::findOrFail($id);

        if (!$jobCard->is_editable) {
            return redirect()
                ->route('cimstyredash.jobcards.show', $jobCard->id)
                ->with('error', 'This job card cannot be edited in its current status (' . $jobCard->status . ').');
        }

        $validated = $request->validate([
            'customer_id'             => 'nullable|integer|exists:cims_tyredash_customers,id',
            'vehicle_id'              => 'nullable|integer|exists:cims_tyredash_vehicles,id',
            'branch_id'               => 'nullable|integer|exists:cims_tyredash_branches,id',
            'technician_name'         => 'nullable|string|max:100',
            'job_date'                => 'required|date',
            'odometer_in'             => 'nullable|integer|min:0',
            'odometer_out'            => 'nullable|integer|min:0',
            'vehicle_condition_notes' => 'nullable|string|max:2000',
            'work_notes'              => 'nullable|string|max:2000',
            // Tyre lines
            'tyres'                   => 'nullable|array',
            'tyres.*.product_id'      => 'required|integer|exists:cims_tyredash_products,id',
            'tyres.*.quantity'        => 'required|integer|min:1|max:20',
            'tyres.*.position'        => 'nullable|string|in:' . implode(',', JobCardTyre::POSITIONS),
            'tyres.*.unit_price'      => 'required|numeric|min:0',
            'tyres.*.serial_number_new' => 'nullable|string|max:50',
            'tyres.*.serial_number_old' => 'nullable|string|max:50',
            // Service lines
            'services'                => 'nullable|array',
            'services.*.service_id'   => 'required|integer|exists:cims_tyredash_services,id',
            'services.*.quantity'     => 'required|integer|min:1|max:100',
            'services.*.unit_price'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($jobCard, $validated, $request) {
            // Update job card header
            $jobCard->update([
                'customer_id'             => $validated['customer_id'] ?? null,
                'vehicle_id'              => $validated['vehicle_id'] ?? null,
                'branch_id'               => $validated['branch_id'] ?? null,
                'technician_name'         => $validated['technician_name'] ?? null,
                'job_date'                => $validated['job_date'],
                'odometer_in'             => $validated['odometer_in'] ?? null,
                'odometer_out'            => $validated['odometer_out'] ?? null,
                'vehicle_condition_notes' => $validated['vehicle_condition_notes'] ?? null,
                'work_notes'              => $validated['work_notes'] ?? null,
                'updated_by'              => auth()->id(),
            ]);

            // Replace tyre lines
            $jobCard->jobCardTyres()->delete();
            if (!empty($validated['tyres'])) {
                foreach ($validated['tyres'] as $tyreData) {
                    $tyreLine = new JobCardTyre([
                        'job_card_id'       => $jobCard->id,
                        'product_id'        => $tyreData['product_id'],
                        'quantity'          => $tyreData['quantity'],
                        'position'          => $tyreData['position'] ?? null,
                        'serial_number_new' => $tyreData['serial_number_new'] ?? null,
                        'serial_number_old' => $tyreData['serial_number_old'] ?? null,
                        'unit_price'        => $tyreData['unit_price'],
                    ]);
                    $tyreLine->line_total = $tyreLine->calculateLineTotal();
                    $tyreLine->save();
                }
            }

            // Replace service lines
            $jobCard->jobCardServices()->delete();
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $serviceData) {
                    $svcLine = new JobCardService([
                        'job_card_id' => $jobCard->id,
                        'service_id'  => $serviceData['service_id'],
                        'quantity'    => $serviceData['quantity'],
                        'unit_price'  => $serviceData['unit_price'],
                        'completed'   => false,
                    ]);
                    $svcLine->line_total = $svcLine->calculateLineTotal();
                    $svcLine->save();
                }
            }

            // Recalculate total
            $jobCard->recalculateTotal();
        });

        return redirect()
            ->route('cimstyredash.jobcards.show', $jobCard->id)
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' updated successfully.');
    }

    /**
     * Change job card status (in_progress, awaiting_parts, complete).
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $jobCard = JobCard::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', [
                JobCard::STATUS_IN_PROGRESS,
                JobCard::STATUS_AWAITING_PARTS,
                JobCard::STATUS_COMPLETE,
                JobCard::STATUS_CANCELLED,
            ]),
        ]);

        $newStatus = $validated['status'];

        // Validate allowed transitions
        $allowedTransitions = [
            JobCard::STATUS_OPEN           => [JobCard::STATUS_IN_PROGRESS, JobCard::STATUS_CANCELLED],
            JobCard::STATUS_IN_PROGRESS    => [JobCard::STATUS_AWAITING_PARTS, JobCard::STATUS_COMPLETE, JobCard::STATUS_CANCELLED],
            JobCard::STATUS_AWAITING_PARTS => [JobCard::STATUS_IN_PROGRESS, JobCard::STATUS_COMPLETE, JobCard::STATUS_CANCELLED],
            JobCard::STATUS_COMPLETE       => [], // Can only go to invoiced via closeToInvoice
        ];

        $allowed = $allowedTransitions[$jobCard->status] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return redirect()
                ->route('cimstyredash.jobcards.show', $jobCard->id)
                ->with('error', "Cannot change status from '{$jobCard->status}' to '{$newStatus}'.");
        }

        $updateData = [
            'status'     => $newStatus,
            'updated_by' => auth()->id(),
        ];

        // Set timestamps for status changes
        if ($newStatus === JobCard::STATUS_IN_PROGRESS && !$jobCard->started_at) {
            $updateData['started_at'] = now();
        }

        if ($newStatus === JobCard::STATUS_COMPLETE) {
            $updateData['completed_at'] = now();
        }

        $jobCard->update($updateData);

        $statusLabels = [
            JobCard::STATUS_IN_PROGRESS    => 'marked as in progress',
            JobCard::STATUS_AWAITING_PARTS => 'set to awaiting parts',
            JobCard::STATUS_COMPLETE       => 'marked as complete',
            JobCard::STATUS_CANCELLED      => 'cancelled',
        ];

        return redirect()
            ->route('cimstyredash.jobcards.show', $jobCard->id)
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' ' . ($statusLabels[$newStatus] ?? 'updated') . '.');
    }

    /**
     * Close a completed job card and create an invoice.
     *
     * Deducts stock for fitted tyres and updates the quote status if applicable.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function closeToInvoice(Request $request, $id): RedirectResponse
    {
        $jobCard = JobCard::with(['jobCardTyres', 'quote'])->findOrFail($id);

        if (!$jobCard->can_invoice) {
            return redirect()
                ->route('cimstyredash.jobcards.show', $jobCard->id)
                ->with('error', 'This job card cannot be invoiced. It must be complete and not already invoiced.');
        }

        DB::transaction(function () use ($jobCard) {
            // Deduct stock for each tyre line
            foreach ($jobCard->jobCardTyres as $tyreLine) {
                if ($jobCard->branch_id) {
                    $stock = Stock::where('product_id', $tyreLine->product_id)
                        ->where('branch_id', $jobCard->branch_id)
                        ->first();

                    if ($stock) {
                        $stock->adjustQuantity(-$tyreLine->quantity);
                        // Release any reserved stock
                        if ($stock->reserved > 0) {
                            $stock->release($tyreLine->quantity);
                        }
                    }
                }
            }

            // Update job card status
            $jobCard->update([
                'status'     => JobCard::STATUS_INVOICED,
                'updated_by' => auth()->id(),
            ]);

            // If created from a quote, mark quote as invoiced too
            if ($jobCard->quote) {
                $jobCard->quote->update([
                    'status'     => Quote::STATUS_INVOICED,
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('cimstyredash.jobcards.show', $jobCard->id)
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' has been closed and invoiced. Stock has been deducted.');
    }

    /**
     * Cancel/delete a job card.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $jobCard = JobCard::findOrFail($id);

        if ($jobCard->status === JobCard::STATUS_INVOICED) {
            return redirect()
                ->route('cimstyredash.jobcards.index')
                ->with('error', 'Cannot delete an invoiced job card.');
        }

        $jobCard->update([
            'status'     => JobCard::STATUS_CANCELLED,
            'updated_by' => auth()->id(),
        ]);
        $jobCard->delete();

        return redirect()
            ->route('cimstyredash.jobcards.index')
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' has been cancelled and deleted.');
    }
}
