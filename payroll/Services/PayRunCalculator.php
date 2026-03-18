<?php

namespace Modules\CIMS_PAYROLL\Services;

use Modules\CIMS_PAYROLL\Models\PayrollEmployee;
use Modules\CIMS_PAYROLL\Models\PayrollPayRun;
use Modules\CIMS_PAYROLL\Models\PayrollPayRunLine;
use Modules\CIMS_PAYROLL\Models\PayrollPayRunLineItem;
use Modules\CIMS_PAYROLL\Models\PayrollTimesheet;
use Modules\CIMS_PAYROLL\Models\PayrollIncomeType;
use Modules\CIMS_PAYROLL\Models\PayrollDeductionType;
use Modules\CIMS_PAYROLL\Models\PayrollCompanyContributionType;
use Modules\CIMS_PAYROLL\Models\PayrollTaxBracket;
use Modules\CIMS_PAYROLL\Models\PayrollTaxRebate;
use Modules\CIMS_PAYROLL\Models\PayrollTaxThreshold;
use Modules\CIMS_PAYROLL\Models\PayrollLoan;
use Modules\CIMS_PAYROLL\Models\PayrollLoanRepayment;

class PayRunCalculator
{
    /**
     * Process a pay run — calculate all employee payslips
     */
    public function process(PayrollPayRun $payRun): PayrollPayRun
    {
        $company = $payRun->company;
        $employees = PayrollEmployee::where('company_id', $company->id)
            ->where('status', 'active')
            ->get();

        $totalGross = 0;
        $totalDeductions = 0;
        $totalEmployerCost = 0;
        $totalNetPay = 0;

        foreach ($employees as $employee) {
            $line = $this->processEmployee($payRun, $employee, $company);
            $totalGross += $line->gross_pay;
            $totalDeductions += $line->total_deductions;
            $totalEmployerCost += $line->total_employer_contributions;
            $totalNetPay += $line->net_pay;
        }

        $payRun->update([
            'status' => 'processed',
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_employer_cost' => $totalEmployerCost,
            'total_net_pay' => $totalNetPay,
            'employee_count' => $employees->count(),
            'processed_at' => now(),
        ]);

        return $payRun->fresh(['lines.employee', 'lines.items']);
    }

