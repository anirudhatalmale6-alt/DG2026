<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 20mm 15mm 20mm 15mm; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }

    /* SmartWeigh Branding */
    .accent-bar { background: #004D40; height: 6px; width: 100%; margin-bottom: 0; }
    .header-band { background: #E0F2F1; border-bottom: 2px solid #009688; padding: 12px 16px; }
    .company-name { font-size: 18px; font-weight: bold; color: #004D40; margin: 0; letter-spacing: 0.5px; }
    .company-details { font-size: 8px; color: #00796B; margin-top: 2px; }
    .payslip-title { font-size: 12px; font-weight: bold; color: #009688; text-align: right; text-transform: uppercase; letter-spacing: 1px; }
    .payslip-period { font-size: 9px; color: #00796B; text-align: right; margin-top: 2px; }

    /* Section headers */
    .section-header { background: #004D40; color: #fff; padding: 5px 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 10px; }

    /* Tables */
    table { width: 100%; border-collapse: collapse; }
    .info-table td { padding: 3px 8px; font-size: 9px; vertical-align: top; }
    .info-label { color: #00796B; font-weight: 600; width: 30%; }
    .info-value { color: #333; }

    .items-table { margin-top: 0; }
    .items-table th { background: #E0F2F1; color: #004D40; padding: 5px 8px; font-size: 8px; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #009688; text-align: left; }
    .items-table td { padding: 4px 8px; font-size: 9px; border-bottom: 1px solid #E0F2F1; }
    .items-table .amount { text-align: right; font-family: 'Courier New', monospace; }
    .items-table .subtotal-row td { border-top: 1px solid #009688; font-weight: bold; background: #f8f9fa; }

    /* Two-column layout */
    .two-col { width: 100%; }
    .two-col td { width: 50%; vertical-align: top; padding: 0; }
    .two-col td:first-child { padding-right: 6px; }
    .two-col td:last-child { padding-left: 6px; }

    /* Net Pay box */
    .net-pay-box { background: #004D40; color: #fff; padding: 10px 16px; margin-top: 10px; border-radius: 4px; }
    .net-pay-label { font-size: 10px; color: #B2DFDB; text-transform: uppercase; letter-spacing: 1px; }
    .net-pay-amount { font-size: 22px; font-weight: bold; color: #fff; font-family: 'Courier New', monospace; text-align: right; }

    /* Summary row */
    .summary-bar { background: #E0F2F1; border: 1px solid #B2DFDB; padding: 6px 12px; margin-top: 6px; }
    .summary-bar table td { padding: 2px 8px; font-size: 9px; }
    .summary-label { color: #00796B; font-weight: 600; }
    .summary-value { text-align: right; font-family: 'Courier New', monospace; font-weight: bold; }

    /* Footer */
    .footer { margin-top: 16px; border-top: 1px solid #B2DFDB; padding-top: 6px; font-size: 7px; color: #999; text-align: center; }
    .footer .confidential { color: #E91E63; font-weight: bold; font-size: 8px; margin-bottom: 2px; }

    /* Employer cost section */
    .employer-section { border: 1px dashed #B2DFDB; padding: 6px; margin-top: 6px; }
    .employer-title { font-size: 8px; color: #00796B; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
</style>
</head>
<body>

<!-- Accent Bar -->
<div class="accent-bar"></div>

<!-- Header -->
<div class="header-band">
    <table style="width:100%;">
        <tr>
            <td style="width:60%;vertical-align:top;">
                <div class="company-name">{{ $company->company_name ?? 'Company' }}</div>
                <div class="company-details">
                    @if($company->trading_name && $company->trading_name !== $company->company_name)t/a {{ $company->trading_name }}<br>@endif
                    @if($company->registration_number)Reg: {{ $company->registration_number }} &nbsp;|&nbsp; @endif
                    @if($company->paye_reference)PAYE: {{ $company->paye_reference }} &nbsp;|&nbsp; @endif
                    @if($company->uif_reference)UIF: {{ $company->uif_reference }}@endif
                    @if($company->address_line1)<br>{{ $company->address_line1 }}@if($company->address_line2), {{ $company->address_line2 }}@endif @if($company->city), {{ $company->city }}@endif @if($company->postal_code) {{ $company->postal_code }}@endif @endif
                </div>
            </td>
            <td style="width:40%;vertical-align:top;">
                <div class="payslip-title">Payslip</div>
                <div class="payslip-period">
                    Period: {{ $payRun->period_start->format('d M') }} — {{ $payRun->period_end->format('d M Y') }}<br>
                    Pay Date: {{ $payRun->approved_at ? $payRun->approved_at->format('d M Y') : $payRun->period_end->format('d M Y') }}
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Employee Details -->
<div class="section-header"><span style="font-size:10px;">&#9679;</span> Employee Details</div>
<table class="info-table">
    <tr>
        <td class="info-label">Employee Name</td>
        <td class="info-value"><strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong></td>
        <td class="info-label">Employee No.</td>
        <td class="info-value">{{ $employee->employee_number }}</td>
    </tr>
    <tr>
        <td class="info-label">ID Number</td>
        <td class="info-value">{{ $employee->id_number ?? '—' }}</td>
        <td class="info-label">Tax Number</td>
        <td class="info-value">{{ $employee->tax_number ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">Job Title</td>
        <td class="info-value">{{ $employee->job_title ?? '—' }}</td>
        <td class="info-label">Department</td>
        <td class="info-value">{{ $employee->department ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">Pay Type</td>
        <td class="info-value">{{ ucfirst($employee->pay_type) }}</td>
        <td class="info-label">Tax Status</td>
        <td class="info-value">{{ $employee->tax_status ?? 'Normal' }}</td>
    </tr>
</table>

<!-- Income & Deductions (side by side) -->
<table class="two-col">
<tr>
    <!-- LEFT: Income -->
    <td>
        <div class="section-header"><span style="font-size:10px;">&#9679;</span> Income / Earnings</div>
        <table class="items-table">
            <thead><tr><th>Description</th><th style="text-align:right;">Hours</th><th style="text-align:right;">Rate</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                @foreach($incomeItems as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="amount">{{ $item->hours > 0 ? number_format($item->hours, 2) : '' }}</td>
                    <td class="amount">{{ $item->rate > 0 ? 'R ' . number_format($item->rate, 2) : '' }}</td>
                    <td class="amount">R {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3">GROSS PAY</td>
                    <td class="amount">R {{ number_format($line->gross_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </td>

    <!-- RIGHT: Deductions -->
    <td>
        <div class="section-header"><span style="font-size:10px;">&#9679;</span> Deductions</div>
        <table class="items-table">
            <thead><tr><th>Description</th><th style="text-align:right;">Rate/Basis</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                @foreach($deductionItems as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="amount">
                        @if($item->calc_type === 'percentage' || ($item->calc_type === 'auto' && $item->rate > 0))
                            {{ number_format($item->rate, 2) }}%
                        @endif
                    </td>
                    <td class="amount">R {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="2">TOTAL DEDUCTIONS</td>
                    <td class="amount">R {{ number_format($line->total_deductions, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
</table>

<!-- Net Pay Box -->
<div class="net-pay-box">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;vertical-align:middle;">
                <div class="net-pay-label">Net Pay</div>
            </td>
            <td style="width:50%;vertical-align:middle;">
                <div class="net-pay-amount">R {{ number_format($line->net_pay, 2) }}</div>
            </td>
        </tr>
    </table>
</div>

<!-- Summary Bar -->
<div class="summary-bar">
    <table>
        <tr>
            <td class="summary-label">Gross Pay</td>
            <td class="summary-value">R {{ number_format($line->gross_pay, 2) }}</td>
            <td class="summary-label">PAYE Tax</td>
            <td class="summary-value">R {{ number_format($line->paye_tax, 2) }}</td>
            <td class="summary-label">UIF (Employee)</td>
            <td class="summary-value">R {{ number_format($line->uif_employee, 2) }}</td>
            <td class="summary-label">Total Ded.</td>
            <td class="summary-value">R {{ number_format($line->total_deductions, 2) }}</td>
        </tr>
    </table>
</div>

<!-- Employer Contributions (informational) -->
@if($employerItems->count() > 0)
<div class="employer-section">
    <div class="employer-title">Employer Contributions (for information only — not deducted from pay)</div>
    <table class="items-table" style="margin:0;">
        <tbody>
            @foreach($employerItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="amount" style="width:100px;">R {{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td>TOTAL EMPLOYER COST</td>
                <td class="amount">R {{ number_format($line->total_employer_contributions, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<!-- Banking Details -->
@if($employee->bank_name)
<div class="section-header"><span style="font-size:10px;">&#9679;</span> Banking Details</div>
<table class="info-table">
    <tr>
        <td class="info-label">Bank</td>
        <td class="info-value">{{ $employee->bank_name }}</td>
        <td class="info-label">Branch Code</td>
        <td class="info-value">{{ $employee->bank_branch_code ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">Account No.</td>
        <td class="info-value">{{ $employee->bank_account_number ? '****' . substr($employee->bank_account_number, -4) : '—' }}</td>
        <td class="info-label">Account Type</td>
        <td class="info-value">{{ ucfirst($employee->bank_account_type ?? '—') }}</td>
    </tr>
</table>
@endif

<!-- Leave Balances -->
@if(isset($leaveBalances) && $leaveBalances->count() > 0)
<div class="section-header"><span style="font-size:10px;">&#9679;</span> Leave Balances</div>
<table class="items-table">
    <thead>
        <tr>
            <th>Leave Type</th>
            <th style="text-align:right;">Entitlement</th>
            <th style="text-align:right;">Taken</th>
            <th style="text-align:right;">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($leaveBalances as $lb)
        <tr>
            <td>{{ $lb->leaveType ? $lb->leaveType->name : 'Leave' }}</td>
            <td class="amount">{{ number_format($lb->entitlement, 2) }}</td>
            <td class="amount">{{ number_format($lb->taken, 2) }}</td>
            <td class="amount">{{ number_format($lb->balance, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Footer -->
<div class="footer">
    <div class="confidential">CONFIDENTIAL — FOR EMPLOYEE USE ONLY</div>
    This payslip is computer generated. If you have any queries, please contact your payroll administrator.<br>
    Generated: {{ now()->format('d M Y H:i') }} &nbsp;|&nbsp; {{ $company->company_name ?? '' }}
</div>

</body>
</html>
