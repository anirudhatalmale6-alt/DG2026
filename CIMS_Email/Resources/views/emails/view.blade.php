@extends('layouts.default')

@section('content')

<style>
.ev-wrapper { max-width: 900px; margin: 0 auto; padding: 20px; }
.ev-header {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ev-header h2 { color: #fff; margin: 0; font-size: 16px; font-weight: 700; }
.ev-back { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 13px; }
.ev-back:hover { color: #fff; }
.ev-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.ev-meta {
    padding: 18px 24px;
    border-bottom: 1px solid #f0f0f0;
}
.ev-subject {
    font-size: 20px;
    font-weight: 700;
    color: #1a3c4d;
    margin-bottom: 12px;
}
.ev-meta-row {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    font-size: 13px;
}
.ev-meta-label {
    font-weight: 700;
    color: #1a3c4d;
    width: 50px;
    flex-shrink: 0;
}
.ev-meta-value { color: #555; }
.ev-meta-date { margin-left: auto; color: #999; font-size: 12px; }
.ev-status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    margin-left: 10px;
}
.ev-status-badge.sent { background: #e8f5e9; color: #2e7d32; }
.ev-status-badge.failed { background: #fce4ec; color: #c62828; }
.ev-status-badge.draft { background: #fff8e1; color: #f57f17; }
.ev-client-link {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    background: #e6f7fa;
    border: 1px solid #b3e0e8;
    border-radius: 16px;
    font-size: 12px;
    color: #0e6977;
    font-weight: 600;
    margin-left: 10px;
}
.ev-client-link i { margin-right: 4px; }
.ev-body { padding: 24px; font-size: 14px; line-height: 1.7; color: #333; }
.ev-body img { max-width: 100%; height: auto; }
.ev-attachments {
    padding: 16px 24px;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
}
.ev-attachments h5 { font-size: 12px; font-weight: 700; color: #1a3c4d; text-transform: uppercase; margin-bottom: 10px; }
.ev-att-item {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 12px;
    color: #555;
    margin-right: 8px;
    margin-bottom: 6px;
}
.ev-att-item i { margin-right: 6px; color: #148f9f; }
.ev-att-size { color: #aaa; margin-left: 6px; }
.ev-actions {
    padding: 16px 24px;
    display: flex;
    gap: 10px;
    border-top: 1px solid #f0f0f0;
}
.ev-actions .btn { font-size: 13px; font-weight: 600; }
</style>

<div class="ev-wrapper">

    <div class="ev-header">
        <h2><i class="fas fa-envelope-open" style="margin-right:8px;"></i> View Email</h2>
        <a href="{{ route('cimsemail.sent') }}" class="ev-back"><i class="fas fa-arrow-left" style="margin-right:4px;"></i> Back</a>
    </div>

    <div class="ev-card">
        {{-- Meta --}}
        <div class="ev-meta">
            <div class="ev-subject">
                {{ $email->subject ?: '(no subject)' }}
                <span class="ev-status-badge {{ $email->status }}">{{ ucfirst($email->status) }}</span>
                @if($client)
                    <span class="ev-client-link"><i class="fas fa-link"></i> {{ $client->client_code }} - {{ $client->company_name }}</span>
                @endif
            </div>
            <div class="ev-meta-row">
                <span class="ev-meta-label">From:</span>
                <span class="ev-meta-value">{{ $email->from_name }} &lt;{{ $email->from_email }}&gt;</span>
                <span class="ev-meta-date">
                    @if($email->sent_at)
                        {{ \Carbon\Carbon::parse($email->sent_at)->format('l, d M Y H:i') }}
                    @else
                        {{ \Carbon\Carbon::parse($email->created_at)->format('l, d M Y H:i') }}
                    @endif
                </span>
            </div>
            <div class="ev-meta-row">
                <span class="ev-meta-label">To:</span>
                <span class="ev-meta-value">{{ implode(', ', json_decode($email->to_emails, true) ?? []) }}</span>
            </div>
            @php $cc = json_decode($email->cc_emails, true) ?? []; @endphp
            @if(count($cc) > 0)
            <div class="ev-meta-row">
                <span class="ev-meta-label">CC:</span>
                <span class="ev-meta-value">{{ implode(', ', $cc) }}</span>
            </div>
            @endif
        </div>

        {{-- Body --}}
        <div class="ev-body">
            {!! $email->body_html !!}
        </div>

        {{-- Attachments --}}
        @if($attachments->count() > 0)
        <div class="ev-attachments">
            <h5><i class="fas fa-paperclip" style="margin-right:4px;"></i> Attachments ({{ $attachments->count() }})</h5>
            @foreach($attachments as $att)
                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="ev-att-item" style="text-decoration:none;">
                    <i class="fas fa-file"></i>
                    {{ $att->original_filename }}
                    <span class="ev-att-size">({{ number_format($att->file_size / 1024, 1) }} KB)</span>
                </a>
            @endforeach
        </div>
        @endif

        {{-- Actions --}}
        <div class="ev-actions">
            <a href="{{ route('cimsemail.compose', ['client_id' => $email->client_id]) }}" class="btn btn-sm" style="background:#148f9f;color:#fff;">
                <i class="fas fa-reply" style="margin-right:4px;"></i> New Email
            </a>
            <form method="POST" action="{{ route('cimsemail.trash', $email->id) }}" style="display:inline;" onsubmit="return confirm('Move to trash?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash" style="margin-right:4px;"></i> Delete
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
