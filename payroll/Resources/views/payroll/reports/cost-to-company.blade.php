@extends('layouts.default')
@section('title', 'Cost to Company Report')
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
            <div class="page-icon"><i class="fas fa-calculator"></i></div>
            <div><h1>Cost to Company Report</h1><p>Total employment cost per employee including employer contributions</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a><span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a><span class="separator">/</span>
            <span class="current">Cost to Company</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.cost-to-company') }}">
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
        <div class="card-header"><h4><i class="fas fa-table"></i> COST TO COMPANY — {{ request('pay_period') }}</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th>Employee</th>
                            <th style="text-align:right;">Gross Pay</th>
                            <th style="text-align:right;">UIF (Employer)</th>
                            <th style="text-align:right;">SDL</th>
                            <th style="text-align:right;">Other Employer</th>
                            <th style="text-align:right;">Total Employer</th>
                            <th style="text-align:right;font-weight:700;background:#E0F2F1;">Total CTC</th>
                            <th style="text-align:right;">Annual CTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $line)
                        @php
                            $otherEmployer = $line->total_employer_contributions - $line->uif_employer - $line->sdl_employer;
                            $totalCtc = $line->gross_pay + $line->total_employer_contributions;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $line->employee->first_name ?? '' }} {{ $line->employee->last_name ?? '' }}</strong>
                                <br><small class="text-muted">#{{ $line->employee->employee_number ?? '' }} — {{ $line->employee->job_title ?? '' }}</small>
                            </td>
                            <td style="text-align:right;">R {{ number_format($line->gross_pay, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($line->uif_employer, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($line->sdl_employer, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($otherEmployer, 2) }}</td>
                            <td style="text-align:right;color:#007bff;">R {{ number_format($line->total_employer_contributions, 2) }}</td>
                            <td style="text-align:right;font-weight:800;color:#004D40;background:#E0F2F1;">R {{ number_format($totalCtc, 2) }}</td>
                            <td style="text-align:right;color:#666;">R {{ number_format($totalCtc * 12, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#004D40;color:#fff;font-weight:700;">
                        <tr>
                            <td>TOTALS ({{ $lines->count() }} employees)</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('gross_pay'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('uif_employer'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('sdl_employer'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('total_employer_contributions') - $lines->sum('uif_employer') - $lines->sum('sdl_employer'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($lines->sum('total_employer_contributions'), 2) }}</td>
                            <td style="text-align:right;font-size:15px;">R {{ number_format($lines->sum('gross_pay') + $lines->sum('total_employer_contributions'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format(($lines->sum('gross_pay') + $lines->sum('total_employer_contributions')) * 12, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('pay_period'))
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No data found for this period.</div>
    @endif
</div>
@endsection
