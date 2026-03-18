<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\Brand;
use Modules\CIMSTyreDash\Models\Product;
use Modules\CIMSTyreDash\Models\Stock;
use Modules\CIMSTyreDash\Models\Quote;
use Modules\CIMSTyreDash\Models\JobCard;
use Modules\CIMSTyreDash\Models\TyreDashSetting;

class DashboardController extends Controller
{
    /**
     * Show the TyreDash dashboard with stats cards, recent quotes, and recent job cards.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Stats cards
        $totalProducts = Product::active()->count();
        $totalBrands   = Brand::active()->count();

        $totalStockValue = Stock::join('cims_tyredash_products', 'cims_tyredash_stock.product_id', '=', 'cims_tyredash_products.id')
            ->selectRaw('COALESCE(SUM(cims_tyredash_stock.quantity * cims_tyredash_products.cost_price), 0) as total_value')
            ->value('total_value');

        $lowStockAlerts = Stock::lowStock()->count();

        $quotesThisMonth = Quote::whereMonth('quote_date', now()->month)
            ->whereYear('quote_date', now()->year)
            ->count();

        $jobCardsToday = JobCard::whereDate('job_date', today())->count();

        // Recent quotes (latest 10)
        $recentQuotes = Quote::with(['customer', 'branch'])
            ->latest('quote_date')
            ->latest('id')
            ->limit(10)
            ->get();

        // Recent job cards (latest 10)
        $recentJobCards = JobCard::with(['customer', 'branch', 'vehicle'])
            ->latest('job_date')
            ->latest('id')
            ->limit(10)
            ->get();

        // Currency symbol for display
        $currencySymbol = TyreDashSetting::getCurrencySymbol();

        return view('cimstyredash::dashboard.index', compact(
            'totalProducts',
            'totalBrands',
            'totalStockValue',
            'lowStockAlerts',
            'quotesThisMonth',
            'jobCardsToday',
            'recentQuotes',
            'recentJobCards',
            'currencySymbol'
        ));
    }
}
