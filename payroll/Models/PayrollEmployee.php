<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollEmployee extends Model
{
    protected $table = 'cims_payroll_employees';

    protected $fillable = [
        'company_id', 'employee_number', 'id_number',
        'title', 'initials', 'first_name', 'second_name', 'last_name', 'known_as',
        'employee_type', 'passport_number', 'passport_country',
        'date_of_birth', 'gender',
        'phone', 'email', 'address',
        'address_line1', 'address_line2', 'city', 'province', 'postal_code',
        'job_title', 'department', 'start_date', 'termination_date', 'termination_reason',
        'tax_number', 'tax_status',
        'pay_type', 'working_hours_per_day', 'working_days_per_week', 'must_capture_hours',
        'basic_salary', 'hourly_rate',
        'bank_name', 'bank_branch_code', 'bank_account_number', 'bank_account_type',
        'pay_method',
        'eti_prescribed_min_wage', 'eti_national_min_wage', 'eti_min_rate',
        'eti_fixed_hours', 'eti_sez', 'eti_connected', 'eti_domestic', 'eti_labour_broker',
        'status', 'is_active', 'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'start_date' => 'date',
        'termination_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'working_hours_per_day' => 'decimal:2',
        'working_days_per_week' => 'decimal:2',
        'eti_min_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'must_capture_hours' => 'boolean',
        'eti_prescribed_min_wage' => 'boolean',
        'eti_national_min_wage' => 'boolean',
        'eti_fixed_hours' => 'boolean',
        'eti_sez' => 'boolean',
        'eti_connected' => 'boolean',
        'eti_domestic' => 'boolean',
        'eti_labour_broker' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(PayrollCompany::class, 'company_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(PayrollLeaveBalance::class, 'employee_id');
    }

    public function leaveApplications()
    {
        return $this->hasMany(PayrollLeaveApplication::class, 'employee_id');
    }

    public function medicalAid()
    {
        return $this->hasMany(PayrollMedicalAid::class, 'employee_id');
    }

    public function privateRA()
    {
        return $this->hasMany(PayrollPrivateRA::class, 'employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
