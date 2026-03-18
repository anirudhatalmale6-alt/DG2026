@extends('layouts.default')

@section('title', 'Loans Register')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div><h1>Loans Register</h1><p>Employee loans and advances</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Loans</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('cimspayroll.loans.create') }}" class="btn button_master_add"><i class="fa fa-plus"></i> New Loan</a>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Filters -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('cimspayroll.loans.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="employee_id" class="form-control">
                                <option value="">-- All Employees --</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }} (#{{ $emp->employee_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">-- All Status --</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Paid Off</option>
                                <option value="written_off" {{ request('status') === 'written_off' ? 'selected' : '' }}>Written Off</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>

    <!-- Loans Table -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LOANS</h4></div>
            <div class="card-body" style="padding:0;">
                @if($loans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th style="text-align:right;">Loan Amount</th>
                                <th style="text-align:right;">Monthly Repay</th>
                                <th style="text-align:right;">Outstanding</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                            @php
                                $pct = $loan->loan_amount > 0 ? round((($loan->loan_amount - $loan->outstanding_balance) / $loan->loan_amount) * 100) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $loan->employee->first_name ?? '' }} {{ $loan->employee->last_name ?? '' }}</strong><br><small class="text-muted">{{ $loan->employee->company->company_name ?? '' }}</small></td>
                                <td>{{ $loan->loan_type }}</td>
                                <td>{{ $loan->start_date->format('d M Y') }}</td>
                                <td style="text-align:right;">R {{ number_format($loan->loan_amount, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($loan->monthly_repayment, 2) }}</td>
                                <td style="text-align:right;font-weight:700;">R {{ number_format($loan->outstanding_balance, 2) }}</td>
                                <td style="width:120px;">
                                    <div style="background:#e9ecef;border-radius:4px;height:20px;position:relative;">
                                        <div style="background:#28a745;border-radius:4px;height:20px;width:{{ $pct }}%;"></div>
                                        <small style="position:absolute;top:2px;left:50%;transform:translateX(-50%);font-size:10px;font-weight:700;">{{ $pct }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @php $statusColors = ['active' => 'warning', 'paid_off' => 'success', 'written_off' => 'danger', 'suspended' => 'secondary']; @endphp
                                    <span class="badge bg-{{ $statusColors[$loan->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $loan->status)) }}</span>
                                </td>
                                <td>
                                    @if($loan->status === 'active')
                                    <a href="{{ route('cimspayroll.loans.edit', $loan->id) }}" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;"><i class="fa fa-edit"></i></a>
                                    <form method="POST" action="{{ route('cimspayroll.loans.destroy', $loan->id) }}" style="display:inline;" onsubmit="return confirm('Delete this loan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @else
                                    <span class="text-muted" style="font-size:12px;">{{ ucfirst(str_replace('_', ' ', $loan->status)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $loans->withQueryString()->links() }}</div>
                @else
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-hand-holding-usd" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No loans found. Click <strong>New Loan</strong> to add one.</p>
                </div>
                @endif
            </div>
        </div>
    </div></div>
</div>
@endsection
