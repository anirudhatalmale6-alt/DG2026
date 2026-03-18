<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * JobCard model representing a tyre fitment / service work order.
 *
 * @property int         $id
 * @property string      $job_card_number
 * @property int|null    $quote_id
 * @property int|null    $customer_id
 * @property int|null    $vehicle_id
 * @property int|null    $branch_id
 * @property int|null    $technician_id
 * @property string|null $technician_name
 * @property \Carbon\Carbon $job_date
 * @property string      $status          open, in_progress, awaiting_parts, complete, invoiced, cancelled
 * @property int|null    $odometer_in
 * @property int|null    $odometer_out
 * @property string|null $vehicle_condition_notes
 * @property string|null $work_notes
 * @property float       $total_amount
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property int|null    $invoice_id      Grow CRM invoice ID
 * @property int|null    $created_by
 * @property int|null    $updated_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Quote|null    $quote
 * @property-read Customer|null $customer
 * @property-read Vehicle|null  $vehicle
 * @property-read Branch|null   $branch
 *
 * @method static Builder|JobCard byStatus(string $status)
 * @method static Builder|JobCard open()
 * @method static Builder|JobCard inProgress()
 * @method static Builder|JobCard complete()
 * @method static Builder|JobCard forBranch(int $branchId)
 * @method static Builder|JobCard forTechnician(int $technicianId)
 * @method static Builder|JobCard dateRange($from, $to)
 */
class JobCard extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_OPEN           = 'open';
    public const STATUS_IN_PROGRESS    = 'in_progress';
    public const STATUS_AWAITING_PARTS = 'awaiting_parts';
    public const STATUS_COMPLETE       = 'complete';
    public const STATUS_INVOICED       = 'invoiced';
    public const STATUS_CANCELLED      = 'cancelled';

    /**
     * All valid status values.
     *
     * @var array<string>
     */
    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_AWAITING_PARTS,
        self::STATUS_COMPLETE,
        self::STATUS_INVOICED,
        self::STATUS_CANCELLED,
    ];

    /** @var string */
    protected $table = 'cims_tyredash_job_cards';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'job_card_number',
        'quote_id',
        'customer_id',
        'vehicle_id',
        'branch_id',
        'technician_id',
        'technician_name',
        'job_date',
        'status',
        'odometer_in',
        'odometer_out',
        'vehicle_condition_notes',
        'work_notes',
        'total_amount',
        'started_at',
        'completed_at',
        'invoice_id',
        'created_by',
        'updated_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'quote_id'      => 'integer',
        'customer_id'   => 'integer',
        'vehicle_id'    => 'integer',
        'branch_id'     => 'integer',
        'technician_id' => 'integer',
        'job_date'      => 'date',
        'odometer_in'   => 'integer',
        'odometer_out'  => 'integer',
        'total_amount'  => 'decimal:2',
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
        'invoice_id'    => 'integer',
        'created_by'    => 'integer',
        'updated_by'    => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Quote this job card was created from (if any).
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    /**
     * Customer for this job card.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Vehicle being serviced.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Branch where the work is being done.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Tyre lines on this job card.
     */
    public function jobCardTyres(): HasMany
    {
        return $this->hasMany(JobCardTyre::class, 'job_card_id');
    }

    /**
     * Service lines on this job card.
     */
    public function jobCardServices(): HasMany
    {
        return $this->hasMany(JobCardService::class, 'job_card_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for open job cards.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for in-progress job cards.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for active job cards (not complete, invoiced, or cancelled).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_AWAITING_PARTS,
        ]);
    }

    /**
     * Scope for completed job cards.
     */
    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETE);
    }

    /**
     * Scope to filter by branch.
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by technician.
     */
    public function scopeForTechnician(Builder $query, int $technicianId): Builder
    {
        return $query->where('technician_id', $technicianId);
    }

    /**
     * Scope to filter job cards within a date range.
     */
    public function scopeDateRange(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('job_date', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | Job Card Number Generation
    |--------------------------------------------------------------------------
    */

    /**
     * Generate the next sequential job card number.
     *
     * Format: {prefix}-{YYMMDD}-{sequence}
     * Example: JC-260318-001, JC-260318-002
     *
     * Uses the job_card_prefix from TyreDashSetting (defaults to "JC").
     *
     * @param  string|null $prefix  Override prefix (uses setting if null)
     * @return string
     */
    public static function generateNextJobCardNumber(?string $prefix = null): string
    {
        $prefix    = $prefix ?? TyreDashSetting::getValue('job_card_prefix', 'JC');
        $dateStr   = now()->format('ymd');
        $dayPrefix = $prefix . '-' . $dateStr . '-';

        // Find the highest sequence number for today
        $lastNumber = static::withTrashed()
            ->where('job_card_number', 'LIKE', $dayPrefix . '%')
            ->orderByRaw('CAST(SUBSTRING(job_card_number, ?) AS UNSIGNED) DESC', [strlen($dayPrefix) + 1])
            ->value('job_card_number');

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber, strlen($dayPrefix));
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $dayPrefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Totals
    |--------------------------------------------------------------------------
    */

    /**
     * Recalculate and return the total amount for this job card.
     *
     * Total = sum of tyre line_totals + sum of service line_totals.
     *
     * @return float
     */
    public function computeTotal(): float
    {
        $tyresTotalAmount   = (float) $this->jobCardTyres()->sum('line_total');
        $servicesTotalAmount = (float) $this->jobCardServices()->sum('line_total');

        return round($tyresTotalAmount + $servicesTotalAmount, 2);
    }

    /**
     * Recalculate and persist the total_amount field.
     *
     * @return bool
     */
    public function recalculateTotal(): bool
    {
        $this->total_amount = $this->computeTotal();

        return $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the job card is still editable (open or in_progress).
     */
    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_AWAITING_PARTS,
        ]);
    }

    /**
     * Check if this job card can be invoiced.
     */
    public function getCanInvoiceAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETE && !$this->invoice_id;
    }

    /**
     * Get the duration in minutes if started and completed.
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->completed_at);
    }
}
