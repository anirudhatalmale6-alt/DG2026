@extends('layouts.default')

@section('title', 'Leave Types')

@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calendar-check"></i></div>
            <div><h1>Leave Types</h1><p>Manage leave types (BCEA compliant)</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Leave Types</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Add New -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-plus"></i> ADD NEW LEAVE TYPE</h4></div>
            <div class="card-body">
                <form method="POST" action="{{ route('cimspayroll.leave-types.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3"><div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required placeholder="e.g. Study Leave"></div></div>
                        <div class="col-md-2"><div class="mb-3"><label class="form-label">Code <span class="text-danger">*</span></label><input type="text" name="code" class="form-control" required placeholder="e.g. STUDY" maxlength="20"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Days/Yr</label><input type="number" step="0.5" name="days_per_year" class="form-control" value="0" min="0"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Cycle</label><input type="number" name="cycle_years" class="form-control" value="1" min="1" max="10"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Paid</label><select name="is_paid" class="form-control"><option value="1">Yes</option><option value="0">No</option></select></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Stat.</label><select name="is_statutory" class="form-control"><option value="0">No</option><option value="1">Yes</option></select></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Order</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div></div>
                        <div class="col-md-2"><div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control" placeholder="Optional"></div></div>
                    </div>
                    <button type="submit" class="btn button_master_save" style="padding:6px 16px;font-size:13px;"><i class="fa fa-plus"></i> Add</button>
                </form>
            </div>
        </div>
    </div></div>

    <!-- List -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LEAVE TYPES ({{ $types->count() }})</h4></div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;"><tr><th>#</th><th>Name</th><th>Code</th><th>Days/Year</th><th>Cycle</th><th>Paid</th><th>Statutory</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($types as $i => $type)
                            <tr class="view-row" id="view-{{ $type->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $type->name }}</strong>@if($type->description)<br><small class="text-muted">{{ $type->description }}</small>@endif</td>
                                <td><code>{{ $type->code }}</code></td>
                                <td>{{ $type->days_per_year }}</td>
                                <td>{{ $type->cycle_years == 1 ? 'Annual' : $type->cycle_years . '-year' }}</td>
                                <td><span class="badge bg-{{ $type->is_paid ? 'success' : 'secondary' }}">{{ $type->is_paid ? 'Paid' : 'Unpaid' }}</span></td>
                                <td><span class="badge bg-{{ $type->is_statutory ? 'info' : 'secondary' }}">{{ $type->is_statutory ? 'BCEA' : 'Custom' }}</span></td>
                                <td><span class="badge bg-{{ $type->is_active ? 'success' : 'secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <button type="button" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" onclick="toggleEdit({{ $type->id }})"><i class="fa fa-edit"></i></button>
                                    @if(!$type->is_statutory)
                                    <form method="POST" action="{{ route('cimspayroll.leave-types.destroy', $type->id) }}" onsubmit="return confirm('Delete?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            <tr class="edit-row" id="edit-{{ $type->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td colspan="8">
                                    <form method="POST" action="{{ route('cimspayroll.leave-types.update', $type->id) }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $type->name }}" required style="flex:2;min-width:120px;">
                                        <input type="text" name="code" value="{{ $type->code }}" required style="width:80px;">
                                        <input type="number" step="0.5" name="days_per_year" value="{{ $type->days_per_year }}" style="width:70px;" min="0">
                                        <input type="number" name="cycle_years" value="{{ $type->cycle_years }}" style="width:55px;" min="1">
                                        <select name="is_paid" style="width:80px;"><option value="1" {{ $type->is_paid ? 'selected' : '' }}>Paid</option><option value="0" {{ !$type->is_paid ? 'selected' : '' }}>Unpaid</option></select>
                                        <select name="is_statutory" style="width:80px;"><option value="0" {{ !$type->is_statutory ? 'selected' : '' }}>Custom</option><option value="1" {{ $type->is_statutory ? 'selected' : '' }}>BCEA</option></select>
                                        <select name="is_active" style="width:90px;"><option value="1" {{ $type->is_active ? 'selected' : '' }}>Active</option><option value="0" {{ !$type->is_active ? 'selected' : '' }}>Inactive</option></select>
                                        <input type="number" name="sort_order" value="{{ $type->sort_order }}" min="0" style="width:55px;">
                                        <input type="text" name="description" value="{{ $type->description }}" placeholder="Description" style="flex:2;min-width:100px;">
                                        <button type="submit" class="btn button_master_save" style="padding:4px 14px;font-size:12px;"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn button_master_cancel" style="padding:4px 14px;font-size:12px;" onclick="toggleEdit({{ $type->id }})"><i class="fa fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    document.getElementById('view-' + id).classList.toggle('hidden');
    document.getElementById('edit-' + id).classList.toggle('active');
}
</script>
@endpush
