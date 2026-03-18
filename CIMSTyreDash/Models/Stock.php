<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Stock model tracking product quantities at each branch.
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int    $id
 * @property int    $product_id
 * @property int    $branch_id
 * @property int    $quantity       Current stock on hand
 * @property int    $min_quantity   Minimum stock alert threshold
 * @property int    $reserved       Quantity reserved for pending quotes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Product $product
 * @property-read Branch  $branch
 *
 * @method static Builder|Stock lowStock()
 * @method static Builder|Stock outOfStock()
 * @method static Builder|Stock hasAvailable()
 * @method static Builder|Stock forProduct(int $productId)
 * @method static Builder|Stock forBranch(int $branchId)
 */
class Stock extends Model
{
    /** @var string */
    protected $table = 'cims_tyredash_stock';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'product_id',
        'branch_id',
        'quantity',
        'min_quantity',
        'reserved',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'product_id'   => 'integer',
        'branch_id'    => 'integer',
        'quantity'     => 'integer',
        'min_quantity' => 'integer',
        'reserved'     => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The product this stock record belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * The branch this stock record belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to stock records where quantity is at or below the minimum threshold.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity')
                     ->where('quantity', '>', 0);
    }

    /**
     * Scope to stock records where quantity is zero.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope to stock records that have available stock (quantity minus reserved > 0).
     */
    public function scopeHasAvailable(Builder $query): Builder
    {
        return $query->whereRaw('(quantity - reserved) > 0');
    }

    /**
     * Scope to filter by product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter by branch.
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get available quantity (on hand minus reserved).
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    /**
     * Check if this stock record is below the minimum threshold.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->min_quantity && $this->quantity > 0;
    }

    /**
     * Check if this stock record is out of stock.
     */
    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity <= 0;
    }

    /**
     * Adjust the stock quantity by a given delta (positive to add, negative to deduct).
     *
     * @param  int  $delta  Amount to add (positive) or subtract (negative)
     * @return bool
     */
    public function adjustQuantity(int $delta): bool
    {
        $this->quantity = max(0, $this->quantity + $delta);

        return $this->save();
    }

    /**
     * Reserve a given quantity for a pending quote or job card.
     *
     * @param  int  $qty  Quantity to reserve
     * @return bool
     */
    public function reserve(int $qty): bool
    {
        $this->reserved = min($this->quantity, $this->reserved + $qty);

        return $this->save();
    }

    /**
     * Release a given quantity from reservation.
     *
     * @param  int  $qty  Quantity to release
     * @return bool
     */
    public function release(int $qty): bool
    {
        $this->reserved = max(0, $this->reserved - $qty);

        return $this->save();
    }
}
