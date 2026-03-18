<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Vehicle model representing customer vehicles.
 *
 * @property int         $id
 * @property int|null    $customer_id
 * @property string|null $registration
 * @property string|null $make
 * @property string|null $model
 * @property string|null $variant
 * @property int|null    $year
 * @property int|null    $odometer_km
 * @property string|null $vin             Vehicle Identification Number
 * @property string|null $colour
 * @property string|null $current_tyre_size
 * @property string|null $notes
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Customer|null $customer
 *
 * @method static Builder|Vehicle search(string $term)
 * @method static Builder|Vehicle byRegistration(string $registration)
 */
class Vehicle extends Model
{
    use SoftDeletes;

    /** @var string */
    protected $table = 'cims_tyredash_vehicles';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'customer_id',
        'registration',
        'make',
        'model',
        'variant',
        'year',
        'odometer_km',
        'vin',
        'colour',
        'current_tyre_size',
        'notes',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'customer_id' => 'integer',
        'year'        => 'integer',
        'odometer_km' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Customer who owns this vehicle.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Quotes associated with this vehicle.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'vehicle_id');
    }

    /**
     * Job cards associated with this vehicle.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'vehicle_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Search vehicles by registration, make, model, or VIN.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = '%' . $term . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('registration', 'LIKE', $term)
              ->orWhere('make', 'LIKE', $term)
              ->orWhere('model', 'LIKE', $term)
              ->orWhere('vin', 'LIKE', $term);
        });
    }

    /**
     * Scope to find a vehicle by exact registration number.
     */
    public function scopeByRegistration(Builder $query, string $registration): Builder
    {
        return $query->where('registration', $registration);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get a display label: "Make Model Variant (Year) - Registration".
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->make,
            $this->model,
            $this->variant,
            $this->year ? "({$this->year})" : null,
        ]);

        $name = implode(' ', $parts);

        if ($this->registration) {
            $name .= ' - ' . strtoupper($this->registration);
        }

        return $name ?: 'Unknown Vehicle';
    }

    /**
     * Get the vehicle make and model combined.
     */
    public function getMakeModelAttribute(): string
    {
        return trim(($this->make ?? '') . ' ' . ($this->model ?? ''));
    }
}
