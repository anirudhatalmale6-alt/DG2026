<?php

use Illuminate\Support\Facades\Route;

Route::prefix('cims/payroll')->middleware(['web', 'auth'])->name('cimspayroll.')->group(function () {

    // Dashboard
    Route::get('/', 'PayrollController@dashboard')->name('dashboard');

    // Companies
    Route::get('/companies', 'PayrollController@companies')->name('companies.index');
    Route::get('/companies/create', 'PayrollController@companyCreate')->name('companies.create');
    Route::post('/companies', 'PayrollController@companyStore')->name('companies.store');
    Route::get('/companies/{id}/edit', 'PayrollController@companyEdit')->name('companies.edit');
    Route::put('/companies/{id}', 'PayrollController@companyUpdate')->name('companies.update');
    Route::delete('/companies/{id}', 'PayrollController@companyDestroy')->name('companies.destroy');

    // Employees
    Route::get('/employees', 'PayrollController@employees')->name('employees.index');
    Route::get('/employees/create', 'PayrollController@employeeCreate')->name('employees.create');
    Route::post('/employees', 'PayrollController@employeeStore')->name('employees.store');
    Route::get('/employees/{id}/edit', 'PayrollController@employeeEdit')->name('employees.edit');
    Route::put('/employees/{id}', 'PayrollController@employeeUpdate')->name('employees.update');
    Route::delete('/employees/{id}', 'PayrollController@employeeDestroy')->name('employees.destroy');

    // Income Types
    Route::get('/income-types', 'PayrollController@incomeTypes')->name('income-types.index');
    Route::post('/income-types', 'PayrollController@incomeTypeStore')->name('income-types.store');
    Route::put('/income-types/{id}', 'PayrollController@incomeTypeUpdate')->name('income-types.update');
    Route::delete('/income-types/{id}', 'PayrollController@incomeTypeDestroy')->name('income-types.destroy');

    // Deduction Types
    Route::get('/deduction-types', 'PayrollController@deductionTypes')->name('deduction-types.index');
    Route::post('/deduction-types', 'PayrollController@deductionTypeStore')->name('deduction-types.store');
    Route::put('/deduction-types/{id}', 'PayrollController@deductionTypeUpdate')->name('deduction-types.update');
    Route::delete('/deduction-types/{id}', 'PayrollController@deductionTypeDestroy')->name('deduction-types.destroy');

    // Company Contribution Types
    Route::get('/contribution-types', 'PayrollController@contributionTypes')->name('contribution-types.index');
    Route::post('/contribution-types', 'PayrollController@contributionTypeStore')->name('contribution-types.store');
    Route::put('/contribution-types/{id}', 'PayrollController@contributionTypeUpdate')->name('contribution-types.update');
    Route::delete('/contribution-types/{id}', 'PayrollController@contributionTypeDestroy')->name('contribution-types.destroy');

    // Tax Tables
    Route::get('/tax-tables', 'PayrollController@taxTables')->name('tax-tables.index');
    Route::post('/tax-tables/brackets', 'PayrollController@taxBracketStore')->name('tax-tables.bracket.store');
    Route::put('/tax-tables/brackets/{id}', 'PayrollController@taxBracketUpdate')->name('tax-tables.bracket.update');
    Route::delete('/tax-tables/brackets/{id}', 'PayrollController@taxBracketDestroy')->name('tax-tables.bracket.destroy');
    Route::post('/tax-tables/rebates', 'PayrollController@taxRebateStore')->name('tax-tables.rebate.store');
    Route::put('/tax-tables/rebates/{id}', 'PayrollController@taxRebateUpdate')->name('tax-tables.rebate.update');
    Route::post('/tax-tables/thresholds', 'PayrollController@taxThresholdStore')->name('tax-tables.threshold.store');
    Route::put('/tax-tables/thresholds/{id}', 'PayrollController@taxThresholdUpdate')->name('tax-tables.threshold.update');

    // ─── PHASE 2: Leave Types ───
    Route::get('/leave-types', 'PayrollController@leaveTypes')->name('leave-types.index');
    Route::post('/leave-types', 'PayrollController@leaveTypeStore')->name('leave-types.store');
    Route::put('/leave-types/{id}', 'PayrollController@leaveTypeUpdate')->name('leave-types.update');
    Route::delete('/leave-types/{id}', 'PayrollController@leaveTypeDestroy')->name('leave-types.destroy');

    // ─── PHASE 2: Leave Balances ───
    Route::get('/leave/balances', 'PayrollController@leaveBalances')->name('leave.balances');
    Route::post('/leave/balances/init', 'PayrollController@leaveBalancesInit')->name('leave.balances.init');
    Route::put('/leave/balances/{id}', 'PayrollController@leaveBalanceUpdate')->name('leave.balances.update');

    // ─── PHASE 2: Leave Applications ───
    Route::get('/leave/applications', 'PayrollController@leaveApplications')->name('leave.applications');
    Route::get('/leave/applications/create', 'PayrollController@leaveApplicationCreate')->name('leave.applications.create');
    Route::post('/leave/applications', 'PayrollController@leaveApplicationStore')->name('leave.applications.store');
    Route::post('/leave/applications/{id}/approve', 'PayrollController@leaveApplicationApprove')->name('leave.applications.approve');
    Route::post('/leave/applications/{id}/reject', 'PayrollController@leaveApplicationReject')->name('leave.applications.reject');
    Route::post('/leave/applications/{id}/cancel', 'PayrollController@leaveApplicationCancel')->name('leave.applications.cancel');

    // ─── PHASE 2: Timesheets ───
    Route::get('/timesheets', 'PayrollController@timesheets')->name('timesheets.index');
    Route::get('/timesheets/create', 'PayrollController@timesheetCreate')->name('timesheets.create');
    Route::post('/timesheets', 'PayrollController@timesheetStore')->name('timesheets.store');
    Route::get('/timesheets/{id}/edit', 'PayrollController@timesheetEdit')->name('timesheets.edit');
    Route::put('/timesheets/{id}', 'PayrollController@timesheetUpdate')->name('timesheets.update');
    Route::post('/timesheets/{id}/approve', 'PayrollController@timesheetApprove')->name('timesheets.approve');
    Route::delete('/timesheets/{id}', 'PayrollController@timesheetDestroy')->name('timesheets.destroy');

    // ─── PHASE 3: Pay Runs ───
    Route::get('/pay-runs', 'PayrollController@payRuns')->name('pay-runs.index');
    Route::get('/pay-runs/employees', 'PayrollController@payRunEmployees')->name('pay-runs.employees');
    Route::get('/pay-runs/create', 'PayrollController@payRunCreate')->name('pay-runs.create');
    Route::post('/pay-runs', 'PayrollController@payRunStore')->name('pay-runs.store');
    Route::get('/pay-runs/{id}', 'PayrollController@payRunShow')->name('pay-runs.show');
    Route::post('/pay-runs/{id}/process', 'PayrollController@payRunProcess')->name('pay-runs.process');
    Route::post('/pay-runs/{id}/approve', 'PayrollController@payRunApprove')->name('pay-runs.approve');
    Route::delete('/pay-runs/{id}', 'PayrollController@payRunDestroy')->name('pay-runs.destroy');

    // ─── PHASE 3: Loans ───
    Route::get('/loans', 'PayrollController@loans')->name('loans.index');
    Route::get('/loans/create', 'PayrollController@loanCreate')->name('loans.create');
    Route::post('/loans', 'PayrollController@loanStore')->name('loans.store');
    Route::get('/loans/{id}/edit', 'PayrollController@loanEdit')->name('loans.edit');
    Route::put('/loans/{id}', 'PayrollController@loanUpdate')->name('loans.update');
    Route::delete('/loans/{id}', 'PayrollController@loanDestroy')->name('loans.destroy');

    // ─── PHASE 4: Payslips ───
    Route::get('/payslips', 'PayrollController@payslips')->name('payslips.index');
    Route::get('/payslips/{payRunId}/preview', 'PayrollController@payslipPreview')->name('payslips.preview');
    Route::get('/payslips/download/{lineId}', 'PayrollController@payslipDownloadSingle')->name('payslips.download-single');
    Route::get('/payslips/{payRunId}/download-all', 'PayrollController@payslipDownloadBulk')->name('payslips.download-bulk');
    Route::get('/payslips/view/{lineId}', 'PayrollController@payslipViewSingle')->name('payslips.view-single');

    // ─── PAYROLL PROCESSING (Unified Screen) ───
    Route::get('/processing', 'PayrollController@processing')->name('processing');
    Route::get('/processing/employees', 'PayrollController@processingEmployees')->name('processing.employees');
    Route::get('/processing/employee/{id}', 'PayrollController@processingEmployee')->name('processing.employee');
    Route::post('/processing/save/{id}', 'PayrollController@processingSave')->name('processing.save');
    Route::get('/processing/generate-payslip/{id}', 'PayrollController@processingGeneratePayslip')->name('processing.generate-payslip');
    Route::get('/processing/tax-calculation/{id}', 'PayrollController@processingTaxCalculation')->name('processing.tax-calculation');
    Route::post('/processing/save-defaults/{id}', 'PayrollController@processingSaveDefaults')->name('processing.save-defaults');

    // ─── PAYE Calculator ───
    Route::get('/paye-calculator', 'PayrollController@payeCalculator')->name('paye-calculator');
    Route::get('/paye-calculator/calculate', 'PayrollController@payeCalculatorCalculate')->name('paye-calculator.calculate');

    // ─── PHASE 5: Reports ───
    Route::get('/reports/payroll-summary', 'PayrollController@reportPayrollSummary')->name('reports.payroll-summary');
    Route::get('/reports/paye', 'PayrollController@reportPAYE')->name('reports.paye');
    Route::get('/reports/uif', 'PayrollController@reportUIF')->name('reports.uif');
    Route::get('/reports/leave', 'PayrollController@reportLeave')->name('reports.leave');
    Route::get('/reports/loans', 'PayrollController@reportLoans')->name('reports.loans');
    Route::get('/reports/cost-to-company', 'PayrollController@reportCostToCompany')->name('reports.cost-to-company');
});
