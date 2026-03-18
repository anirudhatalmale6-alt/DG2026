<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * TyreDashSetting model for key-value configuration storage.
 *
 * Note: This table does NOT use soft deletes.
 *
 * @property int         $id
 * @property string      $setting_key
 * @property string|null $setting_value
 * @property string      $setting_group   e.g. general, pricing, quotes, jobcards, stock
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Builder|TyreDashSetting byGroup(string $group)
 * @method static Builder|TyreDashSetting byKey(string $key)
 */
class TyreDashSetting extends Model
{
    /** Setting group constants */
    public const GROUP_GENERAL  = 'general';
    public const GROUP_PRICING  = 'pricing';
    public const GROUP_QUOTES   = 'quotes';
    public const GROUP_JOBCARDS = 'jobcards';
    public const GROUP_STOCK    = 'stock';

    /** @var string */
    protected $table = 'cims_tyredash_settings';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
    ];

    /** @var array<string, string> */
    protected $casts = [];

    /**
     * In-memory cache to avoid repeated DB queries within a single request.
     *
     * @var array<string, string|null>|null
     */
    protected static ?array $cache = null;

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to filter settings by group.
     */
    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('setting_group', $group);
    }

    /**
     * Scope to filter by setting key.
     */
    public function scopeByKey(Builder $query, string $key): Builder
    {
        return $query->where('setting_key', $key);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get a setting value by key.
     *
     * Uses an in-memory cache to minimize queries within a request lifecycle.
     *
     * @param  string      $key     The setting key (e.g. 'quote_prefix')
     * @param  string|null $default Default value if the key is not found
     * @return string|null
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        // Populate cache on first access
        if (static::$cache === null) {
            static::loadCache();
        }

        return static::$cache[$key] ?? $default;
    }

    /**
     * Set (create or update) a setting value by key.
     *
     * @param  string      $key   The setting key
     * @param  string|null $value The value to store
     * @param  string|null $group Optional group (only used on creation, not update)
     * @return static
     */
    public static function setValue(string $key, ?string $value, ?string $group = null): static
    {
        $setting = static::where('setting_key', $key)->first();

        if ($setting) {
            $setting->setting_value = $value;
            $setting->save();
        } else {
            $setting = static::create([
                'setting_key'   => $key,
                'setting_value' => $value,
                'setting_group' => $group ?? self::GROUP_GENERAL,
            ]);
        }

        // Update the cache
        if (static::$cache !== null) {
            static::$cache[$key] = $value;
        }

        return $setting;
    }

    /**
     * Get all settings within a specific group as a key => value collection.
     *
     * @param  string $group  The setting group (e.g. 'pricing')
     * @return Collection<string, string|null>  Keyed by setting_key
     */
    public static function getByGroup(string $group): Collection
    {
        return static::where('setting_group', $group)
            ->pluck('setting_value', 'setting_key');
    }

    /**
     * Get all settings as a flat key => value array.
     *
     * @return array<string, string|null>
     */
    public static function getAllAsArray(): array
    {
        if (static::$cache === null) {
            static::loadCache();
        }

        return static::$cache;
    }

    /**
     * Get all settings grouped by their setting_group.
     *
     * @return Collection<string, Collection<int, static>>
     */
    public static function getAllGrouped(): Collection
    {
        return static::all()->groupBy('setting_group');
    }

    /*
    |--------------------------------------------------------------------------
    | Convenience Getters for Common Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Get the default VAT rate (as a percentage, e.g. 15).
     */
    public static function getVatRate(): float
    {
        return (float) (static::getValue('vat_rate', '15'));
    }

    /**
     * Get the default markup percentage.
     */
    public static function getDefaultMarkup(): float
    {
        return (float) (static::getValue('default_markup_pct', '20'));
    }

    /**
     * Get the currency symbol.
     */
    public static function getCurrencySymbol(): string
    {
        return static::getValue('currency_symbol', 'R');
    }

    /**
     * Get the quote prefix.
     */
    public static function getQuotePrefix(): string
    {
        return static::getValue('quote_prefix', 'TD');
    }

    /**
     * Get the job card prefix.
     */
    public static function getJobCardPrefix(): string
    {
        return static::getValue('job_card_prefix', 'JC');
    }

    /**
     * Get the quote validity period in days.
     */
    public static function getQuoteValidityDays(): int
    {
        return (int) (static::getValue('quote_validity_days', '14'));
    }

    /**
     * Get the default minimum stock threshold.
     */
    public static function getDefaultMinStock(): int
    {
        return (int) (static::getValue('default_min_stock', '4'));
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Management
    |--------------------------------------------------------------------------
    */

    /**
     * Load all settings into the in-memory cache.
     */
    protected static function loadCache(): void
    {
        static::$cache = static::pluck('setting_value', 'setting_key')->toArray();
    }

    /**
     * Clear the in-memory cache (useful after bulk updates or in tests).
     */
    public static function clearCache(): void
    {
        static::$cache = null;
    }

    /**
     * Boot the model and register event listeners to keep cache in sync.
     */
    protected static function booted(): void
    {
        static::saved(function (TyreDashSetting $setting) {
            if (static::$cache !== null) {
                static::$cache[$setting->setting_key] = $setting->setting_value;
            }
        });

        static::deleted(function (TyreDashSetting $setting) {
            if (static::$cache !== null) {
                unset(static::$cache[$setting->setting_key]);
            }
        });
    }
}
