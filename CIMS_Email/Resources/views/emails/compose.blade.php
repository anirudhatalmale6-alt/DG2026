@extends('layouts.default')

@section('content')

<style>
/* ═══════════════════════════════════════════════ */
/* COMPOSE EMAIL - PREMIUM STYLING                 */
/* ═══════════════════════════════════════════════ */
.ec-wrapper { max-width: 960px; margin: 0 auto; padding: 20px; }
.ec-header {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ec-header h2 { color: #fff; margin: 0; font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
.ec-header .ec-back-btn {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 13px;
    transition: color 0.2s;
}
.ec-header .ec-back-btn:hover { color: #fff; }
.ec-form {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

/* Form fields */
.ec-field-row {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    border-bottom: 1px solid #f0f0f0;
}
.ec-field-label {
    width: 70px;
    font-size: 12px;
    font-weight: 700;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}
.ec-field-input {
    flex: 1;
}
.ec-field-input input, .ec-field-input select {
    width: 100%;
    border: none;
    padding: 6px 0;
    font-size: 14px;
    color: #333;
    outline: none;
    background: transparent;
}
.ec-field-input input::placeholder { color: #bbb; }
.ec-cc-toggle {
    font-size: 12px;
    color: #148f9f;
    cursor: pointer;
    margin-left: 10px;
    font-weight: 600;
}
.ec-cc-toggle:hover { text-decoration: underline; }
.ec-cc-fields { display: none; }
.ec-cc-fields.show { display: block; }

/* Template selector */
.ec-template-bar {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #f0f0f0;
    gap: 10px;
}
.ec-template-bar label {
    font-size: 12px;
    font-weight: 700;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    white-space: nowrap;
}
.ec-template-bar select {
    flex: 1;
    max-width: 350px;
}

/* Client linking */
.ec-client-bar {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background: #f0f8f9;
    border-bottom: 1px solid #e0e0e0;
    gap: 10px;
}
.ec-client-bar label {
    font-size: 12px;
    font-weight: 700;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    white-space: nowrap;
}
.ec-client-contacts {
    margin-left: 10px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.ec-contact-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    background: #e6f7fa;
    border: 1px solid #b3e0e8;
    border-radius: 16px;
    font-size: 11px;
    color: #0e6977;
    cursor: pointer;
    transition: all 0.2s;
}
.ec-contact-chip:hover { background: #148f9f; color: #fff; border-color: #148f9f; }
.ec-contact-chip i { margin-right: 4px; font-size: 10px; }

/* Editor area */
.ec-editor-wrap {
    padding: 0;
    min-height: 350px;
}
.ec-editor-wrap .note-editor {
    border: none !important;
    box-shadow: none !important;
}
.ec-editor-wrap .note-toolbar {
    background: #f8f9fa !important;
    border-bottom: 1px solid #e0e0e0 !important;
    padding: 8px 16px !important;
}

/* Attachments */
.ec-attach-bar {
    padding: 12px 20px;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ec-attach-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border: 1.5px dashed #ccc;
    border-radius: 6px;
    color: #666;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}
.ec-attach-btn:hover { border-color: #148f9f; color: #148f9f; }
.ec-attach-btn i { margin-right: 6px; }
.ec-attach-list { display: flex; gap: 8px; flex-wrap: wrap; }
.ec-attach-item {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: #e9ecef;
    border-radius: 4px;
    font-size: 11px;
    color: #555;
}
.ec-attach-item i { margin-right: 4px; color: #999; }

/* Action buttons */
.ec-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    border-radius: 0 0 8px 8px;
}
.ec-btn-send {
    padding: 10px 30px;
    background: linear-gradient(135deg, #0e6977, #148f9f);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    letter-spacing: 0.5px;
}
.ec-btn-send:hover { background: linear-gradient(135deg, #0a5460, #0e6977); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,105,119,0.3); }
.ec-btn-send i { margin-right: 8px; }
.ec-btn-draft {
    padding: 8px 20px;
    background: #fff;
    color: #666;
    border: 1.5px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.ec-btn-draft:hover { border-color: #148f9f; color: #148f9f; }
.ec-btn-draft i { margin-right: 6px; }
.ec-btn-discard {
    padding: 8px 16px;
    background: transparent;
    color: #dc3545;
    border: none;
    font-size: 13px;
    cursor: pointer;
}
.ec-btn-discard:hover { text-decoration: underline; }
</style>

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">

<div class="ec-wrapper">

    {{-- Header --}}
    <div class="ec-header">
        <h2><i class="fas fa-pen-to-square" style="margin-right:10px;"></i> {{ $draft ? 'Edit Draft' : 'Compose Email' }}</h2>
        <a href="{{ route('cimsemail.index') }}" class="ec-back-btn"><i class="fas fa-arrow-left" style="margin-right:4px;"></i> Back to Inbox</a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('cimsemail.send') }}" enctype="multipart/form-data" id="composeForm">
        @csrf
        @if($draft)
            <input type="hidden" name="draft_id" value="{{ $draft->id }}">
        @endif

        <div class="ec-form">

            {{-- Client Link --}}
            <div class="ec-client-bar">
                <label><i class="fas fa-link" style="margin-right:4px;"></i> Link to Client</label>
                <div style="flex:1;max-width:350px;">
                    <select name="client_id" class="form-control form-control-sm default-select sd_drop_class" data-live-search="true" data-size="8" title="-- No Client --" id="ecClientSelect">
                        @foreach($clients as $c)
                            <option value="{{ $c->client_id }}" {{ $selectedClientId == $c->client_id ? 'selected' : '' }}>{{ $c->client_code }} - {{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ec-client-contacts" id="ecClientContacts"></div>
            </div>

            {{-- Template --}}
            <div class="ec-template-bar">
                <label><i class="fas fa-file-code" style="margin-right:4px;"></i> Template</label>
                <select class="form-control form-control-sm" id="ecTemplateSelect" style="max-width:350px;">
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

            {{-- To --}}
            <div class="ec-field-row">
                <div class="ec-field-label">To</div>
                <div class="ec-field-input">
                    <input type="text" name="to_emails" id="ecTo" placeholder="recipient@example.com (comma separated)" value="{{ $draft ? implode(', ', json_decode($draft->to_emails, true) ?? []) : '' }}">
                </div>
                <span class="ec-cc-toggle" onclick="document.querySelectorAll('.ec-cc-fields').forEach(e => e.classList.toggle('show'))">CC / BCC</span>
            </div>

            {{-- CC --}}
            <div class="ec-field-row ec-cc-fields">
                <div class="ec-field-label">CC</div>
                <div class="ec-field-input">
                    <input type="text" name="cc_emails" id="ecCc" placeholder="cc@example.com" value="{{ $draft ? implode(', ', json_decode($draft->cc_emails, true) ?? []) : '' }}">
                </div>
            </div>

            {{-- BCC --}}
            <div class="ec-field-row ec-cc-fields">
                <div class="ec-field-label">BCC</div>
                <div class="ec-field-input">
                    <input type="text" name="bcc_emails" id="ecBcc" placeholder="bcc@example.com" value="{{ $draft ? implode(', ', json_decode($draft->bcc_emails, true) ?? []) : '' }}">
                </div>
            </div>

            {{-- Subject --}}
            <div class="ec-field-row">
                <div class="ec-field-label">Subject</div>
                <div class="ec-field-input">
                    <input type="text" name="subject" id="ecSubject" placeholder="Email subject..." value="{{ $draft->subject ?? '' }}">
                </div>
            </div>

            {{-- Body (Summernote) --}}
            <div class="ec-editor-wrap">
                <textarea name="body_html" id="ecBody" style="display:none;">{{ $draft->body_html ?? '' }}</textarea>
            </div>

            {{-- Attachments --}}
            <div class="ec-attach-bar">
                <label class="ec-attach-btn" for="ecAttachInput">
                    <i class="fas fa-paperclip"></i> Attach Files
                </label>
                <input type="file" name="attachments[]" id="ecAttachInput" multiple style="display:none;" onchange="showAttachments(this)">
                <div class="ec-attach-list" id="ecAttachList"></div>
            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="ec-actions">
            <div style="display:flex;gap:10px;">
                <button type="submit" class="ec-btn-send" id="ecSendBtn">
                    <i class="fas fa-paper-plane"></i> Send Email
                </button>
                <button type="button" class="ec-btn-draft" onclick="saveDraft()">
                    <i class="fas fa-save"></i> Save Draft
                </button>
            </div>
            <a href="{{ route('cimsemail.index') }}" class="ec-btn-discard">
                <i class="fas fa-times"></i> Discard
            </a>
        </div>

    </form>
</div>

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Summernote
    $('#ecBody').summernote({
        height: 350,
        placeholder: 'Write your email here...',
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
        fontNames: ['Arial', 'Helvetica', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'Tahoma'],
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
        callbacks: {
            onInit: function() {
                // Style the editor frame
                $('.note-editable').css({
                    'font-family': 'Arial, sans-serif',
                    'font-size': '14px',
                    'padding': '20px'
                });
            }
        }
    });

    // Load client contacts on selection
    $('#ecClientSelect').on('changed.bs.select', function() {
        var clientId = $(this).val();
        if (!clientId) {
            $('#ecClientContacts').empty();
            return;
        }
        $.get('{{ url("cims/email/ajax/client-contacts") }}/' + clientId, function(contacts) {
            var html = '';
            contacts.forEach(function(c) {
                html += '<span class="ec-contact-chip" onclick="addToField(\'' + c.email + '\')" title="Click to add to To field">';
                html += '<i class="fas fa-user"></i> ' + c.name + ' (' + c.email + ')';
                html += '</span>';
            });
            $('#ecClientContacts').html(html);
        });
    });

    // Trigger on page load if client pre-selected
    @if($selectedClientId)
    setTimeout(function() {
        $('#ecClientSelect').trigger('changed.bs.select');
    }, 500);
    @endif

    // Load template
    $('#ecTemplateSelect').on('change', function() {
        var tplId = $(this).val();
        if (!tplId) return;
        $.get('{{ url("cims/email/templates") }}/' + tplId + '/load', function(tpl) {
            $('#ecSubject').val(tpl.subject);
            $('#ecBody').summernote('code', tpl.body_html);
        });
    });
});

function addToField(email) {
    var current = $('#ecTo').val();
    if (current && current.indexOf(email) !== -1) return;
    if (current) {
        $('#ecTo').val(current + ', ' + email);
    } else {
        $('#ecTo').val(email);
    }
}

function showAttachments(input) {
    var html = '';
    for (var i = 0; i < input.files.length; i++) {
        html += '<span class="ec-attach-item"><i class="fas fa-file"></i> ' + input.files[i].name + '</span>';
    }
    document.getElementById('ecAttachList').innerHTML = html;
}

function saveDraft() {
    var form = document.getElementById('composeForm');
    form.action = '{{ route("cimsemail.save-draft") }}';
    form.submit();
}
</script>
@endpush

@endsection
