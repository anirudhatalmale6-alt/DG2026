<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollTaxRebate extends Model
{
    protected $table = 'cims_payroll_tax_rebates';

    protected $fillable = [
        'tax_year', 'rebate_type', 'amount', 'age_threshold', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
