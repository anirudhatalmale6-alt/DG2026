<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
use Modules\CIMSTyreDash\Models\QuoteOption;
use Modules\CIMSTyreDash\Models\QuoteService;
use Modules\CIMSTyreDash\Models\Service;
use Modules\CIMSTyreDash\Models\Stock;
use Modules\CIMSTyreDash\Models\TyreDashSetting;
use Modules\CIMSTyreDash\Models\TyreSize;
use Modules\CIMSTyreDash\Models\Vehicle;

class QuoteController extends Controller
{
    /**
     * List quotes with filters (status, branch, salesman, date range, search).
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Quote::with(['customer', 'branch']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }

        if ($request->filled('branch_id')) {
            $query->forBranch($request->input('branch_id'));
        }

        if ($request->filled('salesman_id')) {
            $query->forSalesman($request->input('salesman_id'));
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->input('date_from'), $request->input('date_to'));
        } elseif ($request->filled('date_from')) {
            $query->where('quote_date', '>=', $request->input('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->where('quote_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('quote_number', 'LIKE', $term)
                  ->orWhere('customer_order_ref', 'LIKE', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('first_name', 'LIKE', $term)
                         ->orWhere('last_name', 'LIKE', $term)
                         ->orWhere('company_name', 'LIKE', $term);
                  });
            });
        }

        $quotes   = $query->latest('quote_date')->latest('id')->paginate(25)->withQueryString();
        $branches = Branch::active()->orderBy('name')->get();
        $statuses = Quote::STATUSES;

        return view('cimstyredash::quotes.index', compact('quotes', 'branches', 'statuses'));
    }

    /**
     * Show the POS-style quote builder page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->ordered()->get();
        $sizes    = TyreSize::active()->orderBy('full_size')->get();

        $quoteNumber   = Quote::generateNextQuoteNumber();
        $validityDays  = TyreDashSetting::getQuoteValidityDays();
        $defaultMarkup = TyreDashSetting::getDefaultMarkup();
        $vatRate       = TyreDashSetting::getVatRate();

        return view('cimstyredash::quotes.create', compact(
            'branches',
            'services',
            'sizes',
            'quoteNumber',
            'validityDays',
            'defaultMarkup',
            'vatRate'
        ));
    }

    /**
     * Create a quote with customer, vehicle, options, and services.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'       => 'nullable|integer|exists:cims_tyredash_customers,id',
            'vehicle_id'        => 'nullable|integer|exists:cims_tyredash_vehicles,id',
            'branch_id'         => 'nullable|integer|exists:cims_tyredash_branches,id',
            'salesman_name'     => 'nullable|string|max:100',
            'customer_order_ref'=> 'nullable|string|max:50',
            'quote_date'        => 'required|date',
            'valid_until'       => 'nullable|date|after_or_equal:quote_date',
            'customer_comment'  => 'nullable|string|max:1000',
            'internal_notes'    => 'nullable|string|max:1000',
            // Options array
            'options'                    => 'nullable|array|max:5',
            'options.*.product_id'       => 'required|integer|exists:cims_tyredash_products,id',
            'options.*.quantity'         => 'required|integer|min:1|max:20',
            'options.*.unit_price'       => 'required|numeric|min:0',
            'options.*.unit_cost'        => 'required|numeric|min:0',
            'options.*.markup_pct'       => 'nullable|numeric|min:0|max:999',
            'options.*.discount_pct'     => 'nullable|numeric|min:0|max:100',
            'options.*.is_selected'      => 'sometimes|boolean',
            // Services array
            'services'                   => 'nullable|array',
            'services.*.service_id'      => 'required|integer|exists:cims_tyredash_services,id',
            'services.*.quantity'        => 'required|integer|min:1|max:100',
            'services.*.unit_price'      => 'required|numeric|min:0',
        ]);

        $quote = DB::transaction(function () use ($validated, $request) {
            // Create the quote
            $quote = Quote::create([
                'quote_number'       => Quote::generateNextQuoteNumber(),
                'customer_order_ref' => $validated['customer_order_ref'] ?? null,
                'customer_id'        => $validated['customer_id'] ?? null,
                'vehicle_id'         => $validated['vehicle_id'] ?? null,
                'branch_id'          => $validated['branch_id'] ?? null,
                'salesman_id'        => auth()->id(),
                'salesman_name'      => $validated['salesman_name'] ?? auth()->user()->first_name ?? null,
                'quote_date'         => $validated['quote_date'],
                'valid_until'        => $validated['valid_until'] ?? now()->addDays(TyreDashSetting::getQuoteValidityDays()),
                'status'             => Quote::STATUS_DRAFT,
                'customer_comment'   => $validated['customer_comment'] ?? null,
                'internal_notes'     => $validated['internal_notes'] ?? null,
                'total_amount'       => 0,
                'created_by'         => auth()->id(),
                'updated_by'         => auth()->id(),
            ]);

            // Create quote options (tyre options)
            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $index => $optionData) {
                    $option = new QuoteOption([
                        'option_number' => $index + 1,
                        'product_id'    => $optionData['product_id'],
                        'quantity'      => $optionData['quantity'],
                        'unit_cost'     => $optionData['unit_cost'],
                        'unit_price'    => $optionData['unit_price'],
                        'markup_pct'    => $optionData['markup_pct'] ?? 0,
                        'discount_pct'  => $optionData['discount_pct'] ?? 0,
                        'is_selected'   => ($index === 0), // First option selected by default
                    ]);
                    $option->line_total = $option->calculateLineTotal();
                    $quote->quoteOptions()->save($option);
                }
            }

            // Create quote services
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $serviceData) {
                    $qs = new QuoteService([
                        'service_id' => $serviceData['service_id'],
                        'quantity'   => $serviceData['quantity'],
                        'unit_price' => $serviceData['unit_price'],
                    ]);
                    $qs->line_total = $qs->calculateLineTotal();
                    $quote->quoteServices()->save($qs);
                }
            }

            // Recalculate total
            $quote->recalculateTotal();

            return $quote;
        });

        return redirect()
            ->route('cimstyredash.quotes.show', $quote->id)
            ->with('success', 'Quote ' . $quote->quote_number . ' created successfully.');
    }

    /**
     * View quote details.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $quote = Quote::with([
            'customer',
            'vehicle',
            'branch',
            'quoteOptions.product.brand',
            'quoteOptions.product.size',
            'quoteServices.service',
            'jobCard',
        ])->findOrFail($id);

        $currencySymbol = TyreDashSetting::getCurrencySymbol();

        return view('cimstyredash::quotes.show', compact('quote', 'currencySymbol'));
    }

    /**
     * Show form to edit a quote.
     *
     * @param  int $id
     * @return \Illuminate\View\View|RedirectResponse
     */
    public function edit($id)
    {
        $quote = Quote::with([
            'customer',
            'vehicle',
            'quoteOptions.product.brand',
            'quoteOptions.product.size',
            'quoteServices.service',
        ])->findOrFail($id);

        if (!$quote->is_editable) {
            return redirect()
                ->route('cimstyredash.quotes.show', $quote->id)
                ->with('error', 'This quote cannot be edited in its current status (' . $quote->status . ').');
        }

        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->ordered()->get();
        $sizes    = TyreSize::active()->orderBy('full_size')->get();

        $defaultMarkup = TyreDashSetting::getDefaultMarkup();
        $vatRate       = TyreDashSetting::getVatRate();

        return view('cimstyredash::quotes.edit', compact(
            'quote',
            'branches',
            'services',
            'sizes',
            'defaultMarkup',
            'vatRate'
        ));
    }

