<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * QuoteOption model representing a tyre option on a quote (up to 5 per quote).
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int    $id
 * @property int    $quote_id
 * @property int    $option_number  1-5
 * @property int    $product_id
 * @property int    $quantity       Default: 4
 * @property float  $unit_cost      Cost price at time of quote
 * @property float  $unit_price     Sell price incl VAT
 * @property float  $markup_pct
 * @property float  $discount_pct
 * @property float  $line_total
 * @property bool   $is_selected    Customer selected this option
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Quote   $quote
 * @property-read Product $product
 *
 * @method static Builder|QuoteOption selected()
 * @method static Builder|QuoteOption forQuote(int $quoteId)
 */
class QuoteOption extends Model
{
    /** @var string */
    protected $table = 'cims_tyredash_quote_options';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'quote_id',
        'option_number',
        'product_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'markup_pct',
        'discount_pct',
        'line_total',
        'is_selected',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'quote_id'      => 'integer',
        'option_number' => 'integer',
        'product_id'    => 'integer',
        'quantity'      => 'integer',
        'unit_cost'     => 'decimal:2',
        'unit_price'    => 'decimal:2',
        'markup_pct'    => 'decimal:2',
        'discount_pct'  => 'decimal:2',
        'line_total'    => 'decimal:2',
        'is_selected'   => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Quote this option belongs to.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    /**
     * Product (tyre) for this option.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only selected options.
     */
    public function scopeSelected(Builder $query): Builder
    {
        return $query->where('is_selected', true);
    }

    /**
     * Scope to filter by quote.
     */
    public function scopeForQuote(Builder $query, int $quoteId): Builder
    {
        return $query->where('quote_id', $quoteId);
    }

    /*
    |--------------------------------------------------------------------------
    | Calculation Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate and return the line total based on unit_price, quantity, and discount.
     *
     * Formula: line_total = quantity * unit_price * (1 - discount_pct / 100)
     *
     * @return float
     */
    public function calculateLineTotal(): float
    {
        $subtotal = $this->quantity * (float) $this->unit_price;

        if ($this->discount_pct > 0) {
            $subtotal *= (1 - (float) $this->discount_pct / 100);
        }

        return round($subtotal, 2);
    }

    /**
     * Recalculate and persist the line_total field.
     *
     * @return bool
     */
    public function recalculateLineTotal(): bool
    {
        $this->line_total = $this->calculateLineTotal();

        return $this->save();
    }

    /**
     * Get the effective unit price after discount.
     */
    public function getEffectiveUnitPriceAttribute(): float
    {
        $price = (float) $this->unit_price;

        if ($this->discount_pct > 0) {
            $price *= (1 - (float) $this->discount_pct / 100);
        }

        return round($price, 2);
    }

    /**
     * Get the gross profit for this option line.
     */
    public function getLineProfitAttribute(): float
    {
        $revenue = (float) $this->line_total;
        $cost    = $this->quantity * (float) $this->unit_cost;

        return round($revenue - $cost, 2);
    }
}
