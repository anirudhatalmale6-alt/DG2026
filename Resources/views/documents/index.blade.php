@extends('layouts.default')

@section('content')
<div class="container-fluid">

    {{-- ── Page Header Card ─────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-file-pdf text-primary me-2"></i> Document Generator
                    </h4>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('cimsdocgen.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fa fa-plus me-1"></i> Generate New Document
                        </a>
                        <a href="{{ route('cimsdocgen.templates') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-layer-group me-1"></i> Templates
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cog me-1"></i> Settings
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('cimsdocgen.settings') }}">
                                        <i class="fa fa-sliders-h me-2 text-muted"></i> General Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('cimsdocgen.smtp') }}">
                                        <i class="fa fa-envelope-open-text me-2 text-muted"></i> SMTP Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ── Filter Bar ──────────────────────────────────────── --}}
                    <form method="GET" action="{{ route('cimsdocgen.index') }}" id="filterForm">
                        <div class="row g-2 mb-4">
                            <div class="col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                           placeholder="Search by name, number, or client..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit();">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <select name="template_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit();">
                                    <option value="">All Templates</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" {{ request('template_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-3 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-filter me-1"></i> Filter
                                </button>
                                @if(request('search') || request('status') || request('template_id'))
                                    <a href="{{ route('cimsdocgen.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    {{-- ── Stats Summary Row ───────────────────────────────── --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-3 rounded-3 border"
                                 style="background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle me-3"
                                     style="width: 42px; height: 42px; background: rgba(78, 115, 223, 0.15);">
                                    <i class="fa fa-file-alt text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Documents</div>
                                    <div class="fw-bold fs-5 text-dark">{{ $documents->total() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-3 rounded-3 border"
                                 style="background: linear-gradient(135deg, #f0fff4 0%, #e6ffed 100%);">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle me-3"
                                     style="width: 42px; height: 42px; background: rgba(28, 200, 138, 0.15);">
                                    <i class="fa fa-check-circle text-success"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Active</div>
                                    <div class="fw-bold fs-5 text-dark">{{ $documents->getCollection()->where('status', 'active')->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-3 rounded-3 border"
                                 style="background: linear-gradient(135deg, #fff8f0 0%, #fff3e6 100%);">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle me-3"
                                     style="width: 42px; height: 42px; background: rgba(246, 194, 62, 0.15);">
                                    <i class="fa fa-paper-plane text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Emailed</div>
                                    <div class="fw-bold fs-5 text-dark">{{ $documents->getCollection()->where('emailed', true)->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-3 rounded-3 border"
                                 style="background: linear-gradient(135deg, #f5f0ff 0%, #ede5ff 100%);">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle me-3"
                                     style="width: 42px; height: 42px; background: rgba(111, 66, 193, 0.15);">
                                    <i class="fa fa-calendar-alt text-purple" style="color: #6f42c1;"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">This Month</div>
                                    <div class="fw-bold fs-5 text-dark">{{ $documents->getCollection()->filter(function($d) { return $d->created_at && $d->created_at->isCurrentMonth(); })->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Flash Messages ──────────────────────────────────── --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- ── Documents Table ─────────────────────────────────── --}}
                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr>
                                        <th class="text-muted fw-semibold" style="width: 40px; font-size: 0.8rem;">#</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Document Number</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Document Name</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Client</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Template</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Date</th>
                                        <th class="text-muted fw-semibold text-center" style="font-size: 0.8rem;">Status</th>
                                        <th class="text-muted fw-semibold text-center" style="font-size: 0.8rem;">Emailed</th>
                                        <th class="text-muted fw-semibold text-end" style="font-size: 0.8rem; min-width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $index => $document)
                                        <tr id="document-row-{{ $document->id }}">
                                            <td class="text-muted">{{ $documents->firstItem() + $index }}</td>
                                            <td>
                                                <span class="fw-bold" style="font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', monospace; font-size: 0.85rem;">
                                                    {{ $document->document_number }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('cimsdocgen.show', $document->id) }}" class="text-decoration-none fw-medium">
                                                    {{ $document->document_name }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($document->client_name)
                                                    <div>{{ $document->client_name }}</div>
                                                    <small class="text-muted">{{ $document->client_code }}</small>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->template)
                                                    <span class="badge bg-light text-dark border">{{ $document->template->name }}</span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->document_date)
                                                    {{ $document->document_date->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($document->status === 'active')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Active
                                                    </span>
                                                @elseif($document->status === 'inactive')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Inactive
                                                    </span>
                                                @elseif($document->status === 'deleted')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Deleted
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ ucfirst($document->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($document->emailed)
                                                    <span class="text-success" title="Emailed to {{ $document->emailed_to }} on {{ optional($document->emailed_at)->format('d M Y H:i') }}">
                                                        <i class="fa fa-check-circle"></i>
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fa fa-minus-circle"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    {{-- View --}}
                                                    <a href="{{ route('cimsdocgen.show', $document->id) }}"
                                                       class="btn btn-sm btn-outline-primary" title="View Document"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    {{-- Download --}}
                                                    <a href="{{ route('cimsdocgen.download', $document->id) }}"
                                                       class="btn btn-sm btn-outline-success" title="Download PDF"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-download"></i>
                                                    </a>

                                                    {{-- Email --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-info btn-email-document"
                                                            title="Email Document"
                                                            data-bs-toggle="tooltip"
                                                            data-document-id="{{ $document->id }}"
                                                            data-document-name="{{ $document->document_name }}"
                                                            data-document-number="{{ $document->document_number }}">
                                                        <i class="fa fa-envelope"></i>
                                                    </button>

                                                    {{-- Status / More Actions --}}
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                                title="More Actions">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li class="dropdown-header text-uppercase small fw-semibold text-muted">Change Status</li>
                                                            @if($document->status !== 'active')
                                                                <li>
                                                                    <a class="dropdown-item btn-change-status" href="#"
                                                                       data-document-id="{{ $document->id }}" data-status="active">
                                                                        <i class="fa fa-check-circle text-success me-2"></i> Set Active
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if($document->status !== 'inactive')
                                                                <li>
                                                                    <a class="dropdown-item btn-change-status" href="#"
                                                                       data-document-id="{{ $document->id }}" data-status="inactive">
                                                                        <i class="fa fa-pause-circle text-warning me-2"></i> Set Inactive
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if($document->status !== 'deleted')
                                                                <li>
                                                                    <a class="dropdown-item btn-change-status" href="#"
                                                                       data-document-id="{{ $document->id }}" data-status="deleted">
                                                                        <i class="fa fa-ban text-danger me-2"></i> Set Deleted
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger btn-delete-document" href="#"
                                                                   data-document-id="{{ $document->id }}"
                                                                   data-document-name="{{ $document->document_name }}">
                                                                    <i class="fa fa-trash-alt me-2"></i> Delete Permanently
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ── Pagination ──────────────────────────────────── --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted small">
                                Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }} documents
                            </div>
                            <div>
                                {{ $documents->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        {{-- ── Empty State ─────────────────────────────────── --}}
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-file-pdf fa-3x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted fw-normal">No documents found</h5>
                            <p class="text-muted small mb-4">
                                @if(request('search') || request('status') || request('template_id'))
                                    No documents match your current filters. Try adjusting your search criteria.
                                @else
                                    Get started by generating your first document.
                                @endif
                            </p>
                            <div class="d-flex gap-2 justify-content-center">
                                @if(request('search') || request('status') || request('template_id'))
                                    <a href="{{ route('cimsdocgen.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('cimsdocgen.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus me-1"></i> Generate New Document
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Email Document Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="emailDocumentModal" tabindex="-1" aria-labelledby="emailDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                <h5 class="modal-title" id="emailDocumentModalLabel">
                    <i class="fa fa-envelope text-primary me-2"></i> Email Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="emailDocumentForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="emailDocumentId" value="">
                    <div class="mb-3">
                        <label for="emailTo" class="form-label fw-semibold small text-uppercase text-muted">To Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="emailTo" name="email_to" required
                               placeholder="recipient@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label fw-semibold small text-uppercase text-muted">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="emailSubject" name="email_subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailBody" class="form-label fw-semibold small text-uppercase text-muted">Body</label>
                        <textarea class="form-control" id="emailBody" name="email_body" rows="5"
                                  placeholder="Please find the attached document.">Please find the attached document.</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSendEmail">
                        <i class="fa fa-paper-plane me-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Initialize Bootstrap Tooltips ──────────────────────────────────
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ── Email Modal Trigger ───────────────────────────────────────────
    document.querySelectorAll('.btn-email-document').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var docId = this.getAttribute('data-document-id');
            var docName = this.getAttribute('data-document-name');
            var docNumber = this.getAttribute('data-document-number');

            // Hide tooltip before opening modal
            var tooltip = bootstrap.Tooltip.getInstance(this);
            if (tooltip) tooltip.hide();

            document.getElementById('emailDocumentId').value = docId;
            document.getElementById('emailSubject').value = docName + ' (' + docNumber + ')';
            document.getElementById('emailDocumentForm').action = '{{ url("cims/document-generator/documents") }}/' + docId + '/email';

            var modal = new bootstrap.Modal(document.getElementById('emailDocumentModal'));
            modal.show();
        });
    });

    // ── Email Form Submission ─────────────────────────────────────────
    document.getElementById('emailDocumentForm').addEventListener('submit', function (e) {
        var btnSend = document.getElementById('btnSendEmail');
        btnSend.disabled = true;
        btnSend.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Sending...';
    });

    // ── Change Status ─────────────────────────────────────────────────
    document.querySelectorAll('.btn-change-status').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var docId = this.getAttribute('data-document-id');
            var newStatus = this.getAttribute('data-status');

            if (!confirm('Are you sure you want to change the status to "' + newStatus + '"?')) {
                return;
            }

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("cims/document-generator/documents") }}/' + docId + '/status';
            form.style.display = 'none';

            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            var statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        });
    });

    // ── AJAX Delete Document ──────────────────────────────────────────
    document.querySelectorAll('.btn-delete-document').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var docId = this.getAttribute('data-document-id');
            var docName = this.getAttribute('data-document-name');

            if (!confirm('Are you sure you want to permanently delete "' + docName + '"?\n\nThis action cannot be undone.')) {
                return;
            }

            fetch('{{ url("cims/document-generator/documents") }}/' + docId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    var row = document.getElementById('document-row-' + docId);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0';
                        setTimeout(function () {
                            row.remove();

                            // If table is now empty, reload the page
                            var tbody = document.querySelector('table tbody');
                            if (tbody && tbody.children.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }
                } else {
                    alert(data.message || 'Failed to delete the document.');
                }
            })
            .catch(function (error) {
                alert('An error occurred while deleting the document. Please try again.');
                console.error('Delete error:', error);
            });
        });
    });

});
</script>
@endpush

@endsection
