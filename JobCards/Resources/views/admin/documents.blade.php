@extends('layouts.default')

@section('content')
<style>
    .jc-admin { font-family: 'Poppins', sans-serif; }
    .jc-admin .card-panel { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #eef2f7; }
    .jc-admin .section-title { font-size:16px;font-weight:600;color:#1a1a2e;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #E91E8C; }
    .jc-admin .form-control { border-radius:8px;font-size:13px;font-family:'Poppins',sans-serif; }
    .jc-admin label { font-size:12px;font-weight:600;color:#555;text-transform:uppercase;margin-bottom:4px; }
    .jc-admin .doc-item { display:flex;align-items:center;gap:12px;padding:12px;background:#f8f9ff;border-radius:10px;margin-bottom:8px;border:1px solid #eef2f7; }
    .jc-admin .btn-sm-action { padding:4px 10px;border-radius:6px;font-size:12px;border:none;cursor:pointer; }
    .jc-admin .modal-content { border-radius:12px; }
    .jc-admin .modal-header { background:#1a1a2e;color:#fff;border-radius:12px 12px 0 0; }
    .jc-admin .req-tag { display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600; }
</style>

<div class="jc-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Document Requirements for: {{ $type->name }}</h3>
            <p style="color:#7f8c8d;margin:4px 0 0;font-size:13px;">Configure which documents are needed for this job type</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jobcards.admin.types') }}" class="btn btn-sm" style="background:#eef2f7;color:#666;border-radius:8px;font-size:13px;padding:8px 16px;">
                <i class="fa fa-arrow-left mr-1"></i> Back to Types
            </a>
            <button onclick="openAddDoc()" class="btn btn-sm" style="background:#E91E8C;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;border:none;">
                <i class="fa fa-plus mr-1"></i> Add Document
            </button>
        </div>
    </div>

    <div class="card-panel">
        <h5 class="section-title">Required Documents</h5>
        <div id="docsList">
            @forelse($documents as $doc)
            <div class="doc-item">
                <i class="fa fa-file-o" style="color:#E91E8C;font-size:18px;"></i>
                <span style="flex:1;font-weight:500;color:#1a1a2e;">{{ $doc->document_label }}</span>
                <span class="req-tag" style="background:{{ $doc->is_required ? '#dc354522' : '#28a74522' }};color:{{ $doc->is_required ? '#dc3545' : '#28a745' }};">
                    {{ $doc->is_required ? 'Required' : 'Optional' }}
                </span>
                <button class="btn-sm-action" style="background:#ffc10722;color:#ffc107;" onclick='editDoc(@json($doc))'>
                    <i class="fa fa-pencil"></i>
                </button>
                <button class="btn-sm-action" style="background:#dc354522;color:#dc3545;" onclick="deleteDoc({{ $doc->id }})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            @empty
            <p style="text-align:center;color:#aaa;padding:30px;">No document requirements. Add documents that should be collected for this job type.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Add/Edit Doc Modal -->
<div class="modal fade" id="docModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="docModalTitle" style="font-weight:600;color:#fff !important;">Add Document Requirement</h5>
                <button type="button" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;" onclick="$('#docModal').modal('hide');">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editDocId">
                <div class="form-group mb-3">
                    <label>Document Label *</label>
                    <input type="text" id="docLabel" class="form-control" placeholder="e.g. ID Copy, IRP5 Certificate, Bank Confirmation">
                </div>
                <div class="form-group mb-3">
                    <label>Required</label>
                    <select id="docRequired" class="form-control">
                        <option value="1">Yes — must be uploaded</option>
                        <option value="0">No — optional</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="$('#docModal').modal('hide');" style="background:#dc3545;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">Cancel</button>
                <button onclick="saveDoc()" style="background:#E91E8C;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var csrfToken = '{{ csrf_token() }}';
var typeId = {{ $type->id }};

function openAddDoc() {
    document.getElementById('editDocId').value = '';
    document.getElementById('docLabel').value = '';
    document.getElementById('docRequired').value = '1';
    document.getElementById('docModalTitle').textContent = 'Add Document Requirement';
    $('#docModal').modal('show');
}

function editDoc(d) {
    document.getElementById('editDocId').value = d.id;
    document.getElementById('docLabel').value = d.document_label;
    document.getElementById('docRequired').value = d.is_required ? '1' : '0';
    document.getElementById('docModalTitle').textContent = 'Edit Document Requirement';
    $('#docModal').modal('show');
}

function saveDoc() {
    var id = document.getElementById('editDocId').value;
    var url = id ? '/job-cards/admin/documents/' + id : '/job-cards/admin/types/' + typeId + '/documents';
    var method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            document_label: document.getElementById('docLabel').value,
            is_required: parseInt(document.getElementById('docRequired').value),
        })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message || 'Error'); });
}

function deleteDoc(id) {
    if (!confirm('Delete this document requirement?')) return;
    fetch('/job-cards/admin/documents/' + id, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
</script>
@endsection
