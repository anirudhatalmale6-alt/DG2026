@extends('layouts.default')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<style>
    .fields-card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
        margin-bottom: 1.25rem;
        transition: box-shadow 0.2s ease;
    }
    .fields-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1), 0 2px 4px rgba(0,0,0,0.06);
    }
    .fields-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.85rem 1.15rem;
        color: #344767;
    }
    .fields-card .card-header i {
        width: 20px;
        text-align: center;
        margin-right: 0.4rem;
        opacity: 0.7;
    }
    .fields-card .card-body {
        padding: 1.15rem;
    }
    .field-table th {
        font-weight: 600;
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        padding: 0.6rem 0.5rem;
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fc;
    }
    .field-table td {
        font-size: 0.825rem;
        color: #344767;
        padding: 0.55rem 0.5rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .field-table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.04);
    }
    .field-mono {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.78rem;
        background: #f0f2f5;
        padding: 0.1rem 0.4rem;
        border-radius: 0.2rem;
        color: #2d3748;
    }
    .field-pos {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 0.78rem;
        color: #6f42c1;
    }
    .field-size {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 0.78rem;
        color: #0d6efd;
    }
    .field-font-preview {
        font-size: 0.75rem;
        color: #6c757d;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .form-label-sm {
        font-size: 0.8rem;
        font-weight: 600;
        color: #344767;
        margin-bottom: 0.3rem;
    }
    .font-overrides-toggle {
        cursor: pointer;
        user-select: none;
        color: #6c757d;
        font-size: 0.825rem;
        font-weight: 500;
        transition: color 0.15s ease;
    }
    .font-overrides-toggle:hover {
        color: #344767;
    }
    .font-overrides-toggle i {
        transition: transform 0.2s ease;
    }
    .font-overrides-toggle.collapsed i {
        transform: rotate(-90deg);
    }
    .active-toggle {
        cursor: pointer;
    }
    .badge-source {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.2rem 0.45rem;
        border-radius: 0.2rem;
    }
    .badge-source-client_master { background: rgba(25,135,84,0.1); color: #198754; }
    .badge-source-client_master_directors { background: rgba(111,66,193,0.1); color: #6f42c1; }
    .badge-source-client_master_addresses { background: rgba(13,110,253,0.1); color: #0d6efd; }
    .badge-source-form_input { background: rgba(255,193,7,0.15); color: #cc8800; }
    .empty-state-icon {
        font-size: 2.5rem;
        opacity: 0.25;
        color: #6c757d;
    }
</style>

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                Field Mappings &mdash; {{ $page->page_label }} (Page {{ $page->page_number }})
            </h4>
            <small class="text-muted">
                <i class="fa fa-layer-group me-1"></i> Template: <strong>{{ $page->template->name }}</strong>
            </small>
        </div>
        <a href="{{ route('cimsdocgen.templates.edit', $page->template_id) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Template
        </a>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────── --}}
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

    {{-- ── AJAX Alert Container ─────────────────────────────────────────── --}}
    <div id="ajaxAlertContainer"></div>

    {{-- ── Two Column Layout ────────────────────────────────────────────── --}}
    <div class="row">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- LEFT COLUMN: Field Mappings Table                             --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="col-md-7">
            <div class="card fields-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fa fa-list-alt"></i> Field Mappings
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2" id="fieldCount">
                            {{ $page->fieldMappings->count() }}
                        </span>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover field-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Field Label</th>
                                    <th>Field Name</th>
                                    <th>Source</th>
                                    <th class="text-center">Position (mm)</th>
                                    <th class="text-center">Size</th>
                                    <th>Font</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-end" style="min-width: 90px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="fieldsTableBody">
                                @forelse($page->fieldMappings->sortBy('sort_order') as $index => $field)
                                    <tr id="field-row-{{ $field->id }}" data-field-id="{{ $field->id }}">
                                        <td class="text-muted small">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold" style="font-size: 0.825rem;">{{ $field->field_label }}</span>
                                        </td>
                                        <td>
                                            <span class="field-mono">{{ $field->field_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-source badge-source-{{ $field->field_source }}">
                                                {{ str_replace('_', ' ', $field->field_source) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="field-pos">{{ number_format($field->pos_x, 2) }}, {{ number_format($field->pos_y, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($field->width || $field->height)
                                                <span class="field-size">{{ $field->width ? number_format($field->width, 1) : '&mdash;' }} &times; {{ $field->height ? number_format($field->height, 1) : '&mdash;' }}</span>
                                            @else
                                                <span class="text-muted small">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($field->font_family || $field->font_size)
                                                <span class="field-font-preview" title="{{ $field->font_family }} {{ $field->font_size }}pt {{ $field->font_style }} {{ $field->font_color }}">
                                                    {{ $field->font_family ?: 'Default' }}
                                                    @if($field->font_size)
                                                        <span class="text-muted">{{ $field->font_size }}pt</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted small">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $field->field_type ?: 'text' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block mb-0">
                                                <input class="form-check-input active-toggle" type="checkbox"
                                                       data-field-id="{{ $field->id }}"
                                                       {{ $field->is_active ? 'checked' : '' }}
                                                       title="{{ $field->is_active ? 'Active' : 'Inactive' }}">
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-field"
                                                        data-field-id="{{ $field->id }}"
                                                        data-field='@json($field)'
                                                        title="Edit Field" data-bs-toggle="tooltip">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-field"
                                                        data-field-id="{{ $field->id }}"
                                                        data-field-label="{{ $field->field_label }}"
                                                        title="Delete Field" data-bs-toggle="tooltip">
                                                    <i class="fa fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyStateRow">
                                        <td colspan="10" class="text-center py-5">
                                            <div class="empty-state-icon mb-2">
                                                <i class="fa fa-map-marker-alt"></i>
                                            </div>
                                            <h6 class="text-muted fw-normal">No field mappings yet</h6>
                                            <p class="text-muted small mb-0">
                                                Use the form on the right to add field mappings to this page.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- RIGHT COLUMN: Add New Field Form                              --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="col-md-5">
            <div class="card fields-card">
                <div class="card-header">
                    <i class="fa fa-plus-circle"></i> Add Field Mapping
                </div>
                <div class="card-body">
                    <form id="addFieldForm">
                        <input type="hidden" name="template_page_id" value="{{ $page->id }}">

                        {{-- Field Source --}}
                        <div class="mb-3">
                            <label for="add_field_source" class="form-label form-label-sm">
                                Field Source <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="add_field_source" name="field_source" required>
                                <option value="">-- Select Source --</option>
                                <option value="client_master">Client Master</option>
                                <option value="client_master_directors">Client Master - Directors</option>
                                <option value="client_master_addresses">Client Master - Addresses</option>
                                <option value="form_input">Form Input</option>
                            </select>
                        </div>

                        {{-- Field Name --}}
                        <div class="mb-3">
                            <label for="add_field_name" class="form-label form-label-sm">
                                Field Name <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="add_field_name" name="field_name" required>
                                <option value="">-- Select Field --</option>
                                @foreach($clientFields as $category => $fields)
                                    <optgroup label="{{ $category }}">
                                        @foreach($fields as $fieldKey => $fieldLabel)
                                            <option value="{{ $fieldKey }}" data-label="{{ $fieldLabel }}" data-category="{{ $category }}">
                                                {{ $fieldLabel }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Field Label --}}
                        <div class="mb-3">
                            <label for="add_field_label" class="form-label form-label-sm">
                                Field Label <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="add_field_label" name="field_label"
                                   placeholder="Auto-populated from field name" required>
                            <div class="form-text text-muted" style="font-size: 0.72rem;">
                                Display label for this field. Auto-fills from selection above.
                            </div>
                        </div>

                        {{-- Field Type --}}
                        <div class="mb-3">
                            <label for="add_field_type" class="form-label form-label-sm">
                                Field Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="add_field_type" name="field_type" required>
                                <option value="text" selected>Text</option>
                                <option value="date">Date</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="image">Image</option>
                                <option value="signature">Signature</option>
                            </select>
                        </div>

                        {{-- Position --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="add_pos_x" class="form-label form-label-sm">
                                    Position X (mm) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" id="add_pos_x" name="pos_x"
                                           step="0.01" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">mm</span>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">From left edge</div>
                            </div>
                            <div class="col-6">
                                <label for="add_pos_y" class="form-label form-label-sm">
                                    Position Y (mm) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" id="add_pos_y" name="pos_y"
                                           step="0.01" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">mm</span>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">From top edge</div>
                            </div>
                        </div>

                        {{-- Size --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="add_width" class="form-label form-label-sm">Width (mm)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" id="add_width" name="width"
                                           step="0.1" min="0" placeholder="Auto">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="add_height" class="form-label form-label-sm">Height (mm)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" id="add_height" name="height"
                                           step="0.1" min="0" placeholder="Auto">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                        </div>

                        {{-- Font Overrides (Collapsible) --}}
                        <div class="mb-3">
                            <div class="font-overrides-toggle d-flex align-items-center gap-2 py-2 border-top border-bottom"
                                 data-bs-toggle="collapse" data-bs-target="#addFontOverrides" aria-expanded="false">
                                <i class="fa fa-chevron-down" style="font-size: 0.7rem;"></i>
                                <i class="fa fa-font text-muted" style="font-size: 0.8rem;"></i>
                                Font Overrides
                                <span class="text-muted" style="font-size: 0.72rem;">(optional)</span>
                            </div>
                            <div class="collapse" id="addFontOverrides">
                                <div class="pt-3">

                                    {{-- Font Family --}}
                                    <div class="mb-2">
                                        <label for="add_font_family" class="form-label form-label-sm">Font Family</label>
                                        <select class="form-select form-select-sm" id="add_font_family" name="font_family">
                                            <option value="">Default</option>
                                            <option value="Helvetica">Helvetica</option>
                                            <option value="Times">Times</option>
                                            <option value="Courier">Courier</option>
                                            <option value="Arial">Arial</option>
                                        </select>
                                    </div>

                                    {{-- Font Size --}}
                                    <div class="mb-2">
                                        <label for="add_font_size" class="form-label form-label-sm">Font Size (pt)</label>
                                        <input type="number" class="form-control form-control-sm" id="add_font_size" name="font_size"
                                               step="0.5" min="4" max="72" placeholder="Default">
                                    </div>

                                    {{-- Font Style --}}
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm d-block">Font Style</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="add_style_bold" value="bold">
                                                <label class="form-check-label small fw-bold" for="add_style_bold">B</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="add_style_italic" value="italic">
                                                <label class="form-check-label small fst-italic" for="add_style_italic">I</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="add_style_underline" value="underline">
                                                <label class="form-check-label small text-decoration-underline" for="add_style_underline">U</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Font Color --}}
                                    <div class="mb-2">
                                        <label for="add_font_color" class="form-label form-label-sm">Font Color</label>
                                        <div class="input-group input-group-sm">
                                            <input type="color" class="form-control form-control-color form-control-sm"
                                                   id="add_font_color" name="font_color" value="#000000"
                                                   style="width: 38px; padding: 2px;">
                                            <input type="text" class="form-control form-control-sm font-color-text"
                                                   id="add_font_color_text" value="#000000" maxlength="7"
                                                   pattern="^#[0-9A-Fa-f]{6}$" placeholder="#000000">
                                        </div>
                                    </div>

                                    {{-- Text Align --}}
                                    <div class="mb-0">
                                        <label for="add_text_align" class="form-label form-label-sm">Text Align</label>
                                        <select class="form-select form-select-sm" id="add_text_align" name="text_align">
                                            <option value="">Default</option>
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Date Format (conditional) --}}
                        <div class="mb-3" id="addDateFormatGroup" style="display: none;">
                            <label for="add_date_format" class="form-label form-label-sm">Date Format</label>
                            <input type="text" class="form-control form-control-sm" id="add_date_format" name="date_format"
                                   placeholder="d/m/Y">
                            <div class="form-text text-muted" style="font-size: 0.72rem;">
                                PHP date format string. Examples: d/m/Y, Y-m-d, d F Y
                            </div>
                        </div>

                        {{-- Default Value --}}
                        <div class="mb-3">
                            <label for="add_default_value" class="form-label form-label-sm">Default Value</label>
                            <input type="text" class="form-control form-control-sm" id="add_default_value" name="default_value"
                                   placeholder="Optional fallback value">
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-sm" id="btnAddField">
                                <i class="fa fa-plus-circle me-1"></i> Add Field
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- EDIT FIELD MODAL                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editFieldModal" tabindex="-1" aria-labelledby="editFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                <h5 class="modal-title" id="editFieldModalLabel">
                    <i class="fa fa-pencil-alt text-primary me-2"></i> Edit Field Mapping
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFieldForm">
                <input type="hidden" id="edit_field_id" name="id">
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Field Source --}}
                        <div class="col-md-6">
                            <label for="edit_field_source" class="form-label form-label-sm">
                                Field Source <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="edit_field_source" name="field_source" required>
                                <option value="">-- Select Source --</option>
                                <option value="client_master">Client Master</option>
                                <option value="client_master_directors">Client Master - Directors</option>
                                <option value="client_master_addresses">Client Master - Addresses</option>
                                <option value="form_input">Form Input</option>
                            </select>
                        </div>

                        {{-- Field Name --}}
                        <div class="col-md-6">
                            <label for="edit_field_name" class="form-label form-label-sm">
                                Field Name <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="edit_field_name" name="field_name" required>
                                <option value="">-- Select Field --</option>
                                @foreach($clientFields as $category => $fields)
                                    <optgroup label="{{ $category }}">
                                        @foreach($fields as $fieldKey => $fieldLabel)
                                            <option value="{{ $fieldKey }}" data-label="{{ $fieldLabel }}" data-category="{{ $category }}">
                                                {{ $fieldLabel }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Field Label --}}
                        <div class="col-md-6">
                            <label for="edit_field_label" class="form-label form-label-sm">
                                Field Label <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="edit_field_label" name="field_label" required>
                        </div>

                        {{-- Field Type --}}
                        <div class="col-md-6">
                            <label for="edit_field_type" class="form-label form-label-sm">
                                Field Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="edit_field_type" name="field_type" required>
                                <option value="text">Text</option>
                                <option value="date">Date</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="image">Image</option>
                                <option value="signature">Signature</option>
                            </select>
                        </div>

                        {{-- Position X --}}
                        <div class="col-md-3">
                            <label for="edit_pos_x" class="form-label form-label-sm">
                                Position X (mm) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm" id="edit_pos_x" name="pos_x"
                                       step="0.01" min="0" required>
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>

                        {{-- Position Y --}}
                        <div class="col-md-3">
                            <label for="edit_pos_y" class="form-label form-label-sm">
                                Position Y (mm) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm" id="edit_pos_y" name="pos_y"
                                       step="0.01" min="0" required>
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>

                        {{-- Width --}}
                        <div class="col-md-3">
                            <label for="edit_width" class="form-label form-label-sm">Width (mm)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm" id="edit_width" name="width"
                                       step="0.1" min="0">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>

                        {{-- Height --}}
                        <div class="col-md-3">
                            <label for="edit_height" class="form-label form-label-sm">Height (mm)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm" id="edit_height" name="height"
                                       step="0.1" min="0">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>

                        {{-- Font Overrides Section --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <div class="font-overrides-toggle d-flex align-items-center gap-2 py-2"
                                 data-bs-toggle="collapse" data-bs-target="#editFontOverrides" aria-expanded="false">
                                <i class="fa fa-chevron-down" style="font-size: 0.7rem;"></i>
                                <i class="fa fa-font text-muted" style="font-size: 0.8rem;"></i>
                                Font Overrides
                                <span class="text-muted" style="font-size: 0.72rem;">(optional)</span>
                            </div>
                            <div class="collapse" id="editFontOverrides">
                                <div class="row g-3 pt-2">

                                    {{-- Font Family --}}
                                    <div class="col-md-4">
                                        <label for="edit_font_family" class="form-label form-label-sm">Font Family</label>
                                        <select class="form-select form-select-sm" id="edit_font_family" name="font_family">
                                            <option value="">Default</option>
                                            <option value="Helvetica">Helvetica</option>
                                            <option value="Times">Times</option>
                                            <option value="Courier">Courier</option>
                                            <option value="Arial">Arial</option>
                                        </select>
                                    </div>

                                    {{-- Font Size --}}
                                    <div class="col-md-4">
                                        <label for="edit_font_size" class="form-label form-label-sm">Font Size (pt)</label>
                                        <input type="number" class="form-control form-control-sm" id="edit_font_size" name="font_size"
                                               step="0.5" min="4" max="72" placeholder="Default">
                                    </div>

                                    {{-- Text Align --}}
                                    <div class="col-md-4">
                                        <label for="edit_text_align" class="form-label form-label-sm">Text Align</label>
                                        <select class="form-select form-select-sm" id="edit_text_align" name="text_align">
                                            <option value="">Default</option>
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>

                                    {{-- Font Style --}}
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm d-block">Font Style</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="edit_style_bold" value="bold">
                                                <label class="form-check-label small fw-bold" for="edit_style_bold">B</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="edit_style_italic" value="italic">
                                                <label class="form-check-label small fst-italic" for="edit_style_italic">I</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="edit_style_underline" value="underline">
                                                <label class="form-check-label small text-decoration-underline" for="edit_style_underline">U</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Font Color --}}
                                    <div class="col-md-6">
                                        <label for="edit_font_color" class="form-label form-label-sm">Font Color</label>
                                        <div class="input-group input-group-sm">
                                            <input type="color" class="form-control form-control-color form-control-sm"
                                                   id="edit_font_color" name="font_color" value="#000000"
                                                   style="width: 38px; padding: 2px;">
                                            <input type="text" class="form-control form-control-sm font-color-text"
                                                   id="edit_font_color_text" value="#000000" maxlength="7"
                                                   pattern="^#[0-9A-Fa-f]{6}$" placeholder="#000000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Date Format (conditional) --}}
                        <div class="col-md-6" id="editDateFormatGroup" style="display: none;">
                            <label for="edit_date_format" class="form-label form-label-sm">Date Format</label>
                            <input type="text" class="form-control form-control-sm" id="edit_date_format" name="date_format"
                                   placeholder="d/m/Y">
                            <div class="form-text text-muted" style="font-size: 0.72rem;">
                                PHP date format. Examples: d/m/Y, Y-m-d, d F Y
                            </div>
                        </div>

                        {{-- Default Value --}}
                        <div class="col-md-6">
                            <label for="edit_default_value" class="form-label form-label-sm">Default Value</label>
                            <input type="text" class="form-control form-control-sm" id="edit_default_value" name="default_value"
                                   placeholder="Optional fallback value">
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-md-6">
                            <label for="edit_sort_order" class="form-label form-label-sm">Sort Order</label>
                            <input type="number" class="form-control form-control-sm" id="edit_sort_order" name="sort_order"
                                   min="0" step="1" placeholder="0">
                        </div>

                        {{-- Active --}}
                        <div class="col-md-6">
                            <label class="form-label form-label-sm d-block">Status</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" checked>
                                <label class="form-check-label small" for="edit_is_active">Active</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveField">
                        <i class="fa fa-save me-1"></i> Save Changes
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

    // ─── Configuration ────────────────────────────────────────────────
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var STORE_URL = '{{ route("cimsdocgen.fields.store", $page->id) }}';
    var UPDATE_URL_BASE = '{{ url("cims/document-generator/fields") }}';
    var DELETE_URL_BASE = '{{ url("cims/document-generator/fields") }}';

    // Client fields data for JS filtering
    var clientFieldsData = @json($clientFields);

    // Category-to-source mapping for filtering
    var sourceCategoryMap = {
        'client_master': ['Company Details', 'Tax & Compliance', 'Contact Details', 'SARS Representative', 'Banking Details'],
        'client_master_directors': ['Director Details', 'Partner Details'],
        'client_master_addresses': ['Contact Details'],
        'form_input': ['Form Input Fields']
    };

    // ─── Initialize Tooltips ──────────────────────────────────────────
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    // ─── Utility: Show AJAX Alert ─────────────────────────────────────
    function showAlert(type, message) {
        var container = document.getElementById('ajaxAlertContainer');
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i> ' +
            escapeHtml(message) +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        container.innerHTML = alertHtml;

        // Auto-dismiss: success after 4s, errors stay until manually closed
        if (type === 'success') {
            setTimeout(function() {
                var alert = container.querySelector('.alert');
                if (alert) {
                    var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                }
            }, 4000);
        }
    }

    // ─── Utility: Escape HTML ─────────────────────────────────────────
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ─── Utility: Get Font Styles from Checkboxes ─────────────────────
    function getFontStyles(prefix) {
        var styles = [];
        if (document.getElementById(prefix + '_style_bold').checked) styles.push('bold');
        if (document.getElementById(prefix + '_style_italic').checked) styles.push('italic');
        if (document.getElementById(prefix + '_style_underline').checked) styles.push('underline');
        return styles.join(',');
    }

    // ─── Utility: Set Font Style Checkboxes ───────────────────────────
    function setFontStyles(prefix, styleString) {
        var styles = styleString ? styleString.split(',') : [];
        document.getElementById(prefix + '_style_bold').checked = styles.indexOf('bold') !== -1;
        document.getElementById(prefix + '_style_italic').checked = styles.indexOf('italic') !== -1;
        document.getElementById(prefix + '_style_underline').checked = styles.indexOf('underline') !== -1;
    }

    // ─── Utility: Format Number ───────────────────────────────────────
    function formatNum(val, decimals) {
        if (val === null || val === undefined || val === '') return '&mdash;';
        return parseFloat(val).toFixed(decimals);
    }

    // ─── Utility: Get Source Badge Class ──────────────────────────────
    function getSourceBadgeClass(source) {
        return 'badge badge-source badge-source-' + (source || '');
    }

    // ─── Filter Field Name Options by Source ──────────────────────────
    function filterFieldNameOptions(selectEl, source) {
        var options = selectEl.querySelectorAll('option, optgroup');
        var allowedCategories = source && sourceCategoryMap[source] ? sourceCategoryMap[source] : null;

        // Show all optgroups and options first
        selectEl.querySelectorAll('optgroup').forEach(function(og) {
            og.style.display = '';
            og.querySelectorAll('option').forEach(function(opt) {
                opt.style.display = '';
                opt.disabled = false;
            });
        });

        // If a source is selected, filter by allowed categories
        if (allowedCategories) {
            selectEl.querySelectorAll('optgroup').forEach(function(og) {
                var groupLabel = og.getAttribute('label');
                if (allowedCategories.indexOf(groupLabel) === -1) {
                    og.style.display = 'none';
                    og.querySelectorAll('option').forEach(function(opt) {
                        opt.disabled = true;
                    });
                }
            });
        }

        // Reset selection if current selection is now hidden
        var selectedOption = selectEl.options[selectEl.selectedIndex];
        if (selectedOption && selectedOption.disabled) {
            selectEl.value = '';
        }
    }

    // ─── Auto-populate Label from Field Name ──────────────────────────
    function autoPopulateLabel(nameSelect, labelInput) {
        nameSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.label) {
                labelInput.value = selectedOption.dataset.label;
            }
        });
    }

    // ─── Show/Hide Date Format Based on Field Type ────────────────────
    function toggleDateFormat(typeSelect, dateFormatGroup) {
        typeSelect.addEventListener('change', function() {
            dateFormatGroup.style.display = this.value === 'date' ? '' : 'none';
        });
    }

    // ─── Sync Color Picker and Text Input ─────────────────────────────
    function syncColorInputs(colorInput, textInput) {
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });
        textInput.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    }

    // ─── Initialize Add Form Behaviors ────────────────────────────────
    var addFieldSource = document.getElementById('add_field_source');
    var addFieldName = document.getElementById('add_field_name');
    var addFieldLabel = document.getElementById('add_field_label');
    var addFieldType = document.getElementById('add_field_type');
    var addDateFormatGroup = document.getElementById('addDateFormatGroup');
    var addFontColor = document.getElementById('add_font_color');
    var addFontColorText = document.getElementById('add_font_color_text');

    // Filter field names on source change
    addFieldSource.addEventListener('change', function() {
        filterFieldNameOptions(addFieldName, this.value);
    });

    // Auto-populate label
    autoPopulateLabel(addFieldName, addFieldLabel);

    // Toggle date format
    toggleDateFormat(addFieldType, addDateFormatGroup);

    // Sync color inputs
    syncColorInputs(addFontColor, addFontColorText);

    // ─── Initialize Edit Modal Behaviors ──────────────────────────────
    var editFieldSource = document.getElementById('edit_field_source');
    var editFieldName = document.getElementById('edit_field_name');
    var editFieldLabel = document.getElementById('edit_field_label');
    var editFieldType = document.getElementById('edit_field_type');
    var editDateFormatGroup = document.getElementById('editDateFormatGroup');
    var editFontColor = document.getElementById('edit_font_color');
    var editFontColorText = document.getElementById('edit_font_color_text');

    // Filter field names on source change in edit modal
    editFieldSource.addEventListener('change', function() {
        filterFieldNameOptions(editFieldName, this.value);
    });

    // Auto-populate label in edit
    autoPopulateLabel(editFieldName, editFieldLabel);

    // Toggle date format in edit
    toggleDateFormat(editFieldType, editDateFormatGroup);

    // Sync color inputs in edit
    syncColorInputs(editFontColor, editFontColorText);

    // ─── Build Table Row HTML ─────────────────────────────────────────
    function buildFieldRow(field) {
        var fontInfo = '';
        if (field.font_family || field.font_size) {
            var fontTitle = (field.font_family || 'Default') + ' ' + (field.font_size ? field.font_size + 'pt' : '') + ' ' + (field.font_style || '') + ' ' + (field.font_color || '');
            fontInfo = '<span class="field-font-preview" title="' + escapeHtml(fontTitle.trim()) + '">' +
                escapeHtml(field.font_family || 'Default') +
                (field.font_size ? ' <span class="text-muted">' + field.font_size + 'pt</span>' : '') +
                '</span>';
        } else {
            fontInfo = '<span class="text-muted small">&mdash;</span>';
        }

        var sizeInfo = '';
        if (field.width || field.height) {
            sizeInfo = '<span class="field-size">' +
                (field.width ? parseFloat(field.width).toFixed(1) : '&mdash;') +
                ' &times; ' +
                (field.height ? parseFloat(field.height).toFixed(1) : '&mdash;') +
                '</span>';
        } else {
            sizeInfo = '<span class="text-muted small">&mdash;</span>';
        }

        var isChecked = field.is_active ? 'checked' : '';
        var fieldJson = JSON.stringify(field).replace(/"/g, '&quot;');

        return '<tr id="field-row-' + field.id + '" data-field-id="' + field.id + '">' +
            '<td class="text-muted small">&mdash;</td>' +
            '<td><span class="fw-semibold" style="font-size: 0.825rem;">' + escapeHtml(field.field_label) + '</span></td>' +
            '<td><span class="field-mono">' + escapeHtml(field.field_name) + '</span></td>' +
            '<td><span class="' + getSourceBadgeClass(field.field_source) + '">' + escapeHtml((field.field_source || '').replace(/_/g, ' ')) + '</span></td>' +
            '<td class="text-center"><span class="field-pos">' + parseFloat(field.pos_x).toFixed(2) + ', ' + parseFloat(field.pos_y).toFixed(2) + '</span></td>' +
            '<td class="text-center">' + sizeInfo + '</td>' +
            '<td>' + fontInfo + '</td>' +
            '<td class="text-center"><span class="badge bg-light text-dark border" style="font-size: 0.7rem;">' + escapeHtml(field.field_type || 'text') + '</span></td>' +
            '<td class="text-center">' +
                '<div class="form-check form-switch d-inline-block mb-0">' +
                    '<input class="form-check-input active-toggle" type="checkbox" data-field-id="' + field.id + '" ' + isChecked + '>' +
                '</div>' +
            '</td>' +
            '<td class="text-end">' +
                '<div class="d-flex gap-1 justify-content-end">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-field" data-field-id="' + field.id + '" data-field="' + fieldJson + '" title="Edit Field">' +
                        '<i class="fa fa-pencil-alt"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-field" data-field-id="' + field.id + '" data-field-label="' + escapeHtml(field.field_label) + '" title="Delete Field">' +
                        '<i class="fa fa-trash-alt"></i>' +
                    '</button>' +
                '</div>' +
            '</td>' +
            '</tr>';
    }

    // ─── Update Row Numbers ───────────────────────────────────────────
    function updateRowNumbers() {
        var rows = document.querySelectorAll('#fieldsTableBody tr[data-field-id]');
        rows.forEach(function(row, index) {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        document.getElementById('fieldCount').textContent = rows.length;
    }

    // ─── Remove Empty State Row ───────────────────────────────────────
    function removeEmptyState() {
        var emptyRow = document.getElementById('emptyStateRow');
        if (emptyRow) emptyRow.remove();
    }

    // ─── Show Empty State if No Rows ──────────────────────────────────
    function showEmptyStateIfNeeded() {
        var rows = document.querySelectorAll('#fieldsTableBody tr[data-field-id]');
        if (rows.length === 0) {
            var tbody = document.getElementById('fieldsTableBody');
            tbody.innerHTML = '<tr id="emptyStateRow">' +
                '<td colspan="10" class="text-center py-5">' +
                    '<div class="empty-state-icon mb-2"><i class="fa fa-map-marker-alt"></i></div>' +
                    '<h6 class="text-muted fw-normal">No field mappings yet</h6>' +
                    '<p class="text-muted small mb-0">Use the form on the right to add field mappings to this page.</p>' +
                '</td>' +
                '</tr>';
        }
    }

    // ─── AJAX: Add Field (POST) ───────────────────────────────────────
    document.getElementById('addFieldForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var btn = document.getElementById('btnAddField');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Adding...';

        var formData = {
            field_source: addFieldSource.value,
            field_name: addFieldName.value,
            field_label: addFieldLabel.value,
            field_type: addFieldType.value,
            pos_x: document.getElementById('add_pos_x').value,
            pos_y: document.getElementById('add_pos_y').value,
            width: document.getElementById('add_width').value || null,
            height: document.getElementById('add_height').value || null,
            font_family: document.getElementById('add_font_family').value || null,
            font_size: document.getElementById('add_font_size').value || null,
            font_style: getFontStyles('add') || null,
            font_color: addFontColorText.value !== '#000000' ? addFontColorText.value : null,
            text_align: document.getElementById('add_text_align').value || null,
            date_format: addFieldType.value === 'date' ? document.getElementById('add_date_format').value : null,
            default_value: document.getElementById('add_default_value').value || null
        };

        fetch(STORE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(function(response) {
            if (!response.ok) {
                return response.text().then(function(text) {
                    try { return JSON.parse(text); } catch(e) { throw new Error('Server error (HTTP ' + response.status + '): ' + text.substring(0, 200)); }
                });
            }
            return response.json();
        })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plus-circle me-1"></i> Add Field';

            if (data.success && data.field) {
                removeEmptyState();

                var tbody = document.getElementById('fieldsTableBody');
                tbody.insertAdjacentHTML('beforeend', buildFieldRow(data.field));

                updateRowNumbers();
                bindRowEvents();

                // Reset form
                document.getElementById('addFieldForm').reset();
                addFontColor.value = '#000000';
                addFontColorText.value = '#000000';
                addDateFormatGroup.style.display = 'none';

                showAlert('success', 'Field mapping "' + data.field.field_label + '" added successfully.');
            } else {
                var msg = data.message || 'Failed to add field mapping.';
                if (data.errors) {
                    var errorList = [];
                    for (var key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            errorList.push(data.errors[key].join(', '));
                        }
                    }
                    msg = errorList.join(' ');
                }
                Swal.fire({ icon: 'error', title: 'Add Field Error', text: msg, confirmButtonText: 'OK' });
            }
        })
        .catch(function(error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plus-circle me-1"></i> Add Field';
            Swal.fire({ icon: 'error', title: 'Add Field Error', text: error.message, confirmButtonText: 'OK' });
            console.error('Add field error:', error);
        });
    });

    // ─── AJAX: Edit Field (Open Modal) ────────────────────────────────
    function openEditModal(fieldData) {
        var field = typeof fieldData === 'string' ? JSON.parse(fieldData) : fieldData;

        document.getElementById('edit_field_id').value = field.id;
        editFieldSource.value = field.field_source || '';
        filterFieldNameOptions(editFieldName, field.field_source);
        editFieldName.value = field.field_name || '';
        editFieldLabel.value = field.field_label || '';
        editFieldType.value = field.field_type || 'text';
        document.getElementById('edit_pos_x').value = field.pos_x || '';
        document.getElementById('edit_pos_y').value = field.pos_y || '';
        document.getElementById('edit_width').value = field.width || '';
        document.getElementById('edit_height').value = field.height || '';
        document.getElementById('edit_font_family').value = field.font_family || '';
        document.getElementById('edit_font_size').value = field.font_size || '';
        document.getElementById('edit_text_align').value = field.text_align || '';
        document.getElementById('edit_date_format').value = field.date_format || '';
        document.getElementById('edit_default_value').value = field.default_value || '';
        document.getElementById('edit_sort_order').value = field.sort_order || 0;
        document.getElementById('edit_is_active').checked = field.is_active ? true : false;

        // Font style checkboxes
        setFontStyles('edit', field.font_style || '');

        // Font color
        var fontColor = field.font_color || '#000000';
        editFontColor.value = fontColor;
        editFontColorText.value = fontColor;

        // Show date format if type is date
        editDateFormatGroup.style.display = field.field_type === 'date' ? '' : 'none';

        // Expand font overrides if any font property is set
        var hasFontOverrides = field.font_family || field.font_size || field.font_style || (field.font_color && field.font_color !== '#000000') || field.text_align;
        var editFontCollapse = document.getElementById('editFontOverrides');
        if (hasFontOverrides) {
            editFontCollapse.classList.add('show');
        } else {
            editFontCollapse.classList.remove('show');
        }

        var modal = new bootstrap.Modal(document.getElementById('editFieldModal'));
        modal.show();
    }

    // ─── AJAX: Save Field Edit (PUT) ──────────────────────────────────
    document.getElementById('editFieldForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var fieldId = document.getElementById('edit_field_id').value;
        var btn = document.getElementById('btnSaveField');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';

        var formData = {
            field_source: editFieldSource.value,
            field_name: editFieldName.value,
            field_label: editFieldLabel.value,
            field_type: editFieldType.value,
            pos_x: document.getElementById('edit_pos_x').value,
            pos_y: document.getElementById('edit_pos_y').value,
            width: document.getElementById('edit_width').value || null,
            height: document.getElementById('edit_height').value || null,
            font_family: document.getElementById('edit_font_family').value || null,
            font_size: document.getElementById('edit_font_size').value || null,
            font_style: getFontStyles('edit') || null,
            font_color: editFontColorText.value !== '#000000' ? editFontColorText.value : null,
            text_align: document.getElementById('edit_text_align').value || null,
            date_format: editFieldType.value === 'date' ? document.getElementById('edit_date_format').value : null,
            default_value: document.getElementById('edit_default_value').value || null,
            sort_order: document.getElementById('edit_sort_order').value || 0,
            is_active: document.getElementById('edit_is_active').checked ? 1 : 0
        };

        fetch(UPDATE_URL_BASE + '/' + fieldId, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save me-1"></i> Save Changes';

            if (data.success && data.field) {
                // Update the row in the table
                var existingRow = document.getElementById('field-row-' + fieldId);
                if (existingRow) {
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = '<table><tbody>' + buildFieldRow(data.field) + '</tbody></table>';
                    var newRow = tempDiv.querySelector('tr');
                    existingRow.parentNode.replaceChild(newRow, existingRow);
                }

                updateRowNumbers();
                bindRowEvents();

                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('editFieldModal'));
                if (modal) modal.hide();

                showAlert('success', 'Field mapping "' + data.field.field_label + '" updated successfully.');
            } else {
                var msg = data.message || 'Failed to update field mapping.';
                if (data.errors) {
                    var errorList = [];
                    for (var key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            errorList.push(data.errors[key].join(', '));
                        }
                    }
                    msg = errorList.join(' ');
                }
                showAlert('danger', msg);
            }
        })
        .catch(function(error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save me-1"></i> Save Changes';
            showAlert('danger', 'An error occurred while saving. Please try again.');
            console.error('Edit field error:', error);
        });
    });

    // ─── AJAX: Delete Field (DELETE) ──────────────────────────────────
    function deleteField(fieldId, fieldLabel) {
        if (!confirm('Are you sure you want to delete the field mapping "' + fieldLabel + '"?\n\nThis action cannot be undone.')) {
            return;
        }

        fetch(DELETE_URL_BASE + '/' + fieldId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                var row = document.getElementById('field-row-' + fieldId);
                if (row) {
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-10px)';
                    setTimeout(function() {
                        row.remove();
                        updateRowNumbers();
                        showEmptyStateIfNeeded();
                    }, 300);
                }
                showAlert('success', 'Field mapping deleted successfully.');
            } else {
                showAlert('danger', data.message || 'Failed to delete field mapping.');
            }
        })
        .catch(function(error) {
            showAlert('danger', 'An error occurred while deleting. Please try again.');
            console.error('Delete field error:', error);
        });
    }

    // ─── AJAX: Toggle Active Status ───────────────────────────────────
    function toggleActiveStatus(fieldId, isActive) {
        fetch(UPDATE_URL_BASE + '/' + fieldId, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: isActive ? 1 : 0 })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (!data.success) {
                showAlert('danger', data.message || 'Failed to update status.');
            }
        })
        .catch(function(error) {
            showAlert('danger', 'An error occurred while updating status.');
            console.error('Toggle active error:', error);
        });
    }

    // ─── Bind Events to Table Rows ────────────────────────────────────
    function bindRowEvents() {
        // Edit buttons
        document.querySelectorAll('.btn-edit-field').forEach(function(btn) {
            btn.onclick = function() {
                var tooltip = bootstrap.Tooltip.getInstance(this);
                if (tooltip) tooltip.hide();
                openEditModal(this.dataset.field);
            };
        });

        // Delete buttons
        document.querySelectorAll('.btn-delete-field').forEach(function(btn) {
            btn.onclick = function() {
                var tooltip = bootstrap.Tooltip.getInstance(this);
                if (tooltip) tooltip.hide();
                deleteField(this.dataset.fieldId, this.dataset.fieldLabel);
            };
        });

        // Active toggles
        document.querySelectorAll('.active-toggle').forEach(function(toggle) {
            toggle.onchange = function() {
                toggleActiveStatus(this.dataset.fieldId, this.checked);
            };
        });

        // Re-init tooltips for new elements
        var newTooltips = [].slice.call(document.querySelectorAll('[title]:not([data-bs-original-title])'));
        newTooltips.forEach(function(el) {
            if (el.getAttribute('title')) {
                new bootstrap.Tooltip(el);
            }
        });
    }

    // ─── Initial Event Binding ────────────────────────────────────────
    bindRowEvents();

    // ─── Collapse Toggle Icon Rotation ────────────────────────────────
    document.querySelectorAll('.font-overrides-toggle').forEach(function(toggle) {
        var targetId = toggle.getAttribute('data-bs-target');
        var targetEl = document.querySelector(targetId);
        if (targetEl) {
            targetEl.addEventListener('show.bs.collapse', function() {
                toggle.classList.remove('collapsed');
            });
            targetEl.addEventListener('hide.bs.collapse', function() {
                toggle.classList.add('collapsed');
            });
            // Set initial state
            if (!targetEl.classList.contains('show')) {
                toggle.classList.add('collapsed');
            }
        }
    });

    // ─── Auto-dismiss Flash Messages After 5 Seconds ──────────────────
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

});
</script>
@endpush
