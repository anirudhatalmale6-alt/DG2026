@extends('layouts.default')

@section('content')

<style>
/* ═══════════════════════════════════════════════ */
/* CIMS EMAIL MODULE - PREMIUM STYLING             */
/* ═══════════════════════════════════════════════ */

.em-wrapper { display: flex; min-height: calc(100vh - 120px); background: #f4f6f9; }

/* Sidebar */
.em-sidebar {
    width: 260px;
    background: #fff;
    border-right: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
}
.em-sidebar-header {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    padding: 18px 20px;
    text-align: center;
}
.em-sidebar-header h3 {
    color: #fff;
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.em-sidebar-header .em-subtitle {
    color: rgba(255,255,255,0.7);
    font-size: 11px;
    margin-top: 3px;
}
.em-compose-btn {
    display: block;
    margin: 16px;
    padding: 12px;
    background: linear-gradient(135deg, #d6006e 0%, #e91e8c 100%);
    color: #fff;
    text-align: center;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    letter-spacing: 0.5px;
}
.em-compose-btn:hover { background: linear-gradient(135deg, #b8005d 0%, #d6006e 100%); color: #fff; text-decoration: none; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(214,0,110,0.3); }
.em-compose-btn i { margin-right: 8px; }
.em-nav { list-style: none; padding: 0; margin: 0; }
.em-nav li {}
.em-nav li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}
.em-nav li a:hover { background: #f0f8f9; color: #148f9f; }
.em-nav li a.active { background: #e6f7fa; color: #0e6977; border-left-color: #148f9f; font-weight: 700; }
.em-nav li a i { width: 22px; margin-right: 10px; font-size: 14px; text-align: center; }
.em-nav-count {
    margin-left: auto;
    background: #e9ecef;
    color: #666;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}
.em-nav li a.active .em-nav-count { background: #148f9f; color: #fff; }
.em-nav-divider { height: 1px; background: #e9ecef; margin: 8px 16px; }

/* Main content */
.em-main { flex: 1; display: flex; flex-direction: column; }
.em-toolbar {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    gap: 12px;
}
.em-toolbar-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.em-search-box {
    flex: 1;
    max-width: 400px;
    position: relative;
}
.em-search-box input {
    width: 100%;
    padding: 8px 14px 8px 36px;
    border: 1.5px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
}
.em-search-box input:focus { border-color: #148f9f; }
.em-search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 13px;
}
.em-filter-select { min-width: 200px; }

/* Email list */
.em-list { flex: 1; overflow-y: auto; }
.em-list-item {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.15s;
}
.em-list-item:hover { background: #f8fafb; }
.em-list-item.unread { background: #f0f8ff; }
.em-list-item.unread .em-list-subject { font-weight: 700; }
.em-list-status {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 14px;
    flex-shrink: 0;
}
.em-list-status.sent { background: #28a745; }
.em-list-status.draft { background: #ffc107; }
.em-list-status.failed { background: #dc3545; }
.em-list-status.trash { background: #999; }
.em-list-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #148f9f, #0e6977);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 15px;
    margin-right: 14px;
    flex-shrink: 0;
}
.em-list-body { flex: 1; min-width: 0; }
.em-list-to {
    font-size: 13px;
    font-weight: 600;
    color: #1a3c4d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.em-list-subject {
    font-size: 13px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.em-list-preview {
    font-size: 12px;
    color: #999;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.em-list-meta {
    text-align: right;
    flex-shrink: 0;
    margin-left: 14px;
}
.em-list-date {
    font-size: 11px;
    color: #999;
    white-space: nowrap;
}
.em-list-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 4px;
}
.em-list-badge.sent { background: #e8f5e9; color: #2e7d32; }
.em-list-badge.draft { background: #fff8e1; color: #f57f17; }
.em-list-badge.failed { background: #fce4ec; color: #c62828; }
.em-list-client-tag {
    display: inline-block;
    padding: 1px 6px;
    background: #e6f7fa;
    color: #148f9f;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    margin-top: 4px;
}
.em-list-attachment { color: #999; font-size: 12px; margin-left: 6px; }

/* Empty state */
.em-empty {
    text-align: center;
    padding: 80px 30px;
    color: #bbb;
}
.em-empty i { font-size: 52px; margin-bottom: 16px; color: #ddd; }
.em-empty h4 { color: #888; font-size: 16px; margin-bottom: 6px; }
.em-empty p { color: #aaa; font-size: 13px; }

/* Pagination */
.em-pagination { padding: 12px 20px; background: #fff; border-top: 1px solid #e0e0e0; display: flex; justify-content: center; }
</style>

<div class="em-wrapper">

    {{-- Sidebar --}}
    <div class="em-sidebar">
        <div class="em-sidebar-header">
            <h3><i class="fas fa-envelope"></i> CIMS Mail</h3>
            <div class="em-subtitle">Email Management</div>
        </div>

        <a href="{{ route('cimsemail.compose') }}" class="em-compose-btn">
            <i class="fas fa-pen-to-square"></i> Compose Email
        </a>

        <ul class="em-nav">
            <li><a href="{{ route('cimsemail.sent') }}" class="{{ $folder == 'sent' ? 'active' : '' }}"><i class="fas fa-paper-plane"></i> Sent <span class="em-nav-count">{{ $counts['sent'] ?? 0 }}</span></a></li>
            <li><a href="{{ route('cimsemail.drafts') }}" class="{{ $folder == 'drafts' ? 'active' : '' }}"><i class="fas fa-file-pen"></i> Drafts <span class="em-nav-count">{{ $counts['drafts'] ?? 0 }}</span></a></li>
            <div class="em-nav-divider"></div>
            <li><a href="{{ route('cimsemail.index', ['folder' => 'trash']) }}" class="{{ $folder == 'trash' ? 'active' : '' }}"><i class="fas fa-trash-can"></i> Trash <span class="em-nav-count">{{ $counts['trash'] ?? 0 }}</span></a></li>
            <div class="em-nav-divider"></div>
            <li><a href="{{ route('cimsemail.templates') }}"><i class="fas fa-file-code"></i> Templates</a></li>
        </ul>
    </div>

    {{-- Main Content --}}
    <div class="em-main">

        {{-- Toolbar --}}
        <div class="em-toolbar">
            <div class="em-toolbar-title">
                @if($folder == 'sent') <i class="fas fa-paper-plane" style="color:#148f9f;"></i> Sent
                @elseif($folder == 'drafts') <i class="fas fa-file-pen" style="color:#f57f17;"></i> Drafts
                @elseif($folder == 'trash') <i class="fas fa-trash-can" style="color:#dc3545;"></i> Trash
                @endif
            </div>
            <form method="GET" action="{{ route('cimsemail.index') }}" class="em-search-box">
                <input type="hidden" name="folder" value="{{ $folder }}">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search emails...">
            </form>
            <div class="em-filter-select">
                <select name="client_id" class="form-control form-control-sm default-select sd_drop_class" data-live-search="true" data-size="8" title="All Clients" onchange="window.location='{{ route('cimsemail.index') }}?folder={{ $folder }}&client_id='+this.value">
                    @foreach($clients as $c)
                        <option value="{{ $c->client_id }}" {{ $clientFilter == $c->client_id ? 'selected' : '' }}>{{ $c->client_code }} - {{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Email List --}}
        <div class="em-list">
            @forelse($emails as $email)
                @php
                    $toList = json_decode($email->to_emails, true) ?? [];
                    $toDisplay = implode(', ', array_slice($toList, 0, 2));
                    if (count($toList) > 2) $toDisplay .= ' +' . (count($toList) - 2);
                    $initials = strtoupper(substr($toList[0] ?? 'E', 0, 1));
                @endphp
                <a href="{{ $email->folder == 'drafts' ? route('cimsemail.compose', ['draft_id' => $email->id]) : route('cimsemail.view', $email->id) }}" class="em-list-item {{ !$email->is_read ? 'unread' : '' }}" style="text-decoration:none;">
                    <div class="em-list-status {{ $email->status }}"></div>
                    <div class="em-list-avatar">{{ $initials }}</div>
                    <div class="em-list-body">
                        <div class="em-list-to">{{ $toDisplay ?: '(no recipient)' }}</div>
                        <div class="em-list-subject">{{ $email->subject ?: '(no subject)' }}</div>
                        <div class="em-list-preview">{{ Str::limit($email->body_text, 80) }}</div>
                    </div>
                    <div class="em-list-meta">
                        <div class="em-list-date">
                            @if($email->sent_at)
                                {{ \Carbon\Carbon::parse($email->sent_at)->format('d M Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($email->created_at)->format('d M Y H:i') }}
                            @endif
                        </div>
                        <div class="em-list-badge {{ $email->status }}">{{ ucfirst($email->status) }}</div>
                        @if($email->client_id)
                            @php $cl = $clients->firstWhere('client_id', $email->client_id); @endphp
                            @if($cl)
                                <div class="em-list-client-tag"><i class="fas fa-link" style="margin-right:3px;"></i> {{ $cl->client_code }}</div>
                            @endif
                        @endif
                    </div>
                </a>
            @empty
                <div class="em-empty">
                    <div><i class="fas fa-envelope-open"></i></div>
                    <h4>No emails found</h4>
                    <p>
                        @if($folder == 'sent') You haven't sent any emails yet.
                        @elseif($folder == 'drafts') No saved drafts.
                        @elseif($folder == 'trash') Trash is empty.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($emails->hasPages())
        <div class="em-pagination">
            {{ $emails->appends(request()->query())->links() }}
        </div>
        @endif

    </div>
</div>

@endsection
