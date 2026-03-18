@extends('layouts.default')

@section('content')
<style>
    .jc-admin { font-family: 'Poppins', sans-serif; }
    .jc-admin .card-panel { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #eef2f7; }
    .jc-admin .section-title { font-size:16px;font-weight:600;color:#1a1a2e;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #E91E8C; }
    .jc-admin .step-card { background:#f8f9ff;border:1px solid #eef2f7;border-radius:10px;padding:14px 18px;margin-bottom:10px;display:flex;align-items:center;gap:14px;cursor:grab;transition:box-shadow 0.2s; }
    .jc-admin .step-card:hover { box-shadow:0 2px 8px rgba(0,0,0,0.08); }
    .jc-admin .step-num { width:30px;height:30px;border-radius:50%;background:#E91E8C;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .jc-admin .step-info { flex:1; }
    .jc-admin .step-info .name { font-weight:600;color:#1a1a2e;font-size:14px; }
    .jc-admin .step-info .meta { font-size:11px;color:#7f8c8d;margin-top:2px; }
    .jc-admin .btn-sm-action { padding:4px 10px;border-radius:6px;font-size:12px;border:none;cursor:pointer;margin-left:4px; }
    .jc-admin .form-control { border-radius:8px;font-size:13px;font-family:'Poppins',sans-serif; }
    .jc-admin label { font-size:12px;font-weight:600;color:#555;text-transform:uppercase;margin-bottom:4px; }
    .jc-admin .modal-content { border-radius:12px; }
    .jc-admin .modal-header { background:#1a1a2e;color:#fff;border-radius:12px 12px 0 0; }
    .jc-admin .type-badge { display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
</style>

<div class="jc-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Steps for: {{ $type->name }}</h3>
            <p style="color:#7f8c8d;margin:4px 0 0;font-size:13px;">Configure the checklist steps for this job type</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jobcards.admin.types') }}" class="btn btn-sm" style="background:#eef2f7;color:#666;border-radius:8px;font-size:13px;padding:8px 16px;">
                <i class="fa fa-arrow-left mr-1"></i> Back to Types
            </a>
            <button onclick="openAddStep()" class="btn btn-sm" style="background:#E91E8C;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;border:none;">
                <i class="fa fa-plus mr-1"></i> Add Step
            </button>
        </div>
    </div>

    <div class="card-panel">
        <h5 class="section-title">Steps (drag to reorder)</h5>
        <div id="stepsList">
            @forelse($steps as $i => $step)
            <div class="step-card" data-id="{{ $step->id }}">
                <i class="fa fa-bars" style="color:#ccc;cursor:grab;"></i>
                <span class="step-num">{{ $i + 1 }}</span>
                <div class="step-info">
                    <div class="name">{{ $step->step_name }}</div>
                    <div class="meta">
                        <span class="type-badge" style="background:#E91E8C22;color:#E91E8C;">{{ $step->step_type }}</span>
                        @if($step->is_required)
                            <span class="type-badge" style="background:#dc354522;color:#dc3545;">Required</span>
                        @endif
                        @if(!$step->is_active)
                            <span class="type-badge" style="background:#6c757d22;color:#6c757d;">Inactive</span>
                        @endif
                        @if($step->step_description)
                            — {{ \Illuminate\Support\Str::limit($step->step_description, 60) }}
                        @endif
                    </div>
                </div>
                <button class="btn-sm-action" style="background:#ffc10722;color:#ffc107;" onclick='editStep(@json($step))'>
                    <i class="fa fa-pencil"></i>
                </button>
                <button class="btn-sm-action" style="background:#dc354522;color:#dc3545;" onclick="deleteStep({{ $step->id }})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            @empty
            <p style="text-align:center;color:#aaa;padding:30px;">No steps configured. Add your first step.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Add/Edit Step Modal -->
<div class="modal fade" id="stepModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stepModalTitle" style="font-weight:600;color:#fff !important;">Add Step</h5>
                <button type="button" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;" onclick="$('#stepModal').modal('hide');">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editStepId">
                <div class="form-group mb-3">
                    <label>Step Name *</label>
                    <input type="text" id="stepName" class="form-control" placeholder="e.g. Verify client details">
                </div>
                <div class="form-group mb-3">
                    <label>Description</label>
                    <textarea id="stepDesc" class="form-control" rows="2" placeholder="Optional description"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Step Type</label>
                    <select id="stepType" class="form-control">
                        <option value="checkbox">Checkbox (simple tick-off)</option>
                        <option value="document_required">Document Required (needs file upload)</option>
                        <option value="info_review">Info Review (review information)</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Required</label>
                    <select id="stepRequired" class="form-control">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="$('#stepModal').modal('hide');" style="background:#dc3545;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">Cancel</button>
                <button onclick="saveStep()" style="background:#E91E8C;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
var csrfToken = '{{ csrf_token() }}';
var typeId = {{ $type->id }};

// Drag & drop reorder
var el = document.getElementById('stepsList');
if (el.children.length > 0) {
    new Sortable(el, {
        animation: 150, handle: '.fa-bars',
        onEnd: function() {
            var order = [];
            el.querySelectorAll('.step-card').forEach(function(card) { order.push(card.dataset.id); });
            fetch('/job-cards/admin/types/' + typeId + '/steps/reorder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ order: order })
            });
            // Renumber
            el.querySelectorAll('.step-num').forEach(function(n, i) { n.textContent = i + 1; });
        }
    });
}

function openAddStep() {
    document.getElementById('editStepId').value = '';
    document.getElementById('stepName').value = '';
    document.getElementById('stepDesc').value = '';
    document.getElementById('stepType').value = 'checkbox';
    document.getElementById('stepRequired').value = '1';
    document.getElementById('stepModalTitle').textContent = 'Add Step';
    $('#stepModal').modal('show');
}

function editStep(s) {
    document.getElementById('editStepId').value = s.id;
    document.getElementById('stepName').value = s.step_name;
    document.getElementById('stepDesc').value = s.step_description || '';
    document.getElementById('stepType').value = s.step_type;
    document.getElementById('stepRequired').value = s.is_required ? '1' : '0';
    document.getElementById('stepModalTitle').textContent = 'Edit Step';
    $('#stepModal').modal('show');
}

function saveStep() {
    var id = document.getElementById('editStepId').value;
    var url = id ? '/job-cards/admin/steps/' + id : '/job-cards/admin/types/' + typeId + '/steps';
    var method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            step_name: document.getElementById('stepName').value,
            step_description: document.getElementById('stepDesc').value,
            step_type: document.getElementById('stepType').value,
            is_required: parseInt(document.getElementById('stepRequired').value),
        })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message || 'Error'); });
}

function deleteStep(id) {
    if (!confirm('Delete this step?')) return;
    fetch('/job-cards/admin/steps/' + id, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
</script>
@endsection
