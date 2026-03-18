<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollCompany extends Model
{
    protected $table = 'cims_payroll_companies';

    protected $fillable = [
        'client_id', 'client_code',
        'company_name', 'registration_number', 'trading_name',
        'address_line1', 'address_line2', 'city', 'province', 'postal_code',
        'phone', 'email',
        'paye_reference', 'uif_reference', 'sdl_reference',
        'pay_frequency', 'normal_hours_month', 'normal_days_month', 'normal_hours_day',
        'is_active', 'is_paye', 'is_uif', 'is_sdl', 'created_by',
    ];

    protected $casts = [
        'normal_hours_month' => 'decimal:2',
        'normal_days_month' => 'decimal:2',
        'normal_hours_day' => 'decimal:2',
        'is_active' => 'boolean',
        'is_paye' => 'boolean',
        'is_uif' => 'boolean',
        'is_sdl' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(PayrollEmployee::class, 'company_id');
    }

    public function activeEmployees()
    {
        return $this->hasMany(PayrollEmployee::class, 'company_id')->where('status', 'active');
    }
}
