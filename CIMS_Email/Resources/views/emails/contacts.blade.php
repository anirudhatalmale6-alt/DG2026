@extends('layouts.default')
@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Email</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Contacts</a></li>
            </ol>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-xxl-4">
                                @include('cims_email::emails.partials.sidebar', ['activePage' => 'contacts'])
                            </div>
                            <div class="col-xl-9 col-xxl-8">

                                {{-- Header Row --}}
                                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                    <h4 class="card-title mb-0"><i class="fas fa-address-book me-2" style="color:#E91E8C;"></i>Contacts</h4>
                                    <button class="btn btn-primary" onclick="openContactModal()">
                                        <i class="fas fa-plus me-1"></i> New Contact
                                    </button>
                                </div>

                                @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif
                                @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                {{-- Search & Filters --}}
                                <div class="filter cm-content-box box-primary mb-4">
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body py-3">
                                            <form method="GET" action="{{ route('cimsemail.contacts') }}" class="row g-2 align-items-end">
                                                <div class="col-md-4">
                                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, company..." value="{{ $search ?? '' }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="client_id" class="form-control form-control-sm default-select sd_drop_class" data-live-search="true" data-size="8" title="-- All Clients --">
                                                        @foreach($clients as $c)
                                                            <option value="{{ $c->client_id }}" {{ ($clientFilter ?? '') == $c->client_id ? 'selected' : '' }}>{{ $c->client_code }} - {{ $c->company_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="show_all" value="1" id="showAllToggle" {{ ($showAll ?? false) ? 'checked' : '' }} onchange="this.form.submit()">
                                                        <label class="form-check-label" for="showAllToggle" style="font-size:12px;">Show All</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex gap-1">
                                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Search</button>
                                                    <a href="{{ route('cimsemail.contacts') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times me-1"></i>Clear</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Contacts Count --}}
                                <p class="text-muted mb-3" style="font-size:13px;">
                                    <strong>{{ $contacts->total() }}</strong> contacts found
                                    @if($search) for "<em>{{ $search }}</em>"@endif
                                </p>

                                {{-- Contact Cards Grid --}}
                                @if($contacts->count() > 0)
                                <div class="row">
                                    @foreach($contacts as $contact)
                                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                                        <div class="card contact-card h-100" style="border:1px solid #eee;border-radius:12px;transition:all 0.2s ease;overflow:hidden;">
                                            {{-- Status indicator bar --}}
                                            <div style="height:4px;background:{{ $contact->is_active ? 'linear-gradient(90deg, #E91E8C, #FF6B9D)' : '#ccc' }};"></div>

                                            <div class="card-body text-center pt-4 pb-3">
                                                {{-- Photo / Avatar --}}
                                                <div class="mb-3">
                                                    @if($contact->photo)
                                                        <img src="{{ url('/storage/contact_photos/' . $contact->photo) }}" alt="{{ $contact->first_name }}"
                                                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #f0f0f0;">
                                                    @else
                                                        @php
                                                            $initials = strtoupper(substr($contact->first_name, 0, 1) . substr($contact->last_name, 0, 1));
                                                            $colors = ['#E91E8C','#2196F3','#FF6B35','#25D366','#9C27B0','#FF5722','#00BCD4','#795548'];
                                                            $colorIdx = ord($contact->first_name[0] ?? 'A') % count($colors);
                                                        @endphp
                                                        <div style="width:80px;height:80px;border-radius:50%;background:{{ $colors[$colorIdx] }};display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:700;letter-spacing:1px;">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Name --}}
                                                <h5 class="mb-0" style="font-size:15px;font-weight:700;color:#1a1a2e;">
                                                    {{ $contact->title ? $contact->title . ' ' : '' }}{{ $contact->first_name }} {{ $contact->last_name }}
                                                </h5>
                                                @if($contact->known_as)
                                                <span class="text-muted" style="font-size:12px;">({{ $contact->known_as }})</span>
                                                @endif

                                                {{-- Position --}}
                                                @if($contact->position)
                                                <p class="text-muted mb-1" style="font-size:12px;">{{ $contact->position }}</p>
                                                @endif

                                                {{-- Company --}}
                                                @if($contact->company_name)
                                                <p class="mb-2" style="font-size:12px;">
                                                    <span class="badge bg-light text-dark" style="font-weight:500;">
                                                        <i class="fas fa-building me-1" style="color:#E91E8C;"></i>{{ $contact->client_code }} - {{ $contact->company_name }}
                                                    </span>
                                                </p>
                                                @endif

                                                {{-- Contact Icons --}}
                                                <div class="d-flex justify-content-center gap-2 mt-2 mb-2 flex-wrap">
                                                    @if($contact->email)
                                                    <a href="mailto:{{ $contact->email }}" class="contact-icon-btn" title="{{ $contact->email }}" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#EBF5FF;color:#2196F3;font-size:14px;transition:all 0.2s;">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    @endif
                                                    @if($contact->phone)
                                                    <a href="tel:{{ $contact->phone }}" class="contact-icon-btn" title="{{ $contact->phone }}" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#FFF3E0;color:#FF6B35;font-size:14px;transition:all 0.2s;">
                                                        <i class="fas fa-phone-alt"></i>
                                                    </a>
                                                    @endif
                                                    @if($contact->mobile)
                                                    <a href="tel:{{ $contact->mobile }}" class="contact-icon-btn" title="{{ $contact->mobile }}" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#F3E5F5;color:#9C27B0;font-size:14px;transition:all 0.2s;">
                                                        <i class="fas fa-mobile-alt"></i>
                                                    </a>
                                                    @endif
                                                    @if($contact->whatsapp)
                                                    @php $waNum = preg_replace('/[^0-9]/', '', $contact->whatsapp); @endphp
                                                    <a href="https://wa.me/{{ $waNum }}" target="_blank" class="contact-icon-btn" title="{{ $contact->whatsapp }}" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#E8F5E9;color:#25D366;font-size:14px;transition:all 0.2s;">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                    @endif
                                                </div>

                                                {{-- Email display --}}
                                                @if($contact->email)
                                                <p class="text-muted mb-0" style="font-size:11px;">{{ $contact->email }}</p>
                                                @endif

                                                {{-- Inactive badge --}}
                                                @if(!$contact->is_active)
                                                <span class="badge bg-secondary mt-2">Inactive</span>
                                                @endif
                                                @if($contact->is_primary)
                                                <span class="badge mt-2" style="background:#E91E8C;color:#fff;">Primary</span>
                                                @endif
                                            </div>

                                            {{-- Action Footer --}}
                                            <div class="card-footer bg-transparent text-center py-2" style="border-top:1px solid #f0f0f0;">
                                                <button class="btn btn-xs btn-outline-primary me-1" onclick="editContact({{ $contact->id }})" title="Edit" style="font-size:12px;padding:4px 12px;border-radius:20px;">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <a href="{{ route('cimsemail.compose', ['contact_email' => $contact->email, 'client_id' => $contact->client_id]) }}" class="btn btn-xs btn-outline-success me-1" title="Send Email" style="font-size:12px;padding:4px 12px;border-radius:20px;">
                                                    <i class="fas fa-paper-plane me-1"></i>Email
                                                </a>
                                                <form method="POST" action="{{ route('cimsemail.contacts.delete', $contact->id) }}" style="display:inline;" onsubmit="return confirm('Deactivate contact {{ addslashes($contact->first_name . ' ' . $contact->last_name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-outline-danger" title="Deactivate" style="font-size:12px;padding:4px 12px;border-radius:20px;">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-muted" style="font-size:13px;">
                                        Showing {{ $contacts->firstItem() ?? 0 }} - {{ $contacts->lastItem() ?? 0 }} of {{ $contacts->total() }}
                                    </span>
                                    {{ $contacts->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-address-book fa-3x mb-3" style="color:#ddd;"></i>
                                    <p class="text-muted">No contacts found. Click "New Contact" to add your first contact.</p>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Contact Modal --}}
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="contactForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="contactMethod" value="POST">
                <div class="modal-content" style="border-radius:12px;overflow:hidden;">
                    <div class="modal-header" style="background:linear-gradient(135deg, #E91E8C, #FF6B9D);color:#fff;">
                        <h5 class="modal-title" id="contactModalTitle"><i class="fas fa-user-plus me-2"></i>New Contact</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Duplicate warning area --}}
                        <div id="duplicateWarnings" class="mb-3" style="display:none;"></div>

                        <div class="row">
                            {{-- Photo --}}
                            <div class="col-md-12 mb-3 text-center">
                                <div id="photoPreview" style="width:100px;height:100px;border-radius:50%;background:#f0f0f0;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;overflow:hidden;border:3px solid #eee;">
                                    <i class="fas fa-camera fa-2x" style="color:#ccc;"></i>
                                </div>
                                <br>
                                <label class="btn btn-sm btn-outline-primary" style="border-radius:20px;font-size:12px;cursor:pointer;">
                                    <i class="fas fa-upload me-1"></i>Upload Photo
                                    <input type="file" name="photo" id="contactPhoto" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                                </label>
                            </div>

                            {{-- Client --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Client <span class="text-danger">*</span></label>
                                <select name="client_id" id="contactClientId" class="form-control default-select sd_drop_class" data-live-search="true" data-size="8" title="-- Select Client --" required>
                                    @foreach($clients as $c)
                                        <option value="{{ $c->client_id }}">{{ $c->client_code }} - {{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Title + Gender --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Title</label>
                                <select name="title" id="contactTitle" class="form-control form-control-sm">
                                    <option value="">-- None --</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Prof">Prof</option>
                                    <option value="Adv">Adv</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" id="contactGender" class="form-control form-control-sm">
                                    <option value="">-- None --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Known As</label>
                                <input type="text" name="known_as" id="contactKnownAs" class="form-control form-control-sm" placeholder="e.g. Angus">
                            </div>

                            {{-- Names --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="contactFirstName" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="contactLastName" class="form-control form-control-sm" required>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-envelope me-1" style="color:#2196F3;"></i>Email</label>
                                <input type="email" name="email" id="contactEmail" class="form-control form-control-sm" onblur="checkDuplicate()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-phone-alt me-1" style="color:#FF6B35;"></i>Phone</label>
                                <input type="text" name="phone" id="contactPhone" class="form-control form-control-sm">
                            </div>

                            {{-- Mobile + WhatsApp --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-mobile-alt me-1" style="color:#9C27B0;"></i>Mobile</label>
                                <input type="text" name="mobile" id="contactMobile" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fab fa-whatsapp me-1" style="color:#25D366;"></i>WhatsApp</label>
                                <input type="text" name="whatsapp" id="contactWhatsapp" class="form-control form-control-sm">
                            </div>

                            {{-- Position + Department --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Position / Designation</label>
                                <input type="text" name="position" id="contactPosition" class="form-control form-control-sm" placeholder="e.g. Financial Director">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" id="contactDepartment" class="form-control form-control-sm" placeholder="e.g. Finance">
                            </div>

                            {{-- Primary + Notes --}}
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="contactIsPrimary">
                                    <label class="form-check-label" for="contactIsPrimary">Primary Contact</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" id="contactNotes" class="form-control form-control-sm" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i><span id="contactSaveBtn">Save Contact</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
var contactEditId = null;

function openContactModal(editId) {
    contactEditId = editId || null;
    var form = document.getElementById('contactForm');
    var titleEl = document.getElementById('contactModalTitle');
    var methodEl = document.getElementById('contactMethod');
    var saveBtn = document.getElementById('contactSaveBtn');

    // Reset form
    form.reset();
    document.getElementById('duplicateWarnings').style.display = 'none';
    document.getElementById('duplicateWarnings').innerHTML = '';
    document.getElementById('photoPreview').innerHTML = '<i class="fas fa-camera fa-2x" style="color:#ccc;"></i>';

    if (editId) {
        titleEl.innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Contact';
        methodEl.value = 'PUT';
        form.action = '{{ url("cims/email/contacts") }}/' + editId;
        saveBtn.textContent = 'Update Contact';
        loadContact(editId);
    } else {
        titleEl.innerHTML = '<i class="fas fa-user-plus me-2"></i>New Contact';
        methodEl.value = 'POST';
        form.action = '{{ route("cimsemail.contacts.store") }}';
        saveBtn.textContent = 'Save Contact';
    }

    var modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

function editContact(id) {
    openContactModal(id);
}

function loadContact(id) {
    $.get('{{ url("cims/email/ajax/contact") }}/' + id, function(c) {
        $('#contactClientId').val(c.client_id).trigger('change');
        // For bootstrap-select
        if ($.fn.selectpicker) {
            $('#contactClientId').selectpicker('val', c.client_id);
        }
        $('#contactTitle').val(c.title || '');
        $('#contactGender').val(c.gender || '');
        $('#contactKnownAs').val(c.known_as || '');
        $('#contactFirstName').val(c.first_name || '');
        $('#contactLastName').val(c.last_name || '');
        $('#contactEmail').val(c.email || '');
        $('#contactPhone').val(c.phone || '');
        $('#contactMobile').val(c.mobile || '');
        $('#contactWhatsapp').val(c.whatsapp || '');
        $('#contactPosition').val(c.position || '');
        $('#contactDepartment').val(c.department || '');
        $('#contactIsPrimary').prop('checked', c.is_primary == 1);
        $('#contactNotes').val(c.notes || '');

        if (c.photo) {
            $('#photoPreview').html('<img src="{{ url("/storage/contact_photos") }}/' + c.photo + '" style="width:100%;height:100%;object-fit:cover;">');
        }
    });
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function checkDuplicate() {
    var email = $('#contactEmail').val();
    var clientId = $('#contactClientId').val();
    if (!email) return;

    $.post('{{ route("cimsemail.ajax.check-duplicate") }}', {
        _token: '{{ csrf_token() }}',
        email: email,
        client_id: clientId,
        exclude_id: contactEditId
    }, function(data) {
        var warningDiv = $('#duplicateWarnings');
        if (data.warnings && data.warnings.length > 0) {
            var html = '';
            data.warnings.forEach(function(w) {
                var cls = w.type === 'same_client' ? 'alert-warning' : 'alert-info';
                html += '<div class="alert ' + cls + ' py-2 mb-1" style="font-size:12px;"><i class="fas fa-exclamation-triangle me-1"></i>' + w.message + '</div>';
            });
            warningDiv.html(html).show();
        } else {
            warningDiv.hide().html('');
        }
    });
}

// Card hover effect
jQuery(document).ready(function() {
    jQuery('.contact-card').hover(
        function() { jQuery(this).css({'transform':'translateY(-4px)','box-shadow':'0 8px 25px rgba(0,0,0,0.1)'}); },
        function() { jQuery(this).css({'transform':'translateY(0)','box-shadow':'none'}); }
    );
});
</script>
@endpush
