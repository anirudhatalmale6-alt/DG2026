<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollTaxThreshold extends Model
{
    protected $table = 'cims_payroll_tax_thresholds';

    protected $fillable = [
        'tax_year', 'age_group', 'threshold_amount', 'is_active',
    ];

    protected $casts = [
        'threshold_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