    /**
     * Process a single employee within a pay run
     */
    protected function processEmployee(PayrollPayRun $payRun, PayrollEmployee $employee, $company): PayrollPayRunLine
    {
        // Delete existing line if re-processing
        PayrollPayRunLine::where('pay_run_id', $payRun->id)
            ->where('employee_id', $employee->id)
            ->delete();

        // Get timesheet for this period
        $timesheet = PayrollTimesheet::where('employee_id', $employee->id)
            ->where('period_start', $payRun->period_start)
            ->where('period_end', $payRun->period_end)
            ->first();

        // Create pay run line
        $line = PayrollPayRunLine::create([
            'pay_run_id' => $payRun->id,
            'employee_id' => $employee->id,
            'basic_salary' => $employee->basic_salary,
            'hourly_rate' => $employee->hourly_rate,
            'normal_hours' => $timesheet ? $timesheet->normal_hours : $company->normal_hours_month,
            'overtime_15x_hours' => $timesheet ? $timesheet->overtime_15x_hours : 0,
            'overtime_2x_hours' => $timesheet ? $timesheet->overtime_2x_hours : 0,
            'sunday_hours' => $timesheet ? $timesheet->sunday_hours : 0,
            'public_holiday_hours' => $timesheet ? $timesheet->public_holiday_hours : 0,
        ]);

        // ─── STEP 1: CALCULATE INCOME ───
        $grossPay = $this->calculateIncome($line, $employee, $company);

        // ─── STEP 2: CALCULATE PAYE TAX ───
        $payeTax = $this->calculatePAYE($grossPay, $employee);

        // ─── STEP 3: CALCULATE UIF (Employee 1%) ───
        $uifEmployee = $this->calculateUIF($grossPay);

        // ─── STEP 4: CALCULATE OTHER DEDUCTIONS ───
        $otherDeductions = $this->calculateDeductions($line, $grossPay, $employee);

        // ─── STEP 5: CALCULATE LOAN REPAYMENTS ───
        $loanDeductions = $this->calculateLoanRepayments($line, $employee, $payRun);

        $totalDeductions = $payeTax + $uifEmployee + $otherDeductions + $loanDeductions;

        // ─── STEP 6: EMPLOYER CONTRIBUTIONS ───
        $employerContribs = $this->calculateEmployerContributions($line, $grossPay);

        // ─── STEP 7: NET PAY ───
        $netPay = $grossPay - $totalDeductions;

        // Store PAYE as line item
        if ($payeTax > 0) {
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'deduction',
                'name' => 'PAYE Income Tax',
                'calc_type' => 'auto',
                'amount' => $payeTax,
                'is_taxable' => 0,
                'sort_order' => 1,
            ]);
        }

        // Store UIF Employee as line item
        if ($uifEmployee > 0) {
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'deduction',
                'name' => 'UIF (Employee 1%)',
                'calc_type' => 'auto',
                'rate' => 1.0000,
                'amount' => $uifEmployee,
                'is_taxable' => 0,
                'sort_order' => 2,
            ]);
        }

        // Update the line totals
        $line->update([
            'gross_pay' => $grossPay,
            'total_income' => $grossPay,
            'total_deductions' => $totalDeductions,
            'total_employer_contributions' => $employerContribs,
            'net_pay' => $netPay,
            'paye_tax' => $payeTax,
            'uif_employee' => $uifEmployee,
            'uif_employer' => $this->calculateUIF($grossPay), // same as employee
            'sdl_employer' => round($grossPay * 0.01, 2),
        ]);

        return $line;
    }

    /**
     * Calculate all income items and return gross pay
     */
    protected function calculateIncome(PayrollPayRunLine $line, PayrollEmployee $employee, $company): float
    {
        $grossPay = 0;
        $sortOrder = 0;

        if ($employee->pay_type === 'salaried') {
            // Basic Salary
            $grossPay = (float) $employee->basic_salary;
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Basic Salary',
                'calc_type' => 'fixed',
                'amount' => $grossPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);

            // Hourly rate for OT calculation (salary / hours per month)
            $hourlyRate = $company->normal_hours_month > 0
                ? $employee->basic_salary / $company->normal_hours_month
                : 0;
        } else {
            // Hourly employee: Normal hours pay
            $hourlyRate = (float) $employee->hourly_rate;
            $normalPay = round($hourlyRate * $line->normal_hours, 2);
            $grossPay = $normalPay;

            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Normal Hours Pay',
                'calc_type' => 'hours',
                'rate' => $hourlyRate,
                'hours' => $line->normal_hours,
                'amount' => $normalPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);
        }

        // Overtime 1.5x
        if ($line->overtime_15x_hours > 0) {
            $otPay = round($hourlyRate * 1.5 * $line->overtime_15x_hours, 2);
            $grossPay += $otPay;
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Overtime (1.5x)',
                'calc_type' => 'hours',
                'rate' => round($hourlyRate * 1.5, 4),
                'hours' => $line->overtime_15x_hours,
                'amount' => $otPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);
        }

        // Overtime 2x
        if ($line->overtime_2x_hours > 0) {
            $otPay = round($hourlyRate * 2.0 * $line->overtime_2x_hours, 2);
            $grossPay += $otPay;
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Overtime (2x)',
                'calc_type' => 'hours',
                'rate' => round($hourlyRate * 2.0, 4),
                'hours' => $line->overtime_2x_hours,
                'amount' => $otPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);
        }

        // Sunday hours (2x)
        if ($line->sunday_hours > 0) {
            $sunPay = round($hourlyRate * 2.0 * $line->sunday_hours, 2);
            $grossPay += $sunPay;
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Sunday Work (2x)',
                'calc_type' => 'hours',
                'rate' => round($hourlyRate * 2.0, 4),
                'hours' => $line->sunday_hours,
                'amount' => $sunPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);
        }

        // Public holiday (2x)
        if ($line->public_holiday_hours > 0) {
            $phPay = round($hourlyRate * 2.0 * $line->public_holiday_hours, 2);
            $grossPay += $phPay;
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'income',
                'name' => 'Public Holiday (2x)',
                'calc_type' => 'hours',
                'rate' => round($hourlyRate * 2.0, 4),
                'hours' => $line->public_holiday_hours,
                'amount' => $phPay,
                'is_taxable' => 1,
                'sort_order' => ++$sortOrder,
            ]);
        }

        return round($grossPay, 2);
    }

    /**
     * Calculate PAYE using SARS tax brackets (monthly)
     */
    protected function calculatePAYE(float $grossMonthly, PayrollEmployee $employee): float
    {
        $annualIncome = $grossMonthly * 12;
        $taxYear = cims_tax_year();

        // Get tax brackets
        $brackets = PayrollTaxBracket::where('tax_year', $taxYear)
            ->where('is_active', 1)
            ->orderBy('min_amount')
            ->get();

        if ($brackets->isEmpty()) {
            return 0;
        }

        // Find applicable bracket
        $annualTax = 0;
        foreach ($brackets as $bracket) {
            if ($annualIncome >= $bracket->min_amount && $annualIncome <= $bracket->max_amount) {
                $annualTax = $bracket->base_tax + (($annualIncome - $bracket->min_amount + 1) * $bracket->rate / 100);
                break;
            }
        }

        // Apply rebates
        $rebates = PayrollTaxRebate::where('tax_year', $taxYear)
            ->where('is_active', 1)
            ->get();

        // Calculate age from DOB
        $age = 0;
        if ($employee->date_of_birth) {
            $age = $employee->date_of_birth->age;
        }

        foreach ($rebates as $rebate) {
            if ($rebate->rebate_type === 'primary') {
                $annualTax -= $rebate->amount;
            } elseif ($rebate->rebate_type === 'secondary' && $age >= 65) {
                $annualTax -= $rebate->amount;
            } elseif ($rebate->rebate_type === 'tertiary' && $age >= 75) {
                $annualTax -= $rebate->amount;
            }
        }

        // Check threshold
        $thresholds = PayrollTaxThreshold::where('tax_year', $taxYear)
            ->where('is_active', 1)
            ->get();

        foreach ($thresholds as $threshold) {
            if ($threshold->age_group === 'below_65' && $age < 65 && $annualIncome < $threshold->threshold_amount) {
                return 0;
            }
            if ($threshold->age_group === '65_to_74' && $age >= 65 && $age < 75 && $annualIncome < $threshold->threshold_amount) {
                return 0;
            }
            if ($threshold->age_group === '75_and_over' && $age >= 75 && $annualIncome < $threshold->threshold_amount) {
                return 0;
            }
        }

        $annualTax = max(0, $annualTax);
        $monthlyTax = round($annualTax / 12, 2);

        return $monthlyTax;
    }

    /**
     * Calculate UIF (1% of gross, max R177.12/month based on ceiling R17,712)
     */
    protected function calculateUIF(float $grossMonthly): float
    {
        $uifCeiling = 17712.00; // 2025/2026 ceiling
        $applicable = min($grossMonthly, $uifCeiling);
        return round($applicable * 0.01, 2);
    }

    /**
     * Calculate other deductions (medical aid, provident fund, etc.)
     */
    protected function calculateDeductions(PayrollPayRunLine $line, float $grossPay, PayrollEmployee $employee): float
    {
        $totalDeductions = 0;
        $sortOrder = 10;

        $deductionTypes = PayrollDeductionType::where('is_active', 1)
            ->where('is_statutory', 0) // Skip statutory (PAYE/UIF handled separately)
            ->orderBy('sort_order')
            ->get();

        foreach ($deductionTypes as $dt) {
            if ($dt->default_value <= 0) continue;

            $amount = 0;
            if ($dt->calc_type === 'percentage') {
                $amount = round($grossPay * $dt->default_value / 100, 2);
            } else {
                $amount = round($dt->default_value, 2);
            }

            if ($amount > 0) {
                PayrollPayRunLineItem::create([
                    'pay_run_line_id' => $line->id,
                    'item_type' => 'deduction',
                    'type_id' => $dt->id,
                    'name' => $dt->name,
                    'calc_type' => $dt->calc_type,
                    'rate' => $dt->default_value,
                    'amount' => $amount,
                    'is_taxable' => 0,
                    'sort_order' => ++$sortOrder,
                ]);
                $totalDeductions += $amount;
            }
        }

        return $totalDeductions;
    }

    /**
     * Calculate loan repayments for employee
     */
    protected function calculateLoanRepayments(PayrollPayRunLine $line, PayrollEmployee $employee, PayrollPayRun $payRun): float
    {
        $totalLoan = 0;
        $sortOrder = 50;

        $loans = PayrollLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->get();

        foreach ($loans as $loan) {
            $repayment = min($loan->monthly_repayment, $loan->outstanding_balance);
            if ($repayment <= 0) continue;

            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'deduction',
                'name' => 'Loan: ' . $loan->loan_type,
                'calc_type' => 'fixed',
                'amount' => $repayment,
                'is_taxable' => 0,
                'sort_order' => ++$sortOrder,
            ]);

            // Record repayment
            PayrollLoanRepayment::create([
                'loan_id' => $loan->id,
                'pay_run_id' => $payRun->id,
                'amount' => $repayment,
                'repayment_date' => $payRun->period_end,
            ]);

            // Update outstanding balance
            $newBalance = $loan->outstanding_balance - $repayment;
            $loan->update([
                'outstanding_balance' => max(0, $newBalance),
                'status' => $newBalance <= 0 ? 'paid_off' : 'active',
            ]);

            $totalLoan += $repayment;
        }

        return $totalLoan;
    }

    /**
     * Calculate employer contributions (UIF employer, SDL, provident fund employer, etc.)
     */
    protected function calculateEmployerContributions(PayrollPayRunLine $line, float $grossPay): float
    {
        $totalContribs = 0;
        $sortOrder = 0;

        // UIF Employer (1%)
        $uifEmployer = $this->calculateUIF($grossPay);
        if ($uifEmployer > 0) {
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'employer_contribution',
                'name' => 'UIF (Employer 1%)',
                'calc_type' => 'auto',
                'rate' => 1.0000,
                'amount' => $uifEmployer,
                'is_taxable' => 0,
                'sort_order' => ++$sortOrder,
            ]);
            $totalContribs += $uifEmployer;
        }

        // SDL (1%)
        $sdl = round($grossPay * 0.01, 2);
        if ($sdl > 0) {
            PayrollPayRunLineItem::create([
                'pay_run_line_id' => $line->id,
                'item_type' => 'employer_contribution',
                'name' => 'SDL (1%)',
                'calc_type' => 'auto',
                'rate' => 1.0000,
                'amount' => $sdl,
                'is_taxable' => 0,
                'sort_order' => ++$sortOrder,
            ]);
            $totalContribs += $sdl;
        }

        // Other company contributions
        $contribTypes = PayrollCompanyContributionType::where('is_active', 1)
            ->where('is_statutory', 0) // UIF/SDL already handled
            ->orderBy('sort_order')
            ->get();

        foreach ($contribTypes as $ct) {
            if ($ct->default_value <= 0) continue;

            $amount = 0;
            if ($ct->calc_type === 'percentage') {
                $amount = round($grossPay * $ct->default_value / 100, 2);
            } else {
                $amount = round($ct->default_value, 2);
            }

            if ($amount > 0) {
                PayrollPayRunLineItem::create([
                    'pay_run_line_id' => $line->id,
                    'item_type' => 'employer_contribution',
                    'type_id' => $ct->id,
                    'name' => $ct->name,
                    'calc_type' => $ct->calc_type,
                    'rate' => $ct->default_value,
                    'amount' => $amount,
                    'is_taxable' => 0,
                    'sort_order' => ++$sortOrder,
                ]);
                $totalContribs += $amount;
            }
        }

        return $totalContribs;
    }
}