    /**
     * Update a quote.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $quote = Quote::findOrFail($id);

        if (!$quote->is_editable) {
            return redirect()
                ->route('cimstyredash.quotes.show', $quote->id)
                ->with('error', 'This quote cannot be edited in its current status (' . $quote->status . ').');
        }

        $validated = $request->validate([
            'customer_id'       => 'nullable|integer|exists:cims_tyredash_customers,id',
            'vehicle_id'        => 'nullable|integer|exists:cims_tyredash_vehicles,id',
            'branch_id'         => 'nullable|integer|exists:cims_tyredash_branches,id',
            'salesman_name'     => 'nullable|string|max:100',
            'customer_order_ref'=> 'nullable|string|max:50',
            'quote_date'        => 'required|date',
            'valid_until'       => 'nullable|date|after_or_equal:quote_date',
            'customer_comment'  => 'nullable|string|max:1000',
            'internal_notes'    => 'nullable|string|max:1000',
            // Options array
            'options'                    => 'nullable|array|max:5',
            'options.*.product_id'       => 'required|integer|exists:cims_tyredash_products,id',
            'options.*.quantity'         => 'required|integer|min:1|max:20',
            'options.*.unit_price'       => 'required|numeric|min:0',
            'options.*.unit_cost'        => 'required|numeric|min:0',
            'options.*.markup_pct'       => 'nullable|numeric|min:0|max:999',
            'options.*.discount_pct'     => 'nullable|numeric|min:0|max:100',
            'options.*.is_selected'      => 'sometimes|boolean',
            // Services array
            'services'                   => 'nullable|array',
            'services.*.service_id'      => 'required|integer|exists:cims_tyredash_services,id',
            'services.*.quantity'        => 'required|integer|min:1|max:100',
            'services.*.unit_price'      => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($quote, $validated, $request) {
            // Update quote header
            $quote->update([
                'customer_id'        => $validated['customer_id'] ?? null,
                'vehicle_id'         => $validated['vehicle_id'] ?? null,
                'branch_id'          => $validated['branch_id'] ?? null,
                'salesman_name'      => $validated['salesman_name'] ?? $quote->salesman_name,
                'customer_order_ref' => $validated['customer_order_ref'] ?? null,
                'quote_date'         => $validated['quote_date'],
                'valid_until'        => $validated['valid_until'] ?? $quote->valid_until,
                'customer_comment'   => $validated['customer_comment'] ?? null,
                'internal_notes'     => $validated['internal_notes'] ?? null,
                'updated_by'         => auth()->id(),
            ]);

            // Replace quote options
            $quote->quoteOptions()->delete();
            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $index => $optionData) {
                    $option = new QuoteOption([
                        'option_number' => $index + 1,
                        'product_id'    => $optionData['product_id'],
                        'quantity'      => $optionData['quantity'],
                        'unit_cost'     => $optionData['unit_cost'],
                        'unit_price'    => $optionData['unit_price'],
                        'markup_pct'    => $optionData['markup_pct'] ?? 0,
                        'discount_pct'  => $optionData['discount_pct'] ?? 0,
                        'is_selected'   => !empty($optionData['is_selected']),
                    ]);
                    $option->line_total = $option->calculateLineTotal();
                    $quote->quoteOptions()->save($option);
                }
            }

            // Replace quote services
            $quote->quoteServices()->delete();
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $serviceData) {
                    $qs = new QuoteService([
                        'service_id' => $serviceData['service_id'],
                        'quantity'   => $serviceData['quantity'],
                        'unit_price' => $serviceData['unit_price'],
                    ]);
                    $qs->line_total = $qs->calculateLineTotal();
                    $quote->quoteServices()->save($qs);
                }
            }

            // Recalculate total
            $quote->recalculateTotal();
        });

        return redirect()
            ->route('cimstyredash.quotes.show', $quote->id)
            ->with('success', 'Quote ' . $quote->quote_number . ' updated successfully.');
    }

    /**
     * Soft delete a quote.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $quote = Quote::findOrFail($id);

        if ($quote->status === Quote::STATUS_INVOICED) {
            return redirect()
                ->route('cimstyredash.quotes.index')
                ->with('error', 'Cannot delete an invoiced quote.');
        }

        $quote->delete();

        return redirect()
            ->route('cimstyredash.quotes.index')
            ->with('success', 'Quote ' . $quote->quote_number . ' deleted successfully.');
    }

    /**
     * Change quote status (sent, accepted, declined).
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $quote = Quote::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', [
                Quote::STATUS_SENT,
                Quote::STATUS_ACCEPTED,
                Quote::STATUS_DECLINED,
                Quote::STATUS_EXPIRED,
            ]),
        ]);

        $newStatus = $validated['status'];

        // Validate allowed transitions
        $allowedTransitions = [
            Quote::STATUS_DRAFT    => [Quote::STATUS_SENT, Quote::STATUS_DECLINED],
            Quote::STATUS_SENT     => [Quote::STATUS_ACCEPTED, Quote::STATUS_DECLINED, Quote::STATUS_EXPIRED],
            Quote::STATUS_ACCEPTED => [Quote::STATUS_DECLINED],
            Quote::STATUS_DECLINED => [Quote::STATUS_DRAFT],
        ];

        $allowed = $allowedTransitions[$quote->status] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return redirect()
                ->route('cimstyredash.quotes.show', $quote->id)
                ->with('error', "Cannot change status from '{$quote->status}' to '{$newStatus}'.");
        }

        $quote->update([
            'status'     => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        $statusLabels = [
            Quote::STATUS_SENT     => 'sent to customer',
            Quote::STATUS_ACCEPTED => 'marked as accepted',
            Quote::STATUS_DECLINED => 'marked as declined',
            Quote::STATUS_EXPIRED  => 'marked as expired',
        ];

        return redirect()
            ->route('cimstyredash.quotes.show', $quote->id)
            ->with('success', 'Quote ' . $quote->quote_number . ' ' . ($statusLabels[$newStatus] ?? 'updated') . '.');
    }

    /**
     * Convert an accepted quote to a job card.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function convertToJobCard($id): RedirectResponse
    {
        $quote = Quote::with(['quoteOptions.product', 'quoteServices.service'])->findOrFail($id);

        if (!$quote->can_convert_to_job_card) {
            return redirect()
                ->route('cimstyredash.quotes.show', $quote->id)
                ->with('error', 'This quote cannot be converted to a job card. It must be accepted and not already converted.');
        }

        $jobCard = DB::transaction(function () use ($quote) {
            // Create job card
            $jobCard = JobCard::create([
                'job_card_number'        => JobCard::generateNextJobCardNumber(),
                'quote_id'               => $quote->id,
                'customer_id'            => $quote->customer_id,
                'vehicle_id'             => $quote->vehicle_id,
                'branch_id'              => $quote->branch_id,
                'technician_id'          => null,
                'technician_name'        => null,
                'job_date'               => now()->toDateString(),
                'status'                 => JobCard::STATUS_OPEN,
                'vehicle_condition_notes' => null,
                'work_notes'             => null,
                'total_amount'           => 0,
                'created_by'             => auth()->id(),
                'updated_by'             => auth()->id(),
            ]);

            // Copy the selected option's product as job card tyre line(s)
            $selectedOption = $quote->quoteOptions->firstWhere('is_selected', true)
                              ?? $quote->quoteOptions->first();

            if ($selectedOption) {
                $tyreLine = JobCardTyre::create([
                    'job_card_id' => $jobCard->id,
                    'product_id'  => $selectedOption->product_id,
                    'quantity'    => $selectedOption->quantity,
                    'position'    => null,
                    'unit_price'  => $selectedOption->unit_price,
                    'line_total'  => $selectedOption->line_total,
                ]);
            }

            // Copy quote services to job card services
            foreach ($quote->quoteServices as $qs) {
                JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id'  => $qs->service_id,
                    'quantity'    => $qs->quantity,
                    'unit_price'  => $qs->unit_price,
                    'line_total'  => $qs->line_total,
                    'completed'   => false,
                ]);
            }

            // Recalculate job card total
            $jobCard->recalculateTotal();

            // Update quote status to invoiced (or we keep it as accepted, since job card != invoice)
            // We keep the quote as accepted; it will become "invoiced" when the job card closes to invoice.

            return $jobCard;
        });

        return redirect()
            ->route('cimstyredash.jobcards.show', $jobCard->id)
            ->with('success', 'Job card ' . $jobCard->job_card_number . ' created from quote ' . $quote->quote_number . '.');
    }

    /**
     * Generate a PDF of the quote.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function pdf($id)
    {
        $quote = Quote::with([
            'customer',
            'vehicle',
            'branch',
            'quoteOptions.product.brand',
            'quoteOptions.product.size',
            'quoteServices.service',
        ])->findOrFail($id);

        $currencySymbol = TyreDashSetting::getCurrencySymbol();
        $vatRate        = TyreDashSetting::getVatRate();

        // If a PDF library like Barryvdh\DomPDF is installed, use it.
        // Otherwise, render a print-friendly view.
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cimstyredash::quotes.pdf', compact(
                'quote',
                'currencySymbol',
                'vatRate'
            ));

            return $pdf->download('Quote-' . $quote->quote_number . '.pdf');
        }

        // Fallback: render a print-friendly HTML view
        return view('cimstyredash::quotes.pdf', compact('quote', 'currencySymbol', 'vatRate'));
    }

    /**
     * AJAX - Search customers by name/phone/cell.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxSearchCustomers(Request $request): JsonResponse
    {
        $request->validate([
            'term' => 'required|string|min:2|max:100',
        ]);

        $customers = Customer::active()
            ->search($request->input('term'))
            ->limit(15)
            ->get()
            ->map(function ($customer) {
                return [
                    'id'           => $customer->id,
                    'full_name'    => $customer->full_name,
                    'display_name' => $customer->display_name,
                    'company_name' => $customer->company_name,
                    'phone'        => $customer->phone,
                    'cell'         => $customer->cell,
                    'email'        => $customer->email,
                ];
            });

        return response()->json(['customers' => $customers]);
    }

    /**
     * AJAX - Search vehicles by registration.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxSearchVehicles(Request $request): JsonResponse
    {
        $request->validate([
            'term' => 'required|string|min:2|max:50',
        ]);

        $vehicles = Vehicle::with('customer')
            ->search($request->input('term'))
            ->limit(15)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id'                => $vehicle->id,
                    'registration'      => $vehicle->registration,
                    'display_name'      => $vehicle->display_name,
                    'make'              => $vehicle->make,
                    'model'             => $vehicle->model,
                    'year'              => $vehicle->year,
                    'current_tyre_size' => $vehicle->current_tyre_size,
                    'customer_id'       => $vehicle->customer_id,
                    'customer_name'     => $vehicle->customer ? $vehicle->customer->full_name : null,
                ];
            });

        return response()->json(['vehicles' => $vehicles]);
    }

    /**
     * AJAX - Search products by tyre size for the quote builder.
     *
     * Returns products with brand, price, and SOH per branch for comparison.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxSizeSearch(Request $request): JsonResponse
    {
        $request->validate([
            'size' => 'required|string|max:50',
        ]);

        $sizeString = trim($request->input('size'));

        // Find matching tyre sizes
        $sizes = TyreSize::where('full_size', 'LIKE', '%' . $sizeString . '%')
            ->active()
            ->pluck('id');

        if ($sizes->isEmpty()) {
            return response()->json(['products' => [], 'message' => 'No matching sizes found.']);
        }

        $branches = Branch::active()->orderBy('name')->get();

        $products = Product::with(['brand', 'size', 'stockRecords'])
            ->active()
            ->whereIn('size_id', $sizes)
            ->orderBy('brand_id')
            ->orderBy('sell_price')
            ->get()
            ->map(function ($product) use ($branches) {
                $stockByBranch = [];
                foreach ($branches as $branch) {
                    $stockRecord = $product->stockRecords->firstWhere('branch_id', $branch->id);
                    $stockByBranch[] = [
                        'branch_id'   => $branch->id,
                        'branch_code' => $branch->code,
                        'branch_name' => $branch->name,
                        'soh'         => $stockRecord ? $stockRecord->quantity : 0,
                        'available'   => $stockRecord ? $stockRecord->available_quantity : 0,
                    ];
                }

                return [
                    'id'               => $product->id,
                    'product_code'     => $product->product_code,
                    'brand'            => $product->brand->name ?? 'N/A',
                    'brand_id'         => $product->brand_id,
                    'model_name'       => $product->model_name,
                    'full_description' => $product->full_description,
                    'size'             => $product->size->full_size ?? 'N/A',
                    'load_index'       => $product->load_index,
                    'speed_rating'     => $product->speed_rating,
                    'cost_price'       => (float) $product->cost_price,
                    'sell_price'       => (float) $product->sell_price,
                    'markup_pct'       => (float) $product->markup_pct,
                    'total_stock'      => $product->getTotalStock(),
                    'stock_by_branch'  => $stockByBranch,
                ];
            });

        return response()->json(['products' => $products]);
    }

    /**
     * AJAX - Add a tyre option to an existing quote.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxAddOption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quote_id'     => 'required|integer|exists:cims_tyredash_quotes,id',
            'product_id'   => 'required|integer|exists:cims_tyredash_products,id',
            'quantity'     => 'required|integer|min:1|max:20',
            'unit_price'   => 'required|numeric|min:0',
            'unit_cost'    => 'required|numeric|min:0',
            'markup_pct'   => 'nullable|numeric|min:0|max:999',
            'discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        $quote = Quote::findOrFail($validated['quote_id']);

        if (!$quote->is_editable) {
            return response()->json(['error' => 'Quote is not editable.'], 422);
        }

        // Determine the next option number (max 5)
        $currentMaxOption = $quote->quoteOptions()->max('option_number') ?? 0;
        if ($currentMaxOption >= 5) {
            return response()->json(['error' => 'Maximum of 5 options per quote reached.'], 422);
        }

        $option = new QuoteOption([
            'quote_id'      => $quote->id,
            'option_number' => $currentMaxOption + 1,
            'product_id'    => $validated['product_id'],
            'quantity'      => $validated['quantity'],
            'unit_cost'     => $validated['unit_cost'],
            'unit_price'    => $validated['unit_price'],
            'markup_pct'    => $validated['markup_pct'] ?? 0,
            'discount_pct'  => $validated['discount_pct'] ?? 0,
            'is_selected'   => ($currentMaxOption === 0), // Select first option by default
        ]);
        $option->line_total = $option->calculateLineTotal();
        $option->save();

        $quote->recalculateTotal();

        // Reload the option with product data
        $option->load('product.brand', 'product.size');

        return response()->json([
            'success' => true,
            'option'  => [
                'id'            => $option->id,
                'option_number' => $option->option_number,
                'product_id'    => $option->product_id,
                'product_name'  => $option->product->display_name ?? '',
                'brand'         => $option->product->brand->name ?? 'N/A',
                'size'          => $option->product->size->full_size ?? 'N/A',
                'quantity'      => $option->quantity,
                'unit_price'    => (float) $option->unit_price,
                'unit_cost'     => (float) $option->unit_cost,
                'markup_pct'    => (float) $option->markup_pct,
                'discount_pct'  => (float) $option->discount_pct,
                'line_total'    => (float) $option->line_total,
                'is_selected'   => $option->is_selected,
            ],
            'quote_total' => (float) $quote->fresh()->total_amount,
        ]);
    }

    /**
     * AJAX - Add a service to an existing quote.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxAddService(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quote_id'   => 'required|integer|exists:cims_tyredash_quotes,id',
            'service_id' => 'required|integer|exists:cims_tyredash_services,id',
            'quantity'   => 'required|integer|min:1|max:100',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $quote = Quote::findOrFail($validated['quote_id']);

        if (!$quote->is_editable) {
            return response()->json(['error' => 'Quote is not editable.'], 422);
        }

        $qs = new QuoteService([
            'quote_id'   => $quote->id,
            'service_id' => $validated['service_id'],
            'quantity'   => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
        ]);
        $qs->line_total = $qs->calculateLineTotal();
        $qs->save();

        $quote->recalculateTotal();

        $qs->load('service');

        return response()->json([
            'success' => true,
            'service' => [
                'id'           => $qs->id,
                'service_id'   => $qs->service_id,
                'service_name' => $qs->service->name ?? 'N/A',
                'service_code' => $qs->service->code ?? '',
                'quantity'     => $qs->quantity,
                'unit_price'   => (float) $qs->unit_price,
                'line_total'   => (float) $qs->line_total,
            ],
            'quote_total' => (float) $quote->fresh()->total_amount,
        ]);
    }
}
