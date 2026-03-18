<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Customer model for tyre shop clients.
 *
 * @property int         $id
 * @property int|null    $client_master_id  Link to CIMS client_master
 * @property string      $first_name
 * @property string      $last_name
 * @property string|null $company_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $cell
 * @property string|null $vat_number
 * @property string|null $debtor_account
 * @property string      $customer_type     retail, fleet, corporate, government, insurance
 * @property float       $credit_limit
 * @property float       $balance
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $notes
 * @property bool        $is_active
 * @property int|null    $created_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @method static Builder|Customer active()
 * @method static Builder|Customer search(string $term)
 * @method static Builder|Customer ofType(string $type)
 * @method static Builder|Customer withCreditAvailable()
 */
class Customer extends Model
{
    use SoftDeletes;

    /** Customer type constants */
    public const TYPE_RETAIL     = 'retail';
    public const TYPE_FLEET      = 'fleet';
    public const TYPE_CORPORATE  = 'corporate';
    public const TYPE_GOVERNMENT = 'government';
    public const TYPE_INSURANCE  = 'insurance';

    /** @var string */
    protected $table = 'cims_tyredash_customers';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'client_master_id',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'cell',
        'vat_number',
        'debtor_account',
        'customer_type',
        'credit_limit',
        'balance',
        'address',
        'city',
        'province',
        'postal_code',
        'notes',
        'is_active',
        'created_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'client_master_id' => 'integer',
        'credit_limit'     => 'decimal:2',
        'balance'          => 'decimal:2',
        'is_active'        => 'boolean',
        'created_by'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Vehicles belonging to this customer.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'customer_id');
    }

    /**
     * Quotes for this customer.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'customer_id');
    }

    /**
     * Job cards for this customer.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'customer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active customers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Search customers by name, phone, cell, or email.
     *
     * @param  Builder $query
     * @param  string  $term  Search term
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = '%' . $term . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('first_name', 'LIKE', $term)
              ->orWhere('last_name', 'LIKE', $term)
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term])
              ->orWhere('company_name', 'LIKE', $term)
              ->orWhere('phone', 'LIKE', $term)
              ->orWhere('cell', 'LIKE', $term)
              ->orWhere('email', 'LIKE', $term)
              ->orWhere('debtor_account', 'LIKE', $term);
        });
    }

    /**
     * Scope to filter by customer type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('customer_type', $type);
    }

    /**
     * Scope to customers who have remaining credit (credit_limit > balance).
     */
    public function scopeWithCreditAvailable(Builder $query): Builder
    {
        return $query->where('credit_limit', '>', 0)
                     ->whereColumn('credit_limit', '>', 'balance');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get a display name: company name if available, otherwise full name.
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->company_name)) {
            return $this->company_name . ' (' . $this->full_name . ')';
        }

        return $this->full_name;
    }

    /**
     * Get the available credit remaining.
     */
    public function getAvailableCreditAttribute(): float
    {
        return max(0, (float) $this->credit_limit - (float) $this->balance);
    }

    /**
     * Get the primary contact number (cell preferred, then phone).
     */
    public function getPrimaryPhoneAttribute(): ?string
    {
        return $this->cell ?: $this->phone;
    }

    /**
     * Get all valid customer types.
     *
     * @return array<string>
     */
    public static function getCustomerTypes(): array
    {
        return [
            self::TYPE_RETAIL,
            self::TYPE_FLEET,
            self::TYPE_CORPORATE,
            self::TYPE_GOVERNMENT,
            self::TYPE_INSURANCE,
        ];
    }
}
