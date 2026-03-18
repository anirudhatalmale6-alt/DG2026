<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoanRepayment extends Model
{
    protected $table = 'cims_payroll_loan_repayments';

    protected $fillable = [
        'loan_id', 'pay_run_id', 'amount', 'repayment_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'repayment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(PayrollLoan::class, 'loan_id');
    }

    public function payRun()
    {
        return $this->belongsTo(PayrollPayRun::class, 'pay_run_id');
    }
}
