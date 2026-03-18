<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * JobCardTyre model representing a tyre line item on a job card (fitted/removed).
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int         $id
 * @property int         $job_card_id
 * @property int         $product_id
 * @property int         $quantity
 * @property string|null $position           FL, FR, RL, RR, spare
 * @property string|null $serial_number_new  DOT/serial of new tyre fitted
 * @property string|null $serial_number_old  DOT/serial of old tyre removed
 * @property float       $unit_price
 * @property float       $line_total
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read JobCard $jobCard
 * @property-read Product $product
 *
 * @method static Builder|JobCardTyre forJobCard(int $jobCardId)
 * @method static Builder|JobCardTyre byPosition(string $position)
 */
class JobCardTyre extends Model
{
    /** Tyre position constants */
    public const POSITION_FRONT_LEFT  = 'FL';
    public const POSITION_FRONT_RIGHT = 'FR';
    public const POSITION_REAR_LEFT   = 'RL';
    public const POSITION_REAR_RIGHT  = 'RR';
    public const POSITION_SPARE       = 'spare';

    /**
     * All valid tyre positions.
     *
     * @var array<string>
     */
    public const POSITIONS = [
        self::POSITION_FRONT_LEFT,
        self::POSITION_FRONT_RIGHT,
        self::POSITION_REAR_LEFT,
        self::POSITION_REAR_RIGHT,
        self::POSITION_SPARE,
    ];

    /** @var string */
    protected $table = 'cims_tyredash_job_card_tyres';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'job_card_id',
        'product_id',
        'quantity',
        'position',
        'serial_number_new',
        'serial_number_old',
        'unit_price',
        'line_total',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'job_card_id' => 'integer',
        'product_id'  => 'integer',
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'line_total'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Job card this tyre line belongs to.
     */
    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class, 'job_card_id');
    }

    /**
     * Product (tyre) for this line item.
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
     * Scope to filter by job card.
     */
    public function scopeForJobCard(Builder $query, int $jobCardId): Builder
    {
        return $query->where('job_card_id', $jobCardId);
    }

    /**
     * Scope to filter by tyre position.
     */
    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
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

    /**
     * Get a human-readable position label.
     */
    public function getPositionLabelAttribute(): string
    {
        $labels = [
            self::POSITION_FRONT_LEFT  => 'Front Left',
            self::POSITION_FRONT_RIGHT => 'Front Right',
            self::POSITION_REAR_LEFT   => 'Rear Left',
            self::POSITION_REAR_RIGHT  => 'Rear Right',
            self::POSITION_SPARE       => 'Spare',
        ];

        return $labels[$this->position] ?? ($this->position ?? 'N/A');
    }
}
