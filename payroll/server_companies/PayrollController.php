<?php

namespace Modules\CIMS_PAYROLL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CIMS_PAYROLL\Models\PayrollCompany;
use Modules\CIMS_PAYROLL\Models\PayrollEmployee;
use Modules\CIMS_PAYROLL\Models\PayrollIncomeType;
use Modules\CIMS_PAYROLL\Models\PayrollDeductionType;
use Modules\CIMS_PAYROLL\Models\PayrollCompanyContributionType;
use Modules\CIMS_PAYROLL\Models\PayrollTaxBracket;
use Modules\CIMS_PAYROLL\Models\PayrollTaxRebate;
use Modules\CIMS_PAYROLL\Models\PayrollTaxThreshold;
use Modules\CIMS_PAYROLL\Models\PayrollLeaveType;
use Modules\CIMS_PAYROLL\Models\PayrollLeaveBalance;
use Modules\CIMS_PAYROLL\Models\PayrollLeaveApplication;
use Modules\CIMS_PAYROLL\Models\PayrollTimesheet;
use Modules\CIMS_PAYROLL\Models\PayrollPayRun;
use Modules\CIMS_PAYROLL\Models\PayrollPayRunLine;
use Modules\CIMS_PAYROLL\Models\PayrollPayRunLineItem;
use Modules\CIMS_PAYROLL\Models\PayrollLoan;
use Modules\CIMS_PAYROLL\Models\PayrollLoanRepayment;
use Modules\CIMS_PAYROLL\Models\PayrollMedicalAid;
use Modules\CIMS_PAYROLL\Models\PayrollPrivateRA;
use Modules\CIMS_PAYROLL\Models\PayrollEmployeePayslipDefault;
use Modules\CIMS_PAYROLL\Services\PayRunCalculator;
use Modules\CIMS_PAYROLL\Services\PayslipPdfGenerator;

class PayrollController extends Controller
{
    // ═══════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════

    public function dashboard()
    {
        $stats = [
            'companies' => PayrollCompany::where('is_active', 1)->count(),
            'employees' => PayrollEmployee::where('status', 'active')->count(),
            'income_types' => PayrollIncomeType::where('is_active', 1)->count(),
            'deduction_types' => PayrollDeductionType::where('is_active', 1)->count(),
        ];

        $recentEmployees = PayrollEmployee::with('company')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('cims_payroll::payroll.dashboard', compact('stats', 'recentEmployees'));
    }

    // ═══════════════════════════════════════════════
    // COMPANIES CRUD
    // ═══════════════════════════════════════════════

    public function companies(Request $request)
    {
        $query = PayrollCompany::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%{$s}%")
                  ->orWhere('registration_number', 'like', "%{$s}%")
                  ->orWhere('trading_name', 'like', "%{$s}%")
                  ->orWhere('client_code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $companies = $query->orderBy('company_name')->paginate(20);

        return view('cims_payroll::payroll.companies.index', compact('companies'));
    }

    public function companyCreate()
    {
        $clients = \DB::table('client_master')
            ->select('client_id', 'client_code', 'company_name', 'trading_name')
            ->orderBy('company_name')
            ->get();

        return view('cims_payroll::payroll.companies.form', ['company' => null, 'clients' => $clients]);
    }

    public function companyClientLookup($clientId)
    {
        $client = \DB::table('client_master')
            ->where('client_id', $clientId)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        // Get default address
        $address = \DB::table('client_master_addresses')
            ->where('client_id', $clientId)
            ->where('is_default', 1)
            ->first();

        // If no default address, get the first one
        if (!$address) {
            $address = \DB::table('client_master_addresses')
                ->where('client_id', $clientId)
                ->first();
        }

        $addressLine1 = '';
        if ($address) {
            $parts = array_filter([
                $address->unit_number ? 'Unit ' . $address->unit_number : '',
                $address->complex_name ?? '',
                trim(($address->street_number ?? '') . ' ' . ($address->street_name ?? '')),
            ]);
            $addressLine1 = implode(', ', $parts);
        }

        return response()->json([
            'client_code'    => $client->client_code,
            'company_name'   => $client->company_name,
            'trading_name'   => $client->trading_name,
            'registration_number' => $client->company_reg_number,
            'email'          => $client->email,
            'phone'          => $client->phone_business ?: $client->phone_mobile,
            'paye_reference' => $client->paye_number,
            'uif_reference'  => $client->uif_number,
            'sdl_reference'  => $client->sdl_number,
            'address_line1'  => $addressLine1,
            'address_line2'  => $address->suburb ?? '',
            'city'           => $address->city ?? '',
            'province'       => $address->province ?? '',
            'postal_code'    => $address->postal_code ?? '',
        ]);
    }

    public function companyStore(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|integer',
            'client_code' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'trading_name' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'paye_reference' => 'nullable|string|max:50',
            'uif_reference' => 'nullable|string|max:50',
            'sdl_reference' => 'nullable|string|max:50',
            'pay_frequency' => 'required|in:weekly,fortnightly,monthly',
            'normal_hours_month' => 'required|numeric|min:0',
            'normal_days_month' => 'required|numeric|min:0',
            'normal_hours_day' => 'required|numeric|min:0',
            'is_paye' => 'nullable|in:0,1',
            'is_uif' => 'nullable|in:0,1',
            'is_sdl' => 'nullable|in:0,1',
        ]);

        $validated['is_active'] = 1;
        $validated['is_paye'] = $validated['is_paye'] ?? 1;
        $validated['is_uif'] = $validated['is_uif'] ?? 1;
        $validated['is_sdl'] = $validated['is_sdl'] ?? 1;
        $validated['created_by'] = Auth::id();

        $company = PayrollCompany::create($validated);

