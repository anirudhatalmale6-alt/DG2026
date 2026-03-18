@extends('layouts.default')

@section('title', 'Payroll Employees')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }

/* Employee table */
.employee-table thead th {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #5b676d;
    padding: 10px 8px;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
    white-space: nowrap;
}
.employee-table tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-bottom: 1px solid #eee;
}
.employee-table tbody tr:hover {
    background: #f8fbfd;
}
.employee-name {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
    display: block;
    margin-bottom: 1px;
}
.employee-id {
    font-size: 12px;
    color: #999;
    display: block;
}
.employee-field {
    font-size: 14px;
    font-weight: 600;
    color: #5b676d;
}
.employee-actions {
    white-space: nowrap;
    text-align: center;
}
.employee-actions .btn {
    margin-left: 6px;
}

/* Simple pill tags for table */
.cims-tag {
    display: inline-block;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #0d9488;
    background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(13, 148, 136, 0.15) 100%);
    border: 1px solid rgba(13, 148, 136, 0.2);
    border-radius: 50px;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.cims-tag-danger {
    color: #dc3545;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.15) 100%);
    border: 1px solid rgba(220, 53, 69, 0.2);
}
.cims-tag-warning {
    color: #e67e22;
    background: linear-gradient(135deg, rgba(230, 126, 34, 0.08) 0%, rgba(230, 126, 34, 0.15) 100%);
    border: 1px solid rgba(230, 126, 34, 0.2);
}
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-users"></i></div>
            <div>
                <h1>Employees</h1>
                <p>Manage payroll employees</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Employees</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('cimspayroll.employees.create') }}" class="btn button_master_add"><i class="fa fa-plus"></i> Add Employee</a>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <!-- Search -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-search"></i> SEARCH</h4></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimspayroll.employees.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="mb-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control" placeholder="Search name, employee #, ID number..." value="{{ request('search') }}"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3"><label class="form-label">Company</label><select name="company_id" class="sd_drop_class" data-live-search="true" title="-- All Companies --"><option value="">-- All Companies --</option>@foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach</select></div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="sd_drop_class" title="-- All Status --"><option value="">-- All Status --</option><option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option><option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option><option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option></select></div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3"><button type="submit" class="btn button_master_search w-100"><i class="fa fa-search"></i> Search</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-users"></i> EMPLOYEES LIST ({{ $employees->total() }})</h4></div>
                <div class="card-body" style="padding:0;">
                    @if($employees->count() > 0)
                    <div class="table-responsive">
                        <table class="table employee-table mb-0">
                            <thead>
                                <tr>
                                    <th>Emp #</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Job Title</th>
                                    <th>Pay</th>
                                    <th>Start Date</th>
                                    <th style="text-align:center;">Status</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                <tr>
                                    <td><span class="employee-field">{{ $emp->employee_number }}</span></td>
                                    <td>
                                        <span class="employee-name">{{ $emp->first_name }} {{ $emp->last_name }}</span>
                                        @if($emp->id_number)<span class="employee-id">ID: {{ $emp->id_number }}</span>@endif
                                    </td>
                                    <td><span class="employee-field">{{ $emp->company->company_name ?? '—' }}</span></td>
                                    <td><span class="employee-field">{{ $emp->job_title ?? '—' }}</span></td>
                                    <td class="cims_money_format"><span class="employee-field">@if($emp->pay_type === 'salaried'){{ cims_money_format($emp->basic_salary) }}/m @else {{ cims_money_format($emp->hourly_rate) }}/hr @endif</span></td>
                                    <td><span class="employee-field">{{ $emp->start_date->format('d M Y') }}</span></td>
                                    <td style="text-align:center;">
                                        <span class="cims-tag {{ $emp->status === 'terminated' ? 'cims-tag-danger' : ($emp->status === 'suspended' ? 'cims-tag-warning' : '') }}">{{ ucfirst($emp->status) }}</span>
                                    </td>
                                    <td class="employee-actions">
                                        <a href="{{ route('cimspayroll.employees.edit', $emp->id) }}" class="btn button_master_edit"><i class="fa fa-edit"></i> Edit</a>
                                        <form method="POST" action="{{ route('cimspayroll.employees.destroy', $emp->id) }}" id="delete-form-{{ $emp->id }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn button_master_delete" onclick="confirmDelete({{ $emp->id }}, '{{ addslashes($emp->first_name . ' ' . $emp->last_name) }}')"><i class="fa fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" style="padding:0 15px 15px;">{{ $employees->withQueryString()->links() }}</div>
                    @else
                    <div style="text-align:center;padding:40px;color:#999;">
                        <i class="fas fa-users" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                        <p>No employees found. Click <strong>Add Employee</strong> to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() { $('.sd_drop_class').selectpicker(); });

function confirmDelete(id, name) {
    CIMSAlert.confirmDelete('delete-form-' + id, name);
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    CIMSAlert.success('{{ session("swal_action", "saved") }}', '{{ session("swal_name", "") }}');
    @endif

    @if(session('error'))
    CIMSAlert.error('{!! session("error") !!}');
    @endif
});
</script>
@endpush
