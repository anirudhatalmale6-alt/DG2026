@extends('layouts.default')
@section('title', 'Payroll Summary Report')
@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
@media print { .no-print { display: none !important; } .payroll-wrapper { max-width: 100%; } }
</style>
@endpush
@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header no-print" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-chart-bar"></i></div>
            <div><h1>Payroll Summary Report</h1><p>Monthly payroll overview</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Payroll Summary</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.payroll-summary') }}">
                <div class="row">
                    <div class="col-md-3"><select name="company_id" class="form-control"><option value="">-- All Companies --</option>@foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><input type="month" name="period_from" class="form-control" value="{{ request('period_from', date('Y-01')) }}" placeholder="From"></div>
                    <div class="col-md-2"><input type="month" name="period_to" class="form-control" value="{{ request('period_to', date('Y-m')) }}" placeholder="To"></div>
                    <div class="col-md-2"><button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Generate</button></div>
                </div>
            </form>
        </div>
    </div>

    @if($payRuns->count() > 0)
    <div class="card smartdash-form-card" style="margin-top:12px;">
        <div class="card-header"><h4><i class="fas fa-table"></i> PAYROLL SUMMARY</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr><th>Period</th><th>Company</th><th>Employees</th><th style="text-align:right;">Gross Pay</th><th style="text-align:right;">PAYE</th><th style="text-align:right;">UIF (Emp)</th><th style="text-align:right;">Other Ded.</th><th style="text-align:right;">Total Ded.</th><th style="text-align:right;">Employer Cost</th><th style="text-align:right;font-weight:700;">Net Pay</th></tr>
                    </thead>
                    <tbody>
                        @foreach($payRuns as $pr)
                        <tr>
                            <td><strong>{{ $pr->pay_period }}</strong></td>
                            <td>{{ $pr->company->company_name ?? '—' }}</td>
                            <td>{{ $pr->employee_count }}</td>
                            <td style="text-align:right;">R {{ number_format($pr->total_gross, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($pr->lines->sum('paye_tax'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($pr->lines->sum('uif_employee'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($pr->total_deductions - $pr->lines->sum('paye_tax') - $pr->lines->sum('uif_employee'), 2) }}</td>
                            <td style="text-align:right;color:#dc3545;">R {{ number_format($pr->total_deductions, 2) }}</td>
                            <td style="text-align:right;color:#007bff;">R {{ number_format($pr->total_employer_cost, 2) }}</td>
                            <td style="text-align:right;font-weight:700;color:#155724;">R {{ number_format($pr->total_net_pay, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#E0F2F1;font-weight:700;">
                        <tr>
                            <td colspan="2">GRAND TOTALS</td>
                            <td>{{ $payRuns->sum('employee_count') }}</td>
                            <td style="text-align:right;">R {{ number_format($payRuns->sum('total_gross'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($payRuns->flatMap->lines->sum('paye_tax'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($payRuns->flatMap->lines->sum('uif_employee'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($payRuns->sum('total_deductions') - $payRuns->flatMap->lines->sum('paye_tax') - $payRuns->flatMap->lines->sum('uif_employee'), 2) }}</td>
                            <td style="text-align:right;color:#dc3545;">R {{ number_format($payRuns->sum('total_deductions'), 2) }}</td>
                            <td style="text-align:right;color:#007bff;">R {{ number_format($payRuns->sum('total_employer_cost'), 2) }}</td>
                            <td style="text-align:right;color:#155724;">R {{ number_format($payRuns->sum('total_net_pay'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('period_from'))
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No processed pay runs found for the selected criteria.</div>
    @endif
</div>
@endsection
