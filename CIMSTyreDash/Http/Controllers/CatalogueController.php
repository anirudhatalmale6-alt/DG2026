<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Brand;
use Modules\CIMSTyreDash\Models\Branch;
use Modules\CIMSTyreDash\Models\Category;
use Modules\CIMSTyreDash\Models\Product;
use Modules\CIMSTyreDash\Models\Stock;
use Modules\CIMSTyreDash\Models\TyreSize;

class CatalogueController extends Controller
{
    /**
     * List all products with filters (brand, category, size, search) and pagination.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'size']);

        // Apply filters
        if ($request->filled('brand_id')) {
            $query->byBrand($request->input('brand_id'));
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->input('category_id'));
        }

        if ($request->filled('size_id')) {
            $query->bySize($request->input('size_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('pattern_type')) {
            $query->byPatternType($request->input('pattern_type'));
        }

        if ($request->input('active_only') === '1') {
            $query->active();
        }

        $products   = $query->latest()->paginate(25)->withQueryString();
        $brands     = Brand::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();
        $sizes      = TyreSize::active()->orderBy('full_size')->get();

        return view('cimstyredash::catalogue.index', compact(
            'products',
            'brands',
            'categories',
            'sizes'
        ));
    }

    /**
     * Show the form to create a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $brands     = Brand::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();
        $sizes      = TyreSize::active()->orderBy('full_size')->get();

        return view('cimstyredash::catalogue.create', compact(
            'brands',
            'categories',
            'sizes'
        ));
    }

    /**
     * Validate and save a new product.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_id'         => 'required|integer|exists:cims_tyredash_brands,id',
            'category_id'      => 'required|integer|exists:cims_tyredash_categories,id',
            'size_id'          => 'required|integer|exists:cims_tyredash_sizes,id',
            'model_name'       => 'required|string|max:255',
            'product_code'     => 'required|string|max:50|unique:cims_tyredash_products,product_code',
            'full_description' => 'nullable|string|max:500',
            'load_index'       => 'nullable|string|max:20',
            'speed_rating'     => 'nullable|string|max:5',
            'pattern_type'     => 'nullable|string|max:50',
            'cost_price'       => 'required|numeric|min:0',
            'sell_price'       => 'required|numeric|min:0',
            'markup_pct'       => 'nullable|numeric|min:0|max:999',
            'is_active'        => 'sometimes|boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        Product::create($validated);

        return redirect()
            ->route('cimstyredash.catalogue.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form to edit an existing product.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product    = Product::findOrFail($id);
        $brands     = Brand::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();
        $sizes      = TyreSize::active()->orderBy('full_size')->get();

        return view('cimstyredash::catalogue.edit', compact(
            'product',
            'brands',
            'categories',
            'sizes'
        ));
    }

    /**
     * Validate and update an existing product.
     *
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'brand_id'         => 'required|integer|exists:cims_tyredash_brands,id',
            'category_id'      => 'required|integer|exists:cims_tyredash_categories,id',
            'size_id'          => 'required|integer|exists:cims_tyredash_sizes,id',
            'model_name'       => 'required|string|max:255',
            'product_code'     => 'required|string|max:50|unique:cims_tyredash_products,product_code,' . $product->id,
            'full_description' => 'nullable|string|max:500',
            'load_index'       => 'nullable|string|max:20',
            'speed_rating'     => 'nullable|string|max:5',
            'pattern_type'     => 'nullable|string|max:50',
            'cost_price'       => 'required|numeric|min:0',
            'sell_price'       => 'required|numeric|min:0',
            'markup_pct'       => 'nullable|numeric|min:0|max:999',
            'is_active'        => 'sometimes|boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        return redirect()
            ->route('cimstyredash.catalogue.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Soft delete a product.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()
            ->route('cimstyredash.catalogue.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * AJAX - Search products by tyre size string, return JSON with brand comparison data.
     *
     * Returns products matching the given size, grouped by brand, with price and stock
     * per branch for side-by-side comparison.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function sizeSearch(Request $request): JsonResponse
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

        // Get products for these sizes with brand and stock data
        $products = Product::with(['brand', 'size', 'stockRecords.branch'])
            ->active()
            ->whereIn('size_id', $sizes)
            ->orderBy('brand_id')
            ->orderBy('sell_price')
            ->get();

        $branches = Branch::active()->orderBy('name')->get();

        $results = $products->map(function ($product) use ($branches) {
            $stockByBranch = [];
            foreach ($branches as $branch) {
                $stockRecord = $product->stockRecords->firstWhere('branch_id', $branch->id);
                $stockByBranch[$branch->code] = [
                    'branch_name' => $branch->name,
                    'quantity'    => $stockRecord ? $stockRecord->quantity : 0,
                    'available'   => $stockRecord ? $stockRecord->available_quantity : 0,
                ];
            }

            return [
                'id'               => $product->id,
                'product_code'     => $product->product_code,
                'brand'            => $product->brand->name ?? 'N/A',
                'brand_logo'       => $product->brand->logo_url ?? null,
                'image_url'        => $product->image_url,
                'model_name'       => $product->model_name,
                'full_description' => $product->full_description,
                'size'             => $product->size->full_size ?? 'N/A',
                'load_index'       => $product->load_index,
                'speed_rating'     => $product->speed_rating,
                'cost_price'       => (float) $product->cost_price,
                'sell_price'       => (float) $product->sell_price,
                'markup_pct'       => (float) $product->markup_pct,
                'stock_by_branch'  => $stockByBranch,
                'total_stock'      => $product->getTotalStock(),
            ];
        });

        return response()->json([
            'products' => $results,
            'branches' => $branches->pluck('name', 'code'),
        ]);
    }

    /**
     * AJAX - Search products by term, return JSON.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function ajaxSearchProducts(Request $request): JsonResponse
    {
        $request->validate([
            'term' => 'required|string|min:2|max:100',
        ]);

        $products = Product::with(['brand', 'size'])
            ->active()
            ->search($request->input('term'))
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id'           => $product->id,
                    'product_code' => $product->product_code,
                    'brand'        => $product->brand->name ?? 'N/A',
                    'brand_logo'   => $product->brand->logo_url ?? null,
                    'image_url'    => $product->image_url,
                    'model_name'   => $product->model_name,
                    'size'         => $product->size->full_size ?? 'N/A',
                    'sell_price'   => (float) $product->sell_price,
                    'cost_price'   => (float) $product->cost_price,
                    'display_name' => $product->display_name,
                    'total_stock'  => $product->getTotalStock(),
                ];
            });

        return response()->json(['products' => $products]);
    }
}
