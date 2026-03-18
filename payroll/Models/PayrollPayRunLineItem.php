<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayRunLineItem extends Model
{
    protected $table = 'cims_payroll_pay_run_line_items';

    protected $fillable = [
        'pay_run_line_id', 'item_type', 'type_id', 'name',
        'calc_type', 'rate', 'hours', 'amount',
        'is_taxable', 'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'hours' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    public function payRunLine()
    {
        return $this->belongsTo(PayrollPayRunLine::class, 'pay_run_line_id');
    }
}
