<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Product model representing a specific tyre product (brand + model + size combination).
 *
 * @property int         $id
 * @property int         $brand_id
 * @property int         $category_id
 * @property int         $size_id
 * @property string      $model_name        e.g. "Primacy 5"
 * @property string      $product_code      Stock/supplier code (unique)
 * @property string|null $full_description   e.g. "205/65 R16 95W TL Primacy 5 MI"
 * @property string|null $load_index        e.g. "95", "107/105"
 * @property string|null $speed_rating      e.g. "H", "V", "W"
 * @property string|null $pattern_type      e.g. "Highway", "All-Terrain"
 * @property float       $cost_price        Excl VAT cost from supplier
 * @property float       $sell_price        Incl VAT recommended sell price
 * @property float       $markup_pct        Default markup percentage
 * @property bool        $is_active
 * @property int|null    $created_by
 * @property int|null    $updated_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Brand    $brand
 * @property-read Category $category
 * @property-read TyreSize $size
 *
 * @method static Builder|Product active()
 * @method static Builder|Product bySize(int $sizeId)
 * @method static Builder|Product byBrand(int $brandId)
 * @method static Builder|Product byCategory(int $categoryId)
 * @method static Builder|Product byPatternType(string $patternType)
 * @method static Builder|Product inStock()
 * @method static Builder|Product search(string $term)
 */
class Product extends Model
{
    use SoftDeletes;

    /** @var string */
    protected $table = 'cims_tyredash_products';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'brand_id',
        'category_id',
        'size_id',
        'model_name',
        'product_code',
        'full_description',
        'load_index',
        'speed_rating',
        'pattern_type',
        'cost_price',
        'sell_price',
        'markup_pct',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'brand_id'    => 'integer',
        'category_id' => 'integer',
        'size_id'     => 'integer',
        'cost_price'  => 'decimal:2',
        'sell_price'  => 'decimal:2',
        'markup_pct'  => 'decimal:2',
        'is_active'   => 'boolean',
        'created_by'  => 'integer',
        'updated_by'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Brand this product belongs to.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Tyre size this product is associated with.
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(TyreSize::class, 'size_id');
    }

    /**
     * Stock records for this product across all branches.
     */
    public function stockRecords(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

    /**
     * Quote options that reference this product.
     */
    public function quoteOptions(): HasMany
    {
        return $this->hasMany(QuoteOption::class, 'product_id');
    }

    /**
     * Job card tyre lines that reference this product.
     */
    public function jobCardTyres(): HasMany
    {
        return $this->hasMany(JobCardTyre::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter products by tyre size.
     */
    public function scopeBySize(Builder $query, int $sizeId): Builder
    {
        return $query->where('size_id', $sizeId);
    }

    /**
     * Scope to filter products by brand.
     */
    public function scopeByBrand(Builder $query, int $brandId): Builder
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope to filter products by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter products by pattern type (Highway, All-Terrain, etc.).
     */
    public function scopeByPatternType(Builder $query, string $patternType): Builder
    {
        return $query->where('pattern_type', $patternType);
    }

    /**
     * Scope to only products that have stock > 0 in at least one branch.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('stockRecords', function (Builder $q) {
            $q->where('quantity', '>', 0);
        });
    }

    /**
     * Scope to search products by model name, product code, or full description.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = '%' . $term . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('model_name', 'LIKE', $term)
              ->orWhere('product_code', 'LIKE', $term)
              ->orWhere('full_description', 'LIKE', $term);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the total stock quantity across all branches.
     */
    public function getTotalStock(): int
    {
        return (int) $this->stockRecords()->sum('quantity');
    }

    /**
     * Get the total available stock (quantity minus reserved) across all branches.
     */
    public function getAvailableStock(): int
    {
        return (int) $this->stockRecords()
            ->selectRaw('COALESCE(SUM(quantity - reserved), 0) as available')
            ->value('available');
    }

    /**
     * Get the stock quantity at a specific branch.
     *
     * @param  int $branchId
     * @return int
     */
    public function getStockAtBranch(int $branchId): int
    {
        $stock = $this->stockRecords()->where('branch_id', $branchId)->first();

        return $stock ? $stock->quantity : 0;
    }

    /**
     * Get available stock (minus reserved) at a specific branch.
     *
     * @param  int $branchId
     * @return int
     */
    public function getAvailableStockAtBranch(int $branchId): int
    {
        $stock = $this->stockRecords()->where('branch_id', $branchId)->first();

        return $stock ? ($stock->quantity - $stock->reserved) : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate the sell price from cost price and markup percentage.
     *
     * Formula: sell_price = cost_price * (1 + markup_pct / 100)
     *
     * @param  float|null $costPrice  Override cost price (defaults to model's cost_price)
     * @param  float|null $markupPct  Override markup % (defaults to model's markup_pct)
     * @return float      Calculated sell price rounded to 2 decimal places
     */
    public function calculateSellPrice(?float $costPrice = null, ?float $markupPct = null): float
    {
        $cost   = $costPrice ?? (float) $this->cost_price;
        $markup = $markupPct ?? (float) $this->markup_pct;

        return round($cost * (1 + $markup / 100), 2);
    }

    /**
     * Calculate the gross profit for this product.
     *
     * @return float
     */
    public function getGrossProfitAttribute(): float
    {
        return round((float) $this->sell_price - (float) $this->cost_price, 2);
    }

    /**
     * Calculate the effective margin percentage.
     *
     * @return float
     */
    public function getEffectiveMarginAttribute(): float
    {
        $sell = (float) $this->sell_price;

        if ($sell <= 0) {
            return 0.0;
        }

        return round((($sell - (float) $this->cost_price) / $sell) * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get a short display label: "Brand ModelName (Size)".
     */
    public function getDisplayNameAttribute(): string
    {
        $brand = $this->relationLoaded('brand') ? $this->brand?->name : '';
        $size  = $this->relationLoaded('size') ? $this->size?->full_size : '';

        $parts = array_filter([$brand, $this->model_name, $size ? "({$size})" : '']);

        return implode(' ', $parts);
    }
}
