<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Quote model representing tyre shop quotations.
 *
 * @property int         $id
 * @property string      $quote_number
 * @property string|null $customer_order_ref
 * @property int|null    $customer_id
 * @property int|null    $vehicle_id
 * @property int|null    $branch_id
 * @property int|null    $salesman_id
 * @property string|null $salesman_name
 * @property \Carbon\Carbon $quote_date
 * @property \Carbon\Carbon|null $valid_until
 * @property string      $status          draft, sent, accepted, declined, expired, invoiced
 * @property string|null $customer_comment
 * @property string|null $internal_notes
 * @property float       $total_amount
 * @property int|null    $created_by
 * @property int|null    $updated_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Customer|null $customer
 * @property-read Vehicle|null  $vehicle
 * @property-read Branch|null   $branch
 *
 * @method static Builder|Quote byStatus(string $status)
 * @method static Builder|Quote draft()
 * @method static Builder|Quote sent()
 * @method static Builder|Quote accepted()
 * @method static Builder|Quote declined()
 * @method static Builder|Quote expired()
 * @method static Builder|Quote invoiced()
 * @method static Builder|Quote forBranch(int $branchId)
 * @method static Builder|Quote forSalesman(int $salesmanId)
 * @method static Builder|Quote dateRange(\Carbon\Carbon $from, \Carbon\Carbon $to)
 */
class Quote extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT    = 'draft';
    public const STATUS_SENT     = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_INVOICED = 'invoiced';

    /**
     * All valid status values.
     *
     * @var array<string>
     */
    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SENT,
        self::STATUS_ACCEPTED,
        self::STATUS_DECLINED,
        self::STATUS_EXPIRED,
        self::STATUS_INVOICED,
    ];

    /** @var string */
    protected $table = 'cims_tyredash_quotes';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'quote_number',
        'customer_order_ref',
        'customer_id',
        'vehicle_id',
        'branch_id',
        'salesman_id',
        'salesman_name',
        'quote_date',
        'valid_until',
        'status',
        'customer_comment',
        'internal_notes',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'customer_id'  => 'integer',
        'vehicle_id'   => 'integer',
        'branch_id'    => 'integer',
        'salesman_id'  => 'integer',
        'quote_date'   => 'date',
        'valid_until'  => 'date',
        'total_amount' => 'decimal:2',
        'created_by'   => 'integer',
        'updated_by'   => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Customer this quote is for.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Vehicle this quote is associated with.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Branch this quote was created at.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Tyre options on this quote (up to 5).
     */
    public function quoteOptions(): HasMany
    {
        return $this->hasMany(QuoteOption::class, 'quote_id')->orderBy('option_number');
    }

    /**
     * Services attached to this quote.
     */
    public function quoteServices(): HasMany
    {
        return $this->hasMany(QuoteService::class, 'quote_id');
    }

    /**
     * Job card created from this quote (if any).
     */
    public function jobCard(): HasOne
    {
        return $this->hasOne(JobCard::class, 'quote_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to filter quotes by a given status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for draft quotes.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope for sent quotes.
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope for accepted quotes.
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope for declined quotes.
     */
    public function scopeDeclined(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    /**
     * Scope for expired quotes.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope for invoiced quotes.
     */
    public function scopeInvoiced(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INVOICED);
    }

    /**
     * Scope to filter by branch.
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by salesman.
     */
    public function scopeForSalesman(Builder $query, int $salesmanId): Builder
    {
        return $query->where('salesman_id', $salesmanId);
    }

    /**
     * Scope to filter quotes within a date range.
     */
    public function scopeDateRange(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('quote_date', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | Quote Number Generation
    |--------------------------------------------------------------------------
    */

    /**
     * Generate the next sequential quote number.
     *
     * Format: {prefix}-{YYMMDD}-{sequence}
     * Example: TD-260318-001, TD-260318-002
     *
     * Uses the quote_prefix from TyreDashSetting (defaults to "TD").
     *
     * @param  string|null $prefix  Override prefix (uses setting if null)
     * @return string
     */
    public static function generateNextQuoteNumber(?string $prefix = null): string
    {
        $prefix    = $prefix ?? TyreDashSetting::getValue('quote_prefix', 'TD');
        $dateStr   = now()->format('ymd');
        $dayPrefix = $prefix . '-' . $dateStr . '-';

        // Find the highest sequence number for today
        $lastNumber = static::withTrashed()
            ->where('quote_number', 'LIKE', $dayPrefix . '%')
            ->orderByRaw('CAST(SUBSTRING(quote_number, ?) AS UNSIGNED) DESC', [strlen($dayPrefix) + 1])
            ->value('quote_number');

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
     * Recalculate and return the total amount for this quote.
     *
     * The total is the sum of:
     *   - The selected option's line_total (or the first option if none selected)
     *   - All service line_totals
     *
     * @return float
     */
    public function computeTotal(): float
    {
        // Get the selected tyre option total (or first option if none selected)
        $selectedOption = $this->quoteOptions()
            ->where('is_selected', true)
            ->first();

        $optionTotal = $selectedOption
            ? (float) $selectedOption->line_total
            : (float) ($this->quoteOptions()->first()?->line_total ?? 0);

        // Sum all service line totals
        $servicesTotal = (float) $this->quoteServices()->sum('line_total');

        return round($optionTotal + $servicesTotal, 2);
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
     * Check if the quote has expired based on valid_until date.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    /**
     * Check if the quote is still editable (draft or sent status).
     */
    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]);
    }

    /**
     * Check if the quote can be converted to a job card.
     */
    public function getCanConvertToJobCardAttribute(): bool
    {
        return $this->status === self::STATUS_ACCEPTED && !$this->jobCard;
    }
}
