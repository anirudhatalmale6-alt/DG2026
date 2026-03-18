<?php

namespace Modules\CIMS_EMP201\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMS_EMP201\Models\Emp201Declaration;
use Illuminate\Support\Facades\DB;

class Emp201Controller extends Controller
{
    public function index(Request $request)
    {
        $query = Emp201Declaration::query();

        // Status filter
        $status = $request->get('status');
        if ($status === 'active') {
            $query->where('status', 1);
        } elseif ($status === 'inactive') {
            $query->where('status', 0);
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }

        // Financial year filter
        if ($request->filled('financial_year')) {
            $query->where('financial_year', $request->get('financial_year'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('paye_number', 'like', "%{$search}%");
            });
        }

        $declarations = $query->orderBy('id', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => Emp201Declaration::count(),
            'active' => Emp201Declaration::where('status', 1)->count(),
            'inactive' => Emp201Declaration::where('status', 0)->count(),
            'this_year' => Emp201Declaration::where('financial_year', date('Y'))->count(),
        ];

        // Get unique clients for filter dropdown
        $clients = DB::table('client_master')
            ->select('client_id', 'company_name', 'client_code')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get();

        // Get unique financial years for filter
        $financialYears = Emp201Declaration::select('financial_year')
            ->distinct()
            ->orderBy('financial_year', 'desc')
            ->pluck('financial_year');

        return view('cims_emp201::emp201.index', compact(
            'declarations', 'stats', 'clients', 'financialYears'
        ));
    }

    public function create()
    {
        $clients = DB::table('client_master')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get();

        $periods = DB::table('cims_document_periods')
            ->where('is_active', 1)
            ->where('show_in_return_input', 1)
            ->orderBy('display_order', 'desc')
            ->get();

        return view('cims_emp201::emp201.form', [
            'clients' => $clients,
            'periods' => $periods,
            'declaration' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->getFormData($request);
        $declaration = Emp201Declaration::create($data);

        return redirect()->route('cimsemp201.index')
            ->with('success', 'EMP201 declaration created successfully.');
    }

    public function show($id)
    {
        $declaration = Emp201Declaration::findOrFail($id);
        return view('cims_emp201::emp201.show', compact('declaration'));
    }

    public function edit($id)
    {
        $declaration = Emp201Declaration::findOrFail($id);

        $clients = DB::table('client_master')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get();

        $periods = DB::table('cims_document_periods')
            ->where('is_active', 1)
            ->where('show_in_return_input', 1)
            ->orderBy('display_order', 'desc')
            ->get();

        return view('cims_emp201::emp201.form', [
            'clients' => $clients,
            'periods' => $periods,
            'declaration' => $declaration,
        ]);
    }

    public function update(Request $request, $id)
    {
        $declaration = Emp201Declaration::findOrFail($id);
        $data = $this->getFormData($request);
        $declaration->update($data);

        return redirect()->route('cimsemp201.index')
            ->with('success', 'EMP201 declaration updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $declaration = Emp201Declaration::findOrFail($id);
        $declaration->update(['status' => $request->get('status')]);

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function destroy($id)
    {
        $declaration = Emp201Declaration::findOrFail($id);
        $declaration->delete();

        return response()->json(['success' => true, 'message' => 'EMP201 deleted.']);
    }

    public function apiClientDetail($id)
    {
        $client = DB::table('client_master')->where('client_id', $id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        return response()->json($client);
    }

    public function apiPeriods()
    {
        $periods = DB::table('cims_document_periods')
            ->where('is_active', 1)
            ->where('show_in_return_input', 1)
            ->orderBy('display_order', 'desc')
            ->get();
        return response()->json($periods);
    }

    private function getFormData(Request $request): array
    {
        $data = $request->only([
            // Client reference
            'client_id', 'client_code', 'company_name', 'company_number', 'vat_number',
            'income_tax_number', 'trading_name',
            // Tax references
            'paye_number', 'sdl_number', 'uif_number',
            // Contact / Public Officer
            'first_name', 'surname', 'position',
            'telephone_number', 'mobile_number', 'email',
            // Period
            'pay_period', 'financial_year', 'period_combo', 'payment_period',
            // Payroll Tax
            'paye_liability', 'sdl_liability', 'uif_liability', 'payroll_liability',
            // ETI
            'eti_indicator', 'eti_brought_forward', 'eti_calculated', 'eti_utilised', 'eti_carry_forward',
            // Total Payable
            'paye_payable', 'sdl_payable', 'uif_payable', 'penalty_interest', 'tax_payable',
            // Payment Reference
            'payment_reference',
            // VDP
            'vdp_agreement', 'vdp_application_no',
            // Tax Practitioner
            'tax_practitioner_reg_no', 'tax_practitioner_tel_no',
            // Notes
            'notes',
            // User
            'user_id',
            // Status
            'status',
        ]);

        // Clean numeric fields - remove spaces and formatting
        $numericFields = [
            'paye_liability', 'sdl_liability', 'uif_liability', 'payroll_liability',
            'eti_brought_forward', 'eti_calculated', 'eti_utilised', 'eti_carry_forward',
            'paye_payable', 'sdl_payable', 'uif_payable', 'penalty_interest', 'tax_payable',
        ];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = str_replace([' ', ','], '', $data[$field]);
                $data[$field] = (float) $data[$field];
            }
        }

        // Default status to 1 (active) if not set
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }

        // Handle file uploads
        $fileFields = [
            'file_emp201_return' => 'file_emp201_return',
            'file_emp201_statement' => 'file_emp201_statement',
            'file_working_papers' => 'file_working_papers',
            'file_emp201_pack' => 'file_emp201_pack',
        ];
        foreach ($fileFields as $inputName => $dbColumn) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/emp201'), $filename);
                $data[$dbColumn] = $filename;
            }
        }

        return $data;
    }
}
