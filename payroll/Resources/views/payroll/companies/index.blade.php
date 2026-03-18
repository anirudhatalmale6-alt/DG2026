@extends('layouts.default')

@section('title', 'Payroll Companies')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-building"></i></div>
            <div>
                <h1>Companies</h1>
                <p>Manage payroll companies</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Companies</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('cimspayroll.companies.create') }}" class="btn button_master_add"><i class="fa fa-plus"></i> Add Company</a>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-building"></i> COMPANIES LIST</h4></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimspayroll.companies.index') }}">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control" placeholder="Search company name, registration #..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">-- All Status --</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>

                    @if($companies->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Registration #</th>
                                    <th>City</th>
                                    <th>Pay Frequency</th>
                                    <th>Employees</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                <tr>
                                    <td><strong>{{ $company->company_name }}</strong>
                                        @if($company->trading_name)<br><small class="text-muted">t/a {{ $company->trading_name }}</small>@endif
                                    </td>
                                    <td>{{ $company->registration_number ?? '—' }}</td>
                                    <td>{{ $company->city ?? '—' }}</td>
                                    <td>{{ ucfirst($company->pay_frequency) }}</td>
                                    <td>{{ $company->employees()->count() }}</td>
                                    <td><span class="badge bg-{{ $company->is_active ? 'success' : 'secondary' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td>
                                        <a href="{{ route('cimspayroll.companies.edit', $company->id) }}" class="btn button_master_edit" style="padding:4px 14px;font-size:12px;"><i class="fa fa-edit"></i> Edit</a>
                                        <form method="POST" action="{{ route('cimspayroll.companies.destroy', $company->id) }}" onsubmit="return confirm('Delete this company?');" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn button_master_delete" style="padding:4px 14px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $companies->withQueryString()->links() }}</div>
                    @else
                    <div style="text-align:center;padding:40px;color:#999;">
                        <i class="fas fa-building" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                        <p>No companies found. Click <strong>Add Company</strong> to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
