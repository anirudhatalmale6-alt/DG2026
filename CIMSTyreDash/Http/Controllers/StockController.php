<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CIMSTyreDash\Models\Branch;
use Modules\CIMSTyreDash\Models\Product;
use Modules\CIMSTyreDash\Models\Stock;
use Modules\CIMSTyreDash\Models\TyreDashSetting;
use Modules\CIMSTyreDash\Models\TyreSize;

class StockController extends Controller
{
    /**
     * Stock overview per branch, with search and low stock filter.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Stock::with(['product.brand', 'product.size', 'branch']);

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->forBranch($request->input('branch_id'));
        }

        // Low stock filter
        if ($request->input('low_stock') === '1') {
            $query->lowStock();
        }

        // Out of stock filter
        if ($request->input('out_of_stock') === '1') {
            $query->outOfStock();
        }

        // Search by product name, code, or brand
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->whereHas('product', function ($q) use ($term) {
                $q->search($term);
            });
        }

        // Filter by size
        if ($request->filled('size_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('size_id', $request->input('size_id'));
            });
        }

        $stockRecords = $query->orderBy('branch_id')
            ->orderBy('product_id')
            ->paginate(50)
            ->withQueryString();

        $branches = Branch::active()->orderBy('name')->get();
        $sizes    = TyreSize::active()->orderBy('full_size')->get();

        // Summary stats
        $totalStockValue = Stock::join('cims_tyredash_products', 'cims_tyredash_stock.product_id', '=', 'cims_tyredash_products.id')
            ->selectRaw('COALESCE(SUM(cims_tyredash_stock.quantity * cims_tyredash_products.cost_price), 0) as total_value')
            ->value('total_value');

        $lowStockCount  = Stock::lowStock()->count();
        $outOfStockCount = Stock::outOfStock()->count();

        $currencySymbol = TyreDashSetting::getCurrencySymbol();

        return view('cimstyredash::stock.index', compact(
            'stockRecords',
            'branches',
            'sizes',
            'totalStockValue',
            'lowStockCount',
            'outOfStockCount',
            'currencySymbol'
        ));
    }

    /**
     * Show the manual stock adjustment form.
     *
     * @param  int $id  Stock record ID
     * @return \Illuminate\View\View
     */
    public function adjust($id)
    {
        $stock = Stock::with(['product.brand', 'product.size', 'branch'])->findOrFail($id);

        return view('cimstyredash::stock.adjust', compact('stock'));
    }

    /**
     * Save a manual stock adjustment.
     *
     * @param  Request $request
     * @param  int     $id  Stock record ID
     * @return RedirectResponse
     */
    public function processAdjustment(Request $request, $id): RedirectResponse
    {
        $stock = Stock::with(['product', 'branch'])->findOrFail($id);

        $validated = $request->validate([
            'adjustment_type' => 'required|string|in:add,subtract,set',
            'quantity'        => 'required|integer|min:0',
            'min_quantity'    => 'nullable|integer|min:0',
            'reason'          => 'nullable|string|max:500',
        ]);

        $oldQuantity = $stock->quantity;

        switch ($validated['adjustment_type']) {
            case 'add':
                $stock->adjustQuantity($validated['quantity']);
                break;
            case 'subtract':
                $stock->adjustQuantity(-$validated['quantity']);
                break;
            case 'set':
                $stock->quantity = max(0, $validated['quantity']);
                $stock->save();
                break;
        }

        // Update min_quantity if provided
        if (isset($validated['min_quantity'])) {
            $stock->update(['min_quantity' => $validated['min_quantity']]);
        }

        $newQuantity = $stock->fresh()->quantity;
        $productName = $stock->product->model_name ?? 'Unknown';
        $branchName  = $stock->branch->name ?? 'Unknown';

        return redirect()
            ->route('cimstyredash.stock.index')
            ->with('success', "Stock for '{$productName}' at '{$branchName}' adjusted from {$oldQuantity} to {$newQuantity}.");
    }

    /**
     * Show the inter-branch stock transfer form.
     *
     * @return \Illuminate\View\View
     */
    public function transfer()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $products = Product::active()->with(['brand', 'size'])->orderBy('model_name')->get();

