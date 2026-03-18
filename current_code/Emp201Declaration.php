<?php

namespace Modules\CIMS_EMP201\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emp201Declaration extends Model
{
    use SoftDeletes;

    protected $table = 'cims_emp201_declarations';

    protected $fillable = [
        // Client reference
        'client_id',
        'client_code',
        'company_name',
        'company_number',
        'vat_number',
        'income_tax_number',
        'paye_number',
        'sdl_number',
        'uif_number',

        // Public Officer / Contact
        'title',
        'initial',
        'first_name',
        'surname',
        'position',
        'telephone_number',
        'mobile_number',
        'whatsapp_number',
        'home_number',
        'email',

        // Period
        'pay_period',
        'financial_year',
        'period_combo',

        // Payroll Tax
        'paye_liability',
        'sdl_liability',
        'uif_liability',

        // Penalties
        'penalty',
        'interest',
        'other',

        // Calculated
        'tax_payable',

        // Payment Reference
        'payment_reference',
        'payment_reference_number',

        // Payment
        'payment_date',
        'payment_type',
        'payment_amount',
        'balance_outstanding',

        // Files
        'file_emp201_return',
        'file_emp201_statement',
        'file_working_papers',
        'file_emp201_pack',
        'emp_201_file',

        // Status
        'status',
    ];

    protected $casts = [
        'paye_liability' => 'decimal:2',
        'sdl_liability' => 'decimal:2',
        'uif_liability' => 'decimal:2',
        'penalty' => 'decimal:2',
        'interest' => 'decimal:2',
        'other' => 'decimal:2',
        'tax_payable' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'balance_outstanding' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(\Modules\cims_pm_pro\Models\ClientMaster::class, 'client_id', 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function getPayrollLiabilityAttribute()
    {
        return ($this->paye_liability ?? 0) + ($this->sdl_liability ?? 0) + ($this->uif_liability ?? 0);
    }

    public function getTotalPenaltiesAttribute()
    {
        return ($this->penalty ?? 0) + ($this->interest ?? 0) + ($this->other ?? 0);
    }
}
