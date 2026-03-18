<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Branch model representing physical tyre shop locations.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $code
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $manager_name
 * @property bool        $is_active
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @method static Builder|Branch active()
 * @method static Builder|Branch byCity(string $city)
 * @method static Builder|Branch byProvince(string $province)
 */
class Branch extends Model
{
    use SoftDeletes;

    /** @var string */
    protected $table = 'cims_tyredash_branches';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'manager_name',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Stock records at this branch.
     */
    public function stockRecords(): HasMany
    {
        return $this->hasMany(Stock::class, 'branch_id');
    }

    /**
     * Quotes created at this branch.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'branch_id');
    }

    /**
     * Job cards at this branch.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'branch_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active branches.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter branches by city.
     */
    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    /**
     * Scope to filter branches by province.
     */
    public function scopeByProvince(Builder $query, string $province): Builder
    {
        return $query->where('province', $province);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get a formatted full address string.
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->province,
        ]));
    }
}
