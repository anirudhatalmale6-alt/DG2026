@extends('layouts.default')
@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Email</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">My Signature</a></li>
            </ol>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-xxl-4">
                                @include('cims_email::emails.partials.sidebar', ['activePage' => 'signature'])
                            </div>
                            <div class="col-xl-9 col-xxl-8">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <h4 class="card-title mb-0"><i class="fas fa-signature me-2 text-primary"></i>My Email Signature</h4>
                                    </div>

                                    @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    @if(!empty($editId))
                                    <div class="alert alert-warning py-2 mb-3">
                                        <i class="fas fa-edit me-1"></i>
                                        Editing signature for: <strong>{{ $signature->full_name ?? 'User' }}</strong>
                                        <a href="{{ route('cimsemail.signature') }}" class="btn btn-sm btn-outline-dark ms-2"><i class="fas fa-times me-1"></i>Cancel Edit</a>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('cimsemail.signature.save') }}">
                                        @csrf
                                        @if(!empty($editId))
                                        <input type="hidden" name="edit_id" value="{{ $editId }}">
                                        @endif
                                        {{-- Personal Details --}}
                                        <div class="filter cm-content-box box-primary">
                                            <div class="content-title SlideToolHeader">
                                                <div class="cpa">
                                                    <i class="fas fa-user me-2"></i>Personal Details
                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                                                </div>
                                            </div>
                                            <div class="cm-content-body form excerpt">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="full_name" class="form-control" placeholder="e.g. John Smith" value="{{ $signature->full_name ?? '' }}" required id="sigName">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Designation / Title <span class="text-danger">*</span></label>
                                                            <input type="text" name="designation" class="form-control" placeholder="e.g. Tax Consultant" value="{{ $signature->designation ?? '' }}" required id="sigTitle">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Phone</label>
                                                            <input type="text" name="phone" class="form-control" placeholder="e.g. +27 11 123 4567" value="{{ $signature->phone ?? '' }}" id="sigPhone">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Mobile</label>
                                                            <input type="text" name="mobile" class="form-control" placeholder="e.g. +27 82 123 4567" value="{{ $signature->mobile ?? '' }}" id="sigMobile">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Direct Number</label>
                                                            <input type="text" name="direct_number" class="form-control" placeholder="e.g. +27 11 987 6543" value="{{ $signature->direct_number ?? '' }}" id="sigDirect">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label"><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp Number</label>
                                                            <input type="text" name="whatsapp" class="form-control" placeholder="e.g. +27 82 123 4567" value="{{ $signature->whatsapp ?? '' }}" id="sigWhatsapp">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Company Details --}}
                                        <div class="filter cm-content-box box-primary mt-4">
                                            <div class="content-title SlideToolHeader">
                                                <div class="cpa">
                                                    <i class="fas fa-building me-2"></i>Company Details
                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                                                </div>
                                            </div>
                                            <div class="cm-content-body form excerpt">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Company Name</label>
                                                            <input type="text" name="company_name" class="form-control" placeholder="e.g. Accounting Taxation and Payroll (Pty) Ltd" value="{{ $signature->company_name ?? '' }}" id="sigCompany">
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 mb-3">
                                                            <label class="form-label">Company Website</label>
                                                            <input type="text" name="company_website" class="form-control" placeholder="e.g. www.company.co.za" value="{{ $signature->company_website ?? '' }}" id="sigWebsite">
                                                        </div>
                                                        <div class="col-xl-12 mb-3">
                                                            <label class="form-label"><i class="fas fa-quote-left me-1 text-primary"></i>Slogan / Tagline</label>
                                                            <input type="text" name="slogan" class="form-control" placeholder="e.g. Adding Value - Ensuring Compliance" value="{{ $signature->slogan ?? '' }}" id="sigSlogan">
                                                            <div class="form-text">Displayed below company name in signature</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Custom Signature HTML (Advanced) --}}
                                        <div class="filter cm-content-box box-primary mt-4">
                                            <div class="content-title SlideToolHeader">
                                                <div class="cpa">
                                                    <i class="fas fa-code me-2"></i>Custom Signature (Advanced - Optional)
                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                                                </div>
                                            </div>
                                            <div class="cm-content-body form excerpt" style="display:none;">
                                                <div class="card-body">
                                                    <p class="text-muted mb-3" style="font-size:12px;">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Leave this empty to use the auto-generated signature from the fields above.
                                                        Only use this if you want a fully custom HTML signature.
                                                    </p>
                                                    <textarea name="signature_html" id="sigCustomHtml" class="form-control" rows="6" placeholder="Paste custom HTML signature here (optional)...">{{ $signature->signature_html ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Live Preview --}}
                                        <div class="filter cm-content-box box-primary mt-4">
                                            <div class="content-title SlideToolHeader">
                                                <div class="cpa">
                                                    <i class="fas fa-eye me-2"></i>Signature Preview
                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                                                </div>
                                            </div>
                                            <div class="cm-content-body form excerpt">
                                                <div class="card-body">
                                                    <div id="sigPreview" style="padding:20px;background:#fff;border:1px solid #eee;border-radius:6px;">
                                                        {{-- Preview will be rendered by JS --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="mt-4 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Save Signature
                                            </button>
                                            <button type="button" class="btn btn-info light" onclick="updatePreview()">
                                                <i class="fas fa-eye me-2"></i>Refresh Preview
                                            </button>
                                        </div>
                                    </form>

                                    {{-- All Signatures List --}}
                                    <div class="filter cm-content-box box-primary mt-4">
                                        <div class="content-title SlideToolHeader">
                                            <div class="cpa">
                                                <i class="fas fa-list me-2"></i>All Email Signatures ({{ count($allSignatures ?? []) }})
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                                            </div>
                                        </div>
                                        <div class="cm-content-body form excerpt">
                                            <div class="card-body p-0">
                                                @if(isset($allSignatures) && count($allSignatures) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:30px;">#</th>
                                                                <th>Name</th>
                                                                <th>Designation</th>
                                                                <th>Phone / Mobile</th>
                                                                <th>Company</th>
                                                                <th>System User</th>
                                                                <th>Status</th>
                                                                <th style="width:120px;">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($allSignatures as $idx => $sig)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>
                                                                    <strong>{{ $sig->full_name }}</strong>
                                                                    @if($sig->user_id == auth()->id())
                                                                    <span class="badge badge-sm bg-primary ms-1">You</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $sig->designation }}</td>
                                                                <td>
                                                                    @if($sig->phone)<span style="font-size:12px;">{{ $sig->phone }}</span>@endif
                                                                    @if($sig->phone && $sig->mobile) / @endif
                                                                    @if($sig->mobile)<span style="font-size:12px;">{{ $sig->mobile }}</span>@endif
                                                                </td>
                                                                <td style="font-size:12px;">{{ $sig->company_name }}</td>
                                                                <td style="font-size:12px;">
                                                                    {{ trim(($sig->first_name ?? '') . ' ' . ($sig->last_name ?? '')) ?: '-' }}
                                                                    @if($sig->user_email)
                                                                    <br><span class="text-muted">{{ $sig->user_email }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($sig->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                    @else
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        <a href="{{ route('cimsemail.signature', ['edit_id' => $sig->id]) }}" class="btn btn-xs btn-primary light" style="display:inline-flex;align-items:center;padding:4px 10px;font-size:12px;" title="Edit">
                                                                            <i class="fas fa-edit me-1"></i>Edit
                                                                        </a>
                                                                        <form method="POST" action="{{ route('cimsemail.signature.delete', $sig->id) }}" style="display:inline;" onsubmit="return confirm('Delete signature for {{ addslashes($sig->full_name) }}?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-xs btn-danger light" style="display:inline-flex;align-items:center;padding:4px 10px;font-size:12px;" title="Delete">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else
                                                <div class="card-body text-center py-4">
                                                    <i class="fas fa-signature fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No signatures created yet. Fill in the form above to create your first signature.</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function buildSignatureHtml() {
    var name = document.getElementById('sigName').value || '';
    var title = document.getElementById('sigTitle').value || '';
    var phone = document.getElementById('sigPhone').value || '';
    var mobile = document.getElementById('sigMobile').value || '';
    var direct = document.getElementById('sigDirect').value || '';
    var whatsapp = document.getElementById('sigWhatsapp').value || '';
    var company = document.getElementById('sigCompany').value || '';
    var website = document.getElementById('sigWebsite').value || '';
    var slogan = document.getElementById('sigSlogan').value || '';

    var bannerUrl = '{{ $bannerImageUrl ?? '' }}';
    var logoUrl = '{{ asset("assets/cims_core/atp_cims_logo.jpg") }}';
    var websiteUrl = website ? (website.match(/^https?:\/\//) ? website : 'https://' + website) : '';

    var html = '<table cellpadding="0" cellspacing="0" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;max-width:550px;">';

    // Name + Title with pink accent line
    html += '<tr><td style="padding-bottom:10px;border-bottom:3px solid #E91E8C;">';
    html += '<strong style="font-size:16px;color:#1a1a2e;letter-spacing:0.5px;">' + name + '</strong>';
    if (title) html += '<br><span style="font-size:12px;color:#777;margin-top:2px;display:inline-block;">' + title + '</span>';
    html += '</td></tr>';

    // Contact numbers row - each icon a unique colour
    var contactParts = [];
    if (phone) contactParts.push('<i class="fas fa-phone-alt" style="color:#2196F3;width:15px;font-size:11px;"></i> ' + phone);
    if (direct) contactParts.push('<i class="fas fa-headset" style="color:#9C27B0;width:15px;font-size:11px;"></i> ' + direct);
    if (mobile) contactParts.push('<i class="fas fa-mobile-alt" style="color:#FF6B35;width:15px;font-size:12px;"></i> ' + mobile);
    if (whatsapp) {
        var waNum = whatsapp.replace(/[^0-9]/g, '');
        contactParts.push('<i class="fab fa-whatsapp" style="color:#25D366;width:15px;font-size:13px;"></i> <a href="https://wa.me/' + waNum + '" style="color:#444;text-decoration:none;" target="_blank">' + whatsapp + '</a>');
    }
    if (contactParts.length > 0) {
        html += '<tr><td style="padding:12px 0 10px 0;">';
        html += '<span style="font-size:12px;color:#444;">' + contactParts.join(' &nbsp;<span style="color:#ccc;">|</span>&nbsp; ') + '</span>';
        html += '</td></tr>';
    }

    // Thin elegant divider between contacts and company
    html += '<tr><td style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';

    // Company row - bigger font
    if (company) {
        html += '<tr><td style="padding-top:10px;">';
        html += '<strong style="font-size:14px;color:#1a1a2e;">' + company + '</strong>';
        if (website) html += ' &nbsp;<span style="color:#ccc;">|</span>&nbsp; <a href="' + websiteUrl + '" style="font-size:13px;color:#0066CC;text-decoration:none;">' + website + '</a>';
        html += '</td></tr>';
    }

    // Slogan row - pink, italic (no bold), elegant
    if (slogan) {
        html += '<tr><td style="padding-top:4px;">';
        html += '<em style="font-size:12px;color:#E91E8C;font-style:italic;letter-spacing:0.3px;">' + slogan + '</em>';
        html += '</td></tr>';
    }

    // Banner image (if uploaded)
    if (bannerUrl) {
        html += '<tr><td style="padding-top:14px;">';
        html += '<img src="' + bannerUrl + '" alt="Signature Banner" style="max-width:100%;height:auto;border-radius:4px;">';
        html += '</td></tr>';
    }

    // Subtle separator before logo
    html += '<tr><td style="padding-top:14px;"><div style="border-top:1px solid #e0e0e0;"></div></td></tr>';

    // Company logo with protection - links to website
    var logoLinkStart = websiteUrl ? '<a href="' + websiteUrl + '" target="_blank" style="text-decoration:none;">' : '';
    var logoLinkEnd = websiteUrl ? '</a>' : '';
    html += '<tr><td style="padding-top:10px;">';
    html += logoLinkStart;
    html += '<img src="' + logoUrl + '" alt="CIMS" style="max-width:280px;height:auto;pointer-events:none;-webkit-user-drag:none;user-select:none;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;" draggable="false" oncontextmenu="return false;" ondragstart="return false;">';
    html += logoLinkEnd;
    html += '</td></tr>';

    html += '</table>';
    return html;
}

function buildDisclaimerHtml() {
    var text = {!! json_encode($disclaimerText ?? '') !!};
    if (!text) return '';
    var escaped = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
    return '<div style="margin-top:15px;padding:10px 12px;border-top:1px solid #ddd;font-size:10px;color:#888;line-height:1.5;background:#fafafa;border-radius:4px;">' + escaped + '</div>';
}

function updatePreview() {
    var customHtml = document.getElementById('sigCustomHtml').value.trim();
    var previewDiv = document.getElementById('sigPreview');

    if (customHtml) {
        previewDiv.innerHTML = customHtml + buildDisclaimerHtml();
    } else {
        previewDiv.innerHTML = buildSignatureHtml() + buildDisclaimerHtml();
    }
}

// Live preview on field changes
document.querySelectorAll('#sigName, #sigTitle, #sigPhone, #sigMobile, #sigDirect, #sigWhatsapp, #sigCompany, #sigWebsite, #sigSlogan').forEach(function(el) {
    el.addEventListener('input', function() {
        if (!document.getElementById('sigCustomHtml').value.trim()) {
            updatePreview();
        }
    });
});

document.getElementById('sigCustomHtml').addEventListener('input', function() {
    updatePreview();
});

// Initial preview
updatePreview();

// Fillow SlideToolHeader toggle
jQuery('.SlideToolHeader').on('click', function() {
    var el = jQuery(this).hasClass('expand');
    if (el) {
        jQuery(this).removeClass('expand').addClass('collapse');
        jQuery(this).parents('.cm-content-box').find('.cm-content-body').slideUp(300);
    } else {
        jQuery(this).removeClass('collapse').addClass('expand');
        jQuery(this).parents('.cm-content-box').find('.cm-content-body').slideDown(300);
    }
});
</script>
@endpush
