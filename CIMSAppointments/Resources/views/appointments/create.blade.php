@extends('layouts.default')

@section('title', 'Book Appointment')

@push('styles')
<style>
.booking-card { border-radius: 12px; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
.booking-card .card-header { background: linear-gradient(135deg, #0d3d56, #17A2B8); color: #fff; border-radius: 12px 12px 0 0 !important; }
.step-indicator { display: flex; gap: 10px; margin-bottom: 25px; }
.step-indicator .step { flex: 1; text-align: center; padding: 10px; border-radius: 8px; background: #e9ecef; font-size: 13px; font-weight: 600; color: #999; }
.step-indicator .step.active { background: #17A2B8; color: #fff; }
.step-indicator .step.done { background: #28a745; color: #fff; }
.client-search-results { max-height: 250px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; display: none; }
.client-search-results .client-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
.client-search-results .client-item:hover { background: #f0f8ff; }
.client-search-results .client-item .client-code { font-weight: 700; color: #17A2B8; font-size: 12px; }
.time-slots-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.time-slot-btn { padding: 10px 18px; border: 2px solid #17A2B8; border-radius: 8px; background: #fff; color: #17A2B8; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.time-slot-btn:hover { background: #e0f7fa; }
.time-slot-btn.selected { background: #17A2B8; color: #fff; }
.time-slot-btn:disabled { border-color: #ddd; color: #ccc; cursor: not-allowed; }
.price-display { font-size: 24px; font-weight: 700; color: #28a745; }
.new-client-form { display: none; padding: 20px; background: #f8f9fa; border-radius: 10px; margin-top: 15px; }
.section-divider { border-top: 2px solid #e9ecef; margin: 25px 0; padding-top: 20px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Book Appointment</li>
        </ol>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-xxl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => '', 'counts' => ['pending' => 0]])
        </div>

        <div class="col-xl-9 col-xxl-9">
            <div class="card booking-card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-calendar-plus me-2"></i>Book New Appointment</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsappointments.appointments.store') }}" id="bookingForm">
                        @csrf

                        {{-- STEP 1: Client Selection --}}
                        <h5 class="mb-3"><i class="fas fa-user me-2 text-primary"></i>1. Select Client</h5>

                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="client_source" id="clientExisting" value="existing" checked onchange="toggleClientForm()">
                                <label class="form-check-label" for="clientExisting">Existing Client</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="client_source" id="clientNew" value="new" onchange="toggleClientForm()">
                                <label class="form-check-label" for="clientNew">New Client / Lead</label>
                            </div>
                        </div>

                        {{-- Existing Client Search --}}
                        <div id="existingClientSection">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Search Client</label>
                                <input type="text" class="form-control" id="clientSearch" placeholder="Type company name, client code, email or phone..." autocomplete="off"
                                    value="{{ $selectedClient ? $selectedClient->company_name : old('client_search') }}">
                                <input type="hidden" name="client_id" id="selectedClientId" value="{{ $selectedClient ? $selectedClient->client_id : old('client_id') }}">
                                <div class="client-search-results" id="clientResults"></div>
                            </div>
                            @if($selectedClient)
                                <div class="alert alert-info" id="selectedClientInfo">
                                    <strong>{{ $selectedClient->client_code }}</strong> - {{ $selectedClient->company_name }}
                                    @if($selectedClient->email) | {{ $selectedClient->email }}@endif
                                </div>
                            @else
                                <div class="alert alert-info" id="selectedClientInfo" style="display:none;"></div>
                            @endif
                        </div>

                        {{-- New Client Form --}}
                        <div class="new-client-form" id="newClientSection">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Company / Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="new_client_name" class="form-control" value="{{ old('new_client_name') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="new_client_email" class="form-control" value="{{ old('new_client_email') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Phone</label>
                                    <input type="text" name="new_client_phone" class="form-control" value="{{ old('new_client_phone') }}">
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>This will create a new record in Client Master and Customers automatically with a linked client code.</small>
                        </div>

                        {{-- STEP 2: Service & Staff --}}
                        <div class="section-divider">
                            <h5 class="mb-3"><i class="fas fa-concierge-bell me-2 text-warning"></i>2. Select Service & Staff</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                <select name="service_id" id="serviceSelect" class="form-select" onchange="onServiceChange()">
                                    <option value="">-- Select Service --</option>
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->id }}"
                                            data-chargeable="{{ $svc->is_chargeable ? 1 : 0 }}"
                                            data-price="{{ $svc->price_per_hour }}"
                                            data-min="{{ $svc->getMinHours() }}"
                                            data-max="{{ $svc->getMaxHours() }}"
                                            {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                            {{ $svc->name }}
                                            @if($svc->is_chargeable) (R{{ number_format($svc->price_per_hour, 2) }}/hr) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Staff Member <span class="text-danger">*</span></label>
                                <select name="staff_id" id="staffSelect" class="form-select" onchange="onStaffChange()">
                                    <option value="">-- Select Staff --</option>
                                    @foreach($staffList as $st)
                                        <option value="{{ $st->id }}" {{ old('staff_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Duration (Hours) <span class="text-danger">*</span></label>
                                <select name="duration_hours" id="durationSelect" class="form-select" onchange="onDurationChange()">
                                    <option value="1" {{ old('duration_hours', 1) == 1 ? 'selected' : '' }}>1 Hour</option>
                                    <option value="2" {{ old('duration_hours') == 2 ? 'selected' : '' }}>2 Hours</option>
                                    <option value="3" {{ old('duration_hours') == 3 ? 'selected' : '' }}>3 Hours</option>
                                    <option value="4" {{ old('duration_hours') == 4 ? 'selected' : '' }}>4 Hours</option>
                                </select>
                            </div>
                        </div>

                        {{-- STEP 3: Date & Time --}}
                        <div class="section-divider">
                            <h5 class="mb-3"><i class="fas fa-clock me-2 text-success"></i>3. Select Date & Time</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" id="dateSelect" class="form-control"
                                    min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" onchange="loadTimeSlots()">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Available Time Slots</label>
                                <input type="hidden" name="start_time" id="selectedTime" value="{{ old('start_time') }}">
                                <div id="timeSlotsContainer">
                                    <p class="text-muted"><i class="fas fa-info-circle me-1"></i>Select a service, staff member, and date to see available slots.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Price Display --}}
                        <div id="priceSection" style="display:none;" class="mb-3">
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-receipt fa-2x me-3"></i>
                                <div>
                                    <div style="font-size:12px;text-transform:uppercase;font-weight:700;">Appointment Cost</div>
                                    <div class="price-display" id="priceDisplay">R 0.00</div>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 4: Notes --}}
                        <div class="section-divider">
                            <h5 class="mb-3"><i class="fas fa-sticky-note me-2 text-info"></i>4. Notes (Optional)</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Client Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Notes visible to client in emails...">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Internal Notes</label>
                                <textarea name="internal_notes" class="form-control" rows="3" placeholder="Internal notes (not shared with client)...">{{ old('internal_notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg me-2" id="submitBtn" disabled>
                                <i class="fas fa-calendar-check me-2"></i>Book Appointment
                            </button>
                            <a href="{{ route('cimsappointments.dashboard') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let searchTimeout = null;

function toggleClientForm() {
    const isNew = document.getElementById('clientNew').checked;
    document.getElementById('existingClientSection').style.display = isNew ? 'none' : 'block';
    document.getElementById('newClientSection').style.display = isNew ? 'block' : 'none';
    checkFormReady();
}

// Client search autocomplete
document.getElementById('clientSearch').addEventListener('input', function() {
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('clientResults').style.display = 'none';
        return;
    }
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetch('{{ route("cimsappointments.ajax.search-clients") }}?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(clients => {
                const container = document.getElementById('clientResults');
                if (clients.length === 0) {
                    container.innerHTML = '<div class="client-item text-muted">No clients found. Select "New Client" to create one.</div>';
                } else {
                    container.innerHTML = clients.map(c =>
                        '<div class="client-item" onclick="selectClient(' + c.client_id + ', \'' + escapeHtml(c.company_name) + '\', \'' + escapeHtml(c.client_code || '') + '\', \'' + escapeHtml(c.email || '') + '\')">' +
                        '<div class="client-code">' + (c.client_code || 'N/A') + '</div>' +
                        '<div>' + escapeHtml(c.company_name) + '</div>' +
                        (c.email ? '<small class="text-muted">' + escapeHtml(c.email) + '</small>' : '') +
                        '</div>'
                    ).join('');
                }
                container.style.display = 'block';
            });
    }, 300);
});

function selectClient(id, name, code, email) {
    document.getElementById('selectedClientId').value = id;
    document.getElementById('clientSearch').value = name;
    document.getElementById('clientResults').style.display = 'none';

    const info = document.getElementById('selectedClientInfo');
    info.innerHTML = '<strong>' + code + '</strong> - ' + escapeHtml(name) + (email ? ' | ' + escapeHtml(email) : '');
    info.style.display = 'block';
    checkFormReady();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// Service change - filter staff
function onServiceChange() {
    const serviceId = document.getElementById('serviceSelect').value;
    if (!serviceId) return;

    const opt = document.getElementById('serviceSelect').selectedOptions[0];
    const isChargeable = opt.dataset.chargeable === '1';
    const maxHours = parseInt(opt.dataset.max) || 4;
    const minHours = parseInt(opt.dataset.min) || 1;

    // Update duration options
    const durSelect = document.getElementById('durationSelect');
    durSelect.innerHTML = '';
    for (let h = minHours; h <= maxHours; h++) {
        const o = document.createElement('option');
        o.value = h;
        o.textContent = h + ' Hour' + (h > 1 ? 's' : '');
        durSelect.appendChild(o);
    }

    // Fetch staff for service
    fetch('{{ route("cimsappointments.ajax.staff-for-service") }}?service_id=' + serviceId)
        .then(r => r.json())
        .then(staffList => {
            const staffSelect = document.getElementById('staffSelect');
            staffSelect.innerHTML = '<option value="">-- Select Staff --</option>';
            staffList.forEach(s => {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                staffSelect.appendChild(o);
            });
        });

    updatePrice();
    loadTimeSlots();
}

function onStaffChange() {
    loadTimeSlots();
    checkFormReady();
}

function onDurationChange() {
    updatePrice();
    loadTimeSlots();
}

function updatePrice() {
    const opt = document.getElementById('serviceSelect').selectedOptions[0];
    if (!opt || !opt.value) return;

    const isChargeable = opt.dataset.chargeable === '1';
    const pricePerHour = parseFloat(opt.dataset.price) || 0;
    const hours = parseInt(document.getElementById('durationSelect').value) || 1;

    const priceSection = document.getElementById('priceSection');
    if (isChargeable && pricePerHour > 0) {
        const total = pricePerHour * hours;
        document.getElementById('priceDisplay').textContent = 'R ' + total.toFixed(2);
        priceSection.style.display = 'block';
    } else {
        priceSection.style.display = 'none';
    }
}

function loadTimeSlots() {
    const staffId = document.getElementById('staffSelect').value;
    const date = document.getElementById('dateSelect').value;
    const duration = document.getElementById('durationSelect').value;
    const container = document.getElementById('timeSlotsContainer');

    if (!staffId || !date) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle me-1"></i>Select staff and date to see available slots.</p>';
        return;
    }

    container.innerHTML = '<p class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Loading available slots...</p>';

    fetch('{{ route("cimsappointments.ajax.get-slots") }}?staff_id=' + staffId + '&date=' + date + '&duration_hours=' + duration)
        .then(r => r.json())
        .then(data => {
            if (data.slots.length === 0) {
                container.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i>No available slots for this date. Try a different date or staff member.</div>';
                document.getElementById('selectedTime').value = '';
                checkFormReady();
                return;
            }

            let html = '<div class="time-slots-grid">';
            data.slots.forEach(slot => {
                const endSlot = addHours(slot, parseInt(duration));
                html += '<button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, \'' + slot + '\')">';
                html += slot + ' - ' + endSlot;
                html += '</button>';
            });
            html += '</div>';
            container.innerHTML = html;
            checkFormReady();
        });
}

function addHours(timeStr, hours) {
    const parts = timeStr.split(':');
    let h = parseInt(parts[0]) + hours;
    return (h < 10 ? '0' : '') + h + ':' + parts[1];
}

function selectTimeSlot(btn, time) {
    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('selectedTime').value = time;
    checkFormReady();
}

function checkFormReady() {
    const clientSource = document.querySelector('input[name="client_source"]:checked').value;
    let clientOk = false;

    if (clientSource === 'existing') {
        clientOk = document.getElementById('selectedClientId').value !== '';
    } else {
        clientOk = true; // Will be validated server-side
    }

    const serviceOk = document.getElementById('serviceSelect').value !== '';
    const staffOk = document.getElementById('staffSelect').value !== '';
    const dateOk = document.getElementById('dateSelect').value !== '';
    const timeOk = document.getElementById('selectedTime').value !== '';

    document.getElementById('submitBtn').disabled = !(clientOk && serviceOk && staffOk && dateOk && timeOk);
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#clientSearch') && !e.target.closest('#clientResults')) {
        document.getElementById('clientResults').style.display = 'none';
    }
});
</script>
@endpush
