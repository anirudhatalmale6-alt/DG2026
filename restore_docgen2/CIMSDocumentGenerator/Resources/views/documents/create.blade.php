@extends('layouts.default')

@push('styles')
<style>
/* DocGen Generate Page - Teal Theme consistent with CIMS Persons */
.docgen-card {
    border: 1px solid #17A2B8;
    border-radius: 10px;
    transition: all 0.3s ease;
}
.docgen-card:hover {
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.15);
}
.docgen-card .card-header {
    background: linear-gradient(135deg, #f8fdfe, #eaf7f9) !important;
    border-bottom: 2px solid #17A2B8;
    border-radius: 10px 10px 0 0 !important;
}
.docgen-card .card-header h5 {
    color: #0d3d56;
    font-weight: 600;
    font-size: 1rem;
}
.docgen-card .card-header .badge {
    background: linear-gradient(135deg, #17A2B8, #138496) !important;
    font-weight: 700;
}
.docgen-page-title {
    color: #0d3d56;
    font-weight: 700;
    font-size: 1.4rem;
}
.docgen-page-title i {
    color: #17A2B8;
}
.btn-back-docs {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px 20px;
    box-shadow: 0 3px 10px rgba(23, 162, 184, 0.25);
}
.btn-back-docs:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: #fff;
    transform: translateY(-1px);
}
.btn-generate {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: 1.05rem;
    border-radius: 8px;
    padding: 12px 35px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}
.btn-generate:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: #fff;
    transform: translateY(-2px);
}
.form-select:focus, .form-control:focus {
    border-color: #17A2B8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}
.form-label {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}
#clientInfoCard {
    border-color: #17A2B8 !important;
    background: linear-gradient(135deg, #f8fdfe, #eaf7f9) !important;
}
#clientInfoCard h6 {
    color: #17A2B8 !important;
    font-weight: 700;
}
.sig-preview img {
    border-color: #17A2B8 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 docgen-page-title">
                    <i class="fa fa-magic"></i> Generate New Document
                </h4>
                <a href="{{ route('cimsdocgen.index') }}" class="btn btn-back-docs btn-sm">
                    <i class="fa fa-arrow-left"></i> Back to Documents
                </a>
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

            {{-- Session Messages --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="generateForm" method="POST" action="{{ route('cimsdocgen.generate') }}">
                @csrf

                {{-- ============================================================ --}}
                {{-- SECTION 1: Select Client --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4 docgen-card">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">1</span>
                            <h5 class="mb-0">Select Client</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg @error('client_id') is-invalid @enderror"
                                    id="client_id" name="client_id" required>
                                <option value="">-- Select a Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}"
                                            data-code="{{ $client->client_code }}"
                                            data-reg="{{ $client->company_reg_number }}"
                                            data-regdate="{{ $client->company_reg_date }}"
                                            data-tax="{{ $client->tax_number }}"
                                            {{ old('client_id') == $client->client_id ? 'selected' : '' }}>
                                        {{ $client->company_name }} ({{ $client->client_code }}){{ $client->trading_name ? ' - T/A: ' . $client->trading_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Client Info Card --}}
                        <div id="clientInfoCard" class="card border-success bg-light" style="display: none;">
                            <div class="card-body py-3">
                                <h6 class="text-success mb-3"><i class="fa fa-check-circle"></i> Client Details</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <small class="text-muted d-block">Client Code</small>
                                        <strong id="infoClientCode">-</strong>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <small class="text-muted d-block">Registration Number</small>
                                        <strong id="infoRegNumber">-</strong>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <small class="text-muted d-block">Date of Registration</small>
                                        <strong id="infoRegDate">-</strong>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <small class="text-muted d-block">Income Tax Number</small>
                                        <strong id="infoTaxNumber">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- SECTION 2: Select Template --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4 docgen-card">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">2</span>
                            <h5 class="mb-0">Select Template</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Category Dropdown --}}
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Document Category <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" id="category_id" required>
                                    <option value="">-- Select a Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Template Dropdown (filtered by category) --}}
                            <div class="col-md-6">
                                <label for="template_id" class="form-label">Document Template <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg @error('template_id') is-invalid @enderror"
                                        id="template_id" name="template_id" required>
                                    <option value="">-- Select a Category first --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}"
                                                data-category="{{ $template->category_id }}"
                                                data-title="{{ $template->name }}"
                                                {{ old('template_id') == $template->id ? 'selected' : '' }}
                                                style="display: none;">
                                            {{ $template->name }} ({{ $template->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('template_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- SECTION 3: Document Details --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4 docgen-card">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">3</span>
                            <h5 class="mb-0">Document Details</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Row 1: Document Name (full width) --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label for="document_name" class="form-label">Document Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('document_name') is-invalid @enderror"
                                    id="document_name"
                                    name="document_name"
                                    value="{{ old('document_name') }}"
                                    placeholder="Auto-generated from selections below"
                                    readonly
                                    style="background-color: #f8f9fa;"
                                    required
                                >
                                @error('document_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 2: Date, Time, Period --}}
                        <div class="row g-3">
                            {{-- Document Date --}}
                            <div class="col-md-4">
                                <label for="document_date" class="form-label">Document Date</label>
                                <input
                                    type="date"
                                    class="form-control @error('document_date') is-invalid @enderror"
                                    id="document_date"
                                    name="document_date"
                                    value="{{ old('document_date', date('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}"
                                    required
                                >
                                <div id="document_date_display" class="form-text" style="font-size: 0.8rem;"></div>
                                @error('document_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Document Time --}}
                            <div class="col-md-4">
                                <label for="document_time" class="form-label">Document Time</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="document_time"
                                    name="document_time"
                                    value=""
                                    readonly
                                    style="background-color: #f8f9fa;"
                                >
                            </div>

                            {{-- Period --}}
                            <div class="col-md-4">
                                <label for="period_id" class="form-label">Period</label>
                                <select class="form-select @error('period_id') is-invalid @enderror"
                                        id="period_id" name="period_id">
                                    <option value="">-- Select Period --</option>
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}"
                                                {{ old('period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->period_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('period_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            {{-- Requested By --}}
                            <div class="col-md-6">
                                <label for="requested_by" class="form-label">Requested By</label>
                                <select class="form-select person-select" id="requested_by" name="requested_by">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('requested_by') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_requested_by" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>

                            {{-- Prepared By --}}
                            <div class="col-md-6">
                                <label for="prepared_by" class="form-label">Prepared By</label>
                                <select class="form-select person-select" id="prepared_by" name="prepared_by">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('prepared_by') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_prepared_by" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>

                            {{-- Approved By --}}
                            <div class="col-md-6">
                                <label for="approved_by" class="form-label">Approved By</label>
                                <select class="form-select person-select" id="approved_by" name="approved_by">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('approved_by') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_approved_by" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>

                            {{-- Signed By --}}
                            <div class="col-md-6">
                                <label for="signed_by" class="form-label">Signed By</label>
                                <select class="form-select person-select" id="signed_by" name="signed_by">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('signed_by') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_signed_by" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Row: Witnesses --}}
                        <div class="row g-3">
                            {{-- Witness 1 --}}
                            <div class="col-md-6">
                                <label for="witness_1" class="form-label">Witness 1</label>
                                <select class="form-select person-select" id="witness_1" name="witness_1">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('witness_1') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_witness_1" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>

                            {{-- Witness 2 --}}
                            <div class="col-md-6">
                                <label for="witness_2" class="form-label">Witness 2</label>
                                <select class="form-select person-select" id="witness_2" name="witness_2">
                                    <option value="">-- Select Person --</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}"
                                                data-sig="{{ $person->signature_upload ?: $person->signature_image }}"
                                                {{ old('witness_2') == $person->id ? 'selected' : '' }}>
                                            {{ $person->title }} {{ $person->firstname }} {{ $person->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="sig-preview mt-2" id="sig_witness_2" style="display:none;">
                                    <small class="text-muted d-block mb-1">Signature:</small>
                                    <img src="" alt="Signature" style="max-height:60px; border:1px solid #ddd; border-radius:4px; padding:4px; background:#fff;">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Notes --}}
                        <div class="mb-0">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Optional notes or remarks for this document..."
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- SECTION 4: Generate --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4 docgen-card">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">4</span>
                            <h5 class="mb-0">Generate</h5>
                        </div>
                    </div>
                    <div class="card-body text-center py-4">
                        <p class="text-muted mb-4">Review the information above, then click the button below to generate your PDF document.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('cimsdocgen.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-generate btn-lg px-5" id="btnGenerate">
                                <i class="fa fa-magic me-2"></i> Generate Document
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

    // ─── Live Clock for Document Time ────────────────────────────
    var timeField = document.getElementById('document_time');
    function updateTime() {
        var now = new Date();
        var hours = now.getHours();
        var mins = now.getMinutes();
        var secs = now.getSeconds();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        if (hours === 0) hours = 12;
        var h = (hours < 10 ? '0' : '') + hours;
        var m = (mins < 10 ? '0' : '') + mins;
        var s = (secs < 10 ? '0' : '') + secs;
        timeField.value = h + ' ' + m + ' ' + s + ' ' + ampm;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // ─── Date Picker → Show Formatted Date ───────────────────────
    var dateField = document.getElementById('document_date');
    var dateDisplay = document.getElementById('document_date_display');
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function formatDateDisplay() {
        if (dateField.value) {
            var parts = dateField.value.split('-');
            var d = new Date(parts[0], parts[1] - 1, parts[2]);
            dateDisplay.textContent = days[d.getDay()] + ', ' + String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        } else {
            dateDisplay.textContent = '';
        }
    }
    // Date change is handled below in the buildDocumentName section
    formatDateDisplay();

    // ─── Client Dropdown Change → Show Details ─────────────────
    var clientSelect = document.getElementById('client_id');
    var clientInfoCard = document.getElementById('clientInfoCard');

    function showClientDetails() {
        var selected = clientSelect.options[clientSelect.selectedIndex];
        if (clientSelect.value) {
            document.getElementById('infoClientCode').textContent = selected.dataset.code || '-';
            document.getElementById('infoRegNumber').textContent = selected.dataset.reg || '-';
            document.getElementById('infoRegDate').textContent = selected.dataset.regdate || '-';
            document.getElementById('infoTaxNumber').textContent = selected.dataset.tax || '-';
            clientInfoCard.style.display = 'block';
        } else {
            clientInfoCard.style.display = 'none';
        }
    }

    // Client change is handled below in the buildDocumentName section
    if (clientSelect.value) { showClientDetails(); }

    // ─── Person Dropdown → Show Signature ──────────────────────
    document.querySelectorAll('.person-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var sigDiv = document.getElementById('sig_' + this.id);
            if (!sigDiv) return;
            var selected = this.options[this.selectedIndex];
            var sigPath = selected.dataset.sig;
            if (this.value && sigPath) {
                sigDiv.querySelector('img').src = '/storage/' + sigPath;
                sigDiv.style.display = 'block';
            } else if (this.value && !sigPath) {
                sigDiv.innerHTML = '<small class="text-muted"><i class="fa fa-info-circle"></i> No signature on file for this person</small>';
                sigDiv.style.display = 'block';
            } else {
                sigDiv.style.display = 'none';
            }
        });
        // Trigger on load for pre-selected values
        if (sel.value) { sel.dispatchEvent(new Event('change')); }
    });

    // ─── Category → Template Filtering ─────────────────────────
    var categorySelect = document.getElementById('category_id');
    var templateSelect = document.getElementById('template_id');
    var allTemplateOptions = [];

    // Store all template options data
    for (var i = 0; i < templateSelect.options.length; i++) {
        var opt = templateSelect.options[i];
        if (opt.value) {
            allTemplateOptions.push({
                value: opt.value,
                text: opt.text,
                title: opt.dataset.title || '',
                categoryId: opt.dataset.category,
                selected: opt.selected
            });
        }
    }

    function filterTemplates() {
        var selectedCategory = categorySelect.value;

        // Clear template dropdown
        templateSelect.innerHTML = '';

        if (!selectedCategory) {
            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '-- Select a Category first --';
            templateSelect.appendChild(placeholder);
            return;
        }

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- Select a Template --';
        templateSelect.appendChild(placeholder);

        var count = 0;
        allTemplateOptions.forEach(function(tpl) {
            if (tpl.categoryId === selectedCategory) {
                var opt = document.createElement('option');
                opt.value = tpl.value;
                opt.textContent = tpl.text;
                opt.dataset.title = tpl.title;
                if (tpl.selected) { opt.selected = true; }
                templateSelect.appendChild(opt);
                count++;
            }
        });

        if (count === 0) {
            var noMatch = document.createElement('option');
            noMatch.value = '';
            noMatch.textContent = '-- No templates in this category --';
            noMatch.disabled = true;
            templateSelect.innerHTML = '';
            templateSelect.appendChild(noMatch);
        }
    }

    categorySelect.addEventListener('change', function() {
        filterTemplates();
        buildDocumentName();
    });

    templateSelect.addEventListener('change', buildDocumentName);

    // On page load, if category was pre-selected (validation failure), filter templates
    if (categorySelect.value) { filterTemplates(); }

    // ─── Auto-generate Document Name ─────────────────────────────
    // Format: "CLIENT_CODE - Template Title [Category] { Period } Day DD Mon YYYY HHMMSS.pdf"
    var documentNameField = document.getElementById('document_name');
    var periodSelect = document.getElementById('period_id');

    function buildDocumentName() {
        var parts = [];

        // Client code
        var clientOpt = clientSelect.options[clientSelect.selectedIndex];
        var clientCode = (clientSelect.value && clientOpt) ? (clientOpt.dataset.code || '') : '';

        // Template title
        var tplOpt = templateSelect.options[templateSelect.selectedIndex];
        var tplTitle = (templateSelect.value && tplOpt) ? (tplOpt.dataset.title || '') : '';

        // Category name
        var catOpt = categorySelect.options[categorySelect.selectedIndex];
        var catName = (categorySelect.value && catOpt) ? catOpt.textContent.trim() : '';

        // Period name
        var perOpt = periodSelect.options[periodSelect.selectedIndex];
        var perName = (periodSelect.value && perOpt) ? perOpt.textContent.trim() : '';

        // Date formatted as "Tue 16 Jan 2026"
        var datePart = '';
        if (dateField.value) {
            var dp = dateField.value.split('-');
            var dd = new Date(dp[0], dp[1] - 1, dp[2]);
            datePart = days[dd.getDay()] + ' ' + String(dd.getDate()).padStart(2, '0') + ' ' + months[dd.getMonth()] + ' ' + dd.getFullYear();
        }

        // Time as "HHMMSS" (compact, no spaces)
        var now = new Date();
        var hh = String(now.getHours()).padStart(2, '0');
        var mm = String(now.getMinutes()).padStart(2, '0');
        var ss = String(now.getSeconds()).padStart(2, '0');
        var timePart = hh + mm + ss;

        // Build: "CLIENT_CODE - Template Title [Category] { Period } Date Time.pdf"
        var name = '';
        if (clientCode) name += clientCode;
        if (tplTitle) name += (name ? ' - ' : '') + tplTitle;
        if (catName) name += ' [' + catName + ']';
        if (perName) name += ' { ' + perName + ' }';
        if (datePart) name += ' ' + datePart;
        if (timePart) name += ' ' + timePart;
        if (name) name += '.pdf';

        documentNameField.value = name;
    }

    // Wire up all fields to trigger name rebuild
    clientSelect.addEventListener('change', function() {
        showClientDetails();
        buildDocumentName();
    });
    dateField.addEventListener('change', function() {
        formatDateDisplay();
        buildDocumentName();
    });
    periodSelect.addEventListener('change', buildDocumentName);

    // Build on page load if fields are pre-selected
    buildDocumentName();

    // ─── Form Submission Validation ─────────────────────────────
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        var clientId = document.getElementById('client_id').value;
        var templateId = document.getElementById('template_id').value;
        var documentName = document.getElementById('document_name').value.trim();

        var errors = [];

        if (!clientId) {
            errors.push('Please select a client.');
        }
        if (!templateId) {
            errors.push('Please select a document template.');
        }
        if (!documentName) {
            errors.push('Please enter a document name.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
            return;
        }

        // Disable button to prevent double-submit
        var btn = document.getElementById('btnGenerate');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Generating...';
    });

});
</script>
@endpush
@endsection
