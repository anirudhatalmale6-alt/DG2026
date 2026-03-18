@extends('layouts.default')

@section('content')
<style>
    .jc-show { font-family: 'Poppins', sans-serif; padding: 20px 30px; }
    .jc-show .card-panel {
        background: #fff; border-radius: 10px; padding: 24px 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04); border: 1px solid #e8ecf0; margin-bottom: 20px;
    }
    .jc-show .section-title {
        font-size: 15px; font-weight: 600; color: #1a1a2e; margin-bottom: 14px;
        padding-bottom: 8px; border-bottom: 2px solid #00BCD4; display: flex;
        justify-content: space-between; align-items: center;
    }
    /* Page header card — matches create page */
    .jc-show .header-bar {
        background: #fff; border-radius: 10px; padding: 20px 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04); border: 1px solid #e8ecf0;
        border-left: 4px solid #00BCD4; margin-bottom: 20px;
    }
    .jc-show .header-bar .job-code {
        font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0;
    }
    .jc-show .header-bar .job-sub {
        color: #7f8c8d; margin: 2px 0 0; font-size: 13px;
    }
    .jc-show .status-badge {
        display: inline-block; padding: 6px 16px; border-radius: 20px;
        font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .jc-show .progress-bar-full {
        height: 10px; background: #eef2f7; border-radius: 5px; overflow: hidden; flex: 1;
    }
    .jc-show .progress-bar-full .fill { height: 100%; border-radius: 5px; transition: width 0.5s ease; }
    .jc-show .quick-info {
        display: flex; gap: 20px; margin-top: 12px; flex-wrap: wrap;
    }
    .jc-show .quick-info span {
        font-size: 12px; color: #7f8c8d; display: flex; align-items: center; gap: 4px;
    }
    .jc-show .quick-info span i { color: #00BCD4; }
    .jc-show .info-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;
    }
    .jc-show .info-item { padding: 10px; background: #f8fafb; border-radius: 8px; border: 1px solid #eef2f7; }
    .jc-show .info-item .label { font-size: 11px; color: #7f8c8d; text-transform: uppercase; font-weight: 600; letter-spacing: 0.3px; }
    .jc-show .info-item .value { font-size: 14px; color: #1a1a2e; font-weight: 600; margin-top: 2px; word-break: break-all; }
    .jc-show .step-row {
        display: flex; align-items: center; gap: 12px; padding: 12px 0;
        border-bottom: 1px solid #f0f0f0; transition: background 0.15s;
    }
    .jc-show .step-row:last-child { border-bottom: none; }
    .jc-show .step-row:hover { background: #f8fafb; margin: 0 -12px; padding-left: 12px; padding-right: 12px; border-radius: 6px; }
    .jc-show .step-check {
        width: 28px; height: 28px; border-radius: 50%; border: 2px solid #ddd;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.2s; flex-shrink: 0;
    }
    .jc-show .step-check.completed { background: #28a745; border-color: #28a745; color: #fff; }
    .jc-show .step-check.in_progress { background: #fff; border-color: #00BCD4; color: #00BCD4; }
    .jc-show .step-check.skipped { background: #ffc107; border-color: #ffc107; color: #fff; }
    .jc-show .step-check.pending { background: #fff; border-color: #ddd; }
    .jc-show .step-name { flex: 1; font-size: 13px; color: #333; }
    .jc-show .step-name.done { text-decoration: line-through; color: #aaa; }
    .jc-show .step-meta { font-size: 11px; color: #aaa; flex-shrink: 0; text-align: right; min-width: 120px; }
    .jc-show .step-type-icon { color: #00BCD4; font-size: 12px; margin-right: 4px; }
    .jc-show .doc-row {
        display: flex; align-items: center; gap: 12px; padding: 10px 0;
        border-bottom: 1px solid #f0f0f0; font-size: 13px;
    }
    .jc-show .doc-row:last-child { border-bottom: none; }
    .jc-show .doc-status-icon { font-size: 16px; }
    .jc-show .btn-pack {
        border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 13px;
        border: none; cursor: pointer; transition: all 0.2s;
    }
    .jc-show .btn-back {
        background: #f0f2f5; color: #555; border-radius: 8px; padding: 8px 18px;
        font-size: 13px; font-weight: 500; border: 1px solid #dde2e8;
        text-decoration: none; transition: background 0.2s;
    }
    .jc-show .btn-back:hover { background: #e4e7eb; color: #333; text-decoration: none; }
    .jc-show .form-control {
        border-radius: 8px; border: 1.5px solid #dde2e8; font-size: 14px; padding: 10px 14px;
        font-family: 'Poppins', sans-serif; color: #333; height: auto;
    }
    .jc-show .form-control:focus {
        border-color: #00BCD4; box-shadow: 0 0 0 3px rgba(0,188,212,0.12); outline: none;
    }
    .jc-show label {
        font-size: 11px; font-weight: 600; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .jc-show .modal-content { border-radius: 10px; }
    .jc-show .modal-header { background: #00BCD4; color: #fff; border-radius: 10px 10px 0 0; }

    /* ─── Beneficial Ownership Panel ─── */
    .bo-panel { margin-bottom: 20px; }
    .bo-panel .bo-header {
        background: linear-gradient(135deg, #0d3d56, #17A2B8);
        color: #fff; padding: 16px 24px; border-radius: 10px 10px 0 0;
        display: flex; justify-content: space-between; align-items: center; cursor: pointer;
        user-select: none;
    }
    .bo-panel .bo-header h5 { margin: 0; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }
    .bo-panel .bo-header .bo-badge {
        background: rgba(255,255,255,0.2); padding: 4px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .bo-panel .bo-header .bo-toggle {
        font-size: 14px; transition: transform 0.3s; display: inline-block;
    }
    .bo-panel .bo-header .bo-toggle.open { transform: rotate(90deg); }
    .bo-panel .bo-body {
        background: #fff; border: 1px solid #e8ecf0; border-top: none;
        border-radius: 0 0 10px 10px; padding: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .bo-panel .bo-summary {
        display: flex; gap: 16px; padding: 16px 24px; flex-wrap: wrap;
        border-bottom: 1px solid #eef2f7; background: #f8fafb;
    }
    .bo-panel .bo-summary .bo-stat {
        text-align: center; padding: 10px 16px; background: #fff;
        border-radius: 8px; border: 1px solid #eef2f7; min-width: 120px;
    }
    .bo-panel .bo-summary .bo-stat .bo-stat-value {
        font-size: 20px; font-weight: 700; color: #0d3d56;
    }
    .bo-panel .bo-summary .bo-stat .bo-stat-label {
        font-size: 10px; text-transform: uppercase; color: #7f8c8d;
        letter-spacing: 0.5px; font-weight: 600; margin-top: 2px;
    }
    .bo-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .bo-table thead th {
        background: #0d3d56; color: #fff; font-weight: 700; padding: 10px 12px;
        border: 1px solid #0a2e40; text-align: left; font-size: 11px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .bo-table thead th.text-right { text-align: right; }
    .bo-table thead th.text-center { text-align: center; }
    .bo-table tbody td {
        padding: 10px 12px; border: 1px solid #dde4ec; color: #333; vertical-align: middle;
    }
    .bo-table tbody tr:nth-child(even) { background: #fafbfc; }
    .bo-table tbody tr:hover { background: #e8f4f8; }
    .bo-table tbody td.text-right { text-align: right; font-family: 'Poppins', monospace; font-weight: 500; }
    .bo-table tbody td.text-center { text-align: center; }
    .bo-table tbody tr.bo-total-row { background: #0d3d56; }
    .bo-table tbody tr.bo-total-row td { color: #fff; font-weight: 700; font-size: 12px; border-color: #0a2e40; }
    .bo-doc-badge {
        display: inline-block; padding: 3px 10px; border-radius: 12px;
        font-size: 10px; font-weight: 600; letter-spacing: 0.3px;
    }
    .bo-doc-badge.has-doc { background: #d4edda; color: #155724; }
    .bo-doc-badge.no-doc { background: #f8d7da; color: #721c24; }
    .bo-btn {
        border: none; border-radius: 6px; padding: 5px 12px; font-size: 11px;
        font-weight: 600; cursor: pointer; transition: all 0.2s; margin: 1px;
    }
    .bo-btn-fetch { background: #17A2B8; color: #fff; }
    .bo-btn-fetch:hover { background: #138496; }
    .bo-btn-upload { background: #6f42c1; color: #fff; }
    .bo-btn-upload:hover { background: #5a32a3; }
    .bo-btn-view { background: #28a745; color: #fff; }
    .bo-btn-view:hover { background: #218838; }
    .bo-loading { text-align: center; padding: 40px; color: #7f8c8d; }
    .bo-loading i { font-size: 24px; color: #17A2B8; }
    .bo-btn-cra01 { background: #E91E8C; color: #fff; }
    .bo-btn-cra01:hover { background: #c71678; }
    .bo-btn-poa-fresh { background: #d4edda; color: #155724; font-size: 9px; padding: 2px 8px; border-radius: 8px; display: inline-block; }
    .bo-btn-poa-stale { background: #fff3cd; color: #856404; font-size: 9px; padding: 2px 8px; border-radius: 8px; display: inline-block; }
    .bo-section-divider {
        background: linear-gradient(90deg, #17A2B8, #0d3d56); color: #fff;
        padding: 10px 20px; font-size: 12px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; margin: 0;
    }
    .bo-review-grid { padding: 16px 24px; }
    .bo-review-item { display: inline-block; width: 48%; vertical-align: top; margin-bottom: 10px; padding: 8px 12px; background: #f8fafb; border-radius: 6px; border: 1px solid #eef2f7; }
    .bo-review-item .bo-rv-label { font-size: 9px; text-transform: uppercase; color: #7f8c8d; font-weight: 600; letter-spacing: 0.3px; }
    .bo-review-item .bo-rv-value { font-size: 12px; color: #333; font-weight: 600; margin-top: 2px; }
    .bo-gen-buttons { padding: 16px 24px; display: flex; gap: 8px; flex-wrap: wrap; border-top: 1px solid #eef2f7; }
    .bo-gen-btn {
        border: none; border-radius: 8px; padding: 10px 18px; font-size: 12px;
        font-weight: 600; cursor: pointer; transition: all 0.2s; color: #fff;
    }
    .bo-gen-btn:hover { opacity: 0.9; }
    .bo-gen-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .bo-gen-wrap { display: inline-flex; flex-direction: column; align-items: stretch; gap: 4px; }
    .bo-view-btn {
        display: inline-block; border: none; border-radius: 6px; padding: 5px 12px; font-size: 10px;
        font-weight: 600; cursor: pointer; color: #fff; text-decoration: none; text-align: center;
    }
    .bo-view-btn:hover { opacity: 0.85; color: #fff; text-decoration: none; }
    .bo-gen-msg { padding: 8px 24px; font-size: 12px; }
</style>

@php
    $sc = $statuses[$jobCard->status] ?? ['color' => '#6c757d', 'label' => $jobCard->status, 'icon' => 'fa-file'];
    $pc = $priorities[$jobCard->priority] ?? ['color' => '#007bff', 'label' => $jobCard->priority];
@endphp

<div class="jc-show">
    <!-- Page Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="job-code">{{ $jobCard->job_code }}</h3>
                <p class="job-sub">
                    {{ $jobType->name ?? '' }} — {{ $client->company_name ?? '' }} ({{ $client->client_code ?? '' }})
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge" style="background:{{ $sc['color'] }};color:#fff;">
                    <i class="fa {{ $sc['icon'] }} mr-1"></i>{{ $sc['label'] }}
                </span>
                <a href="{{ route('jobcards.index') }}" class="btn-back">
                    <i class="fa fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="d-flex align-items-center gap-3 mt-3">
            <span style="font-size:13px;font-weight:600;color:#555;">Progress:</span>
            <div class="progress-bar-full">
                <div class="fill" id="progressFill" style="width:{{ $jobCard->completion_percentage }}%;background:{{ $jobCard->completion_percentage >= 100 ? '#28a745' : '#E91E8C' }};"></div>
            </div>
            <span id="progressText" style="font-size:14px;font-weight:700;color:#1a1a2e;">{{ number_format($jobCard->completion_percentage, 0) }}%</span>
        </div>
        <!-- Quick Info -->
        <div class="quick-info">
            <span><i class="fa fa-user"></i> {{ $assignedUser ? $assignedUser->first_name . ' ' . $assignedUser->last_name : 'Unassigned' }}</span>
            <span><i class="fa fa-flag"></i> {{ $pc['label'] }}</span>
            <span><i class="fa fa-calendar"></i> Due: {{ $jobCard->due_date ? \Carbon\Carbon::parse($jobCard->due_date)->format('d/m/Y') : 'Not set' }}</span>
            <span><i class="fa fa-clock-o"></i> Created: {{ \Carbon\Carbon::parse($jobCard->created_at)->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Checklist + Documents -->
        <div class="col-lg-8">
            <!-- Checklist -->
            <div class="card-panel">
                <h5 class="section-title">
                    Checklist
                    <span style="font-size:12px;color:#7f8c8d;font-weight:400;">
                        {{ $progress->where('status', 'completed')->count() }}/{{ $progress->count() }} complete
                    </span>
                </h5>
                <div id="checklist">
                    @foreach($progress as $step)
                    <div class="step-row" id="step-row-{{ $step->step_id }}">
                        <div class="step-check {{ $step->status }}"
                             onclick="toggleStep({{ $jobCard->id }}, {{ $step->step_id }}, '{{ $step->status }}')"
                             id="step-check-{{ $step->step_id }}"
                             title="Click to toggle">
                            @if($step->status === 'completed')
                                <i class="fa fa-check"></i>
                            @elseif($step->status === 'in_progress')
                                <i class="fa fa-ellipsis-h"></i>
                            @elseif($step->status === 'skipped')
                                <i class="fa fa-forward"></i>
                            @endif
                        </div>
                        <div class="step-name {{ $step->status === 'completed' ? 'done' : '' }}" id="step-name-{{ $step->step_id }}">
                            @if($step->step_type === 'document_required')
                                <i class="step-type-icon fa fa-file-o" title="Document required"></i>
                            @elseif($step->step_type === 'info_review')
                                <i class="step-type-icon fa fa-eye" title="Info review"></i>
                            @endif
                            {{ $step->step_name }}
                            @if($step->is_required)
                                <span style="color:#dc3545;font-size:10px;font-weight:600;"> *</span>
                            @endif
                            @if($step->notes)
                                <br><small style="color:#aaa;">{{ $step->notes }}</small>
                            @endif
                        </div>
                        <div class="step-meta" id="step-meta-{{ $step->step_id }}">
                            @if($step->status === 'completed')
                                <i class="fa fa-check-circle" style="color:#28a745;"></i>
                                {{ $step->completed_by_name ?? '' }}
                                @if($step->completed_at)
                                    <br>{{ \Carbon\Carbon::parse($step->completed_at)->format('d/m H:i') }}
                                @endif
                            @endif
                        </div>
                        @if($step->step_type === 'document_required' && $step->status !== 'completed')
                        <form class="upload-form" onsubmit="uploadDoc(event, {{ $jobCard->id }}, {{ $step->step_id }})" style="flex-shrink:0;">
                            <input type="file" name="document" style="display:none;" id="file-{{ $step->step_id }}"
                                   onchange="this.closest('form').dispatchEvent(new Event('submit'));">
                            <button type="button" onclick="document.getElementById('file-{{ $step->step_id }}').click()"
                                    class="btn btn-sm" style="background:#eef2f7;color:#666;border-radius:6px;font-size:11px;border:none;padding:4px 10px;">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Beneficial Ownership — Directors & Shareholders -->
            <div class="bo-panel" id="boPanelWrap">
                <div class="bo-header" onclick="toggleBoPanel()">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span class="bo-toggle" id="boToggle">&#9654;</span>
                        <h5><i class="fa fa-building mr-2"></i>Beneficial Ownership — Directors &amp; Shareholders</h5>
                    </div>
                    <span class="bo-badge" id="boBadge"><i class="fa fa-spinner fa-spin"></i></span>
                </div>
                <div class="bo-body" id="boBody" style="display:none;">
                    <div class="bo-loading" id="boLoading">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p style="margin-top:8px;">Loading directors &amp; shareholders…</p>
                    </div>
                    <div id="boContent" style="display:none;"></div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="card-panel">
                <h5 class="section-title">Attachments</h5>
                @if($attachments->count())
                    @foreach($attachments as $att)
                    <div class="doc-row">
                        <i class="fa fa-file-pdf-o doc-status-icon" style="color:#dc3545;"></i>
                        <span style="flex:1;">{{ $att->file_original_name ?? $att->file_name }}</span>
                        <span style="font-size:11px;color:#aaa;">{{ $att->file_type }}</span>
                        <span style="font-size:11px;color:#aaa;">{{ \Carbon\Carbon::parse($att->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    @endforeach
                @else
                    <p style="color:#aaa;text-align:center;padding:20px 0;">No attachments yet</p>
                @endif
            </div>

            <!-- Pack Generation -->
            <div class="card-panel">
                <h5 class="section-title">Document Packs</h5>
                <div class="d-flex gap-3 flex-wrap">
                    <button onclick="generatePack('internal')" class="btn-pack" style="background:#00BCD4;color:#fff;">
                        <i class="fa fa-file-pdf-o mr-1"></i> Generate Internal Pack
                    </button>
                    <button onclick="generatePack('external')" class="btn-pack" style="background:#00ACC1;color:#fff;">
                        <i class="fa fa-paper-plane mr-1"></i> Generate External Pack
                    </button>
                    @if($jobCard->internal_pack_path)
                    <a href="{{ route('jobcards.downloadPack', [$jobCard->id, 'internal']) }}" class="btn-pack" style="background:#28a745;color:#fff;text-decoration:none;">
                        <i class="fa fa-download mr-1"></i> Download Internal
                    </a>
                    @endif
                    @if($jobCard->external_pack_path)
                    <a href="{{ route('jobcards.downloadPack', [$jobCard->id, 'external']) }}" class="btn-pack" style="background:#17a2b8;color:#fff;text-decoration:none;">
                        <i class="fa fa-download mr-1"></i> Download External
                    </a>
                    @endif
                    <button onclick="$('#emailModal').modal('show')" class="btn-pack" style="background:#6f42c1;color:#fff;">
                        <i class="fa fa-envelope mr-1"></i> Email Pack
                    </button>
                </div>
                <div id="packMessage" style="margin-top:10px;display:none;"></div>
            </div>
        </div>

        <!-- Right Column: Client Info + Job Details -->
        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card-panel">
                <h5 class="section-title">Actions</h5>
                <div class="form-group mb-3">
                    <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Status</label>
                    <select id="statusSelect" class="form-control form-control-sm" style="border-radius:8px;font-size:13px;"
                            onchange="updateStatus({{ $jobCard->id }}, this.value)">
                        @foreach($statuses as $key => $s)
                            <option value="{{ $key }}" {{ $jobCard->status === $key ? 'selected' : '' }}>{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Notes</label>
                    <textarea id="notesField" class="form-control form-control-sm" rows="3" style="border-radius:8px;font-size:13px;">{{ $jobCard->notes }}</textarea>
                    <button onclick="saveNotes({{ $jobCard->id }})" class="btn btn-sm mt-2" style="background:#00BCD4;color:#fff;border-radius:8px;font-size:12px;border:none;padding:8px 16px;font-weight:600;">Save Notes</button>
                </div>
            </div>

            <!-- Client Info -->
            <div class="card-panel">
                <h5 class="section-title">Client Information</h5>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Company Name</div>
                        <div class="value">{{ $client->company_name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Client Code</div>
                        <div class="value">{{ $client->client_code ?? '-' }}</div>
                    </div>
                    @foreach($fields as $f)
                    <div class="info-item">
                        <div class="label">{{ $f->field_label }}</div>
                        <div class="value">{{ $f->value ?: '-' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Required Documents Status -->
            @if($requiredDocs->count())
            <div class="card-panel">
                <h5 class="section-title">Required Documents</h5>
                @foreach($requiredDocs as $rd)
                @php
                    $hasDoc = $attachments->where('document_type_id', $rd->document_type_id)->count() > 0;
                @endphp
                <div class="doc-row">
                    @if($hasDoc)
                        <i class="fa fa-check-circle doc-status-icon" style="color:#28a745;"></i>
                    @else
                        <i class="fa fa-times-circle doc-status-icon" style="color:#dc3545;"></i>
                    @endif
                    <span style="flex:1;">{{ $rd->document_label }}</span>
                    @if($rd->is_required)
                        <span style="font-size:10px;color:#dc3545;font-weight:600;">Required</span>
                    @else
                        <span style="font-size:10px;color:#aaa;">Optional</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-weight:600;color:#fff !important;">Email Pack</h5>
                <button type="button" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;" onclick="$('#emailModal').modal('hide');">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label>Email To *</label>
                    <input type="email" id="emailTo" class="form-control" placeholder="recipient@example.com" value="{{ $client->email ?? '' }}">
                </div>
                <div class="form-group mb-3">
                    <label>Subject</label>
                    <input type="text" id="emailSubject" class="form-control" value="Job Card Pack - {{ $jobCard->job_code }}">
                </div>
                <div class="form-group">
                    <label>Pack Type</label>
                    <select id="emailPackType" class="form-control">
                        <option value="internal">Internal Pack</option>
                        <option value="external">External Pack</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#emailModal').modal('hide');" style="background:#dc3545;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">Cancel</button>
                <button type="button" onclick="emailPack()" style="background:#00BCD4;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;cursor:pointer;">
                    <i class="fa fa-paper-plane mr-1"></i> Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var csrfToken = '{{ csrf_token() }}';

function toggleStep(jobCardId, stepId, currentStatus) {
    var nextStatus = currentStatus === 'pending' ? 'completed' : currentStatus === 'completed' ? 'pending' : 'completed';

    fetch('/job-cards/' + jobCardId + '/update-step', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ step_id: stepId, status: nextStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update checkbox visual
            var check = document.getElementById('step-check-' + stepId);
            check.className = 'step-check ' + nextStatus;
            check.innerHTML = nextStatus === 'completed' ? '<i class="fa fa-check"></i>' : '';
            check.setAttribute('onclick', "toggleStep(" + jobCardId + "," + stepId + ",'" + nextStatus + "')");

            // Update name style
            var name = document.getElementById('step-name-' + stepId);
            if (nextStatus === 'completed') { name.classList.add('done'); } else { name.classList.remove('done'); }

            // Update meta
            var meta = document.getElementById('step-meta-' + stepId);
            if (nextStatus === 'completed') {
                var now = new Date();
                meta.innerHTML = '<i class="fa fa-check-circle" style="color:#28a745;"></i> ' + now.toLocaleDateString('en-GB', {day:'2-digit',month:'2-digit'}) + ' ' + now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit'});
            } else {
                meta.innerHTML = '';
            }

            // Update progress bar
            var pct = data.completion_percentage;
            document.getElementById('progressFill').style.width = pct + '%';
            document.getElementById('progressFill').style.background = pct >= 100 ? '#28a745' : '#E91E8C';
            document.getElementById('progressText').textContent = Math.round(pct) + '%';
        }
    });
}

function updateStatus(jobCardId, status) {
    fetch('/job-cards/' + jobCardId + '/update-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ status: status })
    });
}

function saveNotes(jobCardId) {
    fetch('/job-cards/' + jobCardId, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ notes: document.getElementById('notesField').value })
    }).then(r => r.json()).then(d => { if(d.success) alert('Notes saved.'); });
}

function generatePack(type) {
    var msg = document.getElementById('packMessage');
    msg.style.display = 'block';
    msg.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating ' + type + ' pack...';
    msg.style.color = '#666';

    fetch('/job-cards/{{ $jobCard->id }}/generate-pack', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ type: type })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            msg.innerHTML = '<i class="fa fa-check-circle" style="color:#28a745;"></i> ' + d.message;
            msg.style.color = '#28a745';
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            msg.innerHTML = '<i class="fa fa-exclamation-circle" style="color:#dc3545;"></i> ' + (d.message || 'Error');
            msg.style.color = '#dc3545';
        }
    });
}

function uploadDoc(e, jobCardId, stepId) {
    e.preventDefault();
    var fileInput = document.getElementById('file-' + stepId);
    if (!fileInput.files.length) return;

    var fd = new FormData();
    fd.append('document', fileInput.files[0]);
    fd.append('step_id', stepId);
    fd.append('_token', csrfToken);

    fetch('/job-cards/' + jobCardId + '/upload-document', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); });
}

function emailPack() {
    var emailTo = document.getElementById('emailTo').value;
    if (!emailTo) { alert('Please enter an email address.'); return; }

    fetch('/job-cards/{{ $jobCard->id }}/email-pack', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            email_to: emailTo,
            subject: document.getElementById('emailSubject').value,
            type: document.getElementById('emailPackType').value
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message);
            $('#emailModal').modal('hide');
        } else {
            alert(d.message || 'Error sending email.');
        }
    });
}

/* ─── Beneficial Ownership Panel ─── */
var boLoaded = false;
var boData = null;

function toggleBoPanel() {
    var body = document.getElementById('boBody');
    var toggle = document.getElementById('boToggle');
    if (body.style.display === 'none') {
        body.style.display = 'block';
        toggle.classList.add('open');
        if (!boLoaded) loadBoDirectors();
    } else {
        body.style.display = 'none';
        toggle.classList.remove('open');
    }
}

function loadBoDirectors() {
    fetch('/job-cards/{{ $jobCard->id }}/bo-directors', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        boLoaded = true;
        boData = data;
        document.getElementById('boLoading').style.display = 'none';
        document.getElementById('boContent').style.display = 'block';
        document.getElementById('boBadge').textContent = (data.directors ? data.directors.length : 0) + ' Directors';
        renderBoPanel(data);
    })
    .catch(err => {
        document.getElementById('boLoading').innerHTML = '<i class="fa fa-exclamation-triangle" style="color:#dc3545;font-size:24px;"></i><p style="margin-top:8px;color:#dc3545;">Failed to load directors.</p>';
    });
}

function renderBoPanel(data) {
    var dirs = data.directors || [];
    var totalShares = data.totalShares || 0;
    var shareType = data.shareType || 'Ordinary Shares';
    var dirDocs = data.directorDocs || {};
    var client = data.client || {};

    var activeCount = dirs.filter(function(d){ return d.director_status_name === 'Active'; }).length;
    var totalDirShares = 0;
    dirs.forEach(function(d){ totalDirShares += parseInt(d.number_of_director_shares) || 0; });

    var html = '';

    // ─── Summary stats ───
    html += '<div class="bo-summary">';
    html += '<div class="bo-stat"><div class="bo-stat-value">' + dirs.length + '</div><div class="bo-stat-label">Directors</div></div>';
    html += '<div class="bo-stat"><div class="bo-stat-value">' + activeCount + '</div><div class="bo-stat-label">Active</div></div>';
    html += '<div class="bo-stat"><div class="bo-stat-value" style="color:#17A2B8;">' + numberFormat(totalShares) + '</div><div class="bo-stat-label">Total Shares</div></div>';
    html += '<div class="bo-stat"><div class="bo-stat-value">' + escHtml(shareType) + '</div><div class="bo-stat-label">Share Type</div></div>';
    if (client.company_reg_number) {
        html += '<div class="bo-stat"><div class="bo-stat-value" style="font-size:14px;">' + escHtml(client.company_reg_number) + '</div><div class="bo-stat-label">Reg Number</div></div>';
    }
    html += '</div>';

    // ─── STEP 1: Directors & ID Documents ───
    html += '<div class="bo-section-divider"><i class="fa fa-id-card mr-2"></i>Step 1 — Identity of Beneficial Owners</div>';
    html += '<div style="overflow-x:auto;">';
    html += '<table class="bo-table">';
    html += '<thead><tr>';
    html += '<th style="width:25px;">#</th>';
    html += '<th>Director / Shareholder</th>';
    html += '<th>ID Number</th>';
    html += '<th>Tax Number</th>';
    html += '<th>Type</th>';
    html += '<th>Status</th>';
    html += '<th class="text-right">Shares</th>';
    html += '<th class="text-right">%</th>';
    html += '<th class="text-center">ID Doc</th>';
    html += '<th class="text-center">POA</th>';
    html += '<th class="text-center" style="width:200px;">Actions</th>';
    html += '</tr></thead>';
    html += '<tbody>';

    if (dirs.length === 0) {
        html += '<tr><td colspan="11" style="text-align:center;padding:30px;color:#aaa;">No directors found for this company.</td></tr>';
    }

    dirs.forEach(function(d, i) {
        var dirId = d.director_id;
        var docs = dirDocs[dirId] || [];
        var idDocs = docs.filter(function(dc){ return dc.file_type === 'id_document'; });
        var poaDocs = docs.filter(function(dc){ return dc.file_type === 'poa_document'; });
        var hasIdDoc = d.has_id_document || idDocs.length > 0;
        var hasPoa = d.has_poa || poaDocs.length > 0;
        var statusColor = d.director_status_name === 'Active' ? '#28a745' : '#dc3545';
        var pct = d.calculated_percentage || d.share_percentage || 0;
        var shares = parseInt(d.number_of_director_shares) || 0;

        html += '<tr>';
        html += '<td style="font-weight:700;color:#0d3d56;">' + (i + 1) + '</td>';
        html += '<td><strong>' + escHtml(d.firstname) + ' ' + escHtml(d.surname) + '</strong></td>';
        html += '<td style="font-family:monospace;font-size:10px;">' + escHtml(d.identity_number || '-') + '</td>';
        html += '<td style="font-family:monospace;font-size:10px;">' + escHtml(d.tax_number || '-') + '</td>';
        html += '<td style="font-size:10px;">' + escHtml(d.director_type_name || '-') + '</td>';
        html += '<td><span style="color:' + statusColor + ';font-weight:600;font-size:10px;">' + escHtml(d.director_status_name || '-') + '</span></td>';
        html += '<td class="text-right">' + numberFormat(shares) + '</td>';
        html += '<td class="text-right" style="font-weight:700;color:#17A2B8;">' + pct.toFixed(2) + '%</td>';

        // ID Doc status
        html += '<td class="text-center">';
        if (hasIdDoc) { html += '<span class="bo-doc-badge has-doc"><i class="fa fa-check-circle"></i> OK</span>'; }
        else { html += '<span class="bo-doc-badge no-doc"><i class="fa fa-times-circle"></i> Missing</span>'; }
        html += '</td>';

        // POA status
        html += '<td class="text-center">';
        if (hasPoa) {
            if (d.poa_is_fresh) { html += '<span class="bo-btn-poa-fresh"><i class="fa fa-check"></i> Fresh</span>'; }
            else { html += '<span class="bo-btn-poa-stale"><i class="fa fa-clock-o"></i> On File</span>'; }
        } else {
            html += '<span class="bo-doc-badge no-doc"><i class="fa fa-times-circle"></i> Missing</span>';
        }
        html += '</td>';

        // Actions
        html += '<td class="text-center" style="white-space:nowrap;">';
        // ID actions
        if (!hasIdDoc) {
            html += '<button class="bo-btn bo-btn-fetch" onclick="boFetchId(' + dirId + ')" title="Fetch ID"><i class="fa fa-download"></i></button>';
        }
        html += '<button class="bo-btn bo-btn-upload" onclick="boUploadId(' + dirId + ')" title="Upload ID"><i class="fa fa-id-card"></i></button>';
        // POA actions
        if (!hasPoa) {
            html += '<button class="bo-btn bo-btn-fetch" onclick="boFetchPoa(' + dirId + ')" title="Fetch POA"><i class="fa fa-home"></i></button>';
        }
        html += '<button class="bo-btn bo-btn-upload" onclick="boUploadPoa(' + dirId + ')" title="Upload POA"><i class="fa fa-file-text"></i></button>';
        html += '<button class="bo-btn bo-btn-cra01" onclick="boGenerateCRA01(' + dirId + ')" title="Generate CRA01"><i class="fa fa-magic"></i></button>';
        if (hasIdDoc || hasPoa) {
            html += '<button class="bo-btn bo-btn-view" onclick="boViewDocs(' + dirId + ')" title="View docs"><i class="fa fa-eye"></i></button>';
        }
        html += '<input type="file" id="bo-file-' + dirId + '" style="display:none;" accept="image/*,.pdf" onchange="boDoUpload(' + dirId + ',\'id_front\')">';
        html += '<input type="file" id="bo-poa-file-' + dirId + '" style="display:none;" accept="image/*,.pdf" onchange="boDoUploadPoa(' + dirId + ')">';
        html += '</td>';
        html += '</tr>';
    });

    // Totals row
    if (dirs.length > 0) {
        var totalPct = 0;
        dirs.forEach(function(d){ totalPct += parseFloat(d.calculated_percentage || d.share_percentage || 0); });
        html += '<tr class="bo-total-row">';
        html += '<td colspan="7" style="text-align:right;text-transform:uppercase;letter-spacing:0.5px;">TOTALS</td>';
        html += '<td class="text-right" style="color:#fff;">' + totalPct.toFixed(2) + '%</td>';
        html += '<td colspan="3"></td>';
        html += '</tr>';
    }

    html += '</tbody></table></div>';

    // ─── STEP 3: BO Filing Review ───
    html += '<div class="bo-section-divider"><i class="fa fa-file-text-o mr-2"></i>Step 3 — BO Filing Review (Auto-Populated)</div>';
    html += '<div class="bo-review-grid">';
    html += '<div class="bo-review-item"><div class="bo-rv-label">Company Name</div><div class="bo-rv-value">' + escHtml(client.company_name || '-') + '</div></div>';
    html += '<div class="bo-review-item"><div class="bo-rv-label">Registration Number</div><div class="bo-rv-value">' + escHtml(client.company_reg_number || '-') + '</div></div>';
    html += '<div class="bo-review-item"><div class="bo-rv-label">Total Shares</div><div class="bo-rv-value">' + numberFormat(totalShares) + ' ' + escHtml(shareType) + '</div></div>';
    html += '<div class="bo-review-item"><div class="bo-rv-label">Number of Beneficial Owners</div><div class="bo-rv-value">' + dirs.length + '</div></div>';
    dirs.forEach(function(d, i) {
        var shares = parseInt(d.number_of_director_shares) || 0;
        var pct = d.calculated_percentage || d.share_percentage || 0;
        html += '<div class="bo-review-item" style="width:98%;">';
        html += '<div class="bo-rv-label">Beneficial Owner ' + (i + 1) + '</div>';
        html += '<div class="bo-rv-value">' + escHtml(d.firstname) + ' ' + escHtml(d.surname);
        html += ' &middot; ID: ' + escHtml(d.identity_number || '-');
        html += ' &middot; Tax: ' + escHtml(d.tax_number || '-');
        html += ' &middot; ' + numberFormat(shares) + ' shares (' + pct.toFixed(2) + '%)';
        html += '</div></div>';
    });
    html += '</div>';

    // ─── Document Generation Buttons ───
    html += '<div class="bo-section-divider"><i class="fa fa-file-pdf-o mr-2"></i>Generate BO Documents</div>';
    html += '<div class="bo-gen-buttons">';
    html += '<div class="bo-gen-wrap" id="boWrap_shareholders"><button class="bo-gen-btn" id="boBtn_shareholders" style="background:#0d3d56;" onclick="boGenDoc(\'shareholders\')"><i class="fa fa-list mr-1"></i> Register of Shareholders</button></div>';
    html += '<div class="bo-gen-wrap" id="boWrap_beneficial_owners"><button class="bo-gen-btn" id="boBtn_beneficial_owners" style="background:#17A2B8;" onclick="boGenDoc(\'beneficial_owners\')"><i class="fa fa-users mr-1"></i> Register of Beneficial Owners</button></div>';
    html += '<div class="bo-gen-wrap" id="boWrap_diagram"><button class="bo-gen-btn" id="boBtn_diagram" style="background:#6f42c1;" onclick="boGenDoc(\'diagram\')"><i class="fa fa-sitemap mr-1"></i> BO Diagram</button></div>';
    html += '<div class="bo-gen-wrap" id="boWrap_resolution"><button class="bo-gen-btn" id="boBtn_resolution" style="background:#E91E8C;" onclick="boGenDoc(\'resolution\')"><i class="fa fa-gavel mr-1"></i> Ordinary Resolution</button></div>';
    html += '<div class="bo-gen-wrap" id="boWrap_all"><button class="bo-gen-btn" id="boBtn_all" style="background:#28a745;" onclick="boGenDoc(\'all\')"><i class="fa fa-files-o mr-1"></i> Generate All 4 Documents</button></div>';
    html += '<div class="bo-gen-wrap"><button class="bo-gen-btn" id="boBtn_email" style="background:#1a73e8;" onclick="boEmailPrompt()"><i class="fa fa-envelope mr-1"></i> Email to Client</button></div>';
    html += '</div>';
    html += '<div class="bo-gen-msg" id="boGenMsg" style="display:none;"></div>';

    document.getElementById('boContent').innerHTML = html;
}

function boFetchId(directorId) {
    var btn = event.target.closest('.bo-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    fetch('/job-cards/{{ $jobCard->id }}/bo-fetch-id', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ director_id: directorId })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message);
            boLoaded = false;
            loadBoDirectors();
        } else {
            alert(d.message || 'Failed to fetch ID document.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-download"></i> Fetch';
        }
    })
    .catch(function() {
        alert('Error fetching document.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-download"></i> Fetch';
    });
}

function boUploadId(directorId) {
    document.getElementById('bo-file-' + directorId).click();
}

function boDoUpload(directorId, category) {
    var fileInput = document.getElementById('bo-file-' + directorId);
    if (!fileInput.files.length) return;

    var fd = new FormData();
    fd.append('document', fileInput.files[0]);
    fd.append('director_id', directorId);
    fd.append('document_category', category || 'id_front');
    fd.append('_token', csrfToken);

    fetch('/job-cards/{{ $jobCard->id }}/bo-upload-id', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { alert(d.message || 'Done.'); if (d.success) boReload(); });

    fileInput.value = '';
}

function boViewDocs(directorId) {
    var docs = (boData && boData.directorDocs && boData.directorDocs[directorId]) || [];
    if (docs.length === 0) { alert('No documents found.'); return; }
    var list = docs.map(function(dc){ return dc.file_original_name + ' (' + (dc.document_category || dc.file_type) + ')'; }).join('\n');
    alert('Documents for this director:\n\n' + list);
}

/* ─── POA Functions ─── */
function boFetchPoa(directorId) {
    if (!confirm('Fetch Proof of Address from person record?')) return;
    boAjaxAction('/job-cards/{{ $jobCard->id }}/bo-fetch-poa', { director_id: directorId });
}

function boUploadPoa(directorId) {
    document.getElementById('bo-poa-file-' + directorId).click();
}

function boDoUploadPoa(directorId) {
    var fileInput = document.getElementById('bo-poa-file-' + directorId);
    if (!fileInput.files.length) return;
    var fd = new FormData();
    fd.append('document', fileInput.files[0]);
    fd.append('director_id', directorId);
    fd.append('_token', csrfToken);
    fetch('/job-cards/{{ $jobCard->id }}/bo-upload-poa', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { alert(d.message || 'Done.'); boReload(); });
    fileInput.value = '';
}

function boGenerateCRA01(directorId) {
    if (!confirm('Generate CRA01 SARS Proof of Address form for this director?')) return;
    boAjaxAction('/job-cards/{{ $jobCard->id }}/bo-generate-cra01', { director_id: directorId });
}

var boDocColors = {
    shareholders: '#0d3d56',
    beneficial_owners: '#17A2B8',
    diagram: '#6f42c1',
    resolution: '#E91E8C',
    all: '#28a745'
};

function boGenDoc(docType) {
    var btn = document.getElementById('boBtn_' + docType);
    var wrap = document.getElementById('boWrap_' + docType);
    var msg = document.getElementById('boGenMsg');

    // Disable button and show spinner inside it
    btn.disabled = true;
    var origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Generating...';

    msg.style.display = 'block';
    msg.innerHTML = '<i class="fa fa-spinner fa-spin" style="color:#17A2B8;"></i> Generating ' + docType.replace(/_/g, ' ') + '...';
    msg.style.color = '#666';

    fetch('/job-cards/{{ $jobCard->id }}/bo-generate-doc', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ doc_type: docType })
    })
    .then(r => r.json())
    .then(function(d) {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (d.success) {
            msg.innerHTML = '<i class="fa fa-check-circle" style="color:#28a745;"></i> ' + (d.message || 'Generated!');
            msg.style.color = '#28a745';

            if (docType === 'all' && d.results) {
                // "Generate All" — add view buttons under each individual doc
                var types = ['shareholders', 'beneficialOwners', 'diagram', 'resolution'];
                var wrapIds = ['shareholders', 'beneficial_owners', 'diagram', 'resolution'];
                for (var i = 0; i < types.length; i++) {
                    var r = d.results[types[i]];
                    if (r && r.success && r.attachment_id) {
                        addViewBtn(wrapIds[i], r.attachment_id, boDocColors[wrapIds[i]]);
                    }
                }
            } else if (d.attachment_id) {
                addViewBtn(docType, d.attachment_id, boDocColors[docType]);
            }
        } else {
            msg.innerHTML = '<i class="fa fa-exclamation-circle" style="color:#dc3545;"></i> ' + (d.message || 'Error');
            msg.style.color = '#dc3545';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        msg.innerHTML = '<i class="fa fa-exclamation-circle" style="color:#dc3545;"></i> Network error';
        msg.style.color = '#dc3545';
    });
}

function addViewBtn(docType, attachmentId, color) {
    var wrap = document.getElementById('boWrap_' + docType);
    if (!wrap) return;
    // Remove any existing view button for this doc type
    var old = wrap.querySelector('.bo-view-btn');
    if (old) old.remove();
    var a = document.createElement('a');
    a.className = 'bo-view-btn';
    a.style.background = color;
    a.href = '/job-cards/{{ $jobCard->id }}/bo-attachment/' + attachmentId;
    a.target = '_blank';
    a.innerHTML = '<i class="fa fa-eye mr-1"></i> View';
    wrap.appendChild(a);
}

function boEmailPrompt() {
    var defaultEmail = {!! json_encode(DB::table('client_master')->where('client_id', $jobCard->client_id ?? 0)->value('email') ?? '') !!};

    Swal.fire({
        title: 'Email BO Documents',
        html: '<p style="font-size:13px;color:#666;margin-bottom:12px;">An InfoDocs-style email with all BO document links and PDF attachments will be sent. If documents haven\'t been generated yet, they will be created automatically.</p>'
            + '<input type="email" id="swalBoEmail" class="swal2-input" placeholder="Client email address" value="' + escHtml(defaultEmail) + '" style="font-size:14px;">',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-paper-plane"></i> Send Email',
        confirmButtonColor: '#1a73e8',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: function() {
            var email = document.getElementById('swalBoEmail').value.trim();
            if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                Swal.showValidationMessage('Please enter a valid email address');
                return false;
            }
            return email;
        }
    }).then(function(result) {
        if (!result.isConfirmed || !result.value) return;
        var emailTo = result.value;

        // Show sending progress
        Swal.fire({
            title: 'Sending...',
            html: '<p>Preparing and sending BO documents to<br><strong>' + escHtml(emailTo) + '</strong></p>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function() { Swal.showLoading(); }
        });

        fetch('/job-cards/{{ $jobCard->id }}/bo-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ email_to: emailTo })
        })
        .then(r => r.json())
        .then(function(d) {
            if (d.success) {
                Swal.fire({ icon: 'success', title: 'Email Sent!', text: d.message || 'BO documents emailed successfully.', confirmButtonColor: '#28a745' });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to send email.', confirmButtonColor: '#dc3545' });
            }
        })
        .catch(function() {
            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to the server. Please try again.', confirmButtonColor: '#dc3545' });
        });
    });
}

function boAjaxAction(url, data) {
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => { alert(d.message || 'Done.'); if (d.success) boReload(); });
}

function boReload() { boLoaded = false; loadBoDirectors(); }

function numberFormat(n) {
    return parseInt(n || 0).toLocaleString('en-ZA');
}

function escHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
</script>
@endsection
