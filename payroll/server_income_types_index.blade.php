@extends('layouts.default')

@section('title', 'Income Types')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
.edit-row .bootstrap-select { width: 100% !important; }

/* Income types table */
.income-table thead th {
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
.income-table tbody td {
    vertical-align: middle;
    padding: 10px 8px;
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

/* Flex edit form rows */
.edit-flex-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 10px;
    margin-bottom: 6px;
}
.edit-flex-row .edit-field {
    flex: 1 1 0;
    min-width: 0;
}
.edit-flex-row .edit-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #5b676d;
    margin-bottom: 3px;
}
.edit-flex-row .edit-field input,
.edit-flex-row .edit-field select {
    width: 100%;
    box-sizing: border-box;
}
.edit-flex-row .edit-field-btn {
    flex: 0 0 auto;
    min-width: 90px;
    display: flex;
    align-items: flex-end;
}
.edit-flex-row .edit-field-btn .btn {
    width: 100%;
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
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Taxable</label><select name="is_taxable" class="sd_drop_class"><option value="1">Yes</option><option value="0">No</option></select></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">UIF Applicable</label><select name="is_uif_applicable" class="sd_drop_class"><option value="1">Yes</option><option value="0">No</option></select></div></div>
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
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>SARS Code</th>
                                    <th>Taxable</th>
                                    <th>UIF</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th style="text-align:center;">Actions</th>
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
                                    <td style="text-align:center;"><span class="cims-tag">{{ $type->is_taxable ? 'Yes' : 'No' }}</span></td>
                                    <td style="text-align:center;"><span class="cims-tag">{{ $type->is_uif_applicable ? 'Yes' : 'No' }}</span></td>
                                    <td style="text-align:center;"><span class="cims-tag">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
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
                                    <td colspan="8" style="padding:12px;background:#f0faff;">
                                        <form method="POST" action="{{ route('cimspayroll.income-types.update', $type->id) }}">
                                            @csrf @method('PUT')
                                            <div class="edit-flex-row" style="align-items:flex-end;">
                                                <div class="edit-field" style="flex:2.5;"><label>Name <span class="text-danger">*</span></label><input type="text" name="name" value="{{ $type->name }}" class="form-control" required></div>
                                                <div class="edit-field" style="flex:1;"><label>SARS Code</label><input type="text" name="sars_code" value="{{ $type->sars_code }}" class="form-control" maxlength="10"></div>
                                                <div class="edit-field" style="flex:0.8;"><label>Taxable</label><select name="is_taxable" class="form-control"><option value="1" {{ $type->is_taxable ? 'selected' : '' }}>Yes</option><option value="0" {{ !$type->is_taxable ? 'selected' : '' }}>No</option></select></div>
                                                <div class="edit-field" style="flex:0.8;"><label>UIF Applicable</label><select name="is_uif_applicable" class="form-control"><option value="1" {{ $type->is_uif_applicable ? 'selected' : '' }}>Yes</option><option value="0" {{ !$type->is_uif_applicable ? 'selected' : '' }}>No</option></select></div>
                                                <div class="edit-field" style="flex:0.6;"><label>Order</label><input type="number" name="sort_order" value="{{ $type->sort_order }}" class="form-control" min="0"></div>
                                                <div class="edit-field" style="flex:0.8;"><label>Status</label><select name="is_active" class="form-control"><option value="1" {{ $type->is_active ? 'selected' : '' }}>Active</option><option value="0" {{ !$type->is_active ? 'selected' : '' }}>Inactive</option></select></div>
                                            </div>
                                            <div class="edit-flex-row" style="align-items:flex-end;">
                                                <div class="edit-field" style="flex:4;"><label>Description</label><input type="text" name="description" value="{{ $type->description }}" class="form-control"></div>
                                                <div class="edit-field-btn"><button type="submit" class="btn button_master_save"><i class="fa fa-check"></i> Save</button></div>
                                                <div class="edit-field-btn"><button type="button" class="btn button_master_cancel" onclick="toggleEdit({{ $type->id }})"><i class="fa fa-times"></i> Cancel</button></div>
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

@push('scripts')
<script>
$(document).ready(function() { $('.sd_drop_class').selectpicker(); });

function toggleEdit(id) {
    document.getElementById('view-' + id).classList.toggle('hidden');
    document.getElementById('edit-' + id).classList.toggle('active');
}

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
