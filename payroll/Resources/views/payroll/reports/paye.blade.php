@extends('layouts.default')
@section('title', 'PAYE Report')
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
            <div class="page-icon"><i class="fas fa-file-invoice"></i></div>
            <div><h1>PAYE Report</h1><p>Pay-As-You-Earn tax deductions by employee</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a><span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a><span class="separator">/</span>
            <span class="current">PAYE Report</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.paye') }}">
                <div class="row">
                    <div class="col-md-3"><select name="company_id" class="form-control"><option value="">-- All Companies --</option>@foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><input type="month" name="pay_period" class="form-control" value="{{ request('pay_period', date('Y-m')) }}" required></div>
                    <div class="col-md-2"><button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Generate</button></div>
                </div>
            </form>
        </div>
    </div>

    @if($lines->count() > 0)
    <div class="card smartdash-form-card" style="margin-top:12px;">
        <div class="card-header"><h4><i class="fas fa-table"></i> PAYE DEDUCTIONS — {{ request('pay_period') }}</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr><th>Employee</th><th>ID Number</th><th>Tax Number</th><th style="text-align:right;">Gross Income</th><th style="text-align:right;">Annual Equivalent</th><th style="text-align:right;">PAYE Tax</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $line)
                        <tr>
                            <td><strong>{{ $line->employee->first_name ?? '' }} {{ $line->employee->last_name ?? '' }}</strong><br><small class="text-muted">#{{ $line->employee->employee_number ?? '' }}</small></td>
                            <td>{{ $line->employee->id_number ?? '—' }}</td>
                            <td>{{ $line->employee->tax_number ?? '—' }}</td>
                            <td style="text-align:right;">R {{ number_format($line->gross_pay, 2) }}</td>
                            <td style="text-align:right;color:#666;">R {{ number_format($line->gross_pay * 12, 2) }}</td>
                            <td style="text-align:right;font-weight:700;color:#dc3545;">R {{ number_format($line->paye_tax, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#E0F2F1;font-weight:700;">
                        <tr>
                            <td colspan="3">TOTAL ({{ $lines->count() }} employees)</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('gross_pay'), 2) }}</td>
                            <td></td>
                            <td style="text-align:right;color:#dc3545;">R {{ number_format($lines->sum('paye_tax'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('pay_period'))
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No PAYE data found for this period.</div>
    @endif
</div>
@endsection
