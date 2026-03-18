<?php

namespace Modules\CIMSDocumentGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CimsPeriod extends Model
{
    protected $table = 'cims_periods';

    protected $fillable = [
        'period_name', 'period_month', 'period_year', 'sort_order', 'is_active', 'is_deleted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('is_deleted', 0);
    }
}