        return redirect()->route('cimspayroll.companies.index')
            ->with('success', 'Company created successfully.')
            ->with('swal_name', $company->company_name)
            ->with('swal_action', 'created');
    }

    public function companyEdit($id)
    {
        $company = PayrollCompany::findOrFail($id);
        $clients = \DB::table('client_master')
            ->select('client_id', 'client_code', 'company_name', 'trading_name')
            ->orderBy('company_name')
            ->get();

        return view('cims_payroll::payroll.companies.form', compact('company', 'clients'));
    }

    public function companyUpdate(Request $request, $id)
    {
        $company = PayrollCompany::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'trading_name' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'paye_reference' => 'nullable|string|max:50',
            'uif_reference' => 'nullable|string|max:50',
            'sdl_reference' => 'nullable|string|max:50',
            'pay_frequency' => 'required|in:weekly,fortnightly,monthly',
            'normal_hours_month' => 'required|numeric|min:0',
            'normal_days_month' => 'required|numeric|min:0',
            'normal_hours_day' => 'required|numeric|min:0',
            'is_active' => 'required|in:0,1',
            'is_paye' => 'nullable|in:0,1',
            'is_uif' => 'nullable|in:0,1',
            'is_sdl' => 'nullable|in:0,1',
        ]);

        $validated['is_paye'] = $validated['is_paye'] ?? 0;
        $validated['is_uif'] = $validated['is_uif'] ?? 0;
        $validated['is_sdl'] = $validated['is_sdl'] ?? 0;

        $company->update($validated);

        return redirect()->route('cimspayroll.companies.index')
            ->with('success', 'Company updated successfully.')
            ->with('swal_name', $company->company_name)
            ->with('swal_action', 'updated');
    }

    public function companyDestroy($id)
    {
        $company = PayrollCompany::findOrFail($id);
        $name = $company->company_name;

        // Check for linked employees
        if ($company->employees()->count() > 0) {
            return redirect()->route('cimspayroll.companies.index')
                ->with('error', 'Cannot delete company — it has linked employees. Deactivate instead.');
        }

        $company->delete();

        return redirect()->route('cimspayroll.companies.index')
            ->with('success', 'Company deleted successfully.')
            ->with('swal_name', $name)
            ->with('swal_action', 'deleted');
    }

    // ═══════════════════════════════════════════════
    // EMPLOYEES CRUD
    // ═══════════════════════════════════════════════

    public function employees(Request $request)
    {
        $query = PayrollEmployee::with('company');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('employee_number', 'like', "%{$s}%")
                  ->orWhere('id_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->paginate(20);
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.employees.index', compact('employees', 'companies'));
    }

    public function employeeCreate()
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        return view('cims_payroll::payroll.employees.form', ['employee' => null, 'companies' => $companies]);
    }

    public function employeeStore(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:cims_payroll_companies,id',
            'employee_number' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:13',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'tax_number' => 'nullable|string|max:20',
            'tax_status' => 'required|in:normal,directive',
            'pay_type' => 'required|in:salaried,hourly',
            'basic_salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch_code' => 'nullable|string|max:10',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_account_type' => 'nullable|in:savings,cheque,transmission',
        ]);

        $validated['status'] = 'active';
        $validated['is_active'] = 1;
        $validated['created_by'] = Auth::id();
        $validated['basic_salary'] = $validated['basic_salary'] ?? 0;
        $validated['hourly_rate'] = $validated['hourly_rate'] ?? 0;

        PayrollEmployee::create($validated);

        return redirect()->route('cimspayroll.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function employeeEdit($id)
    {
        $employee = PayrollEmployee::findOrFail($id);
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        return view('cims_payroll::payroll.employees.form', compact('employee', 'companies'));
    }

    public function employeeUpdate(Request $request, $id)
    {
        $employee = PayrollEmployee::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|exists:cims_payroll_companies,id',
            'employee_number' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:13',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'termination_date' => 'nullable|date|after_or_equal:start_date',
            'termination_reason' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'tax_status' => 'required|in:normal,directive',
            'pay_type' => 'required|in:salaried,hourly',
            'basic_salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch_code' => 'nullable|string|max:10',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_account_type' => 'nullable|in:savings,cheque,transmission',
            'status' => 'required|in:active,terminated,suspended',
        ]);

        $validated['basic_salary'] = $validated['basic_salary'] ?? 0;
        $validated['hourly_rate'] = $validated['hourly_rate'] ?? 0;

        $employee->update($validated);

        return redirect()->route('cimspayroll.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function employeeDestroy($id)
    {
        $employee = PayrollEmployee::findOrFail($id);
        $employee->delete();

        return redirect()->route('cimspayroll.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    // ═══════════════════════════════════════════════
    // INCOME TYPES
    // ═══════════════════════════════════════════════

    public function incomeTypes()
    {
        $types = PayrollIncomeType::orderBy('sort_order')->orderBy('name')->get();
        return view('cims_payroll::payroll.income-types.index', compact('types'));
    }

    public function incomeTypeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'is_taxable' => 'required|in:0,1',
            'is_uif_applicable' => 'required|in:0,1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = 1;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $type = PayrollIncomeType::create($validated);

        return redirect()->route('cimspayroll.income-types.index')
            ->with('success', 'Income type created successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'created');
    }

    public function incomeTypeUpdate(Request $request, $id)
    {
        $type = PayrollIncomeType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'is_taxable' => 'required|in:0,1',
            'is_uif_applicable' => 'required|in:0,1',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $type->update($validated);

        return redirect()->route('cimspayroll.income-types.index')
            ->with('success', 'Income type updated successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'updated');
    }

    public function incomeTypeDestroy($id)
    {
        $type = PayrollIncomeType::findOrFail($id);
        $name = $type->name;
        $type->delete();
        return redirect()->route('cimspayroll.income-types.index')
            ->with('success', 'Income type deleted successfully.')
            ->with('swal_name', $name)
            ->with('swal_action', 'deleted');
    }

    // ═══════════════════════════════════════════════
    // DEDUCTION TYPES
    // ═══════════════════════════════════════════════

    public function deductionTypes()
    {
        $types = PayrollDeductionType::orderBy('sort_order')->orderBy('name')->get();
        return view('cims_payroll::payroll.deduction-types.index', compact('types'));
    }

    public function deductionTypeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'calc_type' => 'required|in:percentage,fixed',
            'default_value' => 'nullable|numeric|min:0',
            'is_statutory' => 'required|in:0,1',
            'is_auto_calculated' => 'required|in:0,1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = 1;
        $validated['default_value'] = $validated['default_value'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $type = PayrollDeductionType::create($validated);

        return redirect()->route('cimspayroll.deduction-types.index')
            ->with('success', 'Deduction type created successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'created');
    }

    public function deductionTypeUpdate(Request $request, $id)
    {
        $type = PayrollDeductionType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'calc_type' => 'required|in:percentage,fixed',
            'default_value' => 'nullable|numeric|min:0',
            'is_statutory' => 'required|in:0,1',
            'is_auto_calculated' => 'required|in:0,1',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $type->update($validated);

        return redirect()->route('cimspayroll.deduction-types.index')
            ->with('success', 'Deduction type updated successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'updated');
    }

    public function deductionTypeDestroy($id)
    {
        $type = PayrollDeductionType::findOrFail($id);

        if ($type->is_statutory) {
            return redirect()->route('cimspayroll.deduction-types.index')
                ->with('error', 'Cannot delete statutory deduction types. Deactivate instead.');
        }

        $name = $type->name;
        $type->delete();
        return redirect()->route('cimspayroll.deduction-types.index')
            ->with('success', 'Deduction type deleted successfully.')
            ->with('swal_name', $name)
            ->with('swal_action', 'deleted');
    }

    // ═══════════════════════════════════════════════
    // COMPANY CONTRIBUTION TYPES
    // ═══════════════════════════════════════════════

    public function contributionTypes()
    {
        $types = PayrollCompanyContributionType::orderBy('sort_order')->orderBy('name')->get();
        $deductionTypes = PayrollDeductionType::where('is_active', 1)->orderBy('name')->get();
        return view('cims_payroll::payroll.contribution-types.index', compact('types', 'deductionTypes'));
    }

    public function contributionTypeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'calc_type' => 'required|in:percentage,fixed',
            'default_value' => 'nullable|numeric|min:0',
            'linked_deduction_id' => 'nullable|exists:cims_payroll_deduction_types,id',
            'is_statutory' => 'required|in:0,1',
            'is_auto_calculated' => 'required|in:0,1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = 1;
        $validated['default_value'] = $validated['default_value'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $type = PayrollCompanyContributionType::create($validated);

        return redirect()->route('cimspayroll.contribution-types.index')
            ->with('success', 'Company contribution type created successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'created');
    }

    public function contributionTypeUpdate(Request $request, $id)
    {
        $type = PayrollCompanyContributionType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sars_code' => 'nullable|string|max:10',
            'calc_type' => 'required|in:percentage,fixed',
            'default_value' => 'nullable|numeric|min:0',
            'linked_deduction_id' => 'nullable|exists:cims_payroll_deduction_types,id',
            'is_statutory' => 'required|in:0,1',
            'is_auto_calculated' => 'required|in:0,1',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $type->update($validated);

        return redirect()->route('cimspayroll.contribution-types.index')
            ->with('success', 'Company contribution type updated successfully.')
            ->with('swal_name', $type->name)
            ->with('swal_action', 'updated');
    }

    public function contributionTypeDestroy($id)
    {
        $type = PayrollCompanyContributionType::findOrFail($id);

        if ($type->is_statutory) {
            return redirect()->route('cimspayroll.contribution-types.index')
                ->with('error', 'Cannot delete statutory contribution types. Deactivate instead.');
        }

        $name = $type->name;
        $type->delete();
        return redirect()->route('cimspayroll.contribution-types.index')
            ->with('success', 'Company contribution type deleted successfully.')
            ->with('swal_name', $name)
            ->with('swal_action', 'deleted');
    }

    // ═══════════════════════════════════════════════
    // TAX TABLES
    // ═══════════════════════════════════════════════

    public function taxTables(Request $request)
    {
        $taxYear = $request->get('tax_year', '2026');

        $brackets = PayrollTaxBracket::where('tax_year', $taxYear)
            ->orderBy('min_amount')
            ->get();

        $rebates = PayrollTaxRebate::where('tax_year', $taxYear)
            ->orderBy('rebate_type')
            ->get();

        $thresholds = PayrollTaxThreshold::where('tax_year', $taxYear)
            ->orderBy('age_group')
            ->get();

        $availableYears = PayrollTaxBracket::select('tax_year')
            ->distinct()
            ->orderBy('tax_year', 'desc')
            ->pluck('tax_year');

        return view('cims_payroll::payroll.tax-tables.index', compact('brackets', 'rebates', 'thresholds', 'taxYear', 'availableYears'));
    }

    public function taxBracketStore(Request $request)
    {
        $validated = $request->validate([
            'tax_year' => 'required|string|max:10',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0|max:100',
            'base_tax' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = 1;
        PayrollTaxBracket::create($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $validated['tax_year']])
            ->with('success', 'Tax bracket added successfully.');
    }

    public function taxBracketUpdate(Request $request, $id)
    {
        $bracket = PayrollTaxBracket::findOrFail($id);

        $validated = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0|max:100',
            'base_tax' => 'required|numeric|min:0',
        ]);

        $bracket->update($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $bracket->tax_year])
            ->with('success', 'Tax bracket updated successfully.');
    }

    public function taxBracketDestroy($id)
    {
        $bracket = PayrollTaxBracket::findOrFail($id);
        $year = $bracket->tax_year;
        $bracket->delete();

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $year])
            ->with('success', 'Tax bracket deleted successfully.');
    }

    public function taxRebateStore(Request $request)
    {
        $validated = $request->validate([
            'tax_year' => 'required|string|max:10',
            'rebate_type' => 'required|in:primary,secondary,tertiary',
            'amount' => 'required|numeric|min:0',
            'age_threshold' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = 1;
        PayrollTaxRebate::create($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $validated['tax_year']])
            ->with('success', 'Tax rebate added successfully.');
    }

    public function taxRebateUpdate(Request $request, $id)
    {
        $rebate = PayrollTaxRebate::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'age_threshold' => 'nullable|integer|min:0',
        ]);

        $rebate->update($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $rebate->tax_year])
            ->with('success', 'Tax rebate updated successfully.');
    }

    public function taxThresholdStore(Request $request)
    {
        $validated = $request->validate([
            'tax_year' => 'required|string|max:10',
            'age_group' => 'required|in:below_65,65_to_74,75_and_over',
            'threshold_amount' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = 1;
        PayrollTaxThreshold::create($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $validated['tax_year']])
            ->with('success', 'Tax threshold added successfully.');
    }

    public function taxThresholdUpdate(Request $request, $id)
    {
        $threshold = PayrollTaxThreshold::findOrFail($id);

        $validated = $request->validate([
            'threshold_amount' => 'required|numeric|min:0',
        ]);

        $threshold->update($validated);

        return redirect()->route('cimspayroll.tax-tables.index', ['tax_year' => $threshold->tax_year])
            ->with('success', 'Tax threshold updated successfully.');
    }

    // ═══════════════════════════════════════════════
    // PHASE 2: LEAVE TYPES
    // ═══════════════════════════════════════════════

    public function leaveTypes()
    {
        $types = PayrollLeaveType::orderBy('sort_order')->orderBy('name')->get();
        return view('cims_payroll::payroll.leave-types.index', compact('types'));
    }

    public function leaveTypeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:cims_payroll_leave_types,code',
            'days_per_year' => 'required|numeric|min:0',
            'cycle_years' => 'required|integer|min:1|max:10',
            'is_paid' => 'required|in:0,1',
            'is_statutory' => 'required|in:0,1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = 1;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PayrollLeaveType::create($validated);

        return redirect()->route('cimspayroll.leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function leaveTypeUpdate(Request $request, $id)
    {
        $type = PayrollLeaveType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:cims_payroll_leave_types,code,' . $id,
            'days_per_year' => 'required|numeric|min:0',
            'cycle_years' => 'required|integer|min:1|max:10',
            'is_paid' => 'required|in:0,1',
            'is_statutory' => 'required|in:0,1',
            'is_active' => 'required|in:0,1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $type->update($validated);

        return redirect()->route('cimspayroll.leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    public function leaveTypeDestroy($id)
    {
        $type = PayrollLeaveType::findOrFail($id);

        if ($type->is_statutory) {
            return redirect()->route('cimspayroll.leave-types.index')
                ->with('error', 'Cannot delete statutory leave types. Deactivate instead.');
        }

        $type->delete();
        return redirect()->route('cimspayroll.leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }

    // ═══════════════════════════════════════════════
    // PHASE 2: LEAVE MANAGEMENT
    // ═══════════════════════════════════════════════

    public function leaveBalances(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $companyId = $request->get('company_id');

        $query = PayrollEmployee::with(['company'])
            ->where('status', 'active');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->get();
        $leaveTypes = PayrollLeaveType::where('is_active', 1)->orderBy('sort_order')->get();
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        // Get balances for all employees
        $balances = PayrollLeaveBalance::where('year', $year)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        return view('cims_payroll::payroll.leave.balances', compact(
            'employees', 'leaveTypes', 'companies', 'balances', 'year', 'companyId'
        ));
    }

    public function leaveBalancesInit(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|string|max:4',
            'company_id' => 'nullable|exists:cims_payroll_companies,id',
        ]);

        $year = $validated['year'];
        $query = PayrollEmployee::where('status', 'active');

        if (!empty($validated['company_id'])) {
            $query->where('company_id', $validated['company_id']);
        }

        $employees = $query->get();
        $leaveTypes = PayrollLeaveType::where('is_active', 1)->get();

        $created = 0;
        foreach ($employees as $emp) {
            foreach ($leaveTypes as $lt) {
                $exists = PayrollLeaveBalance::where('employee_id', $emp->id)
                    ->where('leave_type_id', $lt->id)
                    ->where('year', $year)
                    ->exists();

                if (!$exists) {
                    PayrollLeaveBalance::create([
                        'employee_id' => $emp->id,
                        'leave_type_id' => $lt->id,
                        'year' => $year,
                        'entitled_days' => $lt->days_per_year,
                        'taken_days' => 0,
                        'pending_days' => 0,
                        'carried_forward' => 0,
                    ]);
                    $created++;
                }
            }
        }

        return redirect()->route('cimspayroll.leave.balances', [
            'year' => $year,
            'company_id' => $validated['company_id'] ?? '',
        ])->with('success', "Initialized $created leave balance records for $year.");
    }

    public function leaveBalanceUpdate(Request $request, $id)
    {
        $balance = PayrollLeaveBalance::findOrFail($id);

        $validated = $request->validate([
            'entitled_days' => 'required|numeric|min:0',
            'carried_forward' => 'required|numeric|min:0',
        ]);

        $balance->update($validated);

        return redirect()->route('cimspayroll.leave.balances', ['year' => $balance->year])
            ->with('success', 'Leave balance updated.');
    }

    // Leave Applications
    public function leaveApplications(Request $request)
    {
        $query = PayrollLeaveApplication::with(['employee', 'leaveType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = PayrollEmployee::where('status', 'active')->orderBy('last_name')->get();
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.leave.applications', compact('applications', 'employees', 'companies'));
    }

    public function leaveApplicationCreate()
    {
        $employees = PayrollEmployee::where('status', 'active')
            ->orderBy('last_name')->orderBy('first_name')->get();
        $leaveTypes = PayrollLeaveType::where('is_active', 1)->orderBy('sort_order')->get();

        return view('cims_payroll::payroll.leave.apply', compact('employees', 'leaveTypes'));
    }

    public function leaveApplicationStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:cims_payroll_employees,id',
            'leave_type_id' => 'required|exists:cims_payroll_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_requested' => 'required|numeric|min:0.5',
            'reason' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = Auth::id();

        // Update pending days in balance
        $year = date('Y', strtotime($validated['start_date']));
        $balance = PayrollLeaveBalance::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $year)
            ->first();

        if ($balance) {
            $balance->increment('pending_days', $validated['days_requested']);
        }

        PayrollLeaveApplication::create($validated);

        return redirect()->route('cimspayroll.leave.applications')
            ->with('success', 'Leave application submitted successfully.');
    }

    public function leaveApplicationApprove($id)
    {
        $app = PayrollLeaveApplication::findOrFail($id);

        if ($app->status !== 'pending') {
            return redirect()->route('cimspayroll.leave.applications')
                ->with('error', 'Only pending applications can be approved.');
        }

        $app->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Move from pending to taken
        $year = $app->start_date->format('Y');
        $balance = PayrollLeaveBalance::where('employee_id', $app->employee_id)
            ->where('leave_type_id', $app->leave_type_id)
            ->where('year', $year)
            ->first();

        if ($balance) {
            $balance->decrement('pending_days', $app->days_requested);
            $balance->increment('taken_days', $app->days_requested);
        }

        return redirect()->route('cimspayroll.leave.applications')
            ->with('success', 'Leave application approved.');
    }

    public function leaveApplicationReject(Request $request, $id)
    {
        $app = PayrollLeaveApplication::findOrFail($id);

        if ($app->status !== 'pending') {
            return redirect()->route('cimspayroll.leave.applications')
                ->with('error', 'Only pending applications can be rejected.');
        }

        $app->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason', ''),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Release pending days
        $year = $app->start_date->format('Y');
        $balance = PayrollLeaveBalance::where('employee_id', $app->employee_id)
            ->where('leave_type_id', $app->leave_type_id)
            ->where('year', $year)
            ->first();

        if ($balance) {
            $balance->decrement('pending_days', $app->days_requested);
        }

        return redirect()->route('cimspayroll.leave.applications')
            ->with('success', 'Leave application rejected.');
    }

    public function leaveApplicationCancel($id)
    {
        $app = PayrollLeaveApplication::findOrFail($id);

        if (!in_array($app->status, ['pending', 'approved'])) {
            return redirect()->route('cimspayroll.leave.applications')
                ->with('error', 'Cannot cancel this application.');
        }

        $year = $app->start_date->format('Y');
        $balance = PayrollLeaveBalance::where('employee_id', $app->employee_id)
            ->where('leave_type_id', $app->leave_type_id)
            ->where('year', $year)
            ->first();

        if ($balance) {
            if ($app->status === 'pending') {
                $balance->decrement('pending_days', $app->days_requested);
            } elseif ($app->status === 'approved') {
                $balance->decrement('taken_days', $app->days_requested);
            }
        }

        $app->update(['status' => 'cancelled']);

        return redirect()->route('cimspayroll.leave.applications')
            ->with('success', 'Leave application cancelled.');
    }

    // ═══════════════════════════════════════════════
    // PHASE 2: TIMESHEETS
    // ═══════════════════════════════════════════════

    public function timesheets(Request $request)
    {
        $query = PayrollTimesheet::with(['employee.company']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period_month')) {
            $month = $request->period_month; // format: YYYY-MM
            $query->whereRaw("DATE_FORMAT(period_start, '%Y-%m') = ?", [$month]);
        }

        $timesheets = $query->orderBy('period_start', 'desc')->paginate(20);
        $employees = PayrollEmployee::where('status', 'active')->orderBy('last_name')->get();
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.timesheets.index', compact('timesheets', 'employees', 'companies'));
    }

    public function timesheetCreate()
    {
        $employees = PayrollEmployee::with('company')
            ->where('status', 'active')
            ->orderBy('last_name')->orderBy('first_name')->get();

        return view('cims_payroll::payroll.timesheets.form', ['timesheet' => null, 'employees' => $employees]);
    }

    public function timesheetStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:cims_payroll_employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'normal_hours' => 'required|numeric|min:0',
            'overtime_15x_hours' => 'nullable|numeric|min:0',
            'overtime_2x_hours' => 'nullable|numeric|min:0',
            'sunday_hours' => 'nullable|numeric|min:0',
            'public_holiday_hours' => 'nullable|numeric|min:0',
            'days_worked' => 'required|numeric|min:0',
            'days_absent' => 'nullable|numeric|min:0',
            'days_leave' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check for duplicate period
        $exists = PayrollTimesheet::where('employee_id', $validated['employee_id'])
            ->where('period_start', $validated['period_start'])
            ->where('period_end', $validated['period_end'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'A timesheet already exists for this employee and period.');
        }

        $validated['status'] = 'draft';
        $validated['created_by'] = Auth::id();
        $validated['overtime_15x_hours'] = $validated['overtime_15x_hours'] ?? 0;
        $validated['overtime_2x_hours'] = $validated['overtime_2x_hours'] ?? 0;
        $validated['sunday_hours'] = $validated['sunday_hours'] ?? 0;
        $validated['public_holiday_hours'] = $validated['public_holiday_hours'] ?? 0;
        $validated['days_absent'] = $validated['days_absent'] ?? 0;
        $validated['days_leave'] = $validated['days_leave'] ?? 0;

        PayrollTimesheet::create($validated);

        return redirect()->route('cimspayroll.timesheets.index')
            ->with('success', 'Timesheet created successfully.');
    }

    public function timesheetEdit($id)
    {
        $timesheet = PayrollTimesheet::findOrFail($id);

        if ($timesheet->status === 'approved') {
            return redirect()->route('cimspayroll.timesheets.index')
                ->with('error', 'Cannot edit an approved timesheet.');
        }

        $employees = PayrollEmployee::with('company')
            ->where('status', 'active')
            ->orderBy('last_name')->orderBy('first_name')->get();

        return view('cims_payroll::payroll.timesheets.form', compact('timesheet', 'employees'));
    }

    public function timesheetUpdate(Request $request, $id)
    {
        $timesheet = PayrollTimesheet::findOrFail($id);

        if ($timesheet->status === 'approved') {
            return redirect()->route('cimspayroll.timesheets.index')
                ->with('error', 'Cannot edit an approved timesheet.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:cims_payroll_employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'normal_hours' => 'required|numeric|min:0',
            'overtime_15x_hours' => 'nullable|numeric|min:0',
            'overtime_2x_hours' => 'nullable|numeric|min:0',
            'sunday_hours' => 'nullable|numeric|min:0',
            'public_holiday_hours' => 'nullable|numeric|min:0',
            'days_worked' => 'required|numeric|min:0',
            'days_absent' => 'nullable|numeric|min:0',
            'days_leave' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['overtime_15x_hours'] = $validated['overtime_15x_hours'] ?? 0;
        $validated['overtime_2x_hours'] = $validated['overtime_2x_hours'] ?? 0;
        $validated['sunday_hours'] = $validated['sunday_hours'] ?? 0;
        $validated['public_holiday_hours'] = $validated['public_holiday_hours'] ?? 0;
        $validated['days_absent'] = $validated['days_absent'] ?? 0;
        $validated['days_leave'] = $validated['days_leave'] ?? 0;

        $timesheet->update($validated);

        return redirect()->route('cimspayroll.timesheets.index')
            ->with('success', 'Timesheet updated successfully.');
    }

    public function timesheetApprove($id)
    {
        $timesheet = PayrollTimesheet::findOrFail($id);
        $timesheet->update(['status' => 'approved']);

        return redirect()->route('cimspayroll.timesheets.index')
            ->with('success', 'Timesheet approved.');
    }

    public function timesheetDestroy($id)
    {
        $timesheet = PayrollTimesheet::findOrFail($id);

        if ($timesheet->status === 'approved') {
            return redirect()->route('cimspayroll.timesheets.index')
                ->with('error', 'Cannot delete an approved timesheet.');
        }

        $timesheet->delete();

        return redirect()->route('cimspayroll.timesheets.index')
            ->with('success', 'Timesheet deleted successfully.');
    }

    // ═══════════════════════════════════════════════
    // PHASE 3: PAY RUNS
    // ═══════════════════════════════════════════════

    public function payRuns(Request $request)
    {
        $query = PayrollPayRun::with('company');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payRuns = $query->orderBy('created_at', 'desc')->paginate(20);
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.pay-runs.index', compact('payRuns', 'companies'));
    }

    public function payRunCreate()
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        return view('cims_payroll::payroll.pay-runs.create', compact('companies'));
    }

    public function payRunStore(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:cims_payroll_companies,id',
            'pay_period' => 'required|string|max:10',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['status'] = 'draft';
        $validated['created_by'] = Auth::id();

        $payRun = PayrollPayRun::create($validated);

        return redirect()->route('cimspayroll.pay-runs.show', $payRun->id)
            ->with('success', 'Pay run created. Click "Process" to calculate payroll.');
    }

    public function payRunShow($id)
    {
        $payRun = PayrollPayRun::with(['company', 'lines.employee', 'lines.items'])->findOrFail($id);
        return view('cims_payroll::payroll.pay-runs.show', compact('payRun'));
    }

    public function payRunProcess($id)
    {
        $payRun = PayrollPayRun::findOrFail($id);

        if (!in_array($payRun->status, ['draft', 'processed'])) {
            return redirect()->route('cimspayroll.pay-runs.show', $id)
                ->with('error', 'Cannot process — pay run is ' . $payRun->status . '.');
        }

        $calculator = new PayRunCalculator();
        $calculator->process($payRun);

        return redirect()->route('cimspayroll.pay-runs.show', $id)
            ->with('success', 'Pay run processed successfully! Review the results below.');
    }

    public function payRunApprove($id)
    {
        $payRun = PayrollPayRun::findOrFail($id);

        if ($payRun->status !== 'processed') {
            return redirect()->route('cimspayroll.pay-runs.show', $id)
                ->with('error', 'Pay run must be processed before approval.');
        }

        $payRun->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('cimspayroll.pay-runs.show', $id)
            ->with('success', 'Pay run approved and locked.');
    }

    public function payRunDestroy($id)
    {
        $payRun = PayrollPayRun::findOrFail($id);

        if ($payRun->status === 'approved') {
            return redirect()->route('cimspayroll.pay-runs.index')
                ->with('error', 'Cannot delete an approved pay run.');
        }

        $payRun->delete();

        return redirect()->route('cimspayroll.pay-runs.index')
            ->with('success', 'Pay run deleted.');
    }

    // ═══════════════════════════════════════════════
    // PHASE 3: LOANS REGISTER
    // ═══════════════════════════════════════════════

    public function loans(Request $request)
    {
        $query = PayrollLoan::with('employee.company');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = PayrollEmployee::where('status', 'active')->orderBy('last_name')->get();

        return view('cims_payroll::payroll.loans.index', compact('loans', 'employees'));
    }

    public function loanCreate()
    {
        $employees = PayrollEmployee::where('status', 'active')
            ->orderBy('last_name')->orderBy('first_name')->get();
        return view('cims_payroll::payroll.loans.form', ['loan' => null, 'employees' => $employees]);
    }

    public function loanStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:cims_payroll_employees,id',
            'loan_type' => 'required|string|max:100',
            'loan_amount' => 'required|numeric|min:0.01',
            'monthly_repayment' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['outstanding_balance'] = $validated['loan_amount'];
        $validated['status'] = 'active';
        $validated['created_by'] = Auth::id();

        PayrollLoan::create($validated);

        return redirect()->route('cimspayroll.loans.index')
            ->with('success', 'Loan registered successfully.');
    }

    public function loanEdit($id)
    {
        $loan = PayrollLoan::findOrFail($id);
        $employees = PayrollEmployee::where('status', 'active')
            ->orderBy('last_name')->orderBy('first_name')->get();
        return view('cims_payroll::payroll.loans.form', compact('loan', 'employees'));
    }

    public function loanUpdate(Request $request, $id)
    {
        $loan = PayrollLoan::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:cims_payroll_employees,id',
            'loan_type' => 'required|string|max:100',
            'loan_amount' => 'required|numeric|min:0.01',
            'outstanding_balance' => 'required|numeric|min:0',
            'monthly_repayment' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paid_off,written_off,suspended',
            'notes' => 'nullable|string',
        ]);

        $loan->update($validated);

        return redirect()->route('cimspayroll.loans.index')
            ->with('success', 'Loan updated successfully.');
    }

    public function loanDestroy($id)
    {
        $loan = PayrollLoan::findOrFail($id);
        $loan->delete();

        return redirect()->route('cimspayroll.loans.index')
            ->with('success', 'Loan deleted.');
    }

    // ═══════════════════════════════════════════════
    // PAYSLIPS
    // ═══════════════════════════════════════════════

    public function payslips(Request $request)
    {
        $query = PayrollPayRun::with('company')
            ->whereIn('status', ['processed', 'approved']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $payRuns = $query->orderBy('pay_period', 'desc')->paginate(20);
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.payslips.index', compact('payRuns', 'companies'));
    }

    public function payslipPreview($payRunId)
    {
        $payRun = PayrollPayRun::with(['company', 'lines.employee', 'lines.items'])
            ->whereIn('status', ['processed', 'approved'])
            ->findOrFail($payRunId);

        return view('cims_payroll::payroll.payslips.preview', compact('payRun'));
    }

    public function payslipDownloadSingle($lineId)
    {
        $line = PayrollPayRunLine::with(['employee.company', 'payRun', 'items'])->findOrFail($lineId);

        $generator = new PayslipPdfGenerator();
        $pdf = $generator->generateSingle($line);

        $filename = 'Payslip_' . ($line->employee->employee_number ?? $line->employee_id)
            . '_' . $line->payRun->pay_period . '.pdf';

        return $pdf->download($filename);
    }

    public function payslipDownloadBulk($payRunId)
    {
        $payRun = PayrollPayRun::with(['company', 'lines.employee.company', 'lines.items'])
            ->whereIn('status', ['processed', 'approved'])
            ->findOrFail($payRunId);

        $generator = new PayslipPdfGenerator();
        $pdf = $generator->generateBulk($payRun);

        $filename = 'Payslips_' . ($payRun->company->company_name ?? 'Company')
            . '_' . $payRun->pay_period . '.pdf';

        return $pdf->download($filename);
    }

    public function payslipViewSingle($lineId)
    {
        $line = PayrollPayRunLine::with(['employee.company', 'payRun', 'items'])->findOrFail($lineId);

        $generator = new PayslipPdfGenerator();
        $pdf = $generator->generateSingle($line);

        return $pdf->stream('payslip.pdf');
    }

    // ═══════════════════════════════════════════════
    // REPORTS
    // ═══════════════════════════════════════════════

    public function reportPayrollSummary(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        $payRuns = collect();

        if ($request->filled('company_id') || $request->filled('period_from')) {
            $query = PayrollPayRun::with(['company', 'lines'])
                ->whereIn('status', ['processed', 'approved']);

            if ($request->filled('company_id')) {
                $query->where('company_id', $request->company_id);
            }
            if ($request->filled('period_from')) {
                $query->where('pay_period', '>=', $request->period_from);
            }
            if ($request->filled('period_to')) {
                $query->where('pay_period', '<=', $request->period_to);
            }

            $payRuns = $query->orderBy('pay_period', 'desc')->get();
        }

        return view('cims_payroll::payroll.reports.payroll-summary', compact('companies', 'payRuns'));
    }

    public function reportPAYE(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        $lines = collect();

        if ($request->filled('pay_period')) {
            $query = PayrollPayRunLine::with(['employee', 'payRun.company'])
                ->whereHas('payRun', function ($q) use ($request) {
                    $q->where('pay_period', $request->pay_period)
                      ->whereIn('status', ['processed', 'approved']);
                    if ($request->filled('company_id')) {
                        $q->where('company_id', $request->company_id);
                    }
                });

            $lines = $query->orderBy('employee_id')->get();
        }

        return view('cims_payroll::payroll.reports.paye', compact('companies', 'lines'));
    }

    public function reportUIF(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        $lines = collect();

        if ($request->filled('pay_period')) {
            $query = PayrollPayRunLine::with(['employee', 'payRun.company'])
                ->whereHas('payRun', function ($q) use ($request) {
                    $q->where('pay_period', $request->pay_period)
                      ->whereIn('status', ['processed', 'approved']);
                    if ($request->filled('company_id')) {
                        $q->where('company_id', $request->company_id);
                    }
                });

            $lines = $query->orderBy('employee_id')->get();
        }

        return view('cims_payroll::payroll.reports.uif', compact('companies', 'lines'));
    }

    public function reportLeave(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        $balances = collect();

        $year = $request->input('year', date('Y'));

        $query = PayrollLeaveBalance::with(['employee.company', 'leaveType'])
            ->where('year', $year);

        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $balances = $query->get()->groupBy('employee_id');

        return view('cims_payroll::payroll.reports.leave', compact('companies', 'balances', 'year'));
    }

    public function reportLoans(Request $request)
    {
        $query = PayrollLoan::with(['employee.company', 'repayments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->get();
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();

        return view('cims_payroll::payroll.reports.loans', compact('loans', 'companies'));
    }

    public function reportCostToCompany(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        $lines = collect();

        if ($request->filled('pay_period')) {
            $query = PayrollPayRunLine::with(['employee', 'payRun.company', 'items'])
                ->whereHas('payRun', function ($q) use ($request) {
                    $q->where('pay_period', $request->pay_period)
                      ->whereIn('status', ['processed', 'approved']);
                    if ($request->filled('company_id')) {
                        $q->where('company_id', $request->company_id);
                    }
                });

            $lines = $query->orderBy('employee_id')->get();
        }

        return view('cims_payroll::payroll.reports.cost-to-company', compact('companies', 'lines'));
    }

    // ═══════════════════════════════════════════════
    // PAYROLL PROCESSING (Unified Screen)
    // ═══════════════════════════════════════════════

    public function processing(Request $request)
    {
        $companies = PayrollCompany::where('is_active', 1)->orderBy('company_name')->get();
        return view('cims_payroll::payroll.processing', compact('companies'));
    }

    public function processingEmployees(Request $request)
    {
        $companyId = $request->input('company_id');
        if (!$companyId) {
            return response()->json(['employees' => []]);
        }

        $employees = PayrollEmployee::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'employee_number', 'first_name', 'last_name', 'job_title', 'department', 'pay_type', 'status']);

        $employees->each(function ($e) {
            $e->full_name = $e->first_name . ' ' . $e->last_name;
        });

        return response()->json(['employees' => $employees]);
    }

    public function processingEmployee($id)
    {
        $employee = PayrollEmployee::with('company')->findOrFail($id);

        // Get latest pay run line for this employee (for payslip/transactions data)
        $latestPayRunLine = PayrollPayRunLine::with('items')
            ->where('employee_id', $id)
            ->whereHas('payRun', fn($q) => $q->whereIn('status', ['processed', 'approved']))
            ->orderByDesc('id')
            ->first();

        // Build payslip data from latest pay run OR defaults
        $payslip = ['earnings' => [], 'deductions' => [], 'contributions' => [], 'fringe' => []];
        if ($latestPayRunLine) {
            $payslip['earnings'] = $latestPayRunLine->items
                ->where('item_type', 'income')
                ->sortBy('sort_order')
                ->map(fn($i) => ['name' => $i->name, 'amount' => $i->amount, 'hours' => $i->hours, 'rate' => $i->rate])
                ->values();
            $payslip['deductions'] = $latestPayRunLine->items
                ->where('item_type', 'deduction')
                ->sortBy('sort_order')
                ->map(fn($i) => ['name' => $i->name, 'amount' => $i->amount, 'rate' => $i->rate])
                ->values();
            $payslip['contributions'] = $latestPayRunLine->items
                ->where('item_type', 'employer_contribution')
                ->sortBy('sort_order')
                ->map(fn($i) => ['name' => $i->name, 'amount' => $i->amount, 'rate' => $i->rate])
                ->values();
            $payslip['gross_pay'] = $latestPayRunLine->gross_pay;
            $payslip['total_deductions'] = $latestPayRunLine->total_deductions;
            $payslip['net_pay'] = $latestPayRunLine->net_pay;
        } else {
            // No pay run yet — check for saved employee defaults first
            $savedDefaults = PayrollEmployeePayslipDefault::where('employee_id', $id)
                ->orderBy('section')
                ->orderBy('sort_order')
                ->get();

            if ($savedDefaults->count() > 0) {
                // Load from saved employee defaults
                foreach (['earnings', 'deductions', 'contributions', 'fringe'] as $section) {
                    $sectionItems = $savedDefaults->where('section', $section);
                    foreach ($sectionItems as $item) {
                        $hrs = (float) $item->hours;
                        $rate = (float) $item->rate;
                        $amount = $hrs > 0 && $rate > 0 ? round($hrs * $rate, 2) : 0;
                        $payslip[$section][] = [
                            'name' => $item->name,
                            'hours' => $hrs,
                            'rate' => $rate,
                            'amount' => $amount,
                        ];
                    }
                }
                $payslip['has_saved_defaults'] = true;
            } else {
                // No saved defaults — load from global types
                $grossPay = (float) $employee->basic_salary;

                // Default Earnings: Basic salary (hours=1, rate=salary)
                $payslip['earnings'][] = ['name' => 'Basic Salary', 'amount' => $grossPay, 'hours' => 1, 'rate' => $grossPay];

                // Add other active income types
                $incomeTypes = PayrollIncomeType::where('is_active', 1)->orderBy('sort_order')->get();
                foreach ($incomeTypes as $it) {
                    $n = strtolower($it->name);
                    if (strpos($n, 'basic') !== false || strpos($n, 'normal hours') !== false) continue;
                    $payslip['earnings'][] = ['name' => $it->name, 'amount' => 0, 'hours' => 0, 'rate' => 0];
                }

                // Default Deductions: PAYE + UIF + other types
                $payeTax = 0;
                $taxYear = date('Y');
                $annualIncome = $grossPay * 12;
                $brackets = PayrollTaxBracket::where('tax_year', $taxYear)->where('is_active', 1)->orderBy('min_amount')->get();
                foreach ($brackets as $bracket) {
                    if ($annualIncome >= $bracket->min_amount && $annualIncome <= $bracket->max_amount) {
                        $payeTax = $bracket->base_tax + (($annualIncome - $bracket->min_amount + 1) * $bracket->rate / 100);
                        break;
                    }
                }
                $rebates = PayrollTaxRebate::where('tax_year', $taxYear)->where('is_active', 1)->get();
                $age = $employee->date_of_birth ? $employee->date_of_birth->age : 0;
                foreach ($rebates as $rebate) {
                    if ($rebate->rebate_type === 'primary') $payeTax -= $rebate->amount;
                    elseif ($rebate->rebate_type === 'secondary' && $age >= 65) $payeTax -= $rebate->amount;
                    elseif ($rebate->rebate_type === 'tertiary' && $age >= 75) $payeTax -= $rebate->amount;
                }
                $payeTax = max(0, round($payeTax / 12, 2));

                $uifCeiling = 17712.00;
                $uifAmount = round(min($grossPay, $uifCeiling) * 0.01, 2);

                $payslip['deductions'][] = ['name' => 'Tax', 'amount' => $payeTax, 'hours' => 1, 'rate' => $payeTax];
                $payslip['deductions'][] = ['name' => 'Unemployment Insurance Fund', 'amount' => $uifAmount, 'hours' => 1, 'rate' => $uifAmount];

                $deductionTypes = PayrollDeductionType::where('is_active', 1)->where('is_statutory', 0)->orderBy('sort_order')->get();
                foreach ($deductionTypes as $dt) {
                    $amount = 0;
                    if ($dt->default_value > 0) {
                        $amount = $dt->calc_type === 'percentage' ? round($grossPay * $dt->default_value / 100, 2) : round($dt->default_value, 2);
                    }
                    $payslip['deductions'][] = ['name' => $dt->name, 'amount' => $amount, 'hours' => 1, 'rate' => $amount];
                }

                // Default Company Contributions
                $payslip['contributions'][] = ['name' => 'UIF (Employer 1%)', 'amount' => $uifAmount, 'hours' => 1, 'rate' => $uifAmount];
                $sdlAmount = round($grossPay * 0.01, 2);
                $payslip['contributions'][] = ['name' => 'SDL (1%)', 'amount' => $sdlAmount, 'hours' => 1, 'rate' => $sdlAmount];

                $contribTypes = PayrollCompanyContributionType::where('is_active', 1)->where('is_statutory', 0)->orderBy('sort_order')->get();
                foreach ($contribTypes as $ct) {
                    $amount = 0;
                    if ($ct->default_value > 0) {
                        $amount = $ct->calc_type === 'percentage' ? round($grossPay * $ct->default_value / 100, 2) : round($ct->default_value, 2);
                    }
                    $payslip['contributions'][] = ['name' => $ct->name, 'amount' => $amount, 'hours' => 1, 'rate' => $amount];
                }

                $payslip['has_saved_defaults'] = false;
            }

            // Calculate totals
            $grossPay = collect($payslip['earnings'])->sum('amount');
            $totalDeductions = collect($payslip['deductions'])->sum('amount');
            $payslip['gross_pay'] = $grossPay;
            $payslip['total_deductions'] = $totalDeductions;
            $payslip['net_pay'] = $grossPay - $totalDeductions;
        }

        // Build transactions from latest pay run (current period items)
        $transactions = [];
        if ($latestPayRunLine) {
            // Income section
            $incomeItems = $latestPayRunLine->items->where('item_type', 'income')->sortBy('sort_order');
            foreach ($incomeItems as $item) {
                $transactions[] = [
                    'section' => 'INCOME',
                    'description' => $item->name,
                    'input_units' => $item->hours > 0 ? number_format($item->hours, 2) : '',
                    'input_amount' => $item->rate > 0 ? number_format($item->rate, 2) : '',
                    'final_amount' => $item->amount,
                ];
            }
            // Deduction section
            $deductionItems = $latestPayRunLine->items->where('item_type', 'deduction')->sortBy('sort_order');
            foreach ($deductionItems as $item) {
                $transactions[] = [
                    'section' => 'DEDUCTIONS',
                    'description' => $item->name,
                    'input_units' => '',
                    'input_amount' => $item->rate > 0 ? number_format($item->rate, 2) . '%' : '',
                    'final_amount' => $item->amount,
                ];
            }
            // Employer contributions section
            $contribItems = $latestPayRunLine->items->where('item_type', 'employer_contribution')->sortBy('sort_order');
            foreach ($contribItems as $item) {
                $transactions[] = [
                    'section' => 'COMPANY CONTRIBUTIONS',
                    'description' => $item->name,
                    'input_units' => '',
                    'input_amount' => $item->rate > 0 ? number_format($item->rate, 2) . '%' : '',
                    'final_amount' => $item->amount,
                ];
            }
        }

        // Build YTD data from all pay runs in the current tax year
        $taxYear = date('Y');
        $ytdLines = PayrollPayRunLine::with('items')
            ->where('employee_id', $id)
            ->whereHas('payRun', function ($q) use ($taxYear) {
                $q->whereIn('status', ['processed', 'approved'])
                  ->whereYear('period_start', $taxYear);
            })
            ->orderBy('id')
            ->get();

        $ytd = ['sections' => []];
        if ($ytdLines->count() > 0) {
            $incomeByMonth = [];
            $deductionByMonth = [];
            $contribByMonth = [];

            foreach ($ytdLines as $idx => $ytdLine) {
                foreach ($ytdLine->items as $item) {
                    $key = $item->name;
                    if ($item->item_type === 'income') {
                        if (!isset($incomeByMonth[$key])) $incomeByMonth[$key] = array_fill(0, 12, 0);
                        $incomeByMonth[$key][$idx % 12] = (float) $item->amount;
                    } elseif ($item->item_type === 'deduction') {
                        if (!isset($deductionByMonth[$key])) $deductionByMonth[$key] = array_fill(0, 12, 0);
                        $deductionByMonth[$key][$idx % 12] = (float) $item->amount;
                    } elseif ($item->item_type === 'employer_contribution') {
                        if (!isset($contribByMonth[$key])) $contribByMonth[$key] = array_fill(0, 12, 0);
                        $contribByMonth[$key][$idx % 12] = (float) $item->amount;
                    }
                }
            }

            // Build YTD sections
            if (!empty($incomeByMonth)) {
                $items = [];
                $totals = array_fill(0, 12, 0);
                foreach ($incomeByMonth as $name => $months) {
                    $items[] = ['name' => $name, 'months' => $months];
                    foreach ($months as $m => $v) $totals[$m] += $v;
                }
                $ytd['sections'][] = ['name' => 'INCOME', 'items' => $items, 'total' => $totals, 'total_label' => 'TOTAL INCOME'];
            }
            if (!empty($deductionByMonth)) {
                $items = [];
                $totals = array_fill(0, 12, 0);
                foreach ($deductionByMonth as $name => $months) {
                    $items[] = ['name' => $name, 'months' => $months];
                    foreach ($months as $m => $v) $totals[$m] += $v;
                }
                $ytd['sections'][] = ['name' => 'DEDUCTIONS', 'items' => $items, 'total' => $totals, 'total_label' => 'TOTAL DEDUCTIONS'];
            }
            if (!empty($contribByMonth)) {
                $items = [];
                $totals = array_fill(0, 12, 0);
                foreach ($contribByMonth as $name => $months) {
                    $items[] = ['name' => $name, 'months' => $months];
                    foreach ($months as $m => $v) $totals[$m] += $v;
                }
                $ytd['sections'][] = ['name' => 'COMPANY CONTRIBUTIONS', 'items' => $items, 'total' => $totals, 'total_label' => 'TOTAL CONTRIBUTIONS'];
            }
        }

        // Leave balances
        $leaveBalances = PayrollLeaveBalance::with('leaveType')
            ->where('employee_id', $id)
            ->get();

        $leaveTypes = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'family' => 'Family Responsibility',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
            'unpaid' => 'Unpaid Leave',
            'compassionate' => 'Compassionate Leave',
        ];

        $leave = ['balances' => [], 'start_date' => $employee->start_date ? $employee->start_date->format('Y-m-d') : ''];

        foreach ($leaveTypes as $slug => $name) {
            $balance = $leaveBalances->first(function ($b) use ($slug, $name) {
                return $b->leaveType && (
                    strtolower($b->leaveType->name) === $slug ||
                    strtolower($b->leaveType->name) === strtolower($name)
                );
            });

            $leave['balances'][] = [
                'slug' => $slug,
                'name' => $name,
                'rules' => [
                    [
                        'description' => $name,
                        'entitlement' => $balance ? $balance->entitlement : ($slug === 'annual' ? 15 : ($slug === 'sick' ? 30 : 0)),
                        'cycle_start' => $employee->start_date ? $employee->start_date->format('d M Y') : '',
                        'next_cycle' => $employee->start_date ? $employee->start_date->copy()->addYear()->format('d M Y') : '',
                        'opening_balance' => $balance ? $balance->opening_balance : 0,
                        'accrual' => $balance ? $balance->accrued : 0,
                        'taken' => $balance ? $balance->taken : 0,
                        'closing_balance' => $balance ? $balance->balance : 0,
                        'planned' => 0,
                    ],
                ],
            ];
        }

        // Leave history
        $leaveHistory = PayrollLeaveApplication::where('employee_id', $id)
            ->orderByDesc('start_date')
            ->limit(50)
            ->get()
            ->map(fn($app) => [
                'id' => $app->id,
                'description' => $app->leaveType ? $app->leaveType->name : 'Leave',
                'period_captured' => $app->created_at ? $app->created_at->format('M Y') : '',
                'leave_from' => $app->start_date ? $app->start_date->format('d M Y') : '',
                'leave_to' => $app->end_date ? $app->end_date->format('d M Y') : '',
                'units_calculated' => $app->days_requested,
                'units_applied' => $app->days_requested,
            ]);

        // Format date fields for JSON
        $empData = $employee->toArray();
        if ($employee->date_of_birth) $empData['date_of_birth'] = $employee->date_of_birth->format('Y-m-d');
        if ($employee->start_date) $empData['start_date'] = $employee->start_date->format('Y-m-d');
        if ($employee->termination_date) $empData['termination_date'] = $employee->termination_date->format('Y-m-d');

        return response()->json([
            'employee' => $empData,
            'payslip' => $payslip,
            'transactions' => $transactions,
            'ytd' => $ytd,
            'leave' => $leave,
            'leave_history' => $leaveHistory,
        ]);
    }

    public function processingSave(Request $request, $id)
    {
        $employee = PayrollEmployee::findOrFail($id);
        $tab = $request->input('tab');
        $data = $request->input('data', []);

        try {
            switch ($tab) {
                case 'details':
                    $employee->update([
                        'employee_type' => $data['employee_type'] ?? $employee->employee_type,
                        'employee_number' => $data['employee_number'] ?? $employee->employee_number,
                        'title' => $data['title'] ?? $employee->title,
                        'first_name' => $data['first_name'] ?? $employee->first_name,
                        'second_name' => $data['second_name'] ?? $employee->second_name,
                        'initials' => $data['initials'] ?? $employee->initials,
                        'last_name' => $data['last_name'] ?? $employee->last_name,
                        'known_as' => $data['known_as'] ?? $employee->known_as,
                        'id_number' => $data['id_number'] ?? $employee->id_number,
                        'date_of_birth' => $data['date_of_birth'] ?: null,
                        'passport_number' => $data['passport_number'] ?? $employee->passport_number,
                        'passport_country' => $data['passport_country'] ?? $employee->passport_country,
                        'gender' => $data['gender'] ?? $employee->gender,
                        'phone' => $data['phone'] ?? $employee->phone,
                        'email' => $data['email'] ?? $employee->email,
                        'address_line1' => $data['address_line1'] ?? $employee->address_line1,
                        'address_line2' => $data['address_line2'] ?? $employee->address_line2,
                        'city' => $data['city'] ?? $employee->city,
                        'province' => $data['province'] ?? $employee->province,
                        'postal_code' => $data['postal_code'] ?? $employee->postal_code,
                        'job_title' => $data['job_title'] ?? $employee->job_title,
                        'department' => $data['department'] ?? $employee->department,
                        'start_date' => $data['start_date'] ?: null,
                        'status' => $data['status'] ?? $employee->status,
                        'tax_number' => $data['tax_number'] ?? $employee->tax_number,
                        'tax_status' => $data['tax_status'] ?? $employee->tax_status,
                        'bank_name' => $data['bank_name'] ?? $employee->bank_name,
                        'bank_branch_code' => $data['bank_branch_code'] ?? $employee->bank_branch_code,
                        'bank_account_number' => $data['bank_account_number'] ?? $employee->bank_account_number,
                        'bank_account_type' => $data['bank_account_type'] ?? $employee->bank_account_type,
                        'pay_method' => $data['pay_method'] ?? $employee->pay_method,
                    ]);
                    break;

                case 'hours':
                    $employee->update([
                        'working_hours_per_day' => $data['working_hours_per_day'] ?? $employee->working_hours_per_day,
                        'working_days_per_week' => $data['working_days_per_week'] ?? $employee->working_days_per_week,
                        'pay_type' => $data['pay_type'] ?? $employee->pay_type,
                        'basic_salary' => $data['basic_salary'] ?? $employee->basic_salary,
                        'hourly_rate' => $data['hourly_rate'] ?? $employee->hourly_rate,
                        'must_capture_hours' => isset($data['must_capture_hours']) ? $data['must_capture_hours'] : $employee->must_capture_hours,
                    ]);
                    break;

                case 'eti':
                    $employee->update([
                        'eti_prescribed_min_wage' => $data['eti_prescribed_min_wage'] ?? 0,
                        'eti_national_min_wage' => $data['eti_national_min_wage'] ?? 0,
                        'eti_min_rate' => $data['eti_min_rate'] ?? 0,
                        'eti_fixed_hours' => $data['eti_fixed_hours'] ?? 0,
                        'eti_sez' => $data['eti_sez'] ?? 0,
                        'eti_connected' => $data['eti_connected'] ?? 0,
                        'eti_domestic' => $data['eti_domestic'] ?? 0,
                        'eti_labour_broker' => $data['eti_labour_broker'] ?? 0,
                    ]);
                    break;

                default:
                    // For other tabs, try to update any matching employee fields
                    $fillable = $employee->getFillable();
                    $updates = [];
                    foreach ($data as $key => $value) {
                        if (in_array($key, $fillable)) {
                            $updates[$key] = $value;
                        }
                    }
                    if (!empty($updates)) {
                        $employee->update($updates);
                    }
                    break;
            }

            return response()->json(['success' => true, 'message' => 'Saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()], 500);
        }
    }

    public function processingGeneratePayslip($id)
    {
        $employee = PayrollEmployee::findOrFail($id);

        // Find the latest processed/approved pay run line for this employee
        $line = PayrollPayRunLine::with(['employee.company', 'payRun', 'items'])
            ->where('employee_id', $id)
            ->whereHas('payRun', fn($q) => $q->whereIn('status', ['processed', 'approved']))
            ->orderByDesc('id')
            ->first();

        if (!$line) {
            return back()->with('error', 'No processed pay run found for this employee. Please run a pay run first.');
        }

        $generator = new PayslipPdfGenerator();
        $pdf = $generator->generateSingle($line);

        $filename = 'Payslip_' . $employee->employee_number . '_' . $employee->last_name . '.pdf';
        return $pdf->stream($filename);
    }

    public function processingTaxCalculation($id)
    {
        $employee = PayrollEmployee::with('company')->findOrFail($id);
        $grossMonthly = (float) $employee->basic_salary;
        $annualIncome = $grossMonthly * 12;
        $taxYear = date('Y');

        $brackets = PayrollTaxBracket::where('tax_year', $taxYear)->where('is_active', 1)->orderBy('min_amount')->get();
        $rebates = PayrollTaxRebate::where('tax_year', $taxYear)->where('is_active', 1)->get();
        $thresholds = PayrollTaxThreshold::where('tax_year', $taxYear)->where('is_active', 1)->get();

        $age = $employee->date_of_birth ? $employee->date_of_birth->age : 0;

        // Calculate annual tax
        $annualTax = 0;
        $applicableBracket = null;
        foreach ($brackets as $bracket) {
            if ($annualIncome >= $bracket->min_amount && $annualIncome <= $bracket->max_amount) {
                $annualTax = $bracket->base_tax + (($annualIncome - $bracket->min_amount + 1) * $bracket->rate / 100);
                $applicableBracket = $bracket;
                break;
            }
        }

        $totalRebates = 0;
        foreach ($rebates as $rebate) {
            if ($rebate->rebate_type === 'primary') $totalRebates += $rebate->amount;
            elseif ($rebate->rebate_type === 'secondary' && $age >= 65) $totalRebates += $rebate->amount;
            elseif ($rebate->rebate_type === 'tertiary' && $age >= 75) $totalRebates += $rebate->amount;
        }

        $taxAfterRebates = max(0, $annualTax - $totalRebates);
        $monthlyTax = round($taxAfterRebates / 12, 2);
        $uif = min($grossMonthly, 17712) * 0.01;
        $sdl = $grossMonthly * 0.01;

        // Return a simple HTML page with the tax calculation breakdown
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Tax Calculation</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;padding:30px;color:#333;max-width:700px;margin:auto}';
        $html .= 'h1{color:#004D40;border-bottom:3px solid #009688;padding-bottom:10px}';
        $html .= 'table{width:100%;border-collapse:collapse;margin:15px 0}';
        $html .= 'th,td{padding:8px 12px;border:1px solid #ddd;text-align:left}';
        $html .= 'th{background:#E0F2F1;color:#004D40}';
        $html .= '.total{font-weight:bold;background:#004D40;color:#fff}';
        $html .= '.amount{text-align:right;font-family:monospace}</style></head><body>';
        $html .= '<h1>Tax Calculation — ' . $employee->first_name . ' ' . $employee->last_name . '</h1>';
        $html .= '<p><strong>Employee No:</strong> ' . $employee->employee_number . ' | <strong>Age:</strong> ' . $age . ' | <strong>Tax Year:</strong> ' . $taxYear . '</p>';

        $html .= '<h3>Income</h3><table>';
        $html .= '<tr><td>Monthly Gross</td><td class="amount">R ' . number_format($grossMonthly, 2) . '</td></tr>';
        $html .= '<tr><td>Annual Gross (x12)</td><td class="amount">R ' . number_format($annualIncome, 2) . '</td></tr>';
        $html .= '</table>';

        $html .= '<h3>Tax Brackets</h3><table><tr><th>From</th><th>To</th><th>Rate</th><th>Base Tax</th></tr>';
        foreach ($brackets as $b) {
            $highlight = ($applicableBracket && $b->id === $applicableBracket->id) ? ' style="background:#B2DFDB;font-weight:bold"' : '';
            $html .= '<tr' . $highlight . '><td class="amount">R ' . number_format($b->min_amount, 0) . '</td><td class="amount">R ' . number_format($b->max_amount, 0) . '</td><td class="amount">' . $b->rate . '%</td><td class="amount">R ' . number_format($b->base_tax, 0) . '</td></tr>';
        }
        $html .= '</table>';

        $html .= '<h3>Tax Calculation</h3><table>';
        $html .= '<tr><td>Annual Tax (before rebates)</td><td class="amount">R ' . number_format($annualTax, 2) . '</td></tr>';
        $html .= '<tr><td>Less: Total Rebates</td><td class="amount">R ' . number_format($totalRebates, 2) . '</td></tr>';
        $html .= '<tr><td>Annual Tax (after rebates)</td><td class="amount">R ' . number_format($taxAfterRebates, 2) . '</td></tr>';
        $html .= '<tr class="total"><td>Monthly PAYE</td><td class="amount">R ' . number_format($monthlyTax, 2) . '</td></tr>';
        $html .= '</table>';

        $html .= '<h3>Statutory Deductions (Monthly)</h3><table>';
        $html .= '<tr><td>PAYE Income Tax</td><td class="amount">R ' . number_format($monthlyTax, 2) . '</td></tr>';
        $html .= '<tr><td>UIF (Employee 1%)</td><td class="amount">R ' . number_format($uif, 2) . '</td></tr>';
        $html .= '<tr class="total"><td>Total Statutory</td><td class="amount">R ' . number_format($monthlyTax + $uif, 2) . '</td></tr>';
        $html .= '</table>';

        $html .= '<h3>Employer Contributions (Monthly)</h3><table>';
        $html .= '<tr><td>UIF (Employer 1%)</td><td class="amount">R ' . number_format($uif, 2) . '</td></tr>';
        $html .= '<tr><td>SDL (1%)</td><td class="amount">R ' . number_format($sdl, 2) . '</td></tr>';
        $html .= '<tr class="total"><td>Total Employer</td><td class="amount">R ' . number_format($uif + $sdl, 2) . '</td></tr>';
        $html .= '</table>';

        $html .= '<p style="color:#999;font-size:11px;margin-top:30px;text-align:center">Generated: ' . now()->format('d M Y H:i') . ' | SmartWeigh CIMS Payroll</p>';
        $html .= '</body></html>';

        return response($html);
    }

    /**
     * Save payslip defaults for an employee
     */
    public function processingSaveDefaults(Request $request, $id)
    {
        $employee = PayrollEmployee::findOrFail($id);

        try {
            // Delete existing defaults for this employee
            PayrollEmployeePayslipDefault::where('employee_id', $id)->delete();

            $sections = $request->input('sections', []);
            $sortOrder = 0;

            foreach ($sections as $section => $items) {
                if (!in_array($section, ['earnings', 'deductions', 'contributions', 'fringe'])) continue;
                foreach ($items as $item) {
                    if (empty($item['name'])) continue;
                    PayrollEmployeePayslipDefault::create([
                        'employee_id' => $id,
                        'section' => $section,
                        'name' => $item['name'],
                        'hours' => (float) ($item['hours'] ?? 0),
                        'rate' => (float) ($item['rate'] ?? 0),
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payslip defaults saved for ' . $employee->first_name . ' ' . $employee->last_name,
                'count' => $sortOrder,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
