@extends('layouts.default')

@section('content')
<style>
    .jc-admin { font-family: 'Poppins', sans-serif; }
    .jc-admin .card-panel { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #eef2f7; }
    .jc-admin .section-title { font-size:16px;font-weight:600;color:#1a1a2e;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #E91E8C; }
    .jc-admin .form-control { border-radius:8px;font-size:13px;font-family:'Poppins',sans-serif; }
    .jc-admin label { font-size:12px;font-weight:600;color:#555;text-transform:uppercase;margin-bottom:4px; }
    .jc-admin .field-row-item { display:flex;align-items:center;gap:10px;padding:10px;background:#f8f9ff;border-radius:8px;margin-bottom:8px;border:1px solid #eef2f7; }
    .jc-admin .field-row-item .handle { cursor:grab;color:#ccc; }
    .jc-admin .available-field { display:inline-block;padding:6px 14px;border-radius:20px;background:#eef2f7;color:#333;font-size:12px;font-weight:500;margin:4px;cursor:pointer;transition:all 0.2s;border:1px solid transparent; }
    .jc-admin .available-field:hover { background:#E91E8C22;border-color:#E91E8C;color:#E91E8C; }
    .jc-admin .available-field.selected { background:#E91E8C;color:#fff;border-color:#E91E8C; }
</style>

<div class="jc-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Client Fields for: {{ $type->name }}</h3>
            <p style="color:#7f8c8d;margin:4px 0 0;font-size:13px;">Select which client information fields appear on this job type's card</p>
        </div>
        <a href="{{ route('jobcards.admin.types') }}" class="btn btn-sm" style="background:#eef2f7;color:#666;border-radius:8px;font-size:13px;padding:8px 16px;">
            <i class="fa fa-arrow-left mr-1"></i> Back to Types
        </a>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card-panel">
                <h5 class="section-title">Available Fields</h5>
                <p style="font-size:12px;color:#7f8c8d;margin-bottom:12px;">Click to add/remove fields from this job type</p>
                <div id="availableFields">
                    @foreach($availableFields as $fieldName => $fieldLabel)
                    @php $isSelected = $configuredFields->where('field_name', $fieldName)->count() > 0; @endphp
                    <span class="available-field {{ $isSelected ? 'selected' : '' }}"
                          data-field="{{ $fieldName }}" data-label="{{ $fieldLabel }}"
                          onclick="toggleField(this)">
                        {{ $fieldLabel }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card-panel">
                <h5 class="section-title">Selected Fields (drag to reorder)</h5>
                <div id="selectedFields">
                    @foreach($configuredFields as $cf)
                    <div class="field-row-item" data-field="{{ $cf->field_name }}">
                        <i class="fa fa-bars handle"></i>
                        <span style="flex:1;font-weight:500;color:#1a1a2e;">{{ $cf->field_label }}</span>
                        <span style="font-size:11px;color:#aaa;">{{ $cf->field_name }}</span>
                        <label style="margin:0;font-size:11px;display:flex;align-items:center;gap:4px;text-transform:none;">
                            <input type="checkbox" class="req-check" data-field="{{ $cf->field_name }}" {{ $cf->is_required ? 'checked' : '' }}> Req
                        </label>
                        <button onclick="removeField('{{ $cf->field_name }}')" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:14px;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <p id="emptyMsg" style="text-align:center;color:#aaa;padding:20px;{{ $configuredFields->count() ? 'display:none;' : '' }}">
                    No fields selected. Click fields on the left to add them.
                </p>
                <button onclick="saveFields()" class="btn btn-block mt-3" style="background:#E91E8C;color:#fff;border-radius:10px;padding:12px;font-weight:600;font-size:14px;border:none;">
                    <i class="fa fa-save mr-1"></i> Save Field Configuration
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
var csrfToken = '{{ csrf_token() }}';
var typeId = {{ $type->id }};
var availableFields = @json($availableFields);

new Sortable(document.getElementById('selectedFields'), {
    animation: 150, handle: '.handle'
});

function toggleField(el) {
    var fieldName = el.dataset.field;
    var fieldLabel = el.dataset.label;

    if (el.classList.contains('selected')) {
        removeField(fieldName);
        el.classList.remove('selected');
    } else {
        addField(fieldName, fieldLabel);
        el.classList.add('selected');
    }
}

function addField(fieldName, fieldLabel) {
    var container = document.getElementById('selectedFields');
    var div = document.createElement('div');
    div.className = 'field-row-item';
    div.dataset.field = fieldName;
    div.innerHTML = '<i class="fa fa-bars handle"></i>'
        + '<span style="flex:1;font-weight:500;color:#1a1a2e;">' + fieldLabel + '</span>'
        + '<span style="font-size:11px;color:#aaa;">' + fieldName + '</span>'
        + '<label style="margin:0;font-size:11px;display:flex;align-items:center;gap:4px;text-transform:none;"><input type="checkbox" class="req-check" data-field="' + fieldName + '"> Req</label>'
        + '<button onclick="removeField(\'' + fieldName + '\')" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:14px;"><i class="fa fa-times"></i></button>';
    container.appendChild(div);
    document.getElementById('emptyMsg').style.display = 'none';
}

function removeField(fieldName) {
    var el = document.querySelector('#selectedFields .field-row-item[data-field="' + fieldName + '"]');
    if (el) el.remove();
    var avail = document.querySelector('#availableFields .available-field[data-field="' + fieldName + '"]');
    if (avail) avail.classList.remove('selected');
    if (!document.querySelectorAll('#selectedFields .field-row-item').length) {
        document.getElementById('emptyMsg').style.display = 'block';
    }
}

function saveFields() {
    var fields = [];
    document.querySelectorAll('#selectedFields .field-row-item').forEach(function(el, idx) {
        var fieldName = el.dataset.field;
        var req = el.querySelector('.req-check');
        fields.push({
            field_name: fieldName,
            field_label: availableFields[fieldName] || fieldName,
            display_order: idx + 1,
            is_required: req && req.checked ? 1 : 0,
        });
    });

    fetch('/job-cards/admin/types/' + typeId + '/fields', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ fields: fields })
    })
    .then(r => r.json())
    .then(d => { if (d.success) { alert('Fields saved successfully!'); location.reload(); } else alert(d.message || 'Error'); });
}
</script>
@endsection
