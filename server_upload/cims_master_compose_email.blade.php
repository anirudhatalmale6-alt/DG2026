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
.attach-file-icon.img { background:#E91E8C; color:#fff; }
.attach-file-icon.doc { background:#2196F3; color:#fff; }
.attach-file-icon.xls { background:#28A745; color:#fff; }
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

/* Client select dropdown - clean styling */
#ecClientSelectWrap .bootstrap-select { width:100% !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-toggle { background:#fff !important; border:1px solid #d1d5db !important; border-radius:6px !important; padding:10px 14px !important; font-size:18px !important; color:#333 !important; height:auto !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-toggle .filter-option-inner-inner { font-size:18px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-toggle:focus { border-color:#E91E8C !important; box-shadow:0 0 0 3px rgba(233,30,140,0.1) !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu { border:1px solid #e2e8f0 !important; border-radius:8px !important; box-shadow:0 8px 24px rgba(0,0,0,0.12) !important; padding:4px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .inner { overflow-y:auto !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar { width:6px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-track { background:#f1f1f1 !important; border-radius:3px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb { background:#ccc !important; border-radius:3px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb:hover { background:#999 !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input { border-radius:6px !important; border:1px solid #e2e8f0 !important; font-size:18px !important; padding:10px 14px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input:focus { border-color:#E91E8C !important; box-shadow:0 0 0 2px rgba(233,30,140,0.1) !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu li a { padding:8px 14px !important; font-size:18px !important; border-radius:4px !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu li a:hover { background:#fce4f1 !important; color:#E91E8C !important; }
#ecClientSelectWrap .bootstrap-select .dropdown-menu li.selected a { background:#E91E8C !important; color:#fff !important; }


/* TO and CC tag inputs - matching CLIENT text box size */
.ec-recipient-row .email-tags-wrap { min-height:48px; padding:10px 14px; font-size:18px; border:1px solid #d1d5db; border-radius:6px; }
.ec-recipient-row .email-tags-wrap:focus-within { border-color:#E91E8C; box-shadow:0 0 0 3px rgba(233,30,140,0.1); }
.ec-recipient-row .email-tags-input { font-size:18px; }
.ec-recipient-row .email-tag { font-size:13px; padding:4px 10px; }

/* Contacts panel inside grey box */
.ec-contacts-panel { border-top:1px solid #e2e8f0; margin-top:10px; padding-top:10px; }
.ec-contacts-panel .ec-contact-row { display:flex; align-items:center; gap:8px; padding:6px 10px; border-radius:6px; transition:background 0.15s; }
.ec-contacts-panel .ec-contact-row:hover { background:#f8f9fa; }
.ec-contacts-panel .ec-contact-avatar { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; flex-shrink:0; }
.ec-contacts-panel .ec-contact-info { flex:1; min-width:0; }
.ec-contacts-panel .ec-contact-name { font-size:13px; font-weight:600; color:#333; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ec-contacts-panel .ec-contact-email { font-size:11px; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ec-contacts-panel .ec-contact-actions { display:flex; gap:4px; flex-shrink:0; }
.ec-contact-btn { display:inline-flex; align-items:center; gap:3px; padding:3px 10px; border-radius:14px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid; transition:all 0.15s; white-space:nowrap; }
.ec-contact-btn.btn-to { background:#E91E8C10; color:#E91E8C; border-color:#E91E8C40; }
.ec-contact-btn.btn-to:hover { background:#E91E8C; color:#fff; border-color:#E91E8C; }
.ec-contact-btn.btn-cc { background:#2196F310; color:#2196F3; border-color:#2196F340; }
.ec-contact-btn.btn-cc:hover { background:#2196F3; color:#fff; border-color:#2196F3; }
.ec-contacts-empty { text-align:center; padding:12px; font-size:12px; color:#999; }
.ec-contacts-search { border:1px solid #e2e8f0; border-radius:6px; padding:6px 10px; font-size:13px; width:100%; margin-bottom:6px; outline:none; }
.ec-contacts-search:focus { border-color:#E91E8C; box-shadow:0 0 0 2px rgba(233,30,140,0.1); }

/* Period + Template + Client dropdown z-index fix - must appear above Summernote toolbar */
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu,
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu,
#ecClientSelectWrap .bootstrap-select .dropdown-menu { z-index:99999 !important; }

/* Period select dropdown - matching CLIENT styling */
#ecPeriodSelectWrap .bootstrap-select { width:100% !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-toggle { background:#fff !important; border:1px solid #d1d5db !important; border-radius:6px !important; padding:10px 14px !important; font-size:18px !important; color:#333 !important; height:auto !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-toggle .filter-option-inner-inner { font-size:18px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-toggle:focus { border-color:#E91E8C !important; box-shadow:0 0 0 3px rgba(233,30,140,0.1) !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu { border:1px solid #e2e8f0 !important; border-radius:8px !important; box-shadow:0 8px 24px rgba(0,0,0,0.12) !important; padding:4px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .inner { overflow-y:auto !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar { width:6px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-track { background:#f1f1f1 !important; border-radius:3px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb { background:#ccc !important; border-radius:3px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb:hover { background:#999 !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input { border-radius:6px !important; border:1px solid #e2e8f0 !important; font-size:18px !important; padding:10px 14px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input:focus { border-color:#E91E8C !important; box-shadow:0 0 0 2px rgba(233,30,140,0.1) !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu li a { padding:8px 14px !important; font-size:18px !important; border-radius:4px !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu li a:hover { background:#fce4f1 !important; color:#E91E8C !important; }
#ecPeriodSelectWrap .bootstrap-select .dropdown-menu li.selected a { background:#E91E8C !important; color:#fff !important; }

/* Template select dropdown - matching CLIENT styling */
#ecTemplateSelectWrap .bootstrap-select { width:100% !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-toggle { background:#fff !important; border:1px solid #d1d5db !important; border-radius:6px !important; padding:10px 14px !important; font-size:18px !important; color:#333 !important; height:auto !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-toggle .filter-option-inner-inner { font-size:18px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-toggle:focus { border-color:#E91E8C !important; box-shadow:0 0 0 3px rgba(233,30,140,0.1) !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu { border:1px solid #e2e8f0 !important; border-radius:8px !important; box-shadow:0 8px 24px rgba(0,0,0,0.12) !important; padding:4px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .inner { overflow-y:auto !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar { width:6px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-track { background:#f1f1f1 !important; border-radius:3px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb { background:#ccc !important; border-radius:3px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .inner::-webkit-scrollbar-thumb:hover { background:#999 !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input { border-radius:6px !important; border:1px solid #e2e8f0 !important; font-size:18px !important; padding:10px 14px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .bs-searchbox input:focus { border-color:#E91E8C !important; box-shadow:0 0 0 2px rgba(233,30,140,0.1) !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu li a { padding:8px 14px !important; font-size:18px !important; border-radius:4px !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu li a:hover { background:#fce4f1 !important; color:#E91E8C !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu li.selected a { background:#E91E8C !important; color:#fff !important; }
#ecTemplateSelectWrap .bootstrap-select .dropdown-menu .dropdown-header { font-size:12px !important; font-weight:700 !important; color:#E91E8C !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:8px 14px 4px !important; }

/* Subject textarea - must override Bootstrap .form-control height */
textarea#ecSubject.form-control { height:auto !important; min-height:44px !important; overflow:hidden !important; resize:none !important; line-height:1.4 !important; }

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
                                            {{ $rc['known_as'] ?: \Illuminate\Support\Str::limit($rc['name'], 15) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Main compose area --}}
                            <div class="col-xl-9 col-xxl-8">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <h4 class="card-title mb-0"><i class="fas fa-pen-fancy me-2" style="color:#E91E8C;"></i>{{ $draft ? 'Edit Draft' : 'Compose Email' }}</h4>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span id="ecSendStatus" style="font-size:12px;color:#999;"></span>
                                            <button class="btn btn-primary btn-sm" type="submit" form="composeForm" id="ecSendBtn">
                                                <i class="fa fa-paper-plane me-1"></i> Send
                                            </button>
                                            <button class="btn btn-warning light btn-sm" type="button" onclick="saveDraft()">
                                                <i class="fa fa-save me-1"></i> Draft
                                            </button>
                                            <a href="{{ route('cimsemail.index') }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa fa-times me-1"></i> Discard
                                            </a>
                                        </div>
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
                                        <input type="hidden" name="bcc_emails" id="ecBccHidden" value="">

                                        {{-- CLIENT SELECTION (Mandatory) --}}
                                        <div class="mb-3" style="border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;background:#fafbfc;position:relative;z-index:300;overflow:visible;">
                                            <div class="d-flex align-items-center gap-3">
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;">
                                                    <i class="fas fa-building me-1"></i>Client
                                                </span>
                                                <div class="flex-grow-1" id="ecClientSelectWrap">
                                                    <select class="form-control default-select sd_drop_class" data-live-search="true" data-size="6" data-dropup-auto="false" title="-- Select Client (Required) --" id="ecClientSelect" style="width:100%;">
                                                        @foreach($clients as $c)
                                                            <option value="{{ $c->client_id }}" data-code="{{ $c->client_code }}" {{ $selectedClientId == $c->client_id ? 'selected' : '' }}>
                                                                {{ $c->client_code }} - {{ $c->company_name }}{{ !$c->is_active ? ' (Inactive)' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="form-check-label d-flex align-items-center gap-1" style="font-size:11px;cursor:pointer;white-space:nowrap;color:#888;">
                                                    <input type="checkbox" class="form-check-input" id="ecShowAllClients" {{ $showAll ? 'checked' : '' }} style="width:14px;height:14px;">
                                                    Show all
                                                </label>
                                            </div>
                                        </div>

                                        {{-- TO + CC SECTION (Grey box matching CLIENT styling) --}}
                                        <div class="mb-3" style="border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;background:#fafbfc;">
                                            {{-- TO - Tag based input --}}
                                            <div class="d-flex align-items-center gap-3 mb-2 ec-recipient-row">
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;">
                                                    <i class="fas fa-paper-plane me-1"></i>To
                                                </span>
                                                <div class="flex-grow-1">
                                                    <div class="email-tags-wrap" id="ecToWrap" onclick="document.getElementById('ecToInput').focus()">
                                                        <input type="text" class="email-tags-input" id="ecToInput" placeholder="Select from contacts below or type email..." autocomplete="off">
                                                    </div>
                                                </div>
                                                <button type="button" id="ecRefreshContactsBtn" onclick="refreshClientContacts()" title="Refresh client contacts" style="background:#E91E8C10;border:1px solid #E91E8C40;border-radius:8px;padding:8px 12px;cursor:pointer;color:#E91E8C;font-size:16px;transition:all 0.15s;flex-shrink:0;" onmouseover="this.style.background='#E91E8C';this.style.color='#fff';this.style.borderColor='#E91E8C'" onmouseout="this.style.background='#E91E8C10';this.style.color='#E91E8C';this.style.borderColor='#E91E8C40'">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                            {{-- CC - Always visible --}}
                                            <div class="d-flex align-items-center gap-3 ec-recipient-row" style="padding-right:75px;">
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;">
                                                    <i class="fas fa-copy me-1"></i>Cc
                                                </span>
                                                <div class="flex-grow-1">
                                                    <div class="email-tags-wrap" id="ecCcWrap" onclick="document.getElementById('ecCcInput').focus()">
                                                        <input type="text" class="email-tags-input" id="ecCcInput" placeholder="Type email and press Enter..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Contacts panel with TO/CC buttons --}}
                                            <div class="ec-contacts-panel" id="ecContactsPanel" style="display:none;">
                                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#999;margin-bottom:6px;">
                                                    <i class="fas fa-address-book me-1" style="color:#2196F3;"></i> Client Contacts
                                                </div>
                                                <input type="text" class="ec-contacts-search" id="ecContactsSearch" placeholder="Search contacts..." autocomplete="off">
                                                <div id="ecContactsPanelList" style="max-height:200px;overflow-y:auto;"></div>
                                            </div>
                                        </div>

                                        {{-- PERIOD + TEMPLATE SELECTOR (Grey box matching CLIENT styling) --}}
                                        <div class="mb-3" style="border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;background:#fafbfc;position:relative;z-index:200;overflow:visible;">
                                            <div class="d-flex align-items-center gap-3">
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;">
                                                    <i class="fas fa-calendar-alt me-1"></i>Period
                                                </span>
                                                <div style="flex:2;" id="ecPeriodSelectWrap">
                                                    <select class="form-control default-select sd_drop_class" data-live-search="true" data-size="6" data-dropup-auto="false" title="Select Period" id="ecPeriodSelect" name="period_id" style="width:100%;">
                                                        @foreach($periods as $p)
                                                            <option value="{{ $p->id }}" data-subtext="{{ $p->tax_year }}" data-tax-year="{{ $p->tax_year }}" data-period-name="{{ $p->period_name }}">{{ $p->period_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;">
                                                    <i class="fas fa-file-code me-1"></i>Tmpl
                                                </span>
                                                <div style="flex:3;" id="ecTemplateSelectWrap">
                                                    <select class="form-control default-select sd_drop_class" data-live-search="true" data-size="6" data-dropup-auto="false" title="Select Email Template" id="ecTemplateSelect" style="width:100%;">
                                                        @foreach($templates as $t)
                                                            <option value="{{ $t->id }}" data-subtext="{{ $t->category }}">{{ $t->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SUBJECT --}}
                                        <div class="mb-3" style="border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;background:#fafbfc;">
                                            <div class="d-flex align-items-start gap-3">
                                                <span style="font-size:12px;font-weight:700;color:#E91E8C;min-width:55px;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                                                    <i class="fas fa-heading me-1"></i>Subj
                                                </span>
                                                <div class="flex-grow-1">
                                                    <div style="border:1px solid #d1d5db;border-radius:6px;background:#fff;">
                                                        <textarea name="subject" id="ecSubject" class="form-control border-0" placeholder="Subject line..." rows="1" style="box-shadow:none;font-size:18px;padding:10px 14px;resize:none;overflow:hidden;min-height:44px;">{{ $draft->subject ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <button type="button" id="ecRefreshSubjectBtn" onclick="regenerateSubject()" title="Regenerate subject from template" style="background:#E91E8C10;border:1px solid #E91E8C40;border-radius:8px;padding:8px 12px;cursor:pointer;color:#E91E8C;font-size:16px;transition:all 0.15s;flex-shrink:0;margin-top:2px;" onmouseover="this.style.background='#E91E8C';this.style.color='#fff';this.style.borderColor='#E91E8C'" onmouseout="this.style.background='#E91E8C10';this.style.color='#E91E8C';this.style.borderColor='#E91E8C40'">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
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

                                        {{-- EMAIL BODY (Summernote) --}}
                                        <div class="mb-3">
                                            <textarea id="ecBody" name="body_html" class="form-control" rows="12" placeholder="Write your email here...">{{ $draft->body_html ?? '' }}</textarea>
                                        </div>

                                        {{-- End of form fields --}}
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
var selectedPeriod = null;       // { period_name, tax_year } from period dropdown

// ===========================================================================
// INIT
// ===========================================================================
// Manual emails removed - using CC for alternatives

$(document).ready(function() {
    initSummernote();
    initTagInputs();
    initClientSelect();
    initToContactSelect();
    initPeriodSelect();
    initTemplateSelect();
    initDragDrop();
    initFormValidation();
    initSubjectAutoResize();

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

    // Auto-select template if pre-specified (from Document Viewer etc.)
    @if(isset($selectedTemplateId) && $selectedTemplateId)
    setTimeout(function() {
        $('#ecTemplateSelect').val('{{ $selectedTemplateId }}');
        $('#ecTemplateSelect').selectpicker('refresh');
        $('#ecTemplateSelect').trigger('changed.bs.select');
    }, 800);
    @endif

    // Show pre-attached document from Document Manager
    @if(isset($preAttachedDoc) && $preAttachedDoc)
    (function() {
        var doc = {
            id: {{ $preAttachedDoc->id }},
            name: {!! json_encode($preAttachedDoc->file_stored_name ?? $preAttachedDoc->file_original_name ?? 'Document') !!},
            size: {{ $preAttachedDoc->file_size ?? 0 }}
        };
        var ext = doc.name.split('.').pop().toLowerCase();
        var iconClass = 'other', iconLabel = ext.toUpperCase();
        if (ext === 'pdf') iconClass = 'pdf';
        else if (['jpg','jpeg','png','gif','webp'].indexOf(ext) >= 0) iconClass = 'img';
        else if (['doc','docx'].indexOf(ext) >= 0) iconClass = 'doc';
        else if (['xls','xlsx'].indexOf(ext) >= 0) iconClass = 'xls';

        var sizeStr = doc.size > 1048576 ? (doc.size / 1048576).toFixed(1) + ' MB' : (doc.size / 1024).toFixed(1) + ' KB';

        var html = '<div class="attach-file-card mb-2" id="preAttachedDoc_' + doc.id + '">'
            + '<div class="attach-file-icon ' + iconClass + '"><i class="fas fa-file"></i></div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + doc.name + '</div>'
            + '<div style="font-size:11px;color:#999;">' + sizeStr + ' &bull; From Document Manager</div>'
            + '</div>'
            + '<span class="attach-file-remove" onclick="$(\'#preAttachedDoc_' + doc.id + '\').remove();$(\'#preAttachedInput_' + doc.id + '\').remove();">&times;</span>'
            + '<input type="hidden" name="pre_attached_document_ids[]" value="' + doc.id + '" id="preAttachedInput_' + doc.id + '">'
            + '</div>';

        $('#ecAttachList').prepend(html);
        $('#ecAttachSummary').show();
    })();
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
// SUBJECT AUTO-RESIZE (textarea wraps text, grows with content)
// ===========================================================================
function resizeSubject() {
    var el = document.getElementById('ecSubject');
    if (!el) return;
    // Force height to auto first so scrollHeight recalculates
    el.style.setProperty('height', 'auto', 'important');
    // Now read the scroll height and apply it
    var newH = Math.max(44, el.scrollHeight);
    el.style.setProperty('height', newH + 'px', 'important');
}
function initSubjectAutoResize() {
    var el = document.getElementById('ecSubject');
    if (!el) return;
    el.addEventListener('input', resizeSubject);
    el.addEventListener('change', resizeSubject);
    // Initial resize
    setTimeout(resizeSubject, 300);
    // Also resize on window resize (in case column width changes)
    window.addEventListener('resize', resizeSubject);
}

// ===========================================================================
// EMAIL TAG INPUTS (To, CC, BCC)
// ===========================================================================
function initTagInputs() {
    ['To', 'Cc'].forEach(function(field) {
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
    // Restore TO tags from hidden field
    var toHidden = document.getElementById('ecToHidden');
    if (toHidden && toHidden.value) {
        toHidden.value.split(',').forEach(function(e) {
            e = e.trim();
            if (e) addEmailTag('to', e);
        });
    }
    // Restore CC tags from hidden field
    var ccHidden = document.getElementById('ecCcHidden');
    if (ccHidden && ccHidden.value) {
        ccHidden.value.split(',').forEach(function(e) {
            e = e.trim();
            if (e) addEmailTag('cc', e);
        });
    }
}

// ===========================================================================
// CLIENT SELECTION
// ===========================================================================
function initClientSelect() {
    // Disable dropup auto-detection for client select only
    var sp = $('#ecClientSelect').data('selectpicker');
    if (sp) { sp.options.dropupAuto = false; }

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
            populateToContactSelect([]);
            return;
        }

        // Client code prefix is now embedded in subject text via buildSubjectWithPrefix()

        // Load contacts from cims_master_contacts
        $.get('{{ url("cims/email/ajax/client-contacts") }}/' + clientId)
        .done(function(data) {
            selectedClient = data.client;
            clientContacts = data.contacts || [];
            renderContactsList(clientContacts);
            $('#ecAddressBookSection').show();

            // Populate the TO multi-select dropdown
            populateToContactSelect(clientContacts);

            // Auto-select first contact for variable replacement
            if (clientContacts.length > 0) {
                selectContactForVariables(clientContacts[0]);
            }
        })
        .fail(function(xhr) {
            console.error('Failed to load contacts:', xhr.status, xhr.responseText);
            Swal.fire({icon:'error', title:'Error', text:'Could not load contacts for this client. Please try again.'});
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
        if (c.photo) {
            html += '<div class="contact-avatar" style="background:' + color + ';overflow:hidden;"><img src="{{ url("/storage/contact_photos") }}/' + c.photo + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.innerHTML=\'' + initials + '\';"></div>';
        } else {
            html += '<div class="contact-avatar" style="background:' + color + ';">' + initials + '</div>';
        }
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
            html += '<span class="badge badge-xs" style="background:#2196F320;color:#2196F3;font-size:9px;cursor:pointer;" onclick="event.stopPropagation();addEmailToField(\'cc\',\'' + c.email + '\')" title="Add to CC"><i class="fas fa-plus"></i> Cc</span>';
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
    // Add to TO multi-select
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
// CONTACTS PANEL (with TO / CC buttons)
// ===========================================================================
function initToContactSelect() {
    // Search filter for contacts panel
    $('#ecContactsSearch').on('input', function() {
        var q = $(this).val().toLowerCase();
        $('#ecContactsPanelList .ec-contact-row').each(function() {
            var text = $(this).data('search') || '';
            $(this).toggle(text.indexOf(q) !== -1);
        });
    });
}

function populateToContactSelect(contacts) {
    var panel = document.getElementById('ecContactsPanelList');
    var avatarColors = ['#E91E8C','#2196F3','#4CAF50','#FF6B35','#9C27B0','#00BCD4','#FF5722','#795548'];

    if (!contacts || contacts.length === 0) {
        panel.innerHTML = '<div class="ec-contacts-empty"><i class="fas fa-user-slash me-1"></i> No contacts found</div>';
        $('#ecContactsPanel').hide();
        return;
    }

    var html = '';
    contacts.forEach(function(c, idx) {
        if (!c.email) return;
        var initials = ((c.first_name || '').charAt(0) + (c.last_name || '').charAt(0)).toUpperCase();
        var color = avatarColors[idx % avatarColors.length];
        var displayName = (c.title ? c.title + ' ' : '') + (c.known_as || c.first_name) + ' ' + c.last_name;
        var searchText = (displayName + ' ' + c.email + ' ' + (c.position || '')).toLowerCase();

        html += '<div class="ec-contact-row" data-search="' + searchText + '">';
        if (c.photo) {
            html += '<div class="ec-contact-avatar" style="background:' + color + ';overflow:hidden;"><img src="{{ url("/storage/contact_photos") }}/' + c.photo + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.innerHTML=\'' + initials + '\';"></div>';
        } else {
            html += '<div class="ec-contact-avatar" style="background:' + color + ';">' + initials + '</div>';
        }
        html += '<div class="ec-contact-info">';
        html += '<div class="ec-contact-name">' + displayName + '</div>';
        html += '<div class="ec-contact-email">' + c.email + '</div>';
        html += '</div>';
        html += '<div class="ec-contact-actions">';
        html += '<span class="ec-contact-btn btn-to" onclick="addContactTo(\'' + c.email + '\', ' + idx + ')" title="Add to TO"><i class="fas fa-plus" style="font-size:9px;"></i> To</span>';
        html += '<span class="ec-contact-btn btn-cc" onclick="addContactCc(\'' + c.email + '\', ' + idx + ')" title="Add to CC"><i class="fas fa-plus" style="font-size:9px;"></i> Cc</span>';
        html += '</div>';
        html += '</div>';
    });

    panel.innerHTML = html;
    $('#ecContactsPanel').show();
    $('#ecContactsSearch').val('');
}

function addContactTo(email, contactIdx) {
    addEmailTag('to', email);
    // Set contact for variable replacement
    if (clientContacts[contactIdx]) {
        selectContactForVariables(clientContacts[contactIdx]);
    }
}

function addContactCc(email, contactIdx) {
    addEmailTag('cc', email);
}

function syncToHiddenField() {
    // Sync TO hidden field from tags (same as syncHiddenField but for 'to')
    syncHiddenField('to');
}

// ===========================================================================
// REFRESH CLIENT CONTACTS
// ===========================================================================
function refreshClientContacts() {
    var clientId = $('#ecClientSelect').val();
    if (!clientId) {
        Swal.fire({icon:'info', title:'No Client', text:'Please select a client first.'});
        return;
    }

    // Spin the icon for feedback
    var btn = document.getElementById('ecRefreshContactsBtn');
    var icon = btn.querySelector('i');
    icon.classList.add('fa-spin');

    $.get('{{ url("cims/email/ajax/client-contacts") }}/' + clientId)
    .done(function(data) {
        selectedClient = data.client;
        clientContacts = data.contacts || [];
        renderContactsList(clientContacts);
        populateToContactSelect(clientContacts);
        icon.classList.remove('fa-spin');
    })
    .fail(function(xhr) {
        icon.classList.remove('fa-spin');
        Swal.fire({icon:'error', title:'Error', text:'Could not reload contacts. Please try again.'});
    });
}

// ===========================================================================
// PERIOD SELECTION
// ===========================================================================
function initPeriodSelect() {
    $('#ecPeriodSelect').on('changed.bs.select change', function() {
        var opt = $(this).find('option:selected');
        if (!opt.val()) {
            selectedPeriod = null;
            return;
        }
        selectedPeriod = {
            period_name: opt.data('period-name') || opt.text().trim(),
            tax_year: opt.data('tax-year') || ''
        };
    });
}

// ===========================================================================
// TEMPLATE LOADING + VARIABLE REPLACEMENT
// ===========================================================================
function initTemplateSelect() {
    $('#ecTemplateSelect').on('changed.bs.select change', function() {
        var tplId = $(this).val();
        if (!tplId) {
            loadedTemplate = null;
            // Variable buttons section hidden
            return;
        }
        $.get('{{ url("cims/email/templates") }}/' + tplId + '/load', function(tpl) {
            loadedTemplate = tpl;

            // Set subject with variable replacement + client code prefix
            var subjectVal = buildSubjectWithPrefix(replaceTemplateVariables(tpl.subject || ''));
            $('#ecSubject').val(subjectVal);
            resizeSubject();

            // Set body with variable replacement
            var bodyHtml = replaceTemplateVariables(tpl.body_html || '');

            // Strip duplicate company name + address from template body
            // (now shown in the header block, no need to repeat)
            if (selectedClient) {
                var cn = selectedClient.company_name || '';
                var tn = selectedClient.trading_name || '';
                var addr = selectedClient.resolved_address || '';
                // Remove company name lines (bold or plain)
                if (cn) bodyHtml = bodyHtml.replace(new RegExp('<[^>]*>\\s*' + cn.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*<\\/[^>]*>\\s*(<br\\s*/?>)?', 'gi'), '');
                if (tn) bodyHtml = bodyHtml.replace(new RegExp('<[^>]*>\\s*' + tn.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*<\\/[^>]*>\\s*(<br\\s*/?>)?', 'gi'), '');
                // Remove address line
                if (addr) bodyHtml = bodyHtml.replace(new RegExp(addr.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*(<br\\s*/?>)?', 'gi'), '');
                // Clean up leading whitespace/breaks
                bodyHtml = bodyHtml.replace(/^(\s*<br\s*\/?>\s*)+/i, '');
            }

            // Prepend premium client info header block
            var headerBlock = buildEmailHeaderBlock();
            if (headerBlock) {
                // Add formatted date line after header
                var dateLine = buildEmailDateLine();
                bodyHtml = headerBlock + dateLine + bodyHtml;
            }

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
            // Variable buttons section hidden
        });
    });
}

function buildEmailHeaderBlock() {
    if (!selectedClient) return '';

    var companyName = selectedClient.company_name || selectedClient.trading_name || '';
    var clientCode = selectedClient.client_code || '';
    var address = selectedClient.resolved_address || '';
    // Contact fields from client_master
    var phoneBusiness = selectedClient.phone_business || '';
    var phoneDirect = selectedClient.direct || '';
    var phoneMobile = selectedClient.phone_mobile || '';
    var phoneWhatsapp = selectedClient.phone_whatsapp || '';
    var emailCompliance = selectedClient.email || '';
    var emailAdmin = selectedClient.email_admin || '';
    var website = selectedClient.website || '';

    if (!companyName) return '';

    // Get period info for the date line
    var periodText = '';
    if (selectedPeriod && selectedPeriod.period_name) {
        var pMatch = selectedPeriod.period_name.match(/- (.+)/);
        if (pMatch) periodText = pMatch[1];
    }
    if (!periodText) {
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        var now = new Date();
        periodText = months[now.getMonth()] + ' ' + now.getFullYear();
    }

    var html = '';
    // Top accent line
    html += '<div style="border-top:2px solid #E91E8C;margin-bottom:0;"></div>';
    // Header table
    html += '<table cellpadding="0" cellspacing="0" style="width:100%;font-family:Arial,sans-serif;margin:0;padding:0;">';
    // Company name row
    html += '<tr><td style="padding:14px 0 6px 0;">';
    html += '<div style="font-size:18px;font-weight:700;color:#1a1a2e;letter-spacing:0.3px;">' + companyName + '</div>';
    if (clientCode) {
        html += '<div style="font-size:11px;color:#999;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-top:2px;">Client Code: ' + clientCode + '</div>';
    }
    html += '</td>';
    html += '<td style="text-align:right;vertical-align:top;padding:14px 0 6px 0;">';
    html += '<div style="font-size:12px;color:#888;font-weight:600;">' + periodText + '</div>';
    html += '</td></tr>';
    // Thin separator
    html += '<tr><td colspan="2" style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';
    // Address line
    if (address) {
        html += '<tr><td colspan="2" style="padding:8px 0 4px 0;font-size:13px;color:#444;">';
        html += '<span style="color:#E91E8C;font-size:10px;">&#9654;</span>&nbsp; ' + address;
        html += '</td></tr>';
    }
    // Phone details line
    var phoneParts = [];
    if (phoneBusiness) phoneParts.push('<span style="color:#2196F3;font-weight:600;font-size:10px;">TEL</span>&nbsp;' + phoneBusiness);
    if (phoneDirect) phoneParts.push('<span style="color:#9C27B0;font-weight:600;font-size:10px;">DIRECT</span>&nbsp;' + phoneDirect);
    if (phoneMobile) phoneParts.push('<span style="color:#FF6B35;font-weight:600;font-size:10px;">MOBILE</span>&nbsp;' + phoneMobile);
    if (phoneWhatsapp) phoneParts.push('<span style="color:#25D366;font-weight:600;font-size:10px;">WHATSAPP</span>&nbsp;' + phoneWhatsapp);
    if (phoneParts.length > 0) {
        html += '<tr><td colspan="2" style="padding:4px 0;font-size:12px;color:#555;">';
        html += phoneParts.join(' &nbsp;<span style="color:#ddd;">|</span>&nbsp; ');
        html += '</td></tr>';
    }
    // Email + website line
    var emailParts = [];
    if (emailCompliance) emailParts.push('<span style="color:#E91E8C;font-weight:600;font-size:10px;">EMAIL</span>&nbsp;<a href="mailto:' + emailCompliance + '" style="color:#333;text-decoration:none;">' + emailCompliance + '</a>');
    if (emailAdmin) emailParts.push('<span style="color:#E91E8C;font-weight:600;font-size:10px;">ADMIN</span>&nbsp;<a href="mailto:' + emailAdmin + '" style="color:#333;text-decoration:none;">' + emailAdmin + '</a>');
    if (website) {
        var wsUrl = website;
        if (!/^https?:\/\//.test(wsUrl)) wsUrl = 'https://' + wsUrl;
        emailParts.push('<span style="color:#00BCD4;font-weight:600;font-size:10px;">WEB</span>&nbsp;<a href="' + wsUrl + '" style="color:#333;text-decoration:none;" target="_blank">' + website.replace(/^https?:\/\//, '') + '</a>');
    }
    if (emailParts.length > 0) {
        html += '<tr><td colspan="2" style="padding:4px 0 10px 0;font-size:12px;color:#555;">';
        html += emailParts.join(' &nbsp;<span style="color:#ddd;">|</span>&nbsp; ');
        html += '</td></tr>';
    }
    html += '</table>';
    // Bottom divider
    html += '<div style="border-bottom:1px solid #e0e0e0;margin-bottom:16px;"></div>';

    return html;
}

function buildEmailDateLine() {
    var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    // Use South African time (UTC+2 / Africa/Johannesburg)
    var saOptions = { timeZone: 'Africa/Johannesburg' };
    var saStr = new Date().toLocaleString('en-ZA', saOptions);
    var saDate = new Date(saStr);
    var dayName = days[saDate.getDay()];
    var dayNum = saDate.getDate();
    var monthName = months[saDate.getMonth()];
    var year = saDate.getFullYear();

    return '<div style="font-size:13px;color:#333;margin-bottom:12px;"><strong>Date:</strong> ' + dayName + ' ' + dayNum + ' ' + monthName + ' ' + year + '</div>';
}

function buildSubjectWithPrefix(subject) {
    // Prepend [CLIENT_CODE] if a client is selected and prefix not already present
    if (selectedClient && selectedClient.client_code) {
        var prefix = '[' + selectedClient.client_code + '] ';
        if (subject.indexOf(prefix) !== 0) {
            // Remove any existing client code prefix first
            subject = subject.replace(/^\[[A-Z0-9]+\]\s*/i, '');
            subject = prefix + subject;
        }
    }
    return subject;
}

function regenerateSubject() {
    if (!loadedTemplate) {
        Swal.fire({icon:'info', title:'No Template', text:'Please select a template first to regenerate the subject.'});
        return;
    }
    var subjectVal = buildSubjectWithPrefix(replaceTemplateVariables(loadedTemplate.subject || ''));
    $('#ecSubject').val(subjectVal);
    resizeSubject();

    // Quick visual feedback - spin the icon
    var btn = document.getElementById('ecRefreshSubjectBtn');
    var icon = btn.querySelector('i');
    icon.classList.add('fa-spin');
    setTimeout(function() { icon.classList.remove('fa-spin'); }, 600);
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

        // Client address - from resolved_address (loaded from cims_addresses via client_master_addresses)
        html = html.replace(/\{client_address\}/g, selectedClient.resolved_address || '');

        // Client contact details (from client_master fields)
        html = html.replace(/\{client_telephone\}/g, selectedClient.phone_business || '');
        html = html.replace(/\{client_direct\}/g, selectedClient.direct || '');
        html = html.replace(/\{client_mobile\}/g, selectedClient.phone_mobile || '');
        html = html.replace(/\{client_whatsapp\}/g, selectedClient.phone_whatsapp || '');
        html = html.replace(/\{client_email\}/g, selectedClient.email || '');
        html = html.replace(/\{client_email_admin\}/g, selectedClient.email_admin || '');
        html = html.replace(/\{client_website\}/g, selectedClient.website || '');
    }

    // Sender variables
    html = html.replace(/\{sender_name\}/g, @json($fromName ?? ''));
    html = html.replace(/\{sender_email\}/g, @json($fromEmail ?? ''));
    html = html.replace(/\{sender_designation\}/g, ''); // Could be enhanced later

    // Date variables - use selected period if available, otherwise current date
    var now = new Date();
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var periodMonth = months[now.getMonth()];
    var periodYear = now.getFullYear().toString();

    if (selectedPeriod && selectedPeriod.period_name) {
        // period_name format: "202602 - February 2026"
        var pMatch = selectedPeriod.period_name.match(/- (\w+)\s+(\d{4})/);
        if (pMatch) {
            periodMonth = pMatch[1];
            periodYear = pMatch[2];
        }
    }

    html = html.replace(/\{today_date\}/g, now.getDate() + ' ' + periodMonth + ' ' + periodYear);
    html = html.replace(/\{current_month\}/g, periodMonth);
    html = html.replace(/\{current_year\}/g, periodYear);

    return html;
}

// CC is always visible, no toggle needed

// ===========================================================================
// ATTACHMENT FILE LIST (Amazing design) - Accumulates multiple selections
// ===========================================================================
var collectedFiles = []; // Stores all accumulated File objects

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
        for (var i = 0; i < e.dataTransfer.files.length; i++) {
            collectedFiles.push(e.dataTransfer.files[i]);
        }
        syncFileInput();
        renderAttachmentList();
    });
}

function renderAttachments(input) {
    // Add newly selected files to collection
    for (var i = 0; i < input.files.length; i++) {
        collectedFiles.push(input.files[i]);
    }
    syncFileInput();
    renderAttachmentList();
}

function syncFileInput() {
    // Rebuild the file input's FileList from our collected files
    var dt = new DataTransfer();
    collectedFiles.forEach(function(f) { dt.items.add(f); });
    document.getElementById('ecAttachInput').files = dt.files;
}

function removeAttachment(idx) {
    collectedFiles.splice(idx, 1);
    syncFileInput();
    renderAttachmentList();
}

function renderAttachmentList() {
    var list = document.getElementById('ecAttachFileList');
    if (collectedFiles.length === 0) { list.innerHTML = ''; return; }

    var html = '';
    for (var i = 0; i < collectedFiles.length; i++) {
        var file = collectedFiles[i];
        var ext = file.name.split('.').pop().toLowerCase();
        var iconClass = 'other';
        var icon = 'fa-file';

        if (['pdf'].includes(ext)) { iconClass = 'pdf'; icon = 'fa-file-pdf'; }
        else if (['jpg','jpeg','png','gif','webp','svg'].includes(ext)) { iconClass = 'img'; icon = 'fa-file-image'; }
        else if (['doc','docx'].includes(ext)) { iconClass = 'doc'; icon = 'fa-file-word'; }
        else if (['xls','xlsx','csv'].includes(ext)) { iconClass = 'xls'; icon = 'fa-file-excel'; }
        else if (['zip','rar','7z'].includes(ext)) { iconClass = 'other'; icon = 'fa-file-archive'; }

        var sizeStr = file.size < 1024 ? file.size + ' B' : (file.size < 1048576 ? (file.size/1024).toFixed(1) + ' KB' : (file.size/1048576).toFixed(1) + ' MB');

        html += '<div class="attach-file-card">';
        html += '<div class="attach-file-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>';
        html += '<div style="min-width:0;flex:1;">';
        html += '<div style="font-size:13px;font-weight:500;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + file.name + '</div>';
        html += '<div style="font-size:11px;color:#999;">' + sizeStr + '</div>';
        html += '</div>';
        html += '<span class="attach-file-remove" onclick="removeAttachment(' + i + ')" title="Remove"><i class="fas fa-times-circle"></i></span>';
        html += '</div>';
    }

    // File count summary
    var totalSize = 0;
    collectedFiles.forEach(function(f) { totalSize += f.size; });
    var totalStr = totalSize < 1048576 ? (totalSize/1024).toFixed(1) + ' KB' : (totalSize/1048576).toFixed(1) + ' MB';
    html += '<div style="font-size:11px;color:#999;text-align:right;padding-top:4px;">' + collectedFiles.length + ' file(s) &middot; ' + totalStr + ' total</div>';

    list.innerHTML = html;
}

// ===========================================================================
// FORM VALIDATION & DRAFT
// ===========================================================================
function initFormValidation() {
    document.getElementById('composeForm').addEventListener('submit', function(e) {
        // Sync summernote content
        $('#ecBody').summernote('triggerEvent', 'blur');

        // Force sync TO and CC fields before submitting
        syncHiddenField('to');
        syncHiddenField('cc');

        var toVal = document.getElementById('ecToHidden').value;
        console.log('[CIMS Email] Submitting with TO:', toVal);

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

        // Subject - prefix already embedded by buildSubjectWithPrefix()

        document.getElementById('ecSendBtn').disabled = true;
        document.getElementById('ecSendBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
    });
}

function saveDraft() {
    // Sync summernote
    $('#ecBody').summernote('triggerEvent', 'blur');

    var form = document.getElementById('composeForm');
    form.action = '{{ route("cimsemail.save-draft") }}';

    // Subject prefix already embedded by buildSubjectWithPrefix()

    form.submit();
}
</script>
@endpush
