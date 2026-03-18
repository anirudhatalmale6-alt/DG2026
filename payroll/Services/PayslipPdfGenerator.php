<?php

namespace Modules\CIMS_PAYROLL\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\CIMS_PAYROLL\Models\PayrollPayRun;
use Modules\CIMS_PAYROLL\Models\PayrollPayRunLine;
use Modules\CIMS_PAYROLL\Models\PayrollLeaveBalance;

class PayslipPdfGenerator
{
    /**
     * Generate a single employee payslip PDF
     */
    public function generateSingle(PayrollPayRunLine $line): \Barryvdh\DomPDF\PDF
    {
        $line->load(['employee.company', 'payRun', 'items']);

        $data = $this->preparePayslipData($line);

        $pdf = Pdf::loadView('cims_payroll::payroll.payslips.pdf-template', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate bulk payslips for all employees in a pay run (one PDF, multiple pages)
     */
    public function generateBulk(PayrollPayRun $payRun): \Barryvdh\DomPDF\PDF
    {
        $payRun->load(['company', 'lines.employee.company', 'lines.items']);

        $payslips = [];
        foreach ($payRun->lines as $line) {
            $payslips[] = $this->preparePayslipData($line);
        }

        $pdf = Pdf::loadView('cims_payroll::payroll.payslips.pdf-bulk-template', [
            'payslips' => $payslips,
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Prepare data array for a single payslip
     */
    protected function preparePayslipData(PayrollPayRunLine $line): array
    {
        $employee = $line->employee;
        $company = $employee->company;
        $payRun = $line->payRun;

        // Split line items by category
        $incomeItems = $line->items->where('item_type', 'income')->sortBy('sort_order');
        $deductionItems = $line->items->where('item_type', 'deduction')->sortBy('sort_order');
        $employerItems = $line->items->where('item_type', 'employer_contribution')->sortBy('sort_order');

        // Calculate loan deductions total
        $loanTotal = $deductionItems->filter(fn($i) => str_starts_with($i->name, 'Loan:'))->sum('amount');

        // Get leave balances for this employee
        $leaveBalances = PayrollLeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->whereHas('leaveType', fn($q) => $q->where('is_active', 1))
            ->get();

        return [
            'company' => $company,
            'employee' => $employee,
            'payRun' => $payRun,
            'line' => $line,
            'incomeItems' => $incomeItems,
            'deductionItems' => $deductionItems,
            'employerItems' => $employerItems,
            'loanTotal' => $loanTotal,
            'leaveBalances' => $leaveBalances,
        ];
    }
}
