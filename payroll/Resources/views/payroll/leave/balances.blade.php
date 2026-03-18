@extends('layouts.default')

@section('title', 'Leave Balances')

@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
.bal-good { color: #059669; font-weight: 600; }
.bal-warn { color: #d97706; font-weight: 600; }
.bal-bad { color: #dc2626; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-balance-scale"></i></div>
            <div><h1>Leave Balances</h1><p>View and manage employee leave balances for {{ $year }}</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Leave Balances</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Filters -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER & INITIALIZE</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('cimspayroll.leave.balances') }}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-control">
                                    @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-control">
                                    <option value="">-- All Companies --</option>
                                    @foreach($companies as $c)
                                    <option value="{{ $c->id }}" {{ $companyId == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label><br>
                                <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <form method="POST" action="{{ route('cimspayroll.leave.balances.init') }}" onsubmit="return confirm('This will create leave balance records for all active employees. Continue?');">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="company_id" value="{{ $companyId }}">
                    <button type="submit" class="btn button_master_add"><i class="fa fa-magic"></i> Initialize Balances for {{ $year }}</button>
                    <small class="text-muted ms-2">Creates leave balance records for all active employees who don't have one yet.</small>
                </form>
            </div>
        </div>
    </div></div>

    <!-- Balances Table -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LEAVE BALANCES — {{ $year }} ({{ $employees->count() }} employees)</h4></div>
            <div class="card-body" style="padding:0;">
                @if($employees->count() > 0 && $leaveTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Employee</th>
                                <th>Company</th>
                                @foreach($leaveTypes as $lt)
                                <th style="text-align:center;font-size:11px;">{{ $lt->code }}<br><small>{{ $lt->days_per_year }}d</small></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                            @php $empBalances = $balances->get($emp->id, collect()); @endphp
                            <tr>
                                <td><strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong><br><small class="text-muted">#{{ $emp->employee_number }}</small></td>
                                <td>{{ $emp->company->company_name ?? '—' }}</td>
                                @foreach($leaveTypes as $lt)
                                @php
                                    $bal = $empBalances->firstWhere('leave_type_id', $lt->id);
                                    $remaining = $bal ? ($bal->entitled_days + $bal->carried_forward - $bal->taken_days - $bal->pending_days) : null;
                                    $cls = $remaining === null ? '' : ($remaining > 5 ? 'bal-good' : ($remaining > 0 ? 'bal-warn' : 'bal-bad'));
                                @endphp
                                <td style="text-align:center;">
                                    @if($bal)
                                    <span class="{{ $cls }}">{{ number_format($remaining, 1) }}</span>
                                    <br><small class="text-muted">T:{{ $bal->taken_days }} P:{{ $bal->pending_days }}</small>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-calendar-times" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No employees or leave types found. Set up employees and leave types first, then initialize balances.</p>
                </div>
                @endif
            </div>
        </div>
    </div></div>
</div>
@endsection
