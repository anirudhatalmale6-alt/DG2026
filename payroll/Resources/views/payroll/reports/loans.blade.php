@extends('layouts.default')
@section('title', 'Loan Report')
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
            <div class="page-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div><h1>Loan Report</h1><p>Employee loans and outstanding balances</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a><span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a><span class="separator">/</span>
            <span class="current">Loan Report</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.loans') }}">
                <div class="row">
                    <div class="col-md-3"><select name="company_id" class="form-control"><option value="">-- All Companies --</option>@foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><select name="status" class="form-control"><option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Active</option><option value="" {{ request('status') === '' ? 'selected' : '' }}>All</option><option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Paid Off</option><option value="written_off" {{ request('status') === 'written_off' ? 'selected' : '' }}>Written Off</option></select></div>
                    <div class="col-md-2"><button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Generate</button></div>
                </div>
            </form>
        </div>
    </div>

    @if($loans->count() > 0)
    <div class="card smartdash-form-card" style="margin-top:12px;">
        <div class="card-header"><h4><i class="fas fa-table"></i> LOAN REGISTER</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr><th>Employee</th><th>Company</th><th>Type</th><th>Start Date</th><th style="text-align:right;">Loan Amount</th><th style="text-align:right;">Repaid</th><th style="text-align:right;">Outstanding</th><th style="text-align:right;">Monthly</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        @php $repaid = $loan->loan_amount - $loan->outstanding_balance; @endphp
                        <tr>
                            <td><strong>{{ $loan->employee->first_name ?? '' }} {{ $loan->employee->last_name ?? '' }}</strong></td>
                            <td>{{ $loan->employee->company->company_name ?? '—' }}</td>
                            <td>{{ $loan->loan_type }}</td>
                            <td>{{ $loan->start_date->format('d M Y') }}</td>
                            <td style="text-align:right;">R {{ number_format($loan->loan_amount, 2) }}</td>
                            <td style="text-align:right;color:#28a745;">R {{ number_format($repaid, 2) }}</td>
                            <td style="text-align:right;font-weight:700;color:#dc3545;">R {{ number_format($loan->outstanding_balance, 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($loan->monthly_repayment, 2) }}</td>
                            <td><span class="badge bg-{{ $loan->status === 'active' ? 'warning' : ($loan->status === 'paid_off' ? 'success' : 'danger') }}">{{ ucfirst(str_replace('_', ' ', $loan->status)) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#E0F2F1;font-weight:700;">
                        <tr>
                            <td colspan="4">TOTALS ({{ $loans->count() }} loans)</td>
                            <td style="text-align:right;">R {{ number_format($loans->sum('loan_amount'), 2) }}</td>
                            <td style="text-align:right;color:#28a745;">R {{ number_format($loans->sum('loan_amount') - $loans->sum('outstanding_balance'), 2) }}</td>
                            <td style="text-align:right;color:#dc3545;">R {{ number_format($loans->sum('outstanding_balance'), 2) }}</td>
                            <td style="text-align:right;">R {{ number_format($loans->sum('monthly_repayment'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No loans found for the selected criteria.</div>
    @endif
</div>
@endsection
