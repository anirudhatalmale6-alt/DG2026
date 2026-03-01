@extends('layouts.default')

@push('styles')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
/* Summernote Toolbar - CIMS Professional Theme */
.note-editor.note-frame { border:1px solid #e2e8f0 !important; border-radius:8px !important; overflow:visible !important; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.note-editor .note-toolbar-wrapper { overflow:visible !important; }
.note-editor .note-toolbar { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%) !important; border-bottom:1px solid #e2e8f0 !important; padding:8px 10px !important; overflow:visible !important; position:relative; z-index:100; }
.note-editor .note-toolbar .note-btn-group { margin-right:4px !important; position:relative; }
.note-editor .note-toolbar .note-btn { background:#fff !important; border:1px solid #d1d5db !important; border-radius:5px !important; color:#374151 !important; font-size:13px !important; padding:5px 9px !important; margin:1px !important; transition:all 0.15s ease !important; box-shadow:0 1px 2px rgba(0,0,0,0.04) !important; }
.note-editor .note-toolbar .note-btn:hover { background:#E91E8C !important; border-color:#E91E8C !important; color:#fff !important; box-shadow:0 2px 6px rgba(233,30,140,0.3) !important; transform:translateY(-1px); }
.note-editor .note-toolbar .note-btn.active,.note-editor .note-toolbar .note-btn:active { background:#E91E8C !important; border-color:#c4167a !important; color:#fff !important; }
.note-editor .note-toolbar .dropdown-menu { border:1px solid #e2e8f0 !important; border-radius:8px !important; box-shadow:0 8px 24px rgba(0,0,0,0.15) !important; padding:6px !important; z-index:99999 !important; position:absolute !important; display:none; }
.note-editor .note-toolbar .dropdown-menu.show,.note-editor .note-toolbar .open > .dropdown-menu { display:block !important; }
.note-editor .note-toolbar .dropdown-menu .dropdown-item,.note-editor .note-toolbar .dropdown-menu a { border:none !important; border-radius:4px !important; padding:6px 12px !important; font-size:13px !important; color:#374151 !important; background:transparent !important; box-shadow:none !important; cursor:pointer !important; }
.note-editor .note-toolbar .dropdown-menu .dropdown-item:hover,.note-editor .note-toolbar .dropdown-menu a:hover { background:#fce4f1 !important; color:#E91E8C !important; transform:none !important; }
.note-editor .note-toolbar .note-color .dropdown-menu .note-btn { padding:0 !important; margin:0 !important; border:none !important; box-shadow:none !important; width:auto !important; }
.note-editor .note-toolbar .note-color .dropdown-menu .note-btn:hover { transform:none !important; background:transparent !important; }
.note-editor .note-toolbar .note-btn-group + .note-btn-group::before { content:''; display:inline-block; width:1px; height:24px; background:#d1d5db; margin-right:4px; vertical-align:middle; }
.note-editor .note-editing-area .note-editable { background:#fff !important; min-height:300px; color:#000 !important; }
.note-editor .note-statusbar { background:#f8fafc !important; border-top:1px solid #e2e8f0 !important; }

/* Email chip/tag system for To/CC/BCC */
.email-tags-wrap { display:flex; flex-wrap:wrap; align-items:center; gap:4px; min-height:42px; padding:6px 10px; border:1px solid #e2e8f0; border-radius:6px; background:#fff; cursor:text; transition:border-color 0.2s; }
.email-tags-wrap:focus-within { border-color:#E91E8C; box-shadow:0 0 0 3px rgba(233,30,140,0.1); }
.email-tag { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:16px; font-size:12px; font-weight:500; background:linear-gradient(135deg,#E91E8C10,#E91E8C18); color:#333; border:1px solid #E91E8C30; }
.email-tag .tag-remove { cursor:pointer; color:#E91E8C; font-weight:bold; font-size:14px; line-height:1; margin-left:2px; }
.email-tag .tag-remove:hover { color:#c4167a; }
.email-tags-input { border:none; outline:none; flex:1; min-width:120px; font-size:13px; background:transparent; color:#000; }

/* Contact address book cards */
.contact-pick { cursor:pointer; padding:8px 10px; border-radius:6px; transition:all 0.15s; border:1px solid transparent; }
.contact-pick:hover { background:#fce4f1; border-color:#E91E8C40; }
.contact-pick.selected { background:#E91E8C10; border-color:#E91E8C; }
.contact-pick .contact-avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }

/* Attachment file list - amazing look */
.attach-file-card { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:8px; background:linear-gradient(135deg,#f8fafc,#f1f5f9); border:1px solid #e2e8f0; transition:all 0.15s; }
.attach-file-card:hover { border-color:#E91E8C; box-shadow:0 2px 8px rgba(233,30,140,0.12); }
.attach-file-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.attach-file-icon.pdf { background:#FF4444; color:#fff; }
.attach-file-icon.img { background:#4CAF50; color:#fff; }
.attach-file-icon.doc { background:#2196F3; color:#fff; }
.attach-file-icon.xls { background:#FF9800; color:#fff; }
.attach-file-icon.other { background:#9E9E9E; color:#fff; }
.attach-file-remove { cursor:pointer; color:#ccc; font-size:18px; transition:color 0.15s; margin-left:auto; }
.attach-file-remove:hover { color:#E91E8C; }

/* Sticky action bar */
.compose-action-bar { position:sticky; bottom:0; z-index:50; background:#fff; border-top:2px solid #E91E8C; padding:12px 20px; margin:0 -20px -20px -20px; border-radius:0 0 8px 8px; box-shadow:0 -4px 16px rgba(0,0,0,0.06); }

/* Recent contacts quick pills */
.recent-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:16px; font-size:11px; background:#f0f9ff; color:#2196F3; border:1px solid #2196F320; cursor:pointer; transition:all 0.15s; white-space:nowrap; }
.recent-pill:hover { background:#2196F3; color:#fff; border-color:#2196F3; }

/* Section headers */
.compose-section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#999; margin-bottom:6px; }

/* Variable insert buttons */
.var-btn { display:inline-block; padding:3px 10px; margin:2px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#374151; transition:all 0.15s; }
.var-btn:hover { background:#E91E8C; color:#fff; border-color:#E91E8C; }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Email</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $draft ? 'Edit Draft' : 'Compose' }}</a></li>
            </ol>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            {{-- Left sidebar --}}
                            <div class="col-xl-3 col-xxl-4">
                                @include('cims_email::emails.partials.sidebar', ['activePage' => 'compose'])

                                {{-- CLIENT SELECTION (Mandatory) --}}
                                <div class="mt-3 px-2">
                                    <label class="compose-section-label"><i class="fas fa-building me-1" style="color:#E91E8C;"></i> Select Client <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm default-select sd_drop_class" data-live-search="true" data-size="8" title="-- Select Client --" id="ecClientSelect">
                                        @foreach($clients as $c)
                                            <option value="{{ $c->client_id }}" data-code="{{ $c->client_code }}" {{ $selectedClientId == $c->client_id ? 'selected' : '' }}>
                                                {{ $c->client_code }} - {{ $c->company_name }}{{ !$c->is_active ? ' (Inactive)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <label class="form-check-label" style="font-size:11px;cursor:pointer;">
                                            <input type="checkbox" class="form-check-input" id="ecShowAllClients" {{ $showAll ? 'checked' : '' }} style="width:14px;height:14px;">
                                            Show inactive clients
                                        </label>
                                    </div>
                                </div>

                                {{-- TEMPLATE SELECTOR --}}
                                <div class="mt-3 px-2">
                                    <label class="compose-section-label"><i class="fas fa-file-code me-1" style="color:#9C27B0;"></i> Template</label>
                                    <select class="form-control form-control-sm" id="ecTemplateSelect">
                                        <option value="">-- No Template --</option>
                                        @foreach($templates->groupBy('category') as $category => $tpls)
                                            <optgroup label="{{ $category }}">
                                                @foreach($tpls as $t)
                                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- ADDRESS BOOK (loaded via AJAX when client selected) --}}
                                <div class="mt-3 px-2" id="ecAddressBookSection" style="display:none;">
                                    <label class="compose-section-label"><i class="fas fa-address-book me-1" style="color:#2196F3;"></i> Client Contacts</label>
                                    <div id="ecContactsList" style="max-height:300px;overflow-y:auto;"></div>
                                </div>

                                {{-- RECENT CONTACTS (Quick Pick) --}}
                                @if(count($recentContacts) > 0)
                                <div class="mt-3 px-2">
                                    <label class="compose-section-label"><i class="fas fa-clock me-1" style="color:#FF6B35;"></i> Recently Used</label>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($recentContacts as $rc)
                                        <span class="recent-pill" onclick="addEmailToField('to', '{{ $rc['email'] }}')" title="{{ $rc['name'] }}">
                                            <i class="fas fa-user" style="font-size:9px;"></i>
                                            {{ $rc['known_as'] ?: Str::limit($rc['name'], 15) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Main compose area --}}
                            <div class="col-xl-9 col-xxl-8">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="card-title mb-0"><i class="fas fa-pen-fancy me-2" style="color:#E91E8C;"></i>{{ $draft ? 'Edit Draft' : 'Compose Email' }}</h4>
                                    </div>

                                    @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('cimsemail.send') }}" enctype="multipart/form-data" id="composeForm">
                                        @csrf
                                        @if($draft)
                                            <input type="hidden" name="draft_id" value="{{ $draft->id }}">
                                        @endif
                                        <input type="hidden" name="client_id" id="ecClientIdHidden" value="{{ $selectedClientId ?? '' }}">
                                        <input type="hidden" name="to_emails" id="ecToHidden" value="{{ $draft ? implode(', ', json_decode($draft->to_emails, true) ?? []) : '' }}">
                                        <input type="hidden" name="cc_emails" id="ecCcHidden" value="{{ $draft ? implode(', ', json_decode($draft->cc_emails, true) ?? []) : '' }}">
                                        <input type="hidden" name="bcc_emails" id="ecBccHidden" value="{{ $draft ? implode(', ', json_decode($draft->bcc_emails, true) ?? []) : '' }}">

                                        {{-- FROM (read-only) --}}
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center" style="padding:8px 12px;background:#f8f9fa;border-radius:6px;border:1px solid #e2e8f0;">
                                                <span style="font-size:12px;font-weight:600;color:#999;min-width:50px;">FROM</span>
                                                <span style="font-size:13px;color:#333;">{{ $fromName }} &lt;{{ $fromEmail }}&gt;</span>
                                            </div>
                                        </div>

                                        {{-- TO --}}
                                        <div class="mb-2">
                                            <div class="d-flex align-items-start gap-2">
                                                <span style="font-size:12px;font-weight:600;color:#999;min-width:50px;padding-top:10px;">TO</span>
                                                <div class="flex-grow-1">
                                                    <div class="email-tags-wrap" id="ecToWrap" onclick="document.getElementById('ecToInput').focus()">
                                                        <input type="text" class="email-tags-input" id="ecToInput" placeholder="Type email or select from contacts..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- CC/BCC toggle --}}
                                        <div class="mb-2 text-end">
                                            <a href="javascript:void(0)" class="text-primary" style="font-size:12px;font-weight:600;" id="ccBccToggle">
                                                <i class="fas fa-plus me-1"></i>CC / BCC
                                            </a>
                                        </div>

                                        {{-- CC --}}
                                        <div class="mb-2" id="ccRow" style="display:none;">
                                            <div class="d-flex align-items-start gap-2">
                                                <span style="font-size:12px;font-weight:600;color:#999;min-width:50px;padding-top:10px;">CC</span>
                                                <div class="flex-grow-1">
                                                    <div class="email-tags-wrap" id="ecCcWrap" onclick="document.getElementById('ecCcInput').focus()">
                                                        <input type="text" class="email-tags-input" id="ecCcInput" placeholder="CC..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- BCC --}}
                                        <div class="mb-2" id="bccRow" style="display:none;">
                                            <div class="d-flex align-items-start gap-2">
                                                <span style="font-size:12px;font-weight:600;color:#999;min-width:50px;padding-top:10px;">BCC</span>
                                                <div class="flex-grow-1">
                                                    <div class="email-tags-wrap" id="ecBccWrap" onclick="document.getElementById('ecBccInput').focus()">
                                                        <input type="text" class="email-tags-input" id="ecBccInput" placeholder="BCC..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SUBJECT --}}
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span style="font-size:12px;font-weight:600;color:#999;min-width:50px;">SUBJ</span>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                                                        <span id="ecSubjectPrefix" style="padding:8px 10px;background:#f8f9fa;font-size:12px;font-weight:600;color:#E91E8C;white-space:nowrap;border-right:1px solid #e2e8f0;display:none;"></span>
                                                        <input type="text" name="subject" id="ecSubject" class="form-control border-0" placeholder="Subject line..." value="{{ $draft->subject ?? '' }}" style="box-shadow:none;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ATTACHMENTS (before editor, per client request) --}}
                                        <div class="mb-3">
                                            <label class="compose-section-label"><i class="fas fa-paperclip me-1" style="color:#FF6B35;"></i> Attachments</label>
                                            <div id="ecAttachDropZone" style="border:2px dashed #e2e8f0;border-radius:8px;padding:16px;text-align:center;background:#fafbfc;transition:all 0.2s;cursor:pointer;" onclick="document.getElementById('ecAttachInput').click()">
                                                <i class="fas fa-cloud-upload-alt" style="font-size:24px;color:#ccc;display:block;margin-bottom:6px;"></i>
                                                <span style="font-size:12px;color:#999;">Drop files here or click to browse</span>
                                                <input type="file" name="attachments[]" form="composeForm" multiple id="ecAttachInput" style="display:none;" onchange="renderAttachments(this)">
                                            </div>
                                            <div id="ecAttachFileList" class="mt-2 d-flex flex-column gap-2"></div>
                                        </div>

                                        {{-- VARIABLE INSERT BUTTONS (shown when template loaded) --}}
                                        <div id="ecVariableButtons" class="p-3 mb-3" style="background:#f8f9fa;border-radius:8px;border:1px solid #eee;display:none;">
                                            <p class="mb-2" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#999;letter-spacing:0.5px;">
                                                <i class="fas fa-code me-1"></i> Click to insert variable into email body
                                            </p>
                                            <div>
                                                <strong style="font-size:10px;color:#E91E8C;text-transform:uppercase;">Contact:</strong>
                                                <span class="var-btn" onclick="insertVar('{contact_title}')">{contact_title}</span>
                                                <span class="var-btn" onclick="insertVar('{contact_first_name}')">{contact_first_name}</span>
                                                <span class="var-btn" onclick="insertVar('{contact_last_name}')">{contact_last_name}</span>
                                                <span class="var-btn" onclick="insertVar('{contact_known_as}')">{contact_known_as}</span>
                                                <span class="var-btn" onclick="insertVar('{contact_email}')">{contact_email}</span>
                                                <span class="var-btn" onclick="insertVar('{contact_position}')">{contact_position}</span>
                                            </div>
                                            <div class="mt-1">
                                                <strong style="font-size:10px;color:#2196F3;text-transform:uppercase;">Client:</strong>
                                                <span class="var-btn" onclick="insertVar('{client_code}')">{client_code}</span>
                                                <span class="var-btn" onclick="insertVar('{company_name}')">{company_name}</span>
                                                <span class="var-btn" onclick="insertVar('{trading_name}')">{trading_name}</span>
                                                <span class="var-btn" onclick="insertVar('{tax_number}')">{tax_number}</span>
                                            </div>
                                            <div class="mt-1">
                                                <strong style="font-size:10px;color:#25D366;text-transform:uppercase;">Sender:</strong>
                                                <span class="var-btn" onclick="insertVar('{sender_name}')">{sender_name}</span>
                                                <span class="var-btn" onclick="insertVar('{sender_designation}')">{sender_designation}</span>
                                                <span class="var-btn" onclick="insertVar('{sender_email}')">{sender_email}</span>
                                            </div>
                                            <div class="mt-1">
                                                <strong style="font-size:10px;color:#FF6B35;text-transform:uppercase;">Date:</strong>
                                                <span class="var-btn" onclick="insertVar('{today_date}')">{today_date}</span>
                                                <span class="var-btn" onclick="insertVar('{current_month}')">{current_month}</span>
                                                <span class="var-btn" onclick="insertVar('{current_year}')">{current_year}</span>
                                            </div>
                                            <div class="mt-1">
                                                <strong style="font-size:10px;color:#9C27B0;text-transform:uppercase;">Pronouns:</strong>
                                                <span class="var-btn" onclick="insertVar('{his_her}')">{his_her}</span>
                                                <span class="var-btn" onclick="insertVar('{he_she}')">{he_she}</span>
                                                <span class="var-btn" onclick="insertVar('{him_her}')">{him_her}</span>
                                            </div>
                                        </div>

                                        {{-- EMAIL BODY (Summernote) --}}
                                        <div class="mb-3">
                                            <textarea id="ecBody" name="body_html" class="form-control" rows="12" placeholder="Write your email here...">{{ $draft->body_html ?? '' }}</textarea>
                                        </div>

                                        {{-- STICKY ACTION BAR --}}
                                        <div class="compose-action-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" type="submit" id="ecSendBtn">
                                                    <i class="fa fa-paper-plane me-1"></i> Send Email
                                                </button>
                                                <button class="btn btn-warning light" type="button" onclick="saveDraft()">
                                                    <i class="fa fa-save me-1"></i> Save Draft
                                                </button>
                                                <a href="{{ route('cimsemail.index') }}" class="btn btn-outline-secondary">
                                                    <i class="fa fa-times me-1"></i> Discard
                                                </a>
                                            </div>
                                            <div id="ecSendStatus" style="font-size:12px;color:#999;"></div>
                                        </div>
                                    </form>
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
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script>
// ===========================================================================
// STATE
// ===========================================================================
var selectedClient = null;       // { client_id, client_code, company_name, ... }
var clientContacts = [];         // contacts array from AJAX
var selectedContact = null;      // primary selected contact for variable replacement
var loadedTemplate = null;       // current template object

// ===========================================================================
// INIT
// ===========================================================================
$(document).ready(function() {
    initSummernote();
    initTagInputs();
    initClientSelect();
    initTemplateSelect();
    initCcBccToggle();
    initDragDrop();
    initFormValidation();

    // Restore draft tags
    @if($draft)
    restoreTagsFromHidden();
    @endif

    // Auto-trigger if client pre-selected
    @if($selectedClientId)
    setTimeout(function() {
        $('#ecClientSelect').trigger('changed.bs.select');
    }, 500);
    @endif
});

// ===========================================================================
// SUMMERNOTE
// ===========================================================================
function initSummernote() {
    try {
        $('#ecBody').summernote({
            height: 350,
            placeholder: 'Write your email here...',
            disableDragAndDrop: false,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']],
            ],
            fontNames: ['Arial', 'Helvetica', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'Tahoma', 'Trebuchet MS', 'Segoe UI'],
            fontSizes: ['8','9','10','11','12','14','16','18','20','24','28','36','48'],
            callbacks: {
                onInit: function() {
                    $('.note-editable').css({
                        'font-family': 'Arial, sans-serif',
                        'font-size': '14px',
                        'padding': '20px',
                        'color': '#000000'
                    });
                    // Auto-append signature + disclaimer for new emails
                    @if(!$draft)
                    var sigHtml = @json($signatureHtml ?? '');
                    var discHtml = @json($disclaimerHtml ?? '');
                    var body = '';
                    if (sigHtml) {
                        body += '<br><br><div class="email-signature" style="margin-top:20px;padding-top:10px;border-top:1px solid #eee;">' + sigHtml + '</div>';
                    }
                    if (discHtml) {
                        body += discHtml;
                    }
                    if (body) {
                        $('#ecBody').summernote('code', body);
                    }
                    @endif
                }
            }
        });
    } catch(e) {
        console.error('Summernote init error:', e);
        $('#ecBody').show();
    }

    // Fix BS5 dropdown conflict
    $(document).on('click', '.note-toolbar .note-btn[data-toggle="dropdown"],.note-toolbar .dropdown-toggle', function(e) {
        e.preventDefault(); e.stopPropagation();
        var $menu = $(this).closest('.note-btn-group').find('.dropdown-menu');
        $('.note-toolbar .dropdown-menu.show').not($menu).removeClass('show');
        $menu.toggleClass('show');
    });
    $(document).on('click', function(e) { if (!$(e.target).closest('.note-toolbar').length) { $('.note-toolbar .dropdown-menu.show').removeClass('show'); } });
    $(document).on('click', '.note-toolbar .dropdown-menu a, .note-toolbar .dropdown-menu .dropdown-item', function() { $(this).closest('.dropdown-menu').removeClass('show'); });
}

function insertVar(varName) {
    $('#ecBody').summernote('editor.insertText', varName);
}

// ===========================================================================
// EMAIL TAG INPUTS (To, CC, BCC)
// ===========================================================================
function initTagInputs() {
    ['To', 'Cc', 'Bcc'].forEach(function(field) {
        var input = document.getElementById('ec' + field + 'Input');
        if (!input) return;

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',' || e.key === 'Tab') {
                e.preventDefault();
                var val = this.value.trim().replace(/,$/,'').trim();
                if (val && isValidEmail(val)) {
                    addEmailTag(field.toLowerCase(), val);
                    this.value = '';
                }
            }
            if (e.key === 'Backspace' && this.value === '') {
                // Remove last tag
                var wrap = document.getElementById('ec' + field + 'Wrap');
                var tags = wrap.querySelectorAll('.email-tag');
                if (tags.length > 0) {
                    tags[tags.length - 1].remove();
                    syncHiddenField(field.toLowerCase());
                }
            }
        });

        input.addEventListener('blur', function() {
            var val = this.value.trim().replace(/,$/,'').trim();
            if (val && isValidEmail(val)) {
                addEmailTag(field.toLowerCase(), val);
                this.value = '';
            }
        });

        // Paste handling - split by comma/semicolon
        input.addEventListener('paste', function(e) {
            var self = this;
            setTimeout(function() {
                var vals = self.value.split(/[,;]+/);
                vals.forEach(function(v) {
                    v = v.trim();
                    if (v && isValidEmail(v)) {
                        addEmailTag(field.toLowerCase(), v);
                    }
                });
                self.value = '';
            }, 50);
        });
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function addEmailTag(field, email) {
    var capField = field.charAt(0).toUpperCase() + field.slice(1);
    var wrap = document.getElementById('ec' + capField + 'Wrap');
    var input = document.getElementById('ec' + capField + 'Input');

    // Check for duplicates in this field
    var existing = wrap.querySelectorAll('.email-tag');
    for (var i = 0; i < existing.length; i++) {
        if (existing[i].dataset.email === email) return;
    }

    var tag = document.createElement('span');
    tag.className = 'email-tag';
    tag.dataset.email = email;

    // Find name from loaded contacts
    var displayName = email;
    for (var j = 0; j < clientContacts.length; j++) {
        if (clientContacts[j].email === email) {
            displayName = (clientContacts[j].known_as || clientContacts[j].first_name) + ' &lt;' + email + '&gt;';
            break;
        }
    }

    tag.innerHTML = '<i class="fas fa-user" style="font-size:9px;color:#E91E8C;"></i> ' + displayName + ' <span class="tag-remove" onclick="this.parentElement.remove();syncHiddenField(\'' + field + '\')">&times;</span>';

    wrap.insertBefore(tag, input);
    syncHiddenField(field);
}

function addEmailToField(field, email) {
    addEmailTag(field, email);
}

function syncHiddenField(field) {
    var capField = field.charAt(0).toUpperCase() + field.slice(1);
    var wrap = document.getElementById('ec' + capField + 'Wrap');
    var hidden = document.getElementById('ec' + capField + 'Hidden');
    var tags = wrap.querySelectorAll('.email-tag');
    var emails = [];
    tags.forEach(function(t) { emails.push(t.dataset.email); });
    hidden.value = emails.join(', ');
}

function restoreTagsFromHidden() {
    ['to', 'cc', 'bcc'].forEach(function(field) {
        var capField = field.charAt(0).toUpperCase() + field.slice(1);
        var hidden = document.getElementById('ec' + capField + 'Hidden');
        if (hidden && hidden.value) {
            hidden.value.split(',').forEach(function(e) {
                e = e.trim();
                if (e) addEmailTag(field, e);
            });
        }
    });
}

// ===========================================================================
// CLIENT SELECTION
// ===========================================================================
function initClientSelect() {
    $('#ecClientSelect').on('changed.bs.select change', function() {
        var clientId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var clientCode = selectedOption.data('code') || '';

        document.getElementById('ecClientIdHidden').value = clientId || '';

        if (!clientId) {
            selectedClient = null;
            clientContacts = [];
            $('#ecAddressBookSection').hide();
            $('#ecContactsList').empty();
            $('#ecSubjectPrefix').hide();
            return;
        }

        // Set subject prefix
        if (clientCode) {
            $('#ecSubjectPrefix').text('[' + clientCode + '] ').show();
        }

        // Load contacts from cims_master_contacts
        $.get('{{ url("cims/email/ajax/client-contacts") }}/' + clientId, function(data) {
            selectedClient = data.client;
            clientContacts = data.contacts || [];
            renderContactsList(clientContacts);
            $('#ecAddressBookSection').show();

            // Auto-select first contact for variable replacement
            if (clientContacts.length > 0) {
                selectContactForVariables(clientContacts[0]);
            }
        });
    });

    // Show all clients toggle
    $('#ecShowAllClients').on('change', function() {
        var showAll = $(this).is(':checked') ? 1 : 0;
        window.location.href = '{{ route("cimsemail.compose") }}?show_all=' + showAll;
    });
}

function renderContactsList(contacts) {
    var html = '';
    if (contacts.length === 0) {
        html = '<div class="text-center py-3 text-muted" style="font-size:12px;"><i class="fas fa-user-slash me-1"></i> No contacts found for this client</div>';
    }

    var avatarColors = ['#E91E8C','#2196F3','#4CAF50','#FF6B35','#9C27B0','#00BCD4','#FF5722','#795548'];

    contacts.forEach(function(c, idx) {
        var initials = ((c.first_name || '').charAt(0) + (c.last_name || '').charAt(0)).toUpperCase();
        var color = avatarColors[idx % avatarColors.length];
        var displayName = c.known_as ? c.known_as + ' (' + c.first_name + ')' : c.first_name + ' ' + c.last_name;
        var titlePrefix = c.title ? c.title + ' ' : '';

        html += '<div class="contact-pick d-flex align-items-center gap-2 mb-1" data-contact-id="' + c.id + '" onclick="pickContact(this, ' + JSON.stringify(JSON.stringify(c)) + ')">';
        html += '<div class="contact-avatar" style="background:' + color + ';">' + initials + '</div>';
        html += '<div style="min-width:0;flex:1;">';
        html += '<div style="font-size:12px;font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + titlePrefix + displayName + '</div>';
        if (c.email) {
            html += '<div style="font-size:11px;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + c.email + '</div>';
        }
        if (c.position) {
            html += '<div style="font-size:10px;color:#bbb;">' + c.position + '</div>';
        }
        html += '</div>';
        html += '<div class="d-flex gap-1">';
        if (c.email) {
            html += '<span class="badge badge-xs" style="background:#E91E8C20;color:#E91E8C;font-size:9px;cursor:pointer;" onclick="event.stopPropagation();addEmailToField(\'to\',\'' + c.email + '\')" title="Add to TO"><i class="fas fa-plus"></i> To</span>';
            html += '<span class="badge badge-xs" style="background:#2196F320;color:#2196F3;font-size:9px;cursor:pointer;" onclick="event.stopPropagation();showCcBcc();addEmailToField(\'cc\',\'' + c.email + '\')" title="Add to CC"><i class="fas fa-plus"></i> Cc</span>';
        }
        html += '</div>';
        html += '</div>';
    });

    $('#ecContactsList').html(html);
}

function pickContact(el, contactJson) {
    var contact = JSON.parse(contactJson);
    // Highlight selected
    $('.contact-pick').removeClass('selected');
    $(el).addClass('selected');
    // Add to TO field
    if (contact.email) {
        addEmailToField('to', contact.email);
    }
    // Set as selected for variable replacement
    selectContactForVariables(contact);
}

function selectContactForVariables(contact) {
    selectedContact = contact;
}

// ===========================================================================
// TEMPLATE LOADING + VARIABLE REPLACEMENT
// ===========================================================================
function initTemplateSelect() {
    $('#ecTemplateSelect').on('change', function() {
        var tplId = $(this).val();
        if (!tplId) {
            loadedTemplate = null;
            $('#ecVariableButtons').hide();
            return;
        }
        $.get('{{ url("cims/email/templates") }}/' + tplId + '/load', function(tpl) {
            loadedTemplate = tpl;

            // Set subject (without replacing prefix)
            var subjectVal = tpl.subject || '';
            $('#ecSubject').val(subjectVal);

            // Set body with signature appended
            var bodyHtml = replaceTemplateVariables(tpl.body_html || '');

            // Append signature + disclaimer
            var sigHtml = @json($signatureHtml ?? '');
            var discHtml = @json($disclaimerHtml ?? '');
            if (sigHtml) {
                bodyHtml += '<br><br><div class="email-signature" style="margin-top:20px;padding-top:10px;border-top:1px solid #eee;">' + sigHtml + '</div>';
            }
            if (discHtml) {
                bodyHtml += discHtml;
            }

            $('#ecBody').summernote('code', bodyHtml);
            $('#ecVariableButtons').show();
        });
    });
}

function replaceTemplateVariables(html) {
    if (!html) return html;

    // Contact variables
    if (selectedContact) {
        html = html.replace(/\{contact_title\}/g, selectedContact.title || '');
        html = html.replace(/\{contact_first_name\}/g, selectedContact.first_name || '');
        html = html.replace(/\{contact_last_name\}/g, selectedContact.last_name || '');
        html = html.replace(/\{contact_known_as\}/g, selectedContact.known_as || selectedContact.first_name || '');
        html = html.replace(/\{contact_email\}/g, selectedContact.email || '');
        html = html.replace(/\{contact_position\}/g, selectedContact.position || '');

        // Gender pronouns
        var gender = (selectedContact.gender || '').toLowerCase();
        if (gender === 'male') {
            html = html.replace(/\{his_her\}/g, 'his');
            html = html.replace(/\{he_she\}/g, 'he');
            html = html.replace(/\{him_her\}/g, 'him');
        } else if (gender === 'female') {
            html = html.replace(/\{his_her\}/g, 'her');
            html = html.replace(/\{he_she\}/g, 'she');
            html = html.replace(/\{him_her\}/g, 'her');
        } else {
            html = html.replace(/\{his_her\}/g, 'their');
            html = html.replace(/\{he_she\}/g, 'they');
            html = html.replace(/\{him_her\}/g, 'them');
        }
    }

    // Client variables
    if (selectedClient) {
        html = html.replace(/\{client_code\}/g, selectedClient.client_code || '');
        html = html.replace(/\{company_name\}/g, selectedClient.company_name || '');
        html = html.replace(/\{trading_name\}/g, selectedClient.trading_name || '');
        html = html.replace(/\{tax_number\}/g, selectedClient.tax_number || '');
    }

    // Sender variables
    html = html.replace(/\{sender_name\}/g, @json($fromName ?? ''));
    html = html.replace(/\{sender_email\}/g, @json($fromEmail ?? ''));
    html = html.replace(/\{sender_designation\}/g, ''); // Could be enhanced later

    // Date variables
    var now = new Date();
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    html = html.replace(/\{today_date\}/g, now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear());
    html = html.replace(/\{current_month\}/g, months[now.getMonth()]);
    html = html.replace(/\{current_year\}/g, now.getFullYear().toString());

    return html;
}

// ===========================================================================
// CC/BCC TOGGLE
// ===========================================================================
function initCcBccToggle() {
    $('#ccBccToggle').on('click', function() {
        showCcBcc();
    });
}

function showCcBcc() {
    $('#ccRow, #bccRow').show();
    $('#ccBccToggle').hide();
}

// ===========================================================================
// ATTACHMENT FILE LIST (Amazing design)
// ===========================================================================
function initDragDrop() {
    var dropZone = document.getElementById('ecAttachDropZone');
    if (!dropZone) return;

    ['dragenter','dragover'].forEach(function(ev) {
        dropZone.addEventListener(ev, function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#E91E8C';
            dropZone.style.background = '#fce4f1';
        });
    });
    ['dragleave','drop'].forEach(function(ev) {
        dropZone.addEventListener(ev, function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#fafbfc';
        });
    });
    dropZone.addEventListener('drop', function(e) {
        var input = document.getElementById('ecAttachInput');
        input.files = e.dataTransfer.files;
        renderAttachments(input);
    });
}

function renderAttachments(input) {
    var list = document.getElementById('ecAttachFileList');
    var html = '';

    for (var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        var ext = file.name.split('.').pop().toLowerCase();
        var iconClass = 'other';
        var icon = 'fa-file';

        if (['pdf'].includes(ext)) { iconClass = 'pdf'; icon = 'fa-file-pdf'; }
        else if (['jpg','jpeg','png','gif','webp','svg'].includes(ext)) { iconClass = 'img'; icon = 'fa-file-image'; }
        else if (['doc','docx'].includes(ext)) { iconClass = 'doc'; icon = 'fa-file-word'; }
        else if (['xls','xlsx','csv'].includes(ext)) { iconClass = 'xls'; icon = 'fa-file-excel'; }

        var sizeStr = file.size < 1024 ? file.size + ' B' : (file.size < 1048576 ? (file.size/1024).toFixed(1) + ' KB' : (file.size/1048576).toFixed(1) + ' MB');

        html += '<div class="attach-file-card">';
        html += '<div class="attach-file-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>';
        html += '<div style="min-width:0;flex:1;">';
        html += '<div style="font-size:13px;font-weight:500;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + file.name + '</div>';
        html += '<div style="font-size:11px;color:#999;">' + sizeStr + '</div>';
        html += '</div>';
        html += '</div>';
    }

    list.innerHTML = html;
}

// ===========================================================================
// FORM VALIDATION & DRAFT
// ===========================================================================
function initFormValidation() {
    document.getElementById('composeForm').addEventListener('submit', function(e) {
        // Sync summernote content
        $('#ecBody').summernote('triggerEvent', 'blur');

        // Client is mandatory
        var clientId = document.getElementById('ecClientIdHidden').value;
        if (!clientId) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Client Required', text: 'Please select a client before sending the email.', confirmButtonColor: '#E91E8C' });
            return false;
        }

        // At least one TO recipient
        var to = document.getElementById('ecToHidden').value;
        if (!to || to.trim() === '') {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Recipient Required', text: 'Please add at least one email address in the TO field.', confirmButtonColor: '#E91E8C' });
            return false;
        }

        // Subject
        var subj = document.getElementById('ecSubject').value;
        var prefix = document.getElementById('ecSubjectPrefix').textContent || '';
        // Prepend client code prefix to subject
        if (prefix && subj.indexOf(prefix.trim()) !== 0) {
            document.getElementById('ecSubject').value = prefix + subj;
        }

        document.getElementById('ecSendBtn').disabled = true;
        document.getElementById('ecSendBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
    });
}

function saveDraft() {
    // Sync summernote
    $('#ecBody').summernote('triggerEvent', 'blur');

    var form = document.getElementById('composeForm');
    form.action = '{{ route("cimsemail.save-draft") }}';

    // Prefix subject if needed
    var subj = document.getElementById('ecSubject').value;
    var prefix = document.getElementById('ecSubjectPrefix').textContent || '';
    if (prefix && subj && subj.indexOf(prefix.trim()) !== 0) {
        document.getElementById('ecSubject').value = prefix + subj;
    }

    form.submit();
}
</script>
@endpush
