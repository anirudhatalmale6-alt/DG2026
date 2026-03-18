@extends('layouts.default')
@section('title', 'Leave Report')
@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
@media print { .no-print { display: none !important; } .payroll-wrapper { max-width: 100%; } }
.low-balance { color: #dc3545; font-weight: 700; }
</style>
@endpush
@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header no-print" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calendar-check"></i></div>
            <div><h1>Leave Report</h1><p>Employee leave balances and usage</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a><span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a><span class="separator">/</span>
            <span class="current">Leave Report</span>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn button_master_save"><i class="fa fa-print"></i> Print</button>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <div class="card smartdash-form-card no-print">
        <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('cimspayroll.reports.leave') }}">
                <div class="row">
                    <div class="col-md-3"><select name="company_id" class="form-control"><option value="">-- All Companies --</option>@foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2030" placeholder="Year"></div>
                    <div class="col-md-2"><button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Generate</button></div>
                </div>
            </form>
        </div>
    </div>

    @if($balances->count() > 0)
    <div class="card smartdash-form-card" style="margin-top:12px;">
        <div class="card-header"><h4><i class="fas fa-table"></i> LEAVE BALANCES — {{ $year }}</h4></div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th>Employee</th>
                            <th>Company</th>
                            @php $leaveTypes = collect(); @endphp
                            @foreach($balances->first() ?? [] as $bal)
                                @php $leaveTypes->push($bal->leaveType); @endphp
                                <th style="text-align:center;font-size:11px;">{{ $bal->leaveType->name ?? '' }}<br><small>(Entitled / Taken / Remain)</small></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $empId => $empBalances)
                        @php $emp = $empBalances->first()->employee ?? null; @endphp
                        <tr>
                            <td><strong>{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</strong></td>
                            <td>{{ $emp->company->company_name ?? '—' }}</td>
                            @foreach($empBalances as $bal)
                            @php $remaining = ($bal->days_entitled + $bal->carried_forward) - $bal->days_taken - $bal->days_pending; @endphp
                            <td style="text-align:center;">
                                <span>{{ $bal->days_entitled }}</span> /
                                <span style="color:#dc3545;">{{ $bal->days_taken }}</span> /
                                <span class="{{ $remaining <= 2 ? 'low-balance' : '' }}">{{ $remaining }}</span>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info" style="margin-top:12px;"><i class="fa fa-info-circle me-2"></i>No leave balances found. Initialize leave balances from the Leave Balances page first.</div>
    @endif
</div>
@endsection
