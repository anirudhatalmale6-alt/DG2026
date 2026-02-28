@extends('layouts.default')

@section('content')

<style>
.et-wrapper { max-width: 1000px; margin: 0 auto; padding: 20px; }
.et-header {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.et-header h2 { color: #fff; margin: 0; font-size: 18px; font-weight: 700; }
.et-back { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 13px; }
.et-back:hover { color: #fff; }
.et-body {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.et-btn-new {
    display: inline-flex;
    align-items: center;
    padding: 8px 20px;
    background: linear-gradient(135deg, #d6006e, #e91e8c);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    margin-bottom: 20px;
}
.et-btn-new:hover { opacity: 0.9; }
.et-btn-new i { margin-right: 6px; }

/* Template cards */
.et-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.et-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s;
}
.et-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-2px); }
.et-card-header {
    background: #f8f9fa;
    padding: 12px 16px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.et-card-name {
    font-size: 14px;
    font-weight: 700;
    color: #1a3c4d;
}
.et-card-category {
    font-size: 10px;
    padding: 2px 8px;
    background: #e6f7fa;
    color: #148f9f;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
}
.et-card-body {
    padding: 14px 16px;
}
.et-card-subject {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
}
.et-card-subject strong { color: #333; }
.et-card-preview {
    font-size: 11px;
    color: #999;
    max-height: 60px;
    overflow: hidden;
    line-height: 1.4;
}
.et-card-actions {
    padding: 10px 16px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 8px;
}
.et-card-actions .btn { font-size: 11px; padding: 4px 10px; }

/* Modal overrides */
.et-modal .modal-header {
    background: linear-gradient(135deg, #0e6977, #148f9f);
    color: #fff;
}
.et-modal .modal-header .btn-close { filter: brightness(0) invert(1); }
.et-modal .modal-title { font-weight: 700; font-size: 16px; }
</style>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">

<div class="et-wrapper">

    <div class="et-header">
        <h2><i class="fas fa-file-code" style="margin-right:10px;"></i> Email Templates</h2>
        <a href="{{ route('cimsemail.index') }}" class="et-back"><i class="fas fa-arrow-left" style="margin-right:4px;"></i> Back to Mail</a>
    </div>

    <div class="et-body">
        <button type="button" class="et-btn-new" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="resetTemplateForm()">
            <i class="fas fa-plus"></i> New Template
        </button>

        <div class="et-grid">
            @forelse($templates as $tpl)
            <div class="et-card">
                <div class="et-card-header">
                    <div class="et-card-name">{{ $tpl->name }}</div>
                    <div class="et-card-category">{{ $tpl->category }}</div>
                </div>
                <div class="et-card-body">
                    <div class="et-card-subject"><strong>Subject:</strong> {{ $tpl->subject }}</div>
                    <div class="et-card-preview">{{ Str::limit(strip_tags($tpl->body_html), 120) }}</div>
                </div>
                <div class="et-card-actions">
                    <a href="{{ route('cimsemail.compose') }}?template_load={{ $tpl->id }}" class="btn btn-sm" style="background:#148f9f;color:#fff;">
                        <i class="fas fa-pen"></i> Use
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editTemplate({{ json_encode($tpl) }})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('cimsemail.templates.delete', $tpl->id) }}" style="display:inline;" onsubmit="return confirm('Delete this template?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:40px;color:#999;">
                <i class="fas fa-file-code" style="font-size:36px;color:#ddd;margin-bottom:12px;display:block;"></i>
                No templates yet. Create your first email template!
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Template Modal --}}
<div class="modal fade et-modal" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="templateModalTitle"><i class="fas fa-file-code" style="margin-right:8px;"></i> New Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="templateForm" action="{{ route('cimsemail.templates.store') }}">
                @csrf
                <div id="templateMethodField"></div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label" style="font-weight:600;color:#1a3c4d;">Template Name</label>
                            <input type="text" name="name" id="tplName" class="form-control" required placeholder="e.g. Welcome Letter">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#1a3c4d;">Category</label>
                            <select name="category" id="tplCategory" class="form-control">
                                <option value="General">General</option>
                                <option value="Compliance">Compliance</option>
                                <option value="Invoicing">Invoicing</option>
                                <option value="Reminders">Reminders</option>
                                <option value="Notices">Notices</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:#1a3c4d;">Subject Line</label>
                        <input type="text" name="subject" id="tplSubject" class="form-control" required placeholder="Email subject...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:#1a3c4d;">Body</label>
                        <textarea name="body_html" id="tplBody"></textarea>
                    </div>
                    <div class="p-2" style="background:#f8f9fa;border-radius:4px;font-size:11px;color:#888;">
                        <strong>Merge Fields:</strong> {client_name}, {company_name}, {tax_number}, {user_name}, {month}, {year}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#148f9f;color:#fff;font-weight:600;">
                        <i class="fas fa-save" style="margin-right:4px;"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>

@push('scripts')
<script>
var tplEditorInitialized = false;

$('#templateModal').on('shown.bs.modal', function() {
    if (!tplEditorInitialized) {
        $('#tplBody').summernote({
            height: 250,
            placeholder: 'Design your email template...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['codeview']],
            ]
        });
        tplEditorInitialized = true;
    }
});

function resetTemplateForm() {
    document.getElementById('templateModalTitle').innerHTML = '<i class="fas fa-file-code" style="margin-right:8px;"></i> New Template';
    document.getElementById('templateForm').action = '{{ route("cimsemail.templates.store") }}';
    document.getElementById('templateMethodField').innerHTML = '';
    document.getElementById('tplName').value = '';
    document.getElementById('tplSubject').value = '';
    document.getElementById('tplCategory').value = 'General';
    if (tplEditorInitialized) $('#tplBody').summernote('code', '');
}

function editTemplate(tpl) {
    document.getElementById('templateModalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right:8px;"></i> Edit Template';
    document.getElementById('templateForm').action = '{{ url("cims/email/templates") }}/' + tpl.id;
    document.getElementById('templateMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('tplName').value = tpl.name;
    document.getElementById('tplSubject').value = tpl.subject;
    document.getElementById('tplCategory').value = tpl.category;
    if (tplEditorInitialized) {
        $('#tplBody').summernote('code', tpl.body_html || '');
    }
    var modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}
</script>
@endpush

@endsection
