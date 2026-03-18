@extends('layouts.default')

@section('title', ($loan ? 'Edit' : 'New') . ' Loan')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div>
                <h1>{{ $loan ? 'Edit' : 'New' }} Loan</h1>
                <p>{{ $loan ? 'Update loan details' : 'Register a new employee loan or advance' }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.loans.index') }}">Loans</a>
            <span class="separator">/</span>
            <span class="current">{{ $loan ? 'Edit' : 'New' }}</span>
        </div>
        <a href="{{ route('cimspayroll.loans.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row"><div class="col-12">
        <form method="POST" action="{{ $loan ? route('cimspayroll.loans.update', $loan->id) : route('cimspayroll.loans.store') }}">
            @csrf
            @if($loan) @method('PUT') @endif

            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-hand-holding-usd"></i> LOAN DETAILS</h4></div>
                <div class="card-body">

                    <div class="form-section-title"><i class="fa fa-user"></i> EMPLOYEE</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required {{ $loan ? 'disabled' : '' }}>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $loan->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }} (#{{ $emp->employee_number }}) — {{ $emp->company->company_name ?? '' }}</option>
                                    @endforeach
                                </select>
                                @if($loan)
                                <input type="hidden" name="employee_id" value="{{ $loan->employee_id }}">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Loan Type <span class="text-danger">*</span></label>
                                <input type="text" name="loan_type" class="form-control" value="{{ old('loan_type', $loan->loan_type ?? '') }}" required placeholder="e.g. Staff Loan, Advance, Vehicle">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-money-bill-wave"></i> AMOUNTS</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Loan Amount (R) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="loan_amount" class="form-control" value="{{ old('loan_amount', $loan->loan_amount ?? '') }}" required min="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Monthly Repayment (R) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="monthly_repayment" class="form-control" value="{{ old('monthly_repayment', $loan->monthly_repayment ?? '') }}" required min="0.01">
                                <small class="text-muted">Auto-deducted from pay run</small>
                            </div>
                        </div>
                        @if($loan)
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Outstanding Balance</label>
                                <input type="text" class="form-control" value="R {{ number_format($loan->outstanding_balance, 2) }}" disabled style="font-weight:700;color:#dc3545;">
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="form-section-title"><i class="fa fa-calendar"></i> DATES</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $loan ? $loan->start_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $loan && $loan->end_date ? $loan->end_date->format('Y-m-d') : '') }}">
                                <small class="text-muted">Optional — auto-closes when paid off</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-sticky-note"></i> NOTES</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes about this loan">{{ old('notes', $loan->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn {{ $loan ? 'button_master_update' : 'button_master_save' }}"><i class="fa fa-save"></i> {{ $loan ? 'Update Loan' : 'Save Loan' }}</button>
                        <a href="{{ route('cimspayroll.loans.index') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div></div>
</div>
@endsection
