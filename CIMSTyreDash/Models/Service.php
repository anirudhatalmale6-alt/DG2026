<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service model for tyre shop services (alignment, balancing, etc.).
 *
 * @property int         $id
 * @property string      $name
 * @property string      $code
 * @property string|null $description
 * @property float       $price           Default price incl VAT
 * @property bool        $price_per_tyre  If true, multiply by qty of tyres
 * @property int         $sort_order
 * @property bool        $is_active
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @method static Builder|Service active()
 * @method static Builder|Service ordered()
 * @method static Builder|Service perTyre()
 * @method static Builder|Service fixedPrice()
 */
class Service extends Model
{
    use SoftDeletes;

    /** @var string */
    protected $table = 'cims_tyredash_services';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'price_per_tyre',
        'sort_order',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'price'          => 'decimal:2',
        'price_per_tyre' => 'boolean',
        'sort_order'     => 'integer',
        'is_active'      => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Quote service lines referencing this service.
     */
    public function quoteServices(): HasMany
    {
        return $this->hasMany(QuoteService::class, 'service_id');
    }

    /**
     * Job card service lines referencing this service.
     */
    public function jobCardServices(): HasMany
    {
        return $this->hasMany(JobCardService::class, 'service_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active services.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order ascending, then name.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to only services priced per tyre.
     */
    public function scopePerTyre(Builder $query): Builder
    {
        return $query->where('price_per_tyre', true);
    }

    /**
     * Scope to only fixed-price services (not per tyre).
     */
    public function scopeFixedPrice(Builder $query): Builder
    {
        return $query->where('price_per_tyre', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate the total price for this service given a tyre quantity.
     *
     * @param  int   $tyreQuantity  Number of tyres (only relevant if price_per_tyre is true)
     * @param  float|null $overridePrice  Use a custom unit price instead of the default
     * @return float
     */
    public function calculateTotal(int $tyreQuantity = 1, ?float $overridePrice = null): float
    {
        $unitPrice = $overridePrice ?? (float) $this->price;
        $quantity  = $this->price_per_tyre ? $tyreQuantity : 1;

        return round($unitPrice * $quantity, 2);
    }
}
