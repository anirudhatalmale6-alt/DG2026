<?php

namespace Modules\CIMS_EMP201\Models;

use Illuminate\Database\Eloquent\Model;

class SarsStatus extends Model
{
    protected $table = 'sars_status';

    protected $fillable = [
        'status_name',
        'emp201',
        'emp501',
        'itax',
        'vat',
        'is_active',
        'sort_order',
    ];

    /**
     * Get active statuses for a specific form type.
     */
    public static function getByFormType(string $formType)
    {
        return self::where($formType, 1)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
