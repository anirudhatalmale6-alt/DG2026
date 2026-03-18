@extends('smartdash::layouts.default')

@section('content')
<style>
    .docgen-card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
        margin-bottom: 1.25rem;
        transition: box-shadow 0.2s ease;
    }
    .docgen-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1), 0 2px 4px rgba(0,0,0,0.06);
    }
    .docgen-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.85rem 1.15rem;
        color: #344767;
    }
    .docgen-card .card-header i {
        width: 20px;
        text-align: center;
        margin-right: 0.4rem;
        opacity: 0.7;
    }
    .docgen-card .card-body {
        padding: 1.15rem;
    }
    .docgen-table th {
        font-weight: 500;
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        width: 140px;
        vertical-align: top;
        padding: 0.55rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .docgen-table td {
        font-size: 0.875rem;
        color: #344767;
        padding: 0.55rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .docgen-table tr:last-child th,
    .docgen-table tr:last-child td {
        border-bottom: none;
    }
    .doc-mono {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.85rem;
        background: #f0f2f5;
        padding: 0.15rem 0.5rem;
        border-radius: 0.25rem;
        color: #2d3748;
        letter-spacing: 0.5px;
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 50rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-badge-active { background: rgba(25,135,84,0.1); color: #198754; }
    .status-badge-inactive { background: rgba(255,193,7,0.15); color: #cc8800; }
    .status-badge-deleted { background: rgba(220,53,69,0.1); color: #dc3545; }

    /* Audit timeline */
    .audit-timeline {
        position: relative;
        padding-left: 28px;
    }
    .audit-timeline::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: #e9ecef;
        border-radius: 2px;
    }
    .audit-entry {
        position: relative;
        padding-bottom: 1rem;
    }
    .audit-entry:last-child {
        padding-bottom: 0;
    }
    .audit-dot {
        position: absolute;
        left: -23px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #adb5bd;
    }
    .audit-dot-generated { background: #198754; box-shadow: 0 0 0 2px #198754; }
    .audit-dot-emailed { background: #0d6efd; box-shadow: 0 0 0 2px #0d6efd; }
    .audit-dot-downloaded { background: #6610f2; box-shadow: 0 0 0 2px #6610f2; }
    .audit-dot-status_changed { background: #fd7e14; box-shadow: 0 0 0 2px #fd7e14; }
    .audit-dot-viewed { background: #6c757d; box-shadow: 0 0 0 2px #6c757d; }
    .audit-dot-deleted { background: #dc3545; box-shadow: 0 0 0 2px #dc3545; }

    .audit-action {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: capitalize;
        color: #344767;
    }
    .audit-details {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.1rem;
    }
    .audit-meta {
        font-size: 0.72rem;
        color: #adb5bd;
        margin-top: 0.15rem;
    }

    .pdf-preview-frame {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #f8f9fa;
    }

    .email-status-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .btn-action {
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.825rem;
        padding: 0.4rem 0.85rem;
        transition: all 0.2s ease;
    }
    .btn-action:hover {
        transform: translateY(-1px);
    }

    .page-header-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0;
    }

    .status-change-btn {
        font-size: 0.8rem;
        padding: 0.3rem 0.65rem;
        border-radius: 0.3rem;
        font-weight: 500;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .status-change-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
</style>

{{-- Page Header Card --}}
<div class="card docgen-card">
    <div class="card-body py-3 px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('docgen.index') }}" class="btn btn-light btn-sm border" title="Back to Documents">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="page-header-title">{{ $document->document_name }}</h1>
                    <span class="text-muted small">{{ $document->document_number }}</span>
                </div>
                <span class="status-badge status-badge-{{ $document->status }}">
                    {{ ucfirst($document->status) }}
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('docgen.download', $document->id) }}" class="btn btn-primary btn-action">
                    <i class="fas fa-download me-1"></i> Download
                </a>
                <a href="{{ route('docgen.viewer', $document->id) }}" target="_blank" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-file-pdf me-1"></i> View PDF
                </a>
                <button type="button" class="btn btn-outline-info btn-action" data-bs-toggle="modal" data-bs-target="#emailModal">
                    <i class="fas fa-envelope me-1"></i> Email
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-warning btn-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-exchange-alt me-1"></i> Change Status
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        @foreach(['active', 'inactive', 'deleted'] as $status)
                            @if($document->status !== $status)
                                <li>
                                    <form action="{{ route('docgen.status', $document->id) }}" method="POST" class="status-change-form">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                            @if($status === 'active')
                                                <span class="badge bg-success rounded-circle p-1">&nbsp;</span> Set Active
                                            @elseif($status === 'inactive')
                                                <span class="badge bg-warning rounded-circle p-1">&nbsp;</span> Set Inactive
                                            @elseif($status === 'deleted')
                                                <span class="badge bg-danger rounded-circle p-1">&nbsp;</span> Set Deleted
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">

        {{-- Document Information --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-file-alt"></i> Document Information
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless docgen-table mb-0">
                    <tbody>
                        <tr>
                            <th>Document Number</th>
                            <td><span class="doc-mono">{{ $document->document_number }}</span></td>
                        </tr>
                        <tr>
                            <th>Document Name</th>
                            <td>{{ $document->document_name }}</td>
                        </tr>
                        <tr>
                            <th>Template</th>
                            <td>
                                @if($document->template)
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-layer-group me-1 text-muted"></i>
                                        {{ $document->template->name }}
                                    </span>
                                    <span class="text-muted small ms-1">({{ $document->template->code }})</span>
                                @else
                                    <span class="text-muted fst-italic">Template removed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Document Date</th>
                            <td>
                                @if($document->document_date)
                                    {{ $document->document_date->format('d M Y') }}
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>File Size</th>
                            <td>
                                @if($document->file_size)
                                    @if($document->file_size >= 1048576)
                                        {{ number_format($document->file_size / 1048576, 2) }} MB
                                    @elseif($document->file_size >= 1024)
                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                    @else
                                        {{ $document->file_size }} bytes
                                    @endif
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Generated At</th>
                            <td>
                                @if($document->created_at)
                                    {{ $document->created_at->format('d M Y, H:i') }}
                                    <span class="text-muted small ms-1">({{ $document->created_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Client Information --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-building"></i> Client Information
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless docgen-table mb-0">
                    <tbody>
                        <tr>
                            <th>Client Name</th>
                            <td>{{ $document->client_name ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Client Code</th>
                            <td>
                                @if($document->client_code)
                                    <span class="doc-mono">{{ $document->client_code }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Additional Details --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Additional Details
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless docgen-table mb-0">
                    <tbody>
                        <tr>
                            <th>Requested By</th>
                            <td>{{ $document->requested_by ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Prepared By</th>
                            <td>{{ $document->prepared_by ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Approved By</th>
                            <td>{{ $document->approved_by ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Signed By</th>
                            <td>{{ $document->signed_by ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>
                                @if($document->notes)
                                    <div class="bg-light rounded p-2" style="font-size: 0.85rem; white-space: pre-wrap;">{{ $document->notes }}</div>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PDF Preview --}}
        @if($document->file_path)
        <div class="card docgen-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-eye"></i> PDF Preview</span>
                <a href="{{ route('docgen.viewer', $document->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-expand me-1"></i> Full Screen
                </a>
            </div>
            <div class="card-body p-0">
                <div class="pdf-preview-frame">
                    <iframe
                        src="{{ route('docgen.viewer', $document->id) }}"
                        width="100%"
                        height="600"
                        style="border: none; display: block;"
                        title="PDF Preview - {{ $document->document_name }}"
                    ></iframe>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Right Column --}}
    <div class="col-md-4">

        {{-- Status Card --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-toggle-on"></i> Status
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($document->status === 'active')
                        <span class="status-badge status-badge-active" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
                            <i class="fas fa-check-circle me-1"></i> Active
                        </span>
                    @elseif($document->status === 'inactive')
                        <span class="status-badge status-badge-inactive" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
                            <i class="fas fa-pause-circle me-1"></i> Inactive
                        </span>
                    @elseif($document->status === 'deleted')
                        <span class="status-badge status-badge-deleted" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
                            <i class="fas fa-trash me-1"></i> Deleted
                        </span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
                            {{ ucfirst($document->status) }}
                        </span>
                    @endif
                </div>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    @foreach(['active', 'inactive', 'deleted'] as $status)
                        @if($document->status !== $status)
                            <form action="{{ route('docgen.status', $document->id) }}" method="POST" class="d-inline status-change-form">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                @if($status === 'active')
                                    <button type="submit" class="status-change-btn btn-outline-success">
                                        <i class="fas fa-check-circle me-1"></i> Activate
                                    </button>
                                @elseif($status === 'inactive')
                                    <button type="submit" class="status-change-btn btn-outline-warning">
                                        <i class="fas fa-pause-circle me-1"></i> Deactivate
                                    </button>
                                @elseif($status === 'deleted')
                                    <button type="submit" class="status-change-btn btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                @endif
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Email Status Card --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-envelope"></i> Email Status
            </div>
            <div class="card-body">
                @if($document->emailed)
                    <div class="d-flex align-items-start gap-3">
                        <div class="email-status-icon bg-success bg-opacity-10 text-success flex-shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-success" style="font-size: 0.875rem;">Sent</div>
                            <div class="mt-1" style="font-size: 0.825rem;">
                                <i class="fas fa-at text-muted me-1"></i>
                                <span>{{ $document->emailed_to }}</span>
                            </div>
                            @if($document->emailed_at)
                                <div class="mt-1 text-muted" style="font-size: 0.78rem;">
                                    <i class="fas fa-clock me-1"></i>
                                    <span title="{{ $document->emailed_at->format('d M Y, H:i:s') }}">
                                        {{ $document->emailed_at->diffForHumans() }}
                                    </span>
                                    <span class="ms-1">&middot; {{ $document->emailed_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-start gap-3">
                        <div class="email-status-icon bg-secondary bg-opacity-10 text-secondary flex-shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-secondary" style="font-size: 0.875rem;">Not Sent</div>
                            <div class="text-muted mt-1" style="font-size: 0.8rem;">
                                This document has not been emailed yet.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#emailModal">
                                <i class="fas fa-paper-plane me-1"></i> Send Now
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Audit Trail Card --}}
        <div class="card docgen-card">
            <div class="card-header">
                <i class="fas fa-history"></i> Audit Trail
            </div>
            <div class="card-body">
                @if($document->auditLogs && $document->auditLogs->count() > 0)
                    <div class="audit-timeline">
                        @foreach($document->auditLogs as $log)
                            <div class="audit-entry">
                                <div class="audit-dot audit-dot-{{ $log->action }}"></div>
                                <div class="audit-action">
                                    @switch($log->action)
                                        @case('generated')
                                            <i class="fas fa-plus-circle text-success me-1" style="font-size: 0.72rem;"></i>
                                            @break
                                        @case('emailed')
                                            <i class="fas fa-envelope text-primary me-1" style="font-size: 0.72rem;"></i>
                                            @break
                                        @case('downloaded')
                                            <i class="fas fa-download text-purple me-1" style="font-size: 0.72rem; color: #6610f2;"></i>
                                            @break
                                        @case('status_changed')
                                            <i class="fas fa-exchange-alt text-warning me-1" style="font-size: 0.72rem;"></i>
                                            @break
                                        @case('viewed')
                                            <i class="fas fa-eye text-secondary me-1" style="font-size: 0.72rem;"></i>
                                            @break
                                        @case('deleted')
                                            <i class="fas fa-trash text-danger me-1" style="font-size: 0.72rem;"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-muted me-1" style="font-size: 0.72rem;"></i>
                                    @endswitch
                                    {{ str_replace('_', ' ', $log->action) }}
                                </div>
                                @if($log->details)
                                    <div class="audit-details">{{ $log->details }}</div>
                                @endif
                                <div class="audit-meta">
                                    <span title="{{ $log->action_by }}">{{ $log->action_by }}</span>
                                    @if($log->created_at)
                                        &middot;
                                        <span title="{{ $log->created_at->format('d M Y, H:i:s') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if($log->ip_address)
                                        &middot;
                                        <span title="IP Address">{{ $log->ip_address }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-stream d-block mb-2" style="font-size: 1.5rem; opacity: 0.3;"></i>
                        <span style="font-size: 0.825rem;">No audit log entries yet.</span>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Email Modal --}}
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('docgen.email', $document->id) }}" method="POST" id="emailForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="emailModalLabel">
                        <i class="fas fa-paper-plane text-primary me-2"></i>Email Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label for="email_to" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            To Address <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            class="form-control"
                            id="email_to"
                            name="email_to"
                            placeholder="recipient@example.com"
                            value="{{ old('email_to', $document->emailed_to) }}"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="email_subject" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="email_subject"
                            name="email_subject"
                            value="{{ old('email_subject', $document->document_name . ' - ' . $document->document_number) }}"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="email_body" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            Message
                        </label>
                        <textarea
                            class="form-control"
                            id="email_body"
                            name="email_body"
                            rows="5"
                            placeholder="Enter your message..."
                        >{{ old('email_body', "Dear Client,\n\nPlease find the attached document: " . $document->document_name . ".\n\nRegards") }}</textarea>
                    </div>
                    <div class="bg-light rounded p-2 d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                        <i class="fas fa-paperclip text-muted"></i>
                        <span class="text-muted">Attachment:</span>
                        <span class="fw-semibold">{{ $document->file_name ?: $document->document_name . '.pdf' }}</span>
                        @if($document->file_size)
                            <span class="text-muted ms-auto">
                                @if($document->file_size >= 1048576)
                                    {{ number_format($document->file_size / 1048576, 2) }} MB
                                @elseif($document->file_size >= 1024)
                                    {{ number_format($document->file_size / 1024, 1) }} KB
                                @else
                                    {{ $document->file_size }} bytes
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="emailSendBtn">
                        <i class="fas fa-paper-plane me-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Email form submission with loading state
    var emailForm = document.getElementById('emailForm');
    var emailSendBtn = document.getElementById('emailSendBtn');

    if (emailForm) {
        emailForm.addEventListener('submit', function() {
            emailSendBtn.disabled = true;
            emailSendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
        });
    }

    // Status change confirmation for 'deleted' status
    var statusForms = document.querySelectorAll('.status-change-form');
    statusForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var status = form.querySelector('input[name="status"]').value;
            if (status === 'deleted') {
                if (!confirm('Are you sure you want to mark this document as deleted?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // Auto-dismiss flash messages after 5 seconds
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
});
</script>
@endpush