        return view('cimstyredash::stock.transfer', compact('branches', 'products'));
    }

    /**
     * Execute a stock transfer between branches.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function processTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'     => 'required|integer|exists:cims_tyredash_products,id',
            'from_branch_id' => 'required|integer|exists:cims_tyredash_branches,id',
            'to_branch_id'   => 'required|integer|exists:cims_tyredash_branches,id|different:from_branch_id',
            'quantity'       => 'required|integer|min:1',
            'reason'         => 'nullable|string|max:500',
        ]);

        // Find the source stock record
        $fromStock = Stock::where('product_id', $validated['product_id'])
            ->where('branch_id', $validated['from_branch_id'])
            ->first();

        if (!$fromStock || $fromStock->available_quantity < $validated['quantity']) {
            $available = $fromStock ? $fromStock->available_quantity : 0;
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Insufficient stock at source branch. Available: {$available}, requested: {$validated['quantity']}.");
        }

        DB::transaction(function () use ($validated, $fromStock) {
            // Deduct from source branch
            $fromStock->adjustQuantity(-$validated['quantity']);

            // Add to destination branch (create record if it does not exist)
            $toStock = Stock::firstOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'branch_id'  => $validated['to_branch_id'],
                ],
                [
                    'quantity'     => 0,
                    'min_quantity' => TyreDashSetting::getDefaultMinStock(),
                    'reserved'     => 0,
                ]
            );

            $toStock->adjustQuantity($validated['quantity']);
        });

        $product    = Product::find($validated['product_id']);
        $fromBranch = Branch::find($validated['from_branch_id']);
        $toBranch   = Branch::find($validated['to_branch_id']);

        return redirect()
            ->route('cimstyredash.stock.index')
            ->with('success', "Transferred {$validated['quantity']}x '{$product->model_name}' from {$fromBranch->name} to {$toBranch->name}.");
    }

    /**
     * AJAX - Given a tyre size, return stock across all branches for all products of that size.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxStockBySize(Request $request): JsonResponse
    {
        $request->validate([
            'size' => 'required|string|max:50',
        ]);

        $sizeString = trim($request->input('size'));

        // Find matching tyre sizes
        $sizeIds = TyreSize::where('full_size', 'LIKE', '%' . $sizeString . '%')
            ->active()
            ->pluck('id');

        if ($sizeIds->isEmpty()) {
            return response()->json(['products' => [], 'message' => 'No matching sizes found.']);
        }

        $branches = Branch::active()->orderBy('name')->get();

        $products = Product::with(['brand', 'size', 'stockRecords'])
            ->active()
            ->whereIn('size_id', $sizeIds)
            ->orderBy('brand_id')
            ->get()
            ->map(function ($product) use ($branches) {
                $stockByBranch = [];
                foreach ($branches as $branch) {
                    $stockRecord = $product->stockRecords->firstWhere('branch_id', $branch->id);
                    $stockByBranch[] = [
                        'branch_id'   => $branch->id,
                        'branch_code' => $branch->code,
                        'branch_name' => $branch->name,
                        'quantity'    => $stockRecord ? $stockRecord->quantity : 0,
                        'available'   => $stockRecord ? $stockRecord->available_quantity : 0,
                        'min_qty'     => $stockRecord ? $stockRecord->min_quantity : 0,
                        'is_low'      => $stockRecord ? $stockRecord->is_low_stock : false,
                    ];
                }

                return [
                    'id'              => $product->id,
                    'product_code'    => $product->product_code,
                    'brand'           => $product->brand->name ?? 'N/A',
                    'model_name'      => $product->model_name,
                    'size'            => $product->size->full_size ?? 'N/A',
                    'cost_price'      => (float) $product->cost_price,
                    'sell_price'      => (float) $product->sell_price,
                    'total_stock'     => $product->getTotalStock(),
                    'stock_by_branch' => $stockByBranch,
                ];
            });

        return response()->json([
            'products' => $products,
            'branches' => $branches->map(function ($b) {
                return ['id' => $b->id, 'code' => $b->code, 'name' => $b->name];
            }),
        ]);
    }
}
