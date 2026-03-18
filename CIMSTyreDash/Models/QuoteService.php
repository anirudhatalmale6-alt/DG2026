<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * QuoteService model representing a service line item on a quote.
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int    $id
 * @property int    $quote_id
 * @property int    $service_id
 * @property int    $quantity
 * @property float  $unit_price
 * @property float  $line_total
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Quote   $quote
 * @property-read Service $service
 *
 * @method static Builder|QuoteService forQuote(int $quoteId)
 */
class QuoteService extends Model
{
    /** @var string */
    protected $table = 'cims_tyredash_quote_services';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'quote_id',
        'service_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'quote_id'   => 'integer',
        'service_id' => 'integer',
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Quote this service line belongs to.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    /**
     * Service definition.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

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
     * Calculate and return the line total (quantity * unit_price).
     *
     * @return float
     */
    public function calculateLineTotal(): float
    {
        return round($this->quantity * (float) $this->unit_price, 2);
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
}
