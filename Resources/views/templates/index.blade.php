@extends('smartdash::layouts.default')

@section('content')
<div class="container-fluid">

    {{-- ── Page Header Card ─────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-layer-group text-primary me-2"></i> Document Templates
                    </h4>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('dg2026.templates.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fa fa-plus me-1"></i> Create Template
                        </a>
                        <a href="{{ route('dg2026.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Back to Documents
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ── Search / Filter Bar ───────────────────────────────── --}}
                    <form method="GET" action="{{ route('dg2026.templates') }}" id="searchForm">
                        <div class="row g-2 mb-4">
                            <div class="col-md-5 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                           placeholder="Search templates by name, code, or description..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <select name="category" class="form-select form-select-sm" onchange="document.getElementById('searchForm').submit();">
                                    <option value="">All Categories</option>
                                    @foreach($templates->pluck('category')->unique()->filter()->sort() as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-filter me-1"></i> Filter
                                </button>
                                @if(request('search') || request('category'))
                                    <a href="{{ route('dg2026.templates') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    {{-- ── Flash Messages ────────────────────────────────────── --}}
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

                    {{-- ── Templates Table ───────────────────────────────────── --}}
                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr>
                                        <th class="text-muted fw-semibold" style="width: 40px; font-size: 0.8rem;">#</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Name</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Code</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Category</th>
                                        <th class="text-muted fw-semibold text-center" style="font-size: 0.8rem;">Pages</th>
                                        <th class="text-muted fw-semibold text-center" style="font-size: 0.8rem;">Documents</th>
                                        <th class="text-muted fw-semibold text-center" style="font-size: 0.8rem;">Status</th>
                                        <th class="text-muted fw-semibold" style="font-size: 0.8rem;">Created</th>
                                        <th class="text-muted fw-semibold text-end" style="font-size: 0.8rem; min-width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $index => $t)
                                        <tr id="template-row-{{ $t->id }}">
                                            <td class="text-muted">{{ $templates->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-medium">{{ $t->name }}</div>
                                                @if($t->description)
                                                    <small class="text-muted d-block text-truncate" style="max-width: 280px;" title="{{ $t->description }}">
                                                        {{ $t->description }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1"
                                                      style="font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', monospace; font-size: 0.8rem;">
                                                    {{ $t->code }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($t->category)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                                        {{ $t->category }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                                    {{ $t->pages_count }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">
                                                    {{ $t->documents_count }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($t->is_active)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $t->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    {{-- Edit --}}
                                                    <a href="{{ route('dg2026.templates.edit', $t->id) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Edit Template"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    {{-- Delete --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger btn-delete-template"
                                                            title="Delete Template"
                                                            data-bs-toggle="tooltip"
                                                            data-template-id="{{ $t->id }}"
                                                            data-template-name="{{ $t->name }}"
                                                            data-documents-count="{{ $t->documents_count }}">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ── Pagination ────────────────────────────────────── --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted small">
                                Showing {{ $templates->firstItem() }} to {{ $templates->lastItem() }} of {{ $templates->total() }} templates
                            </div>
                            <div>
                                {{ $templates->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        {{-- ── Empty State ───────────────────────────────────── --}}
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-layer-group fa-3x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted fw-normal">No templates found</h5>
                            <p class="text-muted small mb-4">
                                @if(request('search') || request('category'))
                                    No templates match your current filters. Try adjusting your search criteria.
                                @else
                                    Get started by creating your first document template.
                                @endif
                            </p>
                            <div class="d-flex gap-2 justify-content-center">
                                @if(request('search') || request('category'))
                                    <a href="{{ route('dg2026.templates') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('dg2026.templates.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus me-1"></i> Create Template
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
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

    // ── AJAX Delete Template ──────────────────────────────────────────
    document.querySelectorAll('.btn-delete-template').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var templateId = this.getAttribute('data-template-id');
            var templateName = this.getAttribute('data-template-name');
            var documentsCount = parseInt(this.getAttribute('data-documents-count'), 10);

            // Hide tooltip before showing confirm
            var tooltip = bootstrap.Tooltip.getInstance(this);
            if (tooltip) tooltip.hide();

            var message = 'Are you sure you want to delete the template "' + templateName + '"?';
            if (documentsCount > 0) {
                message += '\n\nWarning: This template has ' + documentsCount + ' associated document(s). Deleting it may affect those documents.';
            }
            message += '\n\nThis action cannot be undone.';

            if (!confirm(message)) {
                return;
            }

            fetch('{{ url("dg2026/templates") }}/' + templateId, {
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
                    var row = document.getElementById('template-row-' + templateId);
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
                    alert(data.message || 'Failed to delete the template.');
                }
            })
            .catch(function (error) {
                alert('An error occurred while deleting the template. Please try again.');
                console.error('Delete error:', error);
            });
        });
    });

});
</script>
@endpush

@endsection
