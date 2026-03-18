<?php

namespace Modules\CIMSTyreDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * TyreSize model representing tyre dimensions (e.g. 205/65R16).
 *
 * Note: This table does NOT use soft deletes (no deleted_at column in migration).
 *
 * @property int         $id
 * @property string      $full_size      e.g. "205/65R16", "195R14C"
 * @property int         $width          Section width in mm (e.g. 205)
 * @property int         $profile        Aspect ratio (e.g. 65; 0 for full-profile tyres like 195R14C)
 * @property string      $construction   R=Radial, D=Diagonal
 * @property int         $rim_diameter   Rim diameter in inches (e.g. 16)
 * @property bool        $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Builder|TyreSize active()
 * @method static Builder|TyreSize byRimDiameter(int $diameter)
 * @method static Builder|TyreSize byWidth(int $width)
 * @method static Builder|TyreSize byDimensions(int $width, int $profile, int $rimDiameter)
 */
class TyreSize extends Model
{
    /** @var string */
    protected $table = 'cims_tyredash_sizes';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array<int, string> */
    protected $fillable = [
        'full_size',
        'width',
        'profile',
        'construction',
        'rim_diameter',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'width'        => 'integer',
        'profile'      => 'integer',
        'rim_diameter' => 'integer',
        'is_active'    => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Products available in this tyre size.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'size_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active sizes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by rim diameter.
     */
    public function scopeByRimDiameter(Builder $query, int $diameter): Builder
    {
        return $query->where('rim_diameter', $diameter);
    }

    /**
     * Scope to filter by section width.
     */
    public function scopeByWidth(Builder $query, int $width): Builder
    {
        return $query->where('width', $width);
    }

    /**
     * Scope to filter by exact dimensions (width, profile, rim diameter).
     */
    public function scopeByDimensions(Builder $query, int $width, int $profile, int $rimDiameter): Builder
    {
        return $query->where('width', $width)
                     ->where('profile', $profile)
                     ->where('rim_diameter', $rimDiameter);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Parse a tyre size string into its individual components.
     *
     * Supports formats:
     *   - "205/65R16"    => width=205, profile=65, construction=R, rim=16, commercial=false
     *   - "205/65R16C"   => width=205, profile=65, construction=R, rim=16, commercial=true
     *   - "195R14C"      => width=195, profile=0,  construction=R, rim=14, commercial=true
     *   - "7.50R16"      => width=7.50 (string), profile=0, construction=R, rim=16
     *
     * @param  string $sizeString The tyre size designation (e.g. "205/65R16")
     * @return array{width: int|string, profile: int, construction: string, rim_diameter: int, commercial: bool}|null
     *               Returns null if the string cannot be parsed.
     */
    public static function parseSizeString(string $sizeString): ?array
    {
        $sizeString = trim(strtoupper($sizeString));

        // Standard format: 205/65R16 or 205/65R16C
        if (preg_match('/^(\d{2,3})\/(\d{1,3})\s*([RD])(\d{2})(C?)$/', $sizeString, $matches)) {
            return [
                'width'        => (int) $matches[1],
                'profile'      => (int) $matches[2],
                'construction' => $matches[3],
                'rim_diameter' => (int) $matches[4],
                'commercial'   => $matches[5] === 'C',
            ];
        }

        // Full-profile format (no aspect ratio): 195R14C or 195R14
        if (preg_match('/^(\d{2,3})\s*([RD])(\d{2})(C?)$/', $sizeString, $matches)) {
            return [
                'width'        => (int) $matches[1],
                'profile'      => 0,
                'construction' => $matches[2],
                'rim_diameter' => (int) $matches[3],
                'commercial'   => $matches[4] === 'C',
            ];
        }

        // Numeric width format (e.g. 7.50R16 for light truck)
        if (preg_match('/^([\d.]+)\s*([RD])(\d{2})(C?)$/', $sizeString, $matches)) {
            return [
                'width'        => $matches[1],
                'profile'      => 0,
                'construction' => $matches[2],
                'rim_diameter' => (int) $matches[3],
                'commercial'   => $matches[4] === 'C',
            ];
        }

        return null;
    }

    /**
     * Get the parsed components of this model's full_size field.
     *
     * @return array{width: int|string, profile: int, construction: string, rim_diameter: int, commercial: bool}|null
     */
    public function getParsedSizeAttribute(): ?array
    {
        return static::parseSizeString($this->full_size);
    }

    /**
     * Determine if this is a commercial (C-rated) tyre size.
     */
    public function getIsCommercialAttribute(): bool
    {
        return str_ends_with(strtoupper($this->full_size), 'C');
    }

    /**
     * Get a human-readable label, e.g. "205/65 R16".
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->profile > 0) {
            return sprintf('%d/%d %s%d', $this->width, $this->profile, $this->construction, $this->rim_diameter);
        }

        return sprintf('%d%s%d', $this->width, $this->construction, $this->rim_diameter);
    }
}
