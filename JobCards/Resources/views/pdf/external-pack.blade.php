<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #E91E8C; }
        .header img { max-height: 50px; margin-bottom: 8px; }
        .header h1 { font-size: 20px; color: #1a1a2e; margin: 4px 0; }
        .header .subtitle { font-size: 12px; color: #777; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1a1a2e; padding: 8px 12px; background: #f0f0f5; border-left: 4px solid #E91E8C; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .info-table td.label { font-weight: bold; color: #555; width: 40%; background: #fafafa; }
        .info-table td.value { color: #1a1a2e; }
        .checklist-table { border: 1px solid #ddd; }
        .checklist-table th { background: #1a1a2e; color: #fff; padding: 8px 10px; font-size: 11px; text-align: left; }
        .checklist-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .checklist-table tr:nth-child(even) { background: #fafafa; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 2px solid #eee; font-size: 10px; color: #999; text-align: center; }
        .submission-info { background: #f0f8ff; border: 1px solid #b0d4f1; border-radius: 4px; padding: 12px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo">
        @endif
        <h1>SUBMISSION PACK</h1>
        <div class="subtitle">{{ $companySettings['company_name'] ?? '' }}</div>
    </div>

    <!-- Submission Info -->
    @if($jobType && $jobType->submission_to)
    <div class="submission-info">
        <strong>Submission To:</strong> {{ $jobType->submission_to }}<br>
        <strong>Job Reference:</strong> {{ $jobCard->job_code }}<br>
        <strong>Date Prepared:</strong> {{ $generatedAt }}
    </div>
    @endif

    <!-- Client Information -->
    <div class="section">
        <div class="section-title">Client Information</div>
        <table class="info-table">
            <tr><td class="label">Company Name</td><td class="value">{{ $client->company_name ?? '' }}</td></tr>
            <tr><td class="label">Client Code</td><td class="value">{{ $client->client_code ?? '' }}</td></tr>
            @foreach($fields as $f)
            @if($f->value)
            <tr><td class="label">{{ $f->field_label }}</td><td class="value">{{ $f->value }}</td></tr>
            @endif
            @endforeach
        </table>
    </div>

    <!-- Document Checklist -->
    <div class="section">
        <div class="section-title">Document Checklist</div>
        <table class="checklist-table">
            <thead>
                <tr><th style="width:40px;">#</th><th>Document</th><th style="width:80px;">Status</th></tr>
            </thead>
            <tbody>
                @foreach($requiredDocs as $i => $doc)
                @php
                    $hasDoc = $attachments->where('document_type_id', $doc->document_type_id)->count() > 0;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $doc->document_label }}</td>
                    <td style="color:{{ $hasDoc ? '#28a745' : '#dc3545' }};font-weight:bold;">
                        {{ $hasDoc ? 'Included' : 'Missing' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Prepared By -->
    <div class="section">
        <div class="section-title">Prepared By</div>
        <table class="info-table">
            <tr><td class="label">Prepared By</td><td class="value">{{ $generatedBy }}</td></tr>
            <tr><td class="label">Date</td><td class="value">{{ $generatedAt }}</td></tr>
            <tr><td class="label">Reference</td><td class="value">{{ $jobCard->job_code }}</td></tr>
        </table>
    </div>

    <div class="footer">
        <p>{{ $companySettings['company_name'] ?? '' }} — Prepared {{ $generatedAt }}</p>
        @if($companySettings['phone'] ?? null)
            <p>Tel: {{ $companySettings['phone'] }} | Email: {{ $companySettings['email'] ?? '' }}</p>
        @endif
    </div>
</body>
</html>
