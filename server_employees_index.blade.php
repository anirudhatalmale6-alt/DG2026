@extends('layouts.default')

@section('title', 'Payroll Employees')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
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

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-users"></i> EMPLOYEES LIST</h4></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimspayroll.employees.index') }}">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search name, employee #, ID number..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="company_id" class="form-control">
                                    <option value="">-- All Companies --</option>
                                    @foreach($companies as $c)
                                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">-- All Status --</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>

                    @if($employees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background:#f8f9fa;">
                                <tr><th>Emp #</th><th>Name</th><th>Company</th><th>Job Title</th><th>Pay</th><th>Start Date</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                <tr>
                                    <td>{{ $emp->employee_number }}</td>
                                    <td><strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong>
                                        @if($emp->id_number)<br><small class="text-muted">ID: {{ $emp->id_number }}</small>@endif
                                    </td>
                                    <td>{{ $emp->company->company_name ?? '—' }}</td>
                                    <td>{{ $emp->job_title ?? '—' }}</td>
                                    <td class="text-muted" style="font-size:13px;">
                                        @if($emp->pay_type === 'salaried') R {{ number_format($emp->basic_salary, 2) }}/m
                                        @else R {{ number_format($emp->hourly_rate, 2) }}/hr @endif
                                    </td>
                                    <td>{{ $emp->start_date->format('d M Y') }}</td>
                                    <td><span class="badge bg-{{ $emp->status === 'active' ? 'success' : ($emp->status === 'terminated' ? 'danger' : 'warning') }}">{{ ucfirst($emp->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('cimspayroll.employees.edit', $emp->id) }}" class="btn button_master_edit" style="padding:4px 14px;font-size:12px;"><i class="fa fa-edit"></i> Edit</a>
                                        <form method="POST" action="{{ route('cimspayroll.employees.destroy', $emp->id) }}" onsubmit="return confirm('Delete this employee?');" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn button_master_delete" style="padding:4px 14px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $employees->withQueryString()->links() }}</div>
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
