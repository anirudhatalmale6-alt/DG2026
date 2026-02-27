@extends('layouts.default')

@section('content')
<style>
    .tpl-card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
        margin-bottom: 1.25rem;
        transition: box-shadow 0.2s ease;
    }
    .tpl-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1), 0 2px 4px rgba(0,0,0,0.06);
    }
    .tpl-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.85rem 1.15rem;
        color: #344767;
    }
    .tpl-card .card-header i {
        width: 20px;
        text-align: center;
        margin-right: 0.4rem;
        opacity: 0.7;
    }
    .tpl-card .card-body {
        padding: 1.15rem;
    }
    .tpl-label {
        font-weight: 500;
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .page-row {
        transition: background-color 0.2s ease, opacity 0.3s ease;
    }
    .page-row:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
    .page-row.sortable-ghost {
        opacity: 0.4;
        background: #e8f4fd;
    }
    .page-row.sortable-drag {
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .drag-handle {
        cursor: grab;
        color: #adb5bd;
        transition: color 0.15s ease;
        padding: 0.25rem;
    }
    .drag-handle:hover {
        color: #495057;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .field-preview-table th {
        font-weight: 500;
        font-size: 0.72rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: #f8f9fc;
        padding: 0.4rem 0.6rem;
        border-bottom: 1px solid #e9ecef;
    }
    .field-preview-table td {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
        color: #344767;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        vertical-align: middle;
    }
    .upload-zone {
        border: 2px dashed #c8d0da;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #f8f9fc 0%, #f0f2f8 100%);
        padding: 1.5rem;
        transition: all 0.2s ease;
    }
    .upload-zone:hover, .upload-zone.drag-over {
        border-color: #4e73df;
        background: linear-gradient(135deg, #eef2ff 0%, #e8edff 100%);
    }
    .upload-zone .upload-icon {
        font-size: 2rem;
        color: #b0bec5;
        margin-bottom: 0.5rem;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cdd5e0;
        transition: 0.3s;
        border-radius: 22px;
    }
    .toggle-slider::before {
        content: "";
        position: absolute;
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider {
        background-color: #198754;
    }
    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(18px);
    }
    .inline-edit-input {
        border: 1px solid transparent;
        background: transparent;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        transition: all 0.15s ease;
        width: 100%;
        max-width: 200px;
    }
    .inline-edit-input:hover {
        border-color: #dee2e6;
        background: #f8f9fa;
    }
    .inline-edit-input:focus {
        border-color: #86b7fe;
        background: #fff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .nav-tabs-custom .nav-link {
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
        padding: 0.75rem 1.25rem;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #344767;
        border-color: #dee2e6;
    }
    .nav-tabs-custom .nav-link.active {
        color: #4e73df;
        border-color: #4e73df;
        background: transparent;
    }
    .badge-count {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 50rem;
        vertical-align: middle;
        margin-left: 0.35rem;
    }
    .orientation-badge {
        font-size: 0.72rem;
        font-weight: 500;
        padding: 0.2rem 0.55rem;
        border-radius: 0.25rem;
    }
    .btn-action-sm {
        font-size: 0.78rem;
        padding: 0.25rem 0.55rem;
        border-radius: 0.3rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }
    .btn-action-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .page-number-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .fields-accordion .accordion-button {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.65rem 1rem;
        color: #344767;
    }
    .fields-accordion .accordion-button:not(.collapsed) {
        background-color: #f0f4ff;
        color: #4e73df;
        box-shadow: none;
    }
    .fields-accordion .accordion-body {
        padding: 0;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.35rem;
    }
    .status-dot-active { background: #198754; }
    .status-dot-inactive { background: #dc3545; }
</style>

<div class="container-fluid">

    {{-- ── Page Header Card ─────────────────────────────────────────────── --}}
    <div class="card tpl-card">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('cimsdocgen.templates') }}" class="btn btn-light btn-sm border" title="Back to Templates">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <h4 class="card-title mb-0">
                    <i class="fa fa-edit text-primary me-2"></i> Edit Template: <span class="text-primary">{{ $template->name }}</span>
                </h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($template->is_active)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                        <i class="fa fa-circle me-1" style="font-size: 0.45rem; vertical-align: middle;"></i> Active
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                        <i class="fa fa-circle me-1" style="font-size: 0.45rem; vertical-align: middle;"></i> Inactive
                    </span>
                @endif
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                    <i class="fa fa-copy me-1 text-muted"></i> {{ $template->code }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ────────────────────────────────────────────────── --}}
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fa fa-exclamation-triangle me-1"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Tabs Navigation ───────────────────────────────────────────────── --}}
    <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-0" id="templateEditorTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ session('active_tab', 'details') === 'details' ? 'active' : '' }}" id="details-tab" data-bs-toggle="tab" data-bs-target="#tab-details"
                    type="button" role="tab" aria-controls="tab-details" aria-selected="{{ session('active_tab', 'details') === 'details' ? 'true' : 'false' }}">
                <i class="fa fa-info-circle me-1"></i> Template Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ session('active_tab') === 'pages' ? 'active' : '' }}" id="pages-tab" data-bs-toggle="tab" data-bs-target="#tab-pages"
                    type="button" role="tab" aria-controls="tab-pages" aria-selected="{{ session('active_tab') === 'pages' ? 'true' : 'false' }}">
                <i class="fa fa-file-alt me-1"></i> Pages
                <span class="badge-count bg-primary bg-opacity-10 text-primary">{{ $template->pages->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="fields-tab" data-bs-toggle="tab" data-bs-target="#tab-fields"
                    type="button" role="tab" aria-controls="tab-fields" aria-selected="false">
                <i class="fa fa-th-list me-1"></i> Field Mappings Preview
                <span class="badge-count bg-info bg-opacity-10 text-info">{{ $template->pages->sum(function($p) { return $p->fieldMappings->count(); }) }}</span>
            </button>
        </li>
    </ul>

    {{-- ── Tab Content ───────────────────────────────────────────────────── --}}
    <div class="tab-content" id="templateEditorTabContent">

        {{-- ================================================================ --}}
        {{-- TAB 1: Template Details                                          --}}
        {{-- ================================================================ --}}
        <div class="tab-pane fade {{ session('active_tab', 'details') === 'details' ? 'show active' : '' }}" id="tab-details" role="tabpanel" aria-labelledby="details-tab">
            <div class="card tpl-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                <div class="card-header">
                    <i class="fa fa-cog"></i> Template Configuration
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsdocgen.templates.update', $template->id) }}" id="templateDetailsForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Template Name --}}
                            <div class="col-md-6">
                                <label for="template_name" class="form-label tpl-label">
                                    Template Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="template_name"
                                       name="name"
                                       value="{{ old('name', $template->name) }}"
                                       placeholder="Enter template name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Template Code --}}
                            <div class="col-md-3">
                                <label for="template_code" class="form-label tpl-label">
                                    Code <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('code') is-invalid @enderror"
                                       id="template_code"
                                       name="code"
                                       value="{{ old('code', $template->code) }}"
                                       placeholder="e.g. INV, QUO"
                                       style="text-transform: uppercase; font-family: monospace; letter-spacing: 1px;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-md-3">
                                <label for="template_category" class="form-label tpl-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="template_category"
                                        name="category_id">
                                    <option value="">-- Select --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $template->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-9">
                                <label for="template_description" class="form-label tpl-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="template_description"
                                          name="description"
                                          rows="3"
                                          placeholder="Describe the purpose and usage of this template...">{{ old('description', $template->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Active Toggle --}}
                            <div class="col-md-3">
                                <label class="form-label tpl-label">Status</label>
                                <div class="card border rounded-3 p-3 h-auto" style="background: linear-gradient(135deg, #f8f9fc 0%, #f5f7fa 100%);">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.85rem;" id="statusLabel">
                                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                Toggle template availability
                                            </div>
                                        </div>
                                        <label class="toggle-switch mb-0">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox"
                                                   name="is_active"
                                                   value="1"
                                                   id="templateActiveToggle"
                                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="border-top pt-3 mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('cimsdocgen.templates') }}" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4" id="btnSaveDetails">
                                <i class="fa fa-save me-1"></i> Save Template Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Template Metadata --}}
            <div class="card tpl-card">
                <div class="card-header">
                    <i class="fa fa-info"></i> Template Metadata
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Template ID</div>
                            <div class="fw-bold" style="font-family: monospace;">{{ $template->id }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Created</div>
                            <div class="fw-medium" style="font-size: 0.875rem;">
                                {{ $template->created_at ? $template->created_at->format('d M Y, H:i') : '--' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Last Updated</div>
                            <div class="fw-medium" style="font-size: 0.875rem;">
                                {{ $template->updated_at ? $template->updated_at->format('d M Y, H:i') : '--' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Pages</div>
                            <div class="fw-bold fs-5 text-primary">{{ $template->pages->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- TAB 2: Template Pages                                            --}}
        {{-- ================================================================ --}}
        <div class="tab-pane fade {{ session('active_tab') === 'pages' ? 'show active' : '' }}" id="tab-pages" role="tabpanel" aria-labelledby="pages-tab">

            {{-- Pages List --}}
            <div class="card tpl-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-layer-group"></i> Template Pages
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2" style="font-size: 0.72rem;">
                            {{ $template->pages->count() }} {{ \Illuminate\Support\Str::plural('page', $template->pages->count()) }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <i class="fa fa-grip-vertical me-1"></i> Drag rows to reorder
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($template->pages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="pagesTable">
                                <thead style="background: #f8f9fc;">
                                    <tr>
                                        <th style="width: 36px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase"></th>
                                        <th style="width: 50px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase text-center">#</th>
                                        <th style="font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase">Page Label</th>
                                        <th style="width: 110px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase text-center">Orientation</th>
                                        <th style="width: 80px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase text-center">Status</th>
                                        <th style="width: 80px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase text-center">Fields</th>
                                        <th style="width: 200px; font-size: 0.72rem;" class="text-muted fw-semibold text-uppercase text-end pe-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortablePages">
                                    @foreach($template->pages as $page)
                                        <tr class="page-row" data-page-id="{{ $page->id }}" id="page-row-{{ $page->id }}">
                                            {{-- Drag Handle --}}
                                            <td class="text-center">
                                                <span class="drag-handle" title="Drag to reorder">
                                                    <i class="fa fa-grip-vertical"></i>
                                                </span>
                                            </td>

                                            {{-- Page Number --}}
                                            <td class="text-center">
                                                <span class="page-number-circle bg-primary bg-opacity-10 text-primary page-number-display">
                                                    {{ $page->page_number }}
                                                </span>
                                            </td>

                                            {{-- Page Label (inline editable) --}}
                                            <td>
                                                <input type="text"
                                                       class="inline-edit-input page-label-input"
                                                       value="{{ $page->page_label }}"
                                                       data-page-id="{{ $page->id }}"
                                                       data-original="{{ $page->page_label }}"
                                                       title="Click to edit label">
                                            </td>

                                            {{-- Orientation --}}
                                            <td class="text-center">
                                                @if($page->orientation === 'landscape')
                                                    <span class="orientation-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                        <i class="fa fa-arrows-alt-h me-1" style="font-size: 0.65rem;"></i> Landscape
                                                    </span>
                                                @else
                                                    <span class="orientation-badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                        <i class="fa fa-arrows-alt-v me-1" style="font-size: 0.65rem;"></i> Portrait
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Status Toggle --}}
                                            <td class="text-center">
                                                <label class="toggle-switch mb-0" title="{{ $page->is_active ? 'Active - click to deactivate' : 'Inactive - click to activate' }}">
                                                    <input type="checkbox"
                                                           class="page-active-toggle"
                                                           data-page-id="{{ $page->id }}"
                                                           {{ $page->is_active ? 'checked' : '' }}>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </td>

                                            {{-- Field Count --}}
                                            <td class="text-center">
                                                <span class="badge {{ $page->fieldMappings->count() > 0 ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary' }}"
                                                      style="font-size: 0.75rem;">
                                                    {{ $page->fieldMappings->count() }}
                                                </span>
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-end pe-3">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <a href="{{ route('cimsdocgen.fields', $page->id) }}"
                                                       class="btn btn-outline-primary btn-action-sm"
                                                       title="Configure Field Mappings"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-th-list me-1"></i> Fields
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-action-sm btn-delete-page"
                                                            data-page-id="{{ $page->id }}"
                                                            data-page-label="{{ $page->page_label }}"
                                                            title="Delete Page"
                                                            data-bs-toggle="tooltip">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-file-pdf fa-3x text-muted opacity-50"></i>
                            </div>
                            <h6 class="text-muted fw-normal">No pages added yet</h6>
                            <p class="text-muted small mb-0">
                                Upload PDF pages below to build your template.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Add Page Form --}}
            <div class="card tpl-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f0fff4 0%, #e6ffed 100%); border-bottom: 2px solid #c6f6d5;">
                    <i class="fa fa-plus-circle text-success"></i> Add New Page
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ route('cimsdocgen.pages.store', $template->id) }}"
                          enctype="multipart/form-data"
                          id="addPageForm">
                        @csrf

                        <div class="row g-3 align-items-end">
                            {{-- PDF File Upload --}}
                            <div class="col-md-5">
                                <label class="form-label tpl-label">
                                    PDF File <span class="text-danger">*</span>
                                </label>
                                <div class="upload-zone text-center" id="uploadZone">
                                    <div class="upload-icon">
                                        <i class="fa fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-semibold text-primary" style="font-size: 0.85rem;">Click to browse</span>
                                        <span class="text-muted" style="font-size: 0.8rem;"> or drag & drop</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">PDF files only, max 10MB</div>
                                    <input type="file"
                                           name="pdf_file"
                                           id="pdfFileInput"
                                           accept=".pdf,application/pdf"
                                           class="d-none"
                                           required>
                                    <div id="selectedFileName" class="mt-2 fw-semibold text-success" style="font-size: 0.82rem; display: none;">
                                        <i class="fa fa-check-circle me-1"></i> <span></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Page Label --}}
                            <div class="col-md-3">
                                <label for="new_page_label" class="form-label tpl-label">Page Label</label>
                                <input type="text"
                                       class="form-control"
                                       id="new_page_label"
                                       name="page_label"
                                       placeholder="e.g. Cover Page, Page 1"
                                       value="{{ old('page_label') }}">
                                <div class="form-text" style="font-size: 0.72rem;">Leave blank for auto-numbering</div>
                            </div>

                            {{-- Orientation --}}
                            <div class="col-md-2">
                                <label for="new_page_orientation" class="form-label tpl-label">
                                    Orientation <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="new_page_orientation" name="orientation" required>
                                    <option value="portrait" {{ old('orientation') === 'landscape' ? '' : 'selected' }}>Portrait</option>
                                    <option value="landscape" {{ old('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                                </select>
                            </div>

                            {{-- Submit --}}
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100" id="btnAddPage">
                                    <i class="fa fa-plus me-1"></i> Add Page
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- TAB 3: Field Mappings Preview                                    --}}
        {{-- ================================================================ --}}
        <div class="tab-pane fade" id="tab-fields" role="tabpanel" aria-labelledby="fields-tab">
            <div class="card tpl-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-th-list"></i> Field Mappings Overview
                        <span class="badge bg-info bg-opacity-10 text-info ms-2" style="font-size: 0.72rem;">
                            {{ $template->pages->sum(function($p) { return $p->fieldMappings->count(); }) }} total fields
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <i class="fa fa-info-circle me-1"></i> Read-only preview &mdash; click "Edit Fields" to modify
                    </div>
                </div>
                <div class="card-body">
                    @if($template->pages->count() > 0)
                        <div class="accordion fields-accordion" id="fieldsAccordion">
                            @foreach($template->pages as $pageIndex => $page)
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden"
                                     style="box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                                    <h2 class="accordion-header" id="fieldsHeading{{ $page->id }}">
                                        <button class="accordion-button {{ $pageIndex > 0 ? 'collapsed' : '' }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#fieldsCollapse{{ $page->id }}"
                                                aria-expanded="{{ $pageIndex === 0 ? 'true' : 'false' }}"
                                                aria-controls="fieldsCollapse{{ $page->id }}">
                                            <span class="page-number-circle bg-primary bg-opacity-10 text-primary me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                {{ $page->page_number }}
                                            </span>
                                            {{ $page->page_label }}
                                            <span class="badge {{ $page->fieldMappings->count() > 0 ? 'bg-success' : 'bg-secondary' }} ms-2" style="font-size: 0.68rem;">
                                                {{ $page->fieldMappings->count() }} {{ \Illuminate\Support\Str::plural('field', $page->fieldMappings->count()) }}
                                            </span>
                                            @if(!$page->is_active)
                                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 0.68rem;">Inactive</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="fieldsCollapse{{ $page->id }}"
                                         class="accordion-collapse collapse {{ $pageIndex === 0 ? 'show' : '' }}"
                                         aria-labelledby="fieldsHeading{{ $page->id }}"
                                         data-bs-parent="#fieldsAccordion">
                                        <div class="accordion-body">
                                            @if($page->fieldMappings->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm field-preview-table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Field Label</th>
                                                                <th>Field Name</th>
                                                                <th>Source</th>
                                                                <th class="text-center">Position (X, Y)</th>
                                                                <th class="text-center">Size (W &times; H)</th>
                                                                <th>Font</th>
                                                                <th class="text-center">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($page->fieldMappings as $fieldIndex => $field)
                                                                <tr>
                                                                    <td class="text-muted">{{ $fieldIndex + 1 }}</td>
                                                                    <td class="fw-medium">{{ $field->field_label }}</td>
                                                                    <td>
                                                                        <code style="font-size: 0.75rem; background: #f0f2f5; padding: 0.1rem 0.35rem; border-radius: 0.2rem;">
                                                                            {{ $field->field_name }}
                                                                        </code>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                                                            {{ $field->field_source ?: 'client_master' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center" style="font-family: monospace; font-size: 0.75rem;">
                                                                        {{ number_format($field->pos_x, 1) }}, {{ number_format($field->pos_y, 1) }}
                                                                    </td>
                                                                    <td class="text-center" style="font-family: monospace; font-size: 0.75rem;">
                                                                        @if($field->width || $field->height)
                                                                            {{ $field->width ? number_format($field->width, 1) : '--' }} &times; {{ $field->height ? number_format($field->height, 1) : '--' }}
                                                                        @else
                                                                            <span class="text-muted">--</span>
                                                                        @endif
                                                                    </td>
                                                                    <td style="font-size: 0.75rem;">
                                                                        @if($field->font_family || $field->font_size)
                                                                            {{ $field->font_family ?: 'Default' }}
                                                                            @if($field->font_size)
                                                                                <span class="text-muted">/ {{ $field->font_size }}pt</span>
                                                                            @endif
                                                                            @if($field->font_color && $field->font_color !== '#000000')
                                                                                <span class="ms-1 d-inline-block rounded-circle border" style="width: 10px; height: 10px; background: {{ $field->font_color }}; vertical-align: middle;"></span>
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">Default</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($field->is_active)
                                                                            <span class="status-dot status-dot-active" title="Active"></span>
                                                                        @else
                                                                            <span class="status-dot status-dot-inactive" title="Inactive"></span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center py-3 text-muted" style="font-size: 0.82rem;">
                                                    <i class="fa fa-inbox me-1"></i> No field mappings configured for this page.
                                                </div>
                                            @endif
                                            <div class="border-top p-2 bg-light text-end">
                                                <a href="{{ route('cimsdocgen.fields', $page->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-edit me-1"></i> Edit Fields for {{ $page->page_label }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-th-list fa-3x text-muted opacity-50"></i>
                            </div>
                            <h6 class="text-muted fw-normal">No field mappings to display</h6>
                            <p class="text-muted small mb-0">Add pages first, then configure field mappings for each page.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Available Client Fields Reference --}}
            @if(!empty($clientFields))
                <div class="card tpl-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa fa-database"></i> Available Client Fields Reference
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#clientFieldsCollapse">
                            <i class="fa fa-chevron-down me-1"></i> Toggle
                        </button>
                    </div>
                    <div class="collapse" id="clientFieldsCollapse">
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                @foreach($clientFields as $category => $fields)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded-3 h-100">
                                            <div class="px-3 py-2 bg-light border-bottom fw-semibold" style="font-size: 0.8rem; color: #344767;">
                                                {{ $category }}
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1" style="font-size: 0.65rem;">{{ count($fields) }}</span>
                                            </div>
                                            <div class="px-3 py-2">
                                                @foreach($fields as $fieldName => $fieldLabel)
                                                    <div class="d-flex justify-content-between align-items-center py-1" style="font-size: 0.78rem; border-bottom: 1px solid rgba(0,0,0,0.03);">
                                                        <span class="text-muted">{{ $fieldLabel }}</span>
                                                        <code style="font-size: 0.7rem; background: #f0f2f5; padding: 0.05rem 0.3rem; border-radius: 0.2rem;">{{ $fieldName }}</code>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>{{-- end tab-content --}}

</div>{{-- end container-fluid --}}

{{-- ── Delete Page Confirmation Modal ────────────────────────────────── --}}
<div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-bottom-0">
                <h6 class="modal-title fw-bold text-danger" id="deletePageModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i> Delete Page
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-1">Are you sure you want to delete:</p>
                <p class="fw-bold mb-2" id="deletePageName"></p>
                <p class="text-muted small mb-0">This will also remove all field mappings for this page. This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="btnConfirmDeletePage">
                    <i class="fa fa-trash-alt me-1"></i> Delete Page
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var csrfToken = '{{ csrf_token() }}';

    // ─── Initialize Bootstrap Tooltips ────────────────────────────────
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    // ─── Auto-dismiss flash messages ──────────────────────────────────
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });

    // ─── Template Details Form Submission ─────────────────────────────
    var detailsForm = document.getElementById('templateDetailsForm');
    if (detailsForm) {
        detailsForm.addEventListener('submit', function() {
            var btn = document.getElementById('btnSaveDetails');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
        });
    }

    // ─── Active Toggle Label Update ───────────────────────────────────
    var activeToggle = document.getElementById('templateActiveToggle');
    if (activeToggle) {
        activeToggle.addEventListener('change', function() {
            var label = document.getElementById('statusLabel');
            label.textContent = this.checked ? 'Active' : 'Inactive';
        });
    }

    // ─── File Upload Zone ─────────────────────────────────────────────
    var uploadZone = document.getElementById('uploadZone');
    var pdfFileInput = document.getElementById('pdfFileInput');
    var selectedFileName = document.getElementById('selectedFileName');

    if (uploadZone && pdfFileInput) {
        uploadZone.addEventListener('click', function() {
            pdfFileInput.click();
        });

        pdfFileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                var file = this.files[0];
                var nameSpan = selectedFileName.querySelector('span');
                var sizeMB = (file.size / 1048576).toFixed(2);
                nameSpan.textContent = file.name + ' (' + sizeMB + ' MB)';
                selectedFileName.style.display = 'block';
            } else {
                selectedFileName.style.display = 'none';
            }
        });

        // Drag & drop
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        uploadZone.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            if (e.dataTransfer.files.length > 0) {
                pdfFileInput.files = e.dataTransfer.files;
                pdfFileInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // ─── Add Page Form Submission ─────────────────────────────────────
    var addPageForm = document.getElementById('addPageForm');
    if (addPageForm) {
        addPageForm.addEventListener('submit', function() {
            var btn = document.getElementById('btnAddPage');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Uploading...';
        });
    }

    // ─── Sortable.js for Page Reordering ──────────────────────────────
    var sortableEl = document.getElementById('sortablePages');
    if (sortableEl) {
        new Sortable(sortableEl, {
            handle: '.drag-handle',
            animation: 200,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function() {
                var rows = sortableEl.querySelectorAll('.page-row');
                var pages = [];

                rows.forEach(function(row, index) {
                    var pageId = row.getAttribute('data-page-id');
                    pages.push({
                        id: parseInt(pageId),
                        sort_order: index + 1
                    });

                    // Update displayed page number
                    var numberEl = row.querySelector('.page-number-display');
                    if (numberEl) {
                        numberEl.textContent = index + 1;
                    }
                });

                // AJAX POST to reorder
                fetch('{{ route("cimsdocgen.pages.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ pages: pages })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        showToast('success', 'Pages reordered successfully.');
                    } else {
                        showToast('danger', data.message || 'Failed to reorder pages.');
                    }
                })
                .catch(function(error) {
                    console.error('Reorder error:', error);
                    showToast('danger', 'An error occurred while reordering pages.');
                });
            }
        });
    }

    // ─── Inline Page Label Edit ───────────────────────────────────────
    document.querySelectorAll('.page-label-input').forEach(function(input) {
        input.addEventListener('blur', function() {
            var pageId = this.getAttribute('data-page-id');
            var original = this.getAttribute('data-original');
            var newValue = this.value.trim();

            if (newValue === original || newValue === '') {
                this.value = original;
                return;
            }

            fetch('{{ url("cims/document-generator/pages") }}/' + pageId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ page_label: newValue })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    input.setAttribute('data-original', newValue);
                    showToast('success', 'Page label updated.');
                } else {
                    input.value = original;
                    showToast('danger', data.message || 'Failed to update label.');
                }
            })
            .catch(function(error) {
                input.value = original;
                console.error('Label update error:', error);
                showToast('danger', 'An error occurred while updating the label.');
            });
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
            if (e.key === 'Escape') {
                this.value = this.getAttribute('data-original');
                this.blur();
            }
        });
    });

    // ─── Page Active Toggle (AJAX) ────────────────────────────────────
    document.querySelectorAll('.page-active-toggle').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var pageId = this.getAttribute('data-page-id');
            var isActive = this.checked;
            var toggle = this;

            fetch('{{ url("cims/document-generator/pages") }}/' + pageId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: isActive ? 1 : 0 })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('success', 'Page status updated.');
                } else {
                    toggle.checked = !isActive;
                    showToast('danger', data.message || 'Failed to update page status.');
                }
            })
            .catch(function(error) {
                toggle.checked = !isActive;
                console.error('Toggle error:', error);
                showToast('danger', 'An error occurred while updating page status.');
            });
        });
    });

    // ─── Delete Page (AJAX) ───────────────────────────────────────────
    var deletePageId = null;
    var deletePageModal = document.getElementById('deletePageModal');
    var deleteModalInstance = deletePageModal ? new bootstrap.Modal(deletePageModal) : null;

    document.querySelectorAll('.btn-delete-page').forEach(function(btn) {
        btn.addEventListener('click', function() {
            deletePageId = this.getAttribute('data-page-id');
            var pageLabel = this.getAttribute('data-page-label');

            // Hide tooltip before opening modal
            var tooltip = bootstrap.Tooltip.getInstance(this);
            if (tooltip) tooltip.hide();

            document.getElementById('deletePageName').textContent = '"' + pageLabel + '"';

            if (deleteModalInstance) {
                deleteModalInstance.show();
            }
        });
    });

    var btnConfirmDelete = document.getElementById('btnConfirmDeletePage');
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function() {
            if (!deletePageId) return;

            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Deleting...';

            fetch('{{ url("cims/document-generator/pages") }}/' + deletePageId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    var row = document.getElementById('page-row-' + deletePageId);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        setTimeout(function() {
                            row.remove();

                            // Re-number remaining pages
                            var remaining = document.querySelectorAll('#sortablePages .page-row');
                            remaining.forEach(function(r, idx) {
                                var numEl = r.querySelector('.page-number-display');
                                if (numEl) numEl.textContent = idx + 1;
                            });

                            // If no pages left, reload to show empty state
                            if (remaining.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }
                    showToast('success', 'Page deleted successfully.');
                } else {
                    showToast('danger', data.message || 'Failed to delete page.');
                }

                if (deleteModalInstance) {
                    deleteModalInstance.hide();
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-trash-alt me-1"></i> Delete Page';
                deletePageId = null;
            })
            .catch(function(error) {
                console.error('Delete error:', error);
                showToast('danger', 'An error occurred while deleting the page.');

                if (deleteModalInstance) {
                    deleteModalInstance.hide();
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-trash-alt me-1"></i> Delete Page';
                deletePageId = null;
            });
        });
    }

    // ─── Toast Notification Helper ────────────────────────────────────
    function showToast(type, message) {
        // Remove existing toast
        var existing = document.getElementById('ajaxToast');
        if (existing) existing.remove();

        var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        var toastHtml = '' +
            '<div id="ajaxToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">' +
                '<div class="toast show align-items-center text-white ' + bgClass + ' border-0 shadow-lg" role="alert">' +
                    '<div class="d-flex">' +
                        '<div class="toast-body">' +
                            '<i class="fa ' + icon + ' me-2"></i> ' + message +
                        '</div>' +
                        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.body.insertAdjacentHTML('beforeend', toastHtml);

        // Auto-remove after 3 seconds
        setTimeout(function() {
            var toast = document.getElementById('ajaxToast');
            if (toast) {
                toast.style.transition = 'opacity 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(function() {
                    if (toast.parentNode) toast.remove();
                }, 300);
            }
        }, 3000);
    }

    // ─── Activate Pages Tab if URL has hash ───────────────────────────
    var hash = window.location.hash;
    if (hash) {
        var tabId = hash.replace('#', '') + '-tab';
        var tabEl = document.getElementById(tabId);
        if (tabEl) {
            var tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    // Update hash on tab change
    var tabLinks = document.querySelectorAll('#templateEditorTabs button[data-bs-toggle="tab"]');
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            var target = e.target.getAttribute('data-bs-target');
            if (target) {
                history.replaceState(null, null, target.replace('#tab-', '#'));
            }
        });
    });

});
</script>
@endpush
