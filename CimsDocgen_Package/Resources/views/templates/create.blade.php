@extends('smartdash::layouts.default')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">

            {{-- Card Header --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-plus-circle text-primary me-2"></i> Create New Template
                    </h4>
                    <a href="{{ route('docgen.templates') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to Templates
                    </a>
                </div>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Create Template Form --}}
            <form id="createTemplateForm" method="POST" action="{{ route('docgen.templates.store') }}">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa fa-info-circle text-muted me-2"></i> Template Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Template Name --}}
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    Template Name <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter a descriptive template name"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">A human-readable name for this template.</div>
                            </div>

                            {{-- Template Code --}}
                            <div class="col-12">
                                <label for="code" class="form-label fw-semibold">
                                    Template Code <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control font-monospace @error('code') is-invalid @enderror"
                                    id="code"
                                    name="code"
                                    value="{{ old('code') }}"
                                    placeholder="AUTO_GENERATED_FROM_NAME"
                                    maxlength="50"
                                    required
                                >
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Auto-generated from the name. You may edit it manually. Uppercase letters, numbers, and underscores only.</div>
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea
                                    class="form-control @error('description') is-invalid @enderror"
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="Optional description of what this template is used for..."
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-12">
                                <label for="category" class="form-label fw-semibold">Category</label>
                                <input
                                    type="text"
                                    class="form-control @error('category') is-invalid @enderror"
                                    id="category"
                                    name="category"
                                    value="{{ old('category') }}"
                                    placeholder="e.g. Legal, Finance, HR, Compliance..."
                                >
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Optional grouping category for organising templates.</div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-4">
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('docgen.templates') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnCreateTemplate">
                                <i class="fa fa-plus-circle me-2"></i> Create Template
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ─── Elements ────────────────────────────────────────────────
    var nameInput = document.getElementById('name');
    var codeInput = document.getElementById('code');
    var codeManuallyEdited = false;

    // ─── Auto-generate Code from Name ────────────────────────────
    nameInput.addEventListener('input', function() {
        if (!codeManuallyEdited) {
            var value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9\s_]/g, '')
                .replace(/\s+/g, '_')
                .replace(/_+/g, '_')
                .substring(0, 50);
            codeInput.value = value;
        }
    });

    // ─── Detect Manual Code Edits ────────────────────────────────
    codeInput.addEventListener('input', function() {
        codeManuallyEdited = true;
    });

    // ─── Reset Manual Edit Flag if Code is Cleared ───────────────
    codeInput.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            codeManuallyEdited = false;
            // Re-generate from current name value
            var value = nameInput.value
                .toUpperCase()
                .replace(/[^A-Z0-9\s_]/g, '')
                .replace(/\s+/g, '_')
                .replace(/_+/g, '_')
                .substring(0, 50);
            this.value = value;
        }
    });

    // ─── Prevent Double Submission ───────────────────────────────
    document.getElementById('createTemplateForm').addEventListener('submit', function() {
        var btn = document.getElementById('btnCreateTemplate');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Creating...';
    });

});
</script>
@endpush
@endsection
