<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollTaxBracket extends Model
{
    protected $table = 'cims_payroll_tax_brackets';

    protected $fillable = [
        'tax_year', 'min_amount', 'max_amount', 'rate', 'base_tax', 'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'base_tax' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
