@extends('layouts.default')

@section('title', 'Payroll Companies')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }

/* Company table */
.company-table thead th {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #5b676d;
    padding: 10px 10px;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
    white-space: nowrap;
}
.company-table tbody td {
    vertical-align: middle;
    padding: 10px 10px;
    border-bottom: 1px solid #eee;
}
.company-table tbody tr:hover {
    background: #f8fbfd;
}
.company-name {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
    display: block;
    margin-bottom: 1px;
}
.company-trading {
    font-size: 12px;
    color: #999;
    display: block;
}
.company-field {
    font-size: 14px;
    font-weight: 600;
    color: #5b676d;
}
.company-actions {
    white-space: nowrap;
    text-align: center;
}
.company-actions .btn {
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
</style>
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

    <!-- Search -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-search"></i> SEARCH</h4></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimspayroll.companies.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <div class="mb-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control" placeholder="Search company name, client code, registration #..." value="{{ request('search') }}"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="sd_drop_class" title="-- All Status --"><option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
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
                <div class="card-header"><h4><i class="fas fa-building"></i> COMPANIES LIST ({{ $companies->total() }})</h4></div>
                <div class="card-body" style="padding:0;">
                    @if($companies->count() > 0)
                    <div class="table-responsive">
                        <table class="table company-table mb-0">
                            <thead>
                                <tr>
                                    <th>Client Code</th>
                                    <th>Company Name</th>
                                    <th>Registration Number</th>
                                    <th style="text-align:center;">Status</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $i => $company)
                                <tr>
                                    <td><span class="company-field" style="font-weight:700;">{{ $company->client_code ?? '—' }}</span></td>
                                    <td>
                                        <span class="company-name">{{ $company->company_name }}</span>
                                        @if($company->trading_name)<span class="company-trading">t/a {{ $company->trading_name }}</span>@endif
                                    </td>
                                    <td><span class="company-field">{{ $company->registration_number ?? '—' }}</span></td>
                                    <td style="text-align:center;"><span class="cims-tag">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="company-actions">
                                        <a href="{{ route('cimspayroll.companies.edit', $company->id) }}" class="btn button_master_edit"><i class="fa fa-edit"></i> Edit</a>
                                        <form method="POST" action="{{ route('cimspayroll.companies.destroy', $company->id) }}" id="delete-form-{{ $company->id }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn button_master_delete" onclick="confirmDelete({{ $company->id }}, '{{ addslashes($company->company_name) }}')"><i class="fa fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" style="padding:0 15px 15px;">{{ $companies->withQueryString()->links() }}</div>
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
