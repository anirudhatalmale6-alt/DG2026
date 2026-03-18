@extends('layouts.default')

@section('content')
<style>
    .jc-admin { font-family: 'Poppins', sans-serif; }
    .jc-admin .card-panel {
        background: #fff; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #eef2f7;
    }
    .jc-admin .section-title {
        font-size: 16px; font-weight: 600; color: #1a1a2e; margin-bottom: 16px;
        padding-bottom: 8px; border-bottom: 2px solid #E91E8C;
    }
    .jc-admin .table thead th {
        background: #1a1a2e; color: #fff; font-size: 12px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border: none;
    }
    .jc-admin .table tbody td { padding: 12px 16px; font-size: 13px; vertical-align: middle; border-bottom: 1px solid #f5f5f5; }
    .jc-admin .table tbody tr:hover { background: #f8f9ff; }
    .jc-admin .btn-sm-action {
        padding: 4px 10px; border-radius: 6px; font-size: 12px; border: none; cursor: pointer; margin-right: 4px;
    }
    .jc-admin .form-control { border-radius: 8px; font-size: 13px; font-family: 'Poppins', sans-serif; }
    .jc-admin label { font-size: 12px; font-weight: 600; color: #555; text-transform: uppercase; margin-bottom: 4px; }
    .jc-admin .modal-content { border-radius: 12px; }
    .jc-admin .modal-header { background: #1a1a2e; color: #fff; border-radius: 12px 12px 0 0; }
    .jc-admin .active-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
</style>

<div class="jc-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Job Card Setup</h3>
            <p style="color:#7f8c8d;margin:4px 0 0;font-size:13px;">Manage job types, steps, fields, and document requirements</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jobcards.dashboard') }}" class="btn btn-sm" style="background:#1a1a2e;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;">
                <i class="fa fa-tachometer mr-1"></i> Dashboard
            </a>
            <button onclick="$('#addTypeModal').modal('show')" class="btn btn-sm" style="background:#E91E8C;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;border:none;">
                <i class="fa fa-plus mr-1"></i> Add Job Type
            </button>
        </div>
    </div>

    <div class="card-panel">
        <h5 class="section-title">Job Types</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Submit To</th>
                    <th>Status</th>
                    <th>Configure</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                <tr id="type-row-{{ $type->id }}">
                    <td>{{ $type->display_order }}</td>
                    <td><strong>{{ $type->name }}</strong></td>
                    <td style="max-width:250px;color:#666;">{{ \Illuminate\Support\Str::limit($type->description, 80) }}</td>
                    <td>{{ $type->submission_to ?: '-' }}</td>
                    <td>
                        <span class="active-badge" style="background:{{ $type->is_active ? '#28a74522' : '#dc354522' }};color:{{ $type->is_active ? '#28a745' : '#dc3545' }};">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('jobcards.admin.steps', $type->id) }}" class="btn-sm-action" style="background:#007bff22;color:#007bff;" title="Steps">
                            <i class="fa fa-list-ol"></i> Steps
                        </a>
                        <a href="{{ route('jobcards.admin.fields', $type->id) }}" class="btn-sm-action" style="background:#28a74522;color:#28a745;" title="Fields">
                            <i class="fa fa-columns"></i> Fields
                        </a>
                        <a href="{{ route('jobcards.admin.documents', $type->id) }}" class="btn-sm-action" style="background:#E91E8C22;color:#E91E8C;" title="Documents">
                            <i class="fa fa-file-o"></i> Docs
                        </a>
                    </td>
                    <td>
                        <button class="btn-sm-action" style="background:#ffc10722;color:#ffc107;" onclick="editType({{ json_encode($type) }})">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn-sm-action" style="background:#dc354522;color:#dc3545;" onclick="deleteType({{ $type->id }})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#aaa;">
                        No job types configured. Click "Add Job Type" to create your first one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="typeModalTitle" style="font-weight:600;color:#fff !important;">Add Job Type</h5>
                <button type="button" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;" onclick="$('#addTypeModal').modal('hide');">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editTypeId">
                <div class="form-group mb-3">
                    <label>Name *</label>
                    <input type="text" id="typeName" class="form-control" placeholder="e.g. ITR12 Individual Tax Return">
                </div>
                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea id="typeDesc" class="form-control" rows="2" placeholder="Brief description of this job type"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Submission To</label>
                    <input type="text" id="typeSubmitTo" class="form-control" placeholder="e.g. SARS, CIPC, Other">
                </div>
                <div class="form-group mb-3">
                    <label>Display Order</label>
                    <input type="number" id="typeOrder" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="$('#addTypeModal').modal('hide');" style="background:#dc3545;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">Cancel</button>
                <button onclick="saveType()" style="background:#E91E8C;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var csrfToken = '{{ csrf_token() }}';

function saveType() {
    var id = document.getElementById('editTypeId').value;
    var url = id ? '/job-cards/admin/types/' + id : '/job-cards/admin/types';
    var method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            name: document.getElementById('typeName').value,
            description: document.getElementById('typeDesc').value,
            submission_to: document.getElementById('typeSubmitTo').value,
            display_order: document.getElementById('typeOrder').value,
        })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message || 'Error'); });
}

function editType(t) {
    document.getElementById('editTypeId').value = t.id;
    document.getElementById('typeName').value = t.name;
    document.getElementById('typeDesc').value = t.description || '';
    document.getElementById('typeSubmitTo').value = t.submission_to || '';
    document.getElementById('typeOrder').value = t.display_order || 0;
    document.getElementById('typeModalTitle').textContent = 'Edit Job Type';
    $('#addTypeModal').modal('show');
}

function deleteType(id) {
    if (!confirm('Delete this job type? This will also delete its steps, fields, and document requirements.')) return;
    fetch('/job-cards/admin/types/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message || 'Error'); });
}

// Reset modal on close
$('#addTypeModal').on('hidden.bs.modal', function() {
    document.getElementById('editTypeId').value = '';
    document.getElementById('typeName').value = '';
    document.getElementById('typeDesc').value = '';
    document.getElementById('typeSubmitTo').value = '';
    document.getElementById('typeOrder').value = '0';
    document.getElementById('typeModalTitle').textContent = 'Add Job Type';
});
</script>
@endsection
