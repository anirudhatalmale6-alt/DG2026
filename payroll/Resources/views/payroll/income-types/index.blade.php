@extends('layouts.default')

@section('title', 'Income Types')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 50px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }

/* Income types table */
.income-table thead th {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5b676d;
    padding: 12px 15px;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
}
.income-table tbody td {
    vertical-align: middle;
    padding: 14px 15px;
    border-bottom: 1px solid #eee;
}
.income-table tbody tr.view-row:hover {
    background: #f8fbfd;
}
.income-type-name {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
    display: block;
    margin-bottom: 2px;
}
.income-type-desc {
    font-size: 12px;
    color: #999;
    display: block;
}
.income-type-sars {
    font-size: 14px;
    font-weight: 600;
    color: #5b676d;
}
.income-type-order {
    font-size: 14px;
    font-weight: 600;
    color: #5b676d;
}
.income-actions {
    white-space: nowrap;
    text-align: right;
}
.income-actions .btn {
    margin-left: 6px;
}
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-plus-circle"></i></div>
            <div><h1>Income Types</h1><p>Manage payroll income transaction types</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Income Types</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>


    <!-- Add New -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-plus"></i> ADD NEW INCOME TYPE</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimspayroll.income-types.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4"><div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required placeholder="e.g. Overtime"></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">SARS Code</label><input type="text" name="sars_code" class="form-control" placeholder="e.g. 3601" maxlength="10"></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Taxable</label><select name="is_taxable" class="form-control"><option value="1">Yes</option><option value="0">No</option></select></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">UIF Applicable</label><select name="is_uif_applicable" class="form-control"><option value="1">Yes</option><option value="0">No</option></select></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div></div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-10"><div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control" placeholder="Optional"></div></div>
                            <div class="col-md-2"><div class="mb-3"><button type="submit" class="btn button_master_add w-100 text-center"><i class="fa fa-plus"></i> Add</button></div></div>
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
                <div class="card-header"><h4><i class="fas fa-list"></i> INCOME TYPES ({{ $types->count() }})</h4></div>
                <div class="card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table income-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Name</th>
                                    <th style="width:110px;">SARS Code</th>
                                    <th style="width:110px;text-align:center;">Taxable</th>
                                    <th style="width:110px;text-align:center;">UIF</th>
                                    <th style="width:110px;text-align:center;">Status</th>
                                    <th style="width:70px;text-align:center;">Order</th>
                                    <th style="width:200px;text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $i => $type)
                                <tr class="view-row" id="view-{{ $type->id }}">
                                    <td><span style="font-size:14px;font-weight:600;color:#999;">{{ $i + 1 }}</span></td>
                                    <td>
                                        <span class="income-type-name">{{ $type->name }}</span>
                                        @if($type->description)<span class="income-type-desc">{{ $type->description }}</span>@endif
                                    </td>
                                    <td><span class="income-type-sars">{{ $type->sars_code ?? '—' }}</span></td>
                                    <td style="text-align:center;"><span class="button_master_{{ $type->is_taxable ? 'yes' : 'no' }}">{{ $type->is_taxable ? 'Yes' : 'No' }}</span></td>
                                    <td style="text-align:center;"><span class="button_master_{{ $type->is_uif_applicable ? 'yes' : 'no' }}">{{ $type->is_uif_applicable ? 'Yes' : 'No' }}</span></td>
                                    <td style="text-align:center;"><span class="button_master_{{ $type->is_active ? 'active' : 'inactive' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td style="text-align:center;"><span class="income-type-order">{{ $type->sort_order }}</span></td>
                                    <td class="income-actions">
                                        <button type="button" class="btn button_master_edit" onclick="toggleEdit({{ $type->id }})"><i class="fa fa-edit"></i> Edit</button>
                                        <form method="POST" action="{{ route('cimspayroll.income-types.destroy', $type->id) }}" id="delete-form-{{ $type->id }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn button_master_delete" onclick="confirmDelete({{ $type->id }}, '{{ addslashes($type->name) }}')"><i class="fa fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="edit-row" id="edit-{{ $type->id }}">
                                    <td colspan="8" style="padding:15px 20px;background:#f0faff;">
                                        <form method="POST" action="{{ route('cimspayroll.income-types.update', $type->id) }}">
                                            @csrf @method('PUT')
                                            <div class="row">
                                                <div class="col-md-4"><div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" value="{{ $type->name }}" class="form-control" required></div></div>
                                                <div class="col-md-2"><div class="mb-3"><label class="form-label">SARS Code</label><input type="text" name="sars_code" value="{{ $type->sars_code }}" class="form-control" maxlength="10"></div></div>
                                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Taxable</label><select name="is_taxable" class="form-control"><option value="1" {{ $type->is_taxable ? 'selected' : '' }}>Yes</option><option value="0" {{ !$type->is_taxable ? 'selected' : '' }}>No</option></select></div></div>
                                                <div class="col-md-2"><div class="mb-3"><label class="form-label">UIF Applicable</label><select name="is_uif_applicable" class="form-control"><option value="1" {{ $type->is_uif_applicable ? 'selected' : '' }}>Yes</option><option value="0" {{ !$type->is_uif_applicable ? 'selected' : '' }}>No</option></select></div></div>
                                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" value="{{ $type->sort_order }}" class="form-control" min="0"></div></div>
                                            </div>
                                            <div class="row align-items-end">
                                                <div class="col-md-6"><div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" value="{{ $type->description }}" class="form-control"></div></div>
                                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Status</label><select name="is_active" class="form-control"><option value="1" {{ $type->is_active ? 'selected' : '' }}>Active</option><option value="0" {{ !$type->is_active ? 'selected' : '' }}>Inactive</option></select></div></div>
                                                <div class="col-md-2"><div class="mb-3"><button type="submit" class="btn button_master_save w-100 text-center"><i class="fa fa-check"></i> Save</button></div></div>
                                                <div class="col-md-2"><div class="mb-3"><button type="button" class="btn button_master_cancel w-100 text-center" onclick="toggleEdit({{ $type->id }})"><i class="fa fa-times"></i> Cancel</button></div></div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* SweetAlert item name styling */
.swal-item-name {
    display: block;
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 8px 0;
}
/* SweetAlert button margin fix for system buttons */
.swal2-actions .btn[class*="button_master_"] {
    margin: 5px 8px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleEdit(id) {
    document.getElementById('view-' + id).classList.toggle('hidden');
    document.getElementById('edit-' + id).classList.toggle('active');
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Income Type?',
        html: 'Are you sure you want to delete<br><span class="swal-item-name">' + name + '</span>This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: '<i class="fa fa-times"></i> No',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn button_master_delete',
            cancelButton: 'btn button_master_no'
        },
        buttonsStyling: false
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    Swal.fire({
        title: 'Income Type {{ ucfirst(session("swal_action", "saved")) }}!',
        html: 'The income type<br><span class="swal-item-name">{{ session("swal_name", "") }}</span>has been {{ session("swal_action", "saved") }} successfully.',
        icon: 'success',
        confirmButtonText: '<i class="fa fa-check"></i> OK',
        customClass: {
            confirmButton: 'btn button_master_ok'
        },
        buttonsStyling: false,
        timer: 4000,
        timerProgressBar: true
    });
    @endif

    @if(session('error'))
    Swal.fire({
        title: 'Error!',
        html: '{!! session("error") !!}',
        icon: 'error',
        confirmButtonText: '<i class="fa fa-check"></i> OK',
        customClass: {
            confirmButton: 'btn button_master_ok'
        },
        buttonsStyling: false
    });
    @endif
});
</script>
@endpush
