<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * JobCardService model representing a service line item on a job card.
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int    $id
 * @property int    $job_card_id
 * @property int    $service_id
 * @property int    $quantity
 * @property float  $unit_price
 * @property float  $line_total
 * @property bool   $completed   Whether this service has been performed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read JobCard $jobCard
 * @property-read Service $service
 *
 * @method static Builder|JobCardService forJobCard(int $jobCardId)
 * @method static Builder|JobCardService completed()
 * @method static Builder|JobCardService pending()
 */
class JobCardService extends Model
{
    /** @var string */
    protected $table = 'cims_tyredash_job_card_services';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'job_card_id',
        'service_id',
        'quantity',
        'unit_price',
        'line_total',
        'completed',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'job_card_id' => 'integer',
        'service_id'  => 'integer',
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'line_total'  => 'decimal:2',
        'completed'   => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Job card this service line belongs to.
     */
    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class, 'job_card_id');
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
     * Scope to filter by job card.
     */
    public function scopeForJobCard(Builder $query, int $jobCardId): Builder
    {
        return $query->where('job_card_id', $jobCardId);
    }

    /**
     * Scope to only completed service lines.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('completed', true);
    }

    /**
     * Scope to only pending (not completed) service lines.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('completed', false);
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
     * Mark this service line as completed.
     *
     * @return bool
     */
    public function markCompleted(): bool
    {
        $this->completed = true;

        return $this->save();
    }
}
