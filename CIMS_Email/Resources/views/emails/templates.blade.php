@extends('layouts.default')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
/* Summernote Toolbar - CIMS Professional Theme (same as compose) */
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
.note-editor .note-editing-area .note-editable { background:#fff !important; min-height:200px; color:#000 !important; }
.note-editor .note-statusbar { background:#f8fafc !important; border-top:1px solid #e2e8f0 !important; }
/* Variable buttons */
.var-btn { display:inline-block; padding:3px 10px; margin:2px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#374151; transition:all 0.15s; }
.var-btn:hover { background:#E91E8C; color:#fff; border-color:#E91E8C; }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Email</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Templates</a></li>
            </ol>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-xxl-4">
                                @include('cims_email::emails.partials.sidebar', ['activePage' => 'templates'])
                            </div>
                            <div class="col-xl-9 col-xxl-8">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <h4 class="card-title mb-0"><i class="fas fa-file-code me-2" style="color:#E91E8C;"></i>Email Templates</h4>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="resetTemplateForm()">
                                            <i class="fas fa-plus me-1"></i> New Template
                                        </button>
                                    </div>

                                    @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    <div class="filter cm-content-box box-primary">
                                        <div class="content-title SlideToolHeader">
                                            <div class="cpa"><i class="fa-solid fa-envelope me-1"></i> Template List</div>
                                            <div class="tools"><a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a></div>
                                        </div>
                                        <div class="cm-content-body form excerpt">
                                            <div class="card-body pb-4">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Name</th>
                                                                <th>Category</th>
                                                                <th>Subject</th>
                                                                <th>Status</th>
                                                                <th class="pe-4">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($templates as $idx => $tpl)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td><strong>{{ $tpl->name }}</strong></td>
                                                                <td><span class="badge badge-sm light badge-primary">{{ $tpl->category }}</span></td>
                                                                <td>{{ Str::limit($tpl->subject, 40) }}</td>
                                                                <td>
                                                                    @if($tpl->is_active)
                                                                        <span class="badge badge-success light">Active</span>
                                                                    @else
                                                                        <span class="badge badge-danger light">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-nowrap">
                                                                    <a href="{{ route('cimsemail.compose') }}?template_load={{ $tpl->id }}" class="btn btn-primary btn-sm content-icon" style="display:inline-flex;align-items:center;padding:4px 8px;" title="Use">
                                                                        <i class="fa fa-paper-plane"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-warning btn-sm content-icon" style="display:inline-flex;align-items:center;padding:4px 8px;" onclick="editTemplate({{ json_encode($tpl) }})" title="Edit">
                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                    </button>
                                                                    <form method="POST" action="{{ route('cimsemail.templates.delete', $tpl->id) }}" style="display:inline;" onsubmit="return confirm('Delete this template?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm content-icon me-0" style="display:inline-flex;align-items:center;padding:4px 8px;" title="Delete">
                                                                            <i class="fa-solid fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4 text-muted">
                                                                    <i class="fas fa-file-code" style="font-size:36px;color:#ddd;display:block;margin-bottom:10px;"></i>
                                                                    No templates yet. Create your first email template!
                                                                </td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
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
    </div>

    {{-- Template Modal --}}
    <div class="modal fade" id="templateModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="border-radius:12px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg, #E91E8C, #FF6B9D);color:#fff;">
                    <h5 class="modal-title" id="templateModalTitle"><i class="fas fa-file-code me-2"></i> New Template</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="templateForm" action="{{ route('cimsemail.templates.store') }}">
                    @csrf
                    <div id="templateMethodField"></div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label" style="font-weight:600;">Template Name</label>
                                <input type="text" name="name" id="tplName" class="form-control" required placeholder="e.g. Welcome Letter">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-weight:600;">Category</label>
                                <select name="category" id="tplCategory" class="form-control">
                                    <option value="General">General</option>
                                    <option value="Compliance">Compliance</option>
                                    <option value="Tax">Tax</option>
                                    <option value="Invoicing">Invoicing</option>
                                    <option value="Reminders">Reminders</option>
                                    <option value="Notices">Notices</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Welcome">Welcome</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-weight:600;">Subject Line</label>
                                <input type="text" name="subject" id="tplSubject" class="form-control" required placeholder="Email subject...">
                            </div>
                        </div>

                        {{-- Variable Insert Buttons --}}
                        <div class="p-3 mb-3" style="background:#f8f9fa;border-radius:8px;border:1px solid #eee;">
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

                        <div class="mb-3">
                            <label class="form-label" style="font-weight:600;">Email Body</label>
                            <textarea name="body_html" id="tplBody"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script>
var tplEditorInitialized = false;

$('#templateModal').on('shown.bs.modal', function() {
    if (!tplEditorInitialized) {
        $('#tplBody').summernote({
            height: 300,
            placeholder: 'Design your email template... Use the variable buttons above to insert merge fields.',
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
            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '36', '48'],
            callbacks: {
                onInit: function() {
                    $('.note-editable').css({ 'font-family': 'Arial, sans-serif', 'font-size': '14px', 'padding': '20px', 'color': '#000000' });
                }
            }
        });
        tplEditorInitialized = true;

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
});

function insertVar(varName) {
    if (tplEditorInitialized) {
        $('#tplBody').summernote('editor.insertText', varName);
    }
}

function resetTemplateForm() {
    document.getElementById('templateModalTitle').innerHTML = '<i class="fas fa-file-code me-2"></i> New Template';
    document.getElementById('templateForm').action = '{{ route("cimsemail.templates.store") }}';
    document.getElementById('templateMethodField').innerHTML = '';
    document.getElementById('tplName').value = '';
    document.getElementById('tplSubject').value = '';
    document.getElementById('tplCategory').value = 'General';
    if (tplEditorInitialized) $('#tplBody').summernote('code', '');
}

function editTemplate(tpl) {
    document.getElementById('templateModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i> Edit Template';
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
