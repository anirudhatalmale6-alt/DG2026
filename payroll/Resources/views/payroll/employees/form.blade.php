@extends('layouts.default')

@section('title', ($employee ? 'Edit' : 'Add') . ' Employee')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-user-plus"></i></div>
            <div>
                <h1>{{ $employee ? 'Edit' : 'Add' }} Employee</h1>
                <p>{{ $employee ? 'Update employee details' : 'Register a new employee' }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.employees.index') }}">Employees</a>
            <span class="separator">/</span>
            <span class="current">{{ $employee ? 'Edit' : 'Add' }}</span>
        </div>
        <a href="{{ route('cimspayroll.employees.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ $employee ? route('cimspayroll.employees.update', $employee->id) : route('cimspayroll.employees.store') }}">
                @csrf
                @if($employee) @method('PUT') @endif

                <div class="card smartdash-form-card">
                    <div class="card-header"><h4><i class="fas fa-user"></i> EMPLOYEE DETAILS</h4></div>
                    <div class="card-body">

                        <!-- Company & Employment -->
                        <div class="form-section-title"><i class="fa fa-building"></i> COMPANY & EMPLOYMENT</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" class="form-control" required>
                                        <option value="">-- Select Company --</option>
                                        @foreach($companies as $c)
                                        <option value="{{ $c->id }}" {{ old('company_id', $employee->company_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Employee Number <span class="text-danger">*</span></label>
                                    <input type="text" name="employee_number" class="form-control" value="{{ old('employee_number', $employee->employee_number ?? '') }}" required maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $employee ? $employee->start_date->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="form-section-title"><i class="fa fa-id-card"></i> PERSONAL INFORMATION</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SA ID Number</label>
                                    <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $employee->id_number ?? '') }}" maxlength="13" placeholder="13-digit SA ID">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee && $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="male" {{ old('gender', $employee->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $employee->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $employee->gender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="form-section-title"><i class="fa fa-briefcase"></i> JOB DETAILS</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Job Title</label>
                                    <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $employee->job_title ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Pay Information -->
                        <div class="form-section-title"><i class="fa fa-money-bill-wave"></i> PAY INFORMATION</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Pay Type <span class="text-danger">*</span></label>
                                    <select name="pay_type" id="pay_type" class="form-control" required onchange="togglePayFields()">
                                        <option value="salaried" {{ old('pay_type', $employee->pay_type ?? 'salaried') === 'salaried' ? 'selected' : '' }}>Salaried (Monthly)</option>
                                        <option value="hourly" {{ old('pay_type', $employee->pay_type ?? '') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="salary_field">
                                <div class="mb-3">
                                    <label class="form-label">Basic Salary (R/month)</label>
                                    <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $employee->basic_salary ?? '0.00') }}">
                                </div>
                            </div>
                            <div class="col-md-3" id="hourly_field" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label">Hourly Rate (R/hour)</label>
                                    <input type="number" step="0.01" name="hourly_rate" class="form-control" value="{{ old('hourly_rate', $employee->hourly_rate ?? '0.00') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Tax Information -->
                        <div class="form-section-title"><i class="fa fa-landmark"></i> TAX INFORMATION</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tax Number</label>
                                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $employee->tax_number ?? '') }}" placeholder="SARS tax number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tax Status <span class="text-danger">*</span></label>
                                    <select name="tax_status" class="form-control" required>
                                        <option value="normal" {{ old('tax_status', $employee->tax_status ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="directive" {{ old('tax_status', $employee->tax_status ?? '') === 'directive' ? 'selected' : '' }}>Directive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Details -->
                        <div class="form-section-title"><i class="fa fa-university"></i> BANKING DETAILS</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <select name="bank_name" class="form-control">
                                        <option value="">-- Select Bank --</option>
                                        @foreach(['ABSA','Capitec','FNB','Nedbank','Standard Bank','African Bank','Discovery Bank','Investec','TymeBank'] as $bank)
                                        <option value="{{ $bank }}" {{ old('bank_name', $employee->bank_name ?? '') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Branch Code</label>
                                    <input type="text" name="bank_branch_code" class="form-control" value="{{ old('bank_branch_code', $employee->bank_branch_code ?? '') }}" maxlength="10">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Account Type</label>
                                    <select name="bank_account_type" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="cheque" {{ old('bank_account_type', $employee->bank_account_type ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="savings" {{ old('bank_account_type', $employee->bank_account_type ?? '') === 'savings' ? 'selected' : '' }}>Savings</option>
                                        <option value="transmission" {{ old('bank_account_type', $employee->bank_account_type ?? '') === 'transmission' ? 'selected' : '' }}>Transmission</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($employee)
                        <!-- Status & Termination -->
                        <div class="form-section-title"><i class="fa fa-toggle-on"></i> STATUS & TERMINATION</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        <option value="suspended" {{ old('status', $employee->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Termination Date</label>
                                    <input type="date" name="termination_date" class="form-control" value="{{ old('termination_date', $employee->termination_date ? $employee->termination_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Termination Reason</label>
                                    <input type="text" name="termination_reason" class="form-control" value="{{ old('termination_reason', $employee->termination_reason ?? '') }}">
                                </div>
                            </div>
                        </div>
                        @endif

                        <div style="margin-top: 16px;">
                            <button type="submit" class="btn {{ $employee ? 'button_master_update' : 'button_master_save' }}"><i class="fa fa-save"></i> {{ $employee ? 'Update Employee' : 'Save Employee' }}</button>
                            <a href="{{ route('cimspayroll.employees.index') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePayFields() {
    var type = document.getElementById('pay_type').value;
    document.getElementById('salary_field').style.display = type === 'salaried' ? '' : 'none';
    document.getElementById('hourly_field').style.display = type === 'hourly' ? '' : 'none';
}
togglePayFields();
</script>
@endpush
