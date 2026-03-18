@extends('layouts.default')
@section('title', 'UIF Report')
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
            <div class="page-icon"><i class="fas fa-shield-alt"></i></div>
            <div><h1>UIF Report</h1><p>Unemployment Insurance Fund contributions</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a><span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a><span class="separator">/</span>
            <span class="current">UIF Report</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.uif') }}">
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
        <div class="card-header"><h4><i class="fas fa-table"></i> UIF CONTRIBUTIONS — {{ request('pay_period') }}</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr><th>Employee</th><th>ID Number</th><th style="text-align:right;">Gross Pay</th><th style="text-align:right;">UIF (Employee 1%)</th><th style="text-align:right;">UIF (Employer 1%)</th><th style="text-align:right;font-weight:700;">Total UIF</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $line)
                        <tr>
                            <td><strong>{{ $line->employee->first_name ?? '' }} {{ $line->employee->last_name ?? '' }}</strong><br><small class="text-muted">#{{ $line->employee->employee_number ?? '' }}</small></td>
                            <td>{{ $line->employee->id_number ?? '—' }}</td>
                            <td style="text-align:right;">R {{ number_format($line->gross_pay, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($line->uif_employee, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($line->uif_employer, 2) }}</td>
                            <td style="text-align:right;font-weight:700;">R {{ number_format($line->uif_employee + $line->uif_employer, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#E0F2F1;font-weight:700;">
                        <tr>
                            <td colspan="2">TOTAL ({{ $lines->count() }} employees)</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('gross_pay'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('uif_employee'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('uif_employer'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('uif_employee') + $lines->sum('uif_employer'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="p-3" style="background:#f8f9fa;font-size:12px;color:#666;">
                <strong>Note:</strong> UIF ceiling is R17,712/month. Maximum UIF per employee: R177.12/month (employee) + R177.12/month (employer). Total payable to UIF Commissioner: R {{ number_format($lines->sum('uif_employee') + $lines->sum('uif_employer'), 2) }}
            </div>
        </div>
    </div>
    @elseif(request()->has('pay_period'))
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No UIF data found for this period.</div>
    @endif
</div>
@endsection
