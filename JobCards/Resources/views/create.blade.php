@extends('layouts.default')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Flatpickr theme override to match CIMS teal */
    .flatpickr-day.selected, .flatpickr-day.selected:hover {
        background: #00BCD4 !important; border-color: #00BCD4 !important;
    }
    .flatpickr-day:hover { background: #e0f7fa !important; }
    .flatpickr-months .flatpickr-month { background: #00BCD4 !important; }
    .flatpickr-current-month .flatpickr-monthDropdown-months { background: #00BCD4 !important; }
    .flatpickr-weekdays { background: #00BCD4 !important; }
    span.flatpickr-weekday { color: #fff !important; }
    .jc-create { font-family: 'Poppins', sans-serif; padding: 20px 30px; }

    /* Page header card */
    .jc-create .page-header {
        background: #fff; border-radius: 10px; padding: 20px 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04); border: 1px solid #e8ecf0;
        border-left: 4px solid #00BCD4; margin-bottom: 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .jc-create .page-header h3 {
        font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0;
    }
    .jc-create .page-header p {
        color: #7f8c8d; margin: 2px 0 0; font-size: 13px;
    }
    .jc-create .btn-back {
        background: #f0f2f5; color: #555; border-radius: 8px; padding: 8px 18px;
        font-size: 13px; font-weight: 500; border: 1px solid #dde2e8;
        text-decoration: none; transition: background 0.2s;
    }
    .jc-create .btn-back:hover { background: #e4e7eb; color: #333; text-decoration: none; }

    /* Form cards */
    .jc-create .form-card {
        background: #fff; border-radius: 10px; padding: 24px 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04); border: 1px solid #e8ecf0; margin-bottom: 20px;
    }
    .jc-create .section-title {
        font-size: 15px; font-weight: 600; color: #1a1a2e; margin-bottom: 16px;
        padding-bottom: 8px; border-bottom: 2px solid #00BCD4;
    }

    /* Labels */
    .jc-create label {
        font-size: 12px; font-weight: 600; color: #555; text-transform: uppercase;
        letter-spacing: 0.3px; margin-bottom: 4px;
    }

    /* Form controls */
    .jc-create .form-control {
        border-radius: 8px; border: 1.5px solid #dde2e8; font-size: 14px; padding: 10px 14px;
        font-family: 'Poppins', sans-serif; color: #333; height: auto;
    }
    .jc-create .form-control:focus {
        border-color: #00BCD4; box-shadow: 0 0 0 3px rgba(0,188,212,0.12); outline: none;
    }

    /* (client search styles removed - now using select dropdown) */

    /* Client info panel */
    .jc-create .client-info-panel {
        background: #f8fafb; border-radius: 8px; padding: 16px; border: 1px solid #e8ecf4;
    }
    .jc-create .field-row {
        display: flex; justify-content: space-between; padding: 6px 0;
        border-bottom: 1px solid #eef2f7; font-size: 13px;
    }
    .jc-create .field-row:last-child { border-bottom: none; }
    .jc-create .field-label { color: #7f8c8d; font-weight: 500; }
    .jc-create .field-value { color: #1a1a2e; font-weight: 600; }

    /* Steps preview */
    .jc-create .steps-preview {
        background: #fafafa; border-radius: 8px; padding: 12px 16px; border: 1px solid #eef2f7;
    }
    .jc-create .step-item {
        padding: 8px 0; border-bottom: 1px solid #eef2f7; font-size: 13px;
        display: flex; align-items: center; gap: 10px;
    }
    .jc-create .step-item:last-child { border-bottom: none; }
    .jc-create .step-num {
        width: 24px; height: 24px; border-radius: 50%; background: #00BCD4; color: #fff;
        font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Submit button */
    .jc-create .btn-submit {
        background: #00BCD4; color: #fff; border-radius: 8px; padding: 12px 24px;
        font-weight: 600; font-size: 14px; border: none; width: 100%;
        cursor: pointer; transition: background 0.2s;
    }
    .jc-create .btn-submit:hover { background: #00ACC1; }
    .jc-create .btn-submit:disabled { background: #b2dfdb; cursor: not-allowed; }
</style>

<div class="jc-create">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h3>Create Job Card</h3>
            <p>Select a client and job type to begin</p>
        </div>
        <a href="{{ route('jobcards.index') }}" class="btn-back">
            <i class="fa fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form id="createForm" method="POST" action="{{ route('jobcards.store') }}">
        @csrf
        <div class="row">
            <!-- Left: Form Fields -->
            <div class="col-lg-5">
                <div class="form-card">
                    <h5 class="section-title">Job Details</h5>

                    <!-- Client Select -->
                    <div class="form-group mb-3">
                        <label>Client *</label>
                        <select name="client_id" id="clientSelect" class="form-control" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->client_id }}">{{ $c->company_name }} ({{ $c->client_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="form-group mb-3">
                        <label>Category</label>
                        <select id="jobCategory" class="form-control">
                            <option value="">All Categories</option>
                        </select>
                    </div>

                    <!-- Job Type -->
                    <div class="form-group mb-3">
                        <label>Job Type *</label>
                        <select name="job_type_id" id="jobType" class="form-control" required>
                            <option value="">Select Job Type</option>
                            @foreach($jobTypes as $jt)
                                <option value="{{ $jt->id }}" data-category="{{ $jt->submission_to }}">{{ $jt->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach($statuses as $key => $s)
                                <option value="{{ $key }}" {{ $key === 'draft' ? 'selected' : '' }}>{{ $s['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="form-group mb-3">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            @foreach($priorities as $key => $p)
                                <option value="{{ $key }}" {{ $key === 'normal' ? 'selected' : '' }}>{{ $p['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="form-group mb-3">
                        <label>Due Date</label>
                        <input type="text" name="due_date" id="dueDate" class="form-control" placeholder="Select date..." readonly>
                    </div>

                    <!-- Assigned To -->
                    <div class="form-group mb-3">
                        <label>Assigned To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">-- Select User --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Followed By -->
                    <div class="form-group mb-3">
                        <label>Followed By</label>
                        <select name="followed_by" class="form-control">
                            <option value="">-- Select Follower --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="btn-submit" disabled>
                        <i class="fa fa-plus mr-1"></i> Create Job Card
                    </button>
                </div>
            </div>

            <!-- Right: Preview Panel -->
            <div class="col-lg-7">
                <!-- Client Info -->
                <div class="form-card" id="clientInfoPanel" style="display:none;">
                    <h5 class="section-title">Client Information</h5>
                    <div id="clientInfoFields" class="client-info-panel">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Steps Preview -->
                <div class="form-card" id="stepsPanel" style="display:none;">
                    <h5 class="section-title">Checklist Steps</h5>
                    <div id="stepsPreview" class="steps-preview">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Required Documents -->
                <div class="form-card" id="docsPanel" style="display:none;">
                    <h5 class="section-title">Required Documents</h5>
                    <div id="docsPreview">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
var selectedClientId = null;
var selectedJobTypeId = null;

// Category filter logic
(function() {
    var jobTypeSelect = document.getElementById('jobType');
    var categorySelect = document.getElementById('jobCategory');
    var allOptions = [];

    for (var i = 1; i < jobTypeSelect.options.length; i++) {
        var opt = jobTypeSelect.options[i];
        allOptions.push({
            value: opt.value,
            text: opt.text,
            category: opt.getAttribute('data-category') || ''
        });
    }

    var categories = {};
    allOptions.forEach(function(o) {
        var cat = o.category.trim();
        if (cat && !categories[cat]) categories[cat] = true;
    });
    Object.keys(categories).sort().forEach(function(cat) {
        var option = document.createElement('option');
        option.value = cat;
        option.textContent = cat;
        categorySelect.appendChild(option);
    });

    categorySelect.addEventListener('change', function() {
        var selected = this.value;
        jobTypeSelect.innerHTML = '<option value="">Select Job Type</option>';
        var filtered = selected
            ? allOptions.filter(function(o) { return o.category === selected; })
            : allOptions;
        filtered.forEach(function(o) {
            var option = document.createElement('option');
            option.value = o.value;
            option.textContent = o.text;
            option.setAttribute('data-category', o.category);
            jobTypeSelect.appendChild(option);
        });
        selectedJobTypeId = null;
        document.getElementById('stepsPanel').style.display = 'none';
        document.getElementById('docsPanel').style.display = 'none';
        checkReady();
    });
})();

// Client select change
document.getElementById('clientSelect').addEventListener('change', function() {
    selectedClientId = this.value || null;
    if (!selectedClientId) {
        document.getElementById('clientInfoPanel').style.display = 'none';
    }
    checkReady();
    loadClientInfo();
});

// Job type change
document.getElementById('jobType').addEventListener('change', function() {
    selectedJobTypeId = this.value;
    if (this.value) {
        fetch('{{ url("job-cards/api/job-type-config") }}/' + this.value)
            .then(r => r.json())
            .then(config => {
                var stepsHtml = '';
                config.steps.forEach(function(s, i) {
                    var typeIcon = s.step_type === 'document_required' ? 'fa-file-o' : s.step_type === 'info_review' ? 'fa-eye' : 'fa-check';
                    stepsHtml += '<div class="step-item"><span class="step-num">' + (i+1) + '</span>'
                        + '<i class="fa ' + typeIcon + '" style="color:#aaa;"></i> '
                        + escHtml(s.step_name)
                        + (s.is_required ? ' <span style="color:#dc3545;font-size:10px;">*Required</span>' : '')
                        + '</div>';
                });
                document.getElementById('stepsPreview').innerHTML = stepsHtml || '<p style="color:#aaa;text-align:center;padding:20px;">No steps configured</p>';
                document.getElementById('stepsPanel').style.display = config.steps.length ? 'block' : 'none';

                var docsHtml = '';
                config.documents.forEach(function(d) {
                    docsHtml += '<div style="padding:8px 0;border-bottom:1px solid #eef2f7;font-size:13px;">'
                        + '<i class="fa fa-file-o" style="color:#00BCD4;margin-right:8px;"></i>'
                        + escHtml(d.document_label)
                        + (d.is_required ? ' <span style="color:#dc3545;font-size:10px;">*Required</span>' : ' <span style="color:#aaa;font-size:10px;">Optional</span>')
                        + '</div>';
                });
                document.getElementById('docsPreview').innerHTML = docsHtml || '<p style="color:#aaa;text-align:center;padding:20px;">No document requirements configured</p>';
                document.getElementById('docsPanel').style.display = config.documents.length ? 'block' : 'none';
            });
    } else {
        document.getElementById('stepsPanel').style.display = 'none';
        document.getElementById('docsPanel').style.display = 'none';
    }
    checkReady();
    loadClientInfo();
});

function loadClientInfo() {
    if (!selectedClientId || !selectedJobTypeId) return;
    fetch('{{ url("job-cards/api/client-info") }}/' + selectedClientId + '?job_type_id=' + selectedJobTypeId)
        .then(r => r.json())
        .then(data => {
            if (data.fields && data.fields.length) {
                var html = '';
                data.fields.forEach(function(f) {
                    html += '<div class="field-row">'
                        + '<span class="field-label">' + escHtml(f.field_label) + '</span>'
                        + '<span class="field-value">' + (f.value ? escHtml(f.value) : '<em style="color:#ccc;">Not set</em>') + '</span>'
                        + '</div>';
                });
                document.getElementById('clientInfoFields').innerHTML = html;
                document.getElementById('clientInfoPanel').style.display = 'block';
            } else {
                document.getElementById('clientInfoPanel').style.display = 'none';
            }
        });
}

function checkReady() {
    document.getElementById('submitBtn').disabled = !(selectedClientId && document.getElementById('jobType').value);
}

function escHtml(t) {
    var d = document.createElement('div'); d.textContent = t || ''; return d.innerHTML;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#dueDate', {
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'l d F Y',
    allowInput: false,
    minDate: 'today'
});
</script>
@endsection
