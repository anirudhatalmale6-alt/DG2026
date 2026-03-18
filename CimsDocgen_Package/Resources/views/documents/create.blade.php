@extends('smartdash::layouts.default')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            {{-- Card Header --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-magic text-primary"></i> Generate New Document
                    </h4>
                    <a href="{{ route('docgen.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Documents
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

            {{-- Session Messages --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="generateForm" method="POST" action="{{ route('docgen.generate') }}">
                @csrf

                {{-- ============================================================ --}}
                {{-- SECTION 1: Select Client --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">1</span>
                            <h5 class="mb-0">Select Client</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">

                        {{-- Search Input --}}
                        <div class="mb-3">
                            <label for="clientSearch" class="form-label">Search Client <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                                    <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="clientSearch"
                                        placeholder="Type company name, client code, or trading name..."
                                        autocomplete="off"
                                    >
                                    <span class="input-group-text bg-white" id="clientSearchSpinner" style="display: none;">
                                        <i class="fa fa-spinner fa-spin text-primary"></i>
                                    </span>
                                </div>

                                {{-- Dropdown Results --}}
                                <div id="clientDropdown" class="dropdown-menu w-100 shadow-sm" style="display: none; max-height: 300px; overflow-y: auto;">
                                    {{-- Results injected by JS --}}
                                </div>
                            </div>
                            <div class="form-text text-muted">Start typing to search clients from the database.</div>
                        </div>

                        {{-- Client Info Summary Card --}}
                        <div id="clientInfoCard" class="card border-success bg-light mt-3" style="display: none;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-success mb-3"><i class="fa fa-check-circle"></i> Client Selected</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Company Name</small>
                                                <strong id="infoCompanyName">-</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Client Code</small>
                                                <strong id="infoClientCode">-</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Trading Name</small>
                                                <strong id="infoTradingName">-</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Registration Number</small>
                                                <strong id="infoRegNumber">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearClient" title="Clear selection">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- SECTION 2: Select Template --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">2</span>
                            <h5 class="mb-0">Select Template</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($templates->isEmpty())
                            <div class="text-center py-4">
                                <i class="fa fa-file-alt fa-3x text-muted mb-3" style="display: block;"></i>
                                <p class="text-muted mb-0">No active templates available. Please create a template first.</p>
                                <a href="{{ route('docgen.templates.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fa fa-plus"></i> Create Template
                                </a>
                            </div>
                        @else
                            <p class="text-muted mb-3">Choose a document template to generate.</p>
                            <div class="row g-3">
                                @foreach($templates as $template)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card template-card h-100 border-2 {{ old('template_id') == $template->id ? 'border-primary bg-primary bg-opacity-10' : '' }}" data-template-id="{{ $template->id }}" style="cursor: pointer; transition: all 0.2s ease;">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start">
                                                    <div class="form-check me-2 mt-1">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="template_id"
                                                            id="template_{{ $template->id }}"
                                                            value="{{ $template->id }}"
                                                            {{ old('template_id') == $template->id ? 'checked' : '' }}
                                                            required
                                                        >
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-check-label fw-bold d-block mb-1" for="template_{{ $template->id }}" style="cursor: pointer;">
                                                            {{ $template->name }}
                                                        </label>
                                                        <span class="badge bg-secondary bg-opacity-25 text-secondary mb-2">{{ $template->code }}</span>
                                                        @if($template->description)
                                                            <p class="text-muted small mb-0">{{ $template->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- SECTION 3: Document Details --}}
                {{-- ============================================================ --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">3</span>
                            <h5 class="mb-0">Document Details</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Document Name --}}
                            <div class="col-md-8">
                                <label for="document_name" class="form-label">Document Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('document_name') is-invalid @enderror"
                                    id="document_name"
                                    name="document_name"
                                    value="{{ old('document_name') }}"
                                    placeholder="Enter a descriptive name for this document"
                                    required
                                >
                                @error('document_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Document Date --}}
                            <div class="col-md-4">
                                <label for="document_date" class="form-label">Document Date</label>
                                <input
                                    type="date"
                                    class="form-control @error('document_date') is-invalid @enderror"
                                    id="document_date"
                                    name="document_date"
                                    value="{{ old('document_date', date('Y-m-d')) }}"
                                >
                                @error('document_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            {{-- Requested By --}}
                            <div class="col-md-6">
                                <label for="requested_by" class="form-label">Requested By</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="requested_by"
                                    name="requested_by"
                                    value="{{ old('requested_by') }}"
                                    placeholder="Person who requested the document"
                                >
                            </div>

                            {{-- Prepared By --}}
                            <div class="col-md-6">
                                <label for="prepared_by" class="form-label">Prepared By</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="prepared_by"
                                    name="prepared_by"
                                    value="{{ old('prepared_by') }}"
                                    placeholder="Person who prepared the document"
                                >
                            </div>

                            {{-- Approved By --}}
                            <div class="col-md-6">
                                <label for="approved_by" class="form-label">Approved By</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="approved_by"
                                    name="approved_by"
                                    value="{{ old('approved_by') }}"
                                    placeholder="Person who approved the document"
                                >
                            </div>

                            {{-- Signed By --}}
                            <div class="col-md-6">
                                <label for="signed_by" class="form-label">Signed By</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="signed_by"
                                    name="signed_by"
                                    value="{{ old('signed_by') }}"
                                    placeholder="Person who signed the document"
                                >
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
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;">4</span>
                            <h5 class="mb-0">Generate</h5>
                        </div>
                    </div>
                    <div class="card-body text-center py-4">
                        <p class="text-muted mb-4">Review the information above, then click the button below to generate your PDF document.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('docgen.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnGenerate">
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

    // ─── Elements ────────────────────────────────────────────────
    var clientSearch = document.getElementById('clientSearch');
    var clientDropdown = document.getElementById('clientDropdown');
    var clientIdInput = document.getElementById('client_id');
    var clientInfoCard = document.getElementById('clientInfoCard');
    var clientSearchSpinner = document.getElementById('clientSearchSpinner');
    var btnClearClient = document.getElementById('btnClearClient');

    var infoCompanyName = document.getElementById('infoCompanyName');
    var infoClientCode = document.getElementById('infoClientCode');
    var infoTradingName = document.getElementById('infoTradingName');
    var infoRegNumber = document.getElementById('infoRegNumber');

    var debounceTimer = null;

    // ─── AJAX Client Search with Debounce ────────────────────────
    clientSearch.addEventListener('input', function() {
        var query = this.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            clientDropdown.style.display = 'none';
            clientSearchSpinner.style.display = 'none';
            return;
        }

        clientSearchSpinner.style.display = '';

        debounceTimer = setTimeout(function() {
            fetch('{{ route("docgen.api.clients") }}?q=' + encodeURIComponent(query), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(clients) {
                clientSearchSpinner.style.display = 'none';
                renderClientDropdown(clients);
            })
            .catch(function(error) {
                clientSearchSpinner.style.display = 'none';
                console.error('Client search error:', error);
            });
        }, 350);
    });

    function renderClientDropdown(clients) {
        clientDropdown.innerHTML = '';

        if (!clients || clients.length === 0) {
            clientDropdown.innerHTML = '<div class="dropdown-item text-muted py-3 text-center"><i class="fa fa-info-circle"></i> No clients found</div>';
            clientDropdown.style.display = 'block';
            return;
        }

        clients.forEach(function(client) {
            var item = document.createElement('a');
            item.href = '#';
            item.className = 'dropdown-item py-2';
            item.innerHTML =
                '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                        '<strong>' + escapeHtml(client.company_name) + '</strong>' +
                        (client.trading_name ? '<br><small class="text-muted">T/A: ' + escapeHtml(client.trading_name) + '</small>' : '') +
                    '</div>' +
                    '<span class="badge bg-light text-dark">' + escapeHtml(client.client_code) + '</span>' +
                '</div>';

            item.addEventListener('click', function(e) {
                e.preventDefault();
                selectClient(client.client_id);
                clientDropdown.style.display = 'none';
            });

            clientDropdown.appendChild(item);
        });

        clientDropdown.style.display = 'block';
    }

    // ─── Select Client & Load Full Details ──────────────────────
    function selectClient(clientId) {
        clientSearch.value = '';
        clientSearchSpinner.style.display = '';

        var url = '{{ route("docgen.api.client", ":id") }}'.replace(':id', clientId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(client) {
            clientSearchSpinner.style.display = 'none';

            if (client.error) {
                alert('Client not found.');
                return;
            }

            // Set hidden input value
            clientIdInput.value = client.client_id;

            // Populate info card
            infoCompanyName.textContent = client.company_name || '-';
            infoClientCode.textContent = client.client_code || '-';
            infoTradingName.textContent = client.trading_name || '-';
            infoRegNumber.textContent = client.company_reg_number || '-';

            // Show info card, hide search
            clientInfoCard.style.display = 'block';
            clientSearch.closest('.mb-3').style.display = 'none';
        })
        .catch(function(error) {
            clientSearchSpinner.style.display = 'none';
            console.error('Client detail error:', error);
            alert('Error loading client details. Please try again.');
        });
    }

    // ─── Clear Client Selection ─────────────────────────────────
    btnClearClient.addEventListener('click', function() {
        clientIdInput.value = '';
        clientInfoCard.style.display = 'none';
        clientSearch.closest('.mb-3').style.display = 'block';
        clientSearch.value = '';
        clientSearch.focus();
    });

    // ─── Close Dropdown on Outside Click ────────────────────────
    document.addEventListener('click', function(e) {
        if (!clientSearch.contains(e.target) && !clientDropdown.contains(e.target)) {
            clientDropdown.style.display = 'none';
        }
    });

    // ─── Restore Client on Validation Failure ───────────────────
    @if(old('client_id'))
        selectClient('{{ old("client_id") }}');
    @endif

    // ─── Template Card Selection ────────────────────────────────
    document.querySelectorAll('.template-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            // Remove highlight from all cards
            document.querySelectorAll('.template-card').forEach(function(c) {
                c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            });

            // Highlight selected card
            this.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    // ─── Form Submission Validation ─────────────────────────────
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        var clientId = clientIdInput.value;
        var templateSelected = document.querySelector('input[name="template_id"]:checked');
        var documentName = document.getElementById('document_name').value.trim();

        var errors = [];

        if (!clientId) {
            errors.push('Please select a client.');
        }
        if (!templateSelected) {
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

    // ─── Utility: Escape HTML ───────────────────────────────────
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

});
</script>
@endpush
@endsection
