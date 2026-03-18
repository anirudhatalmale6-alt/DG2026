<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #333; }
    .page { padding: 30px 40px; }
    .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #0d3d56; padding-bottom: 15px; }
    .header h1 { font-size: 18px; font-weight: 700; color: #0d3d56; text-transform: uppercase; letter-spacing: 1px; }
    .header .company-name { font-size: 14px; color: #17A2B8; font-weight: 600; margin-top: 6px; }
    .header .sub { font-size: 10px; color: #666; margin-top: 4px; }
    .logo { text-align: center; margin-bottom: 10px; }
    .logo img { max-height: 50px; }
    table.reg-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table.reg-table thead th {
        background: #0d3d56; color: #fff; font-weight: 700; padding: 8px 10px;
        text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;
        border: 1px solid #0a2e40;
    }
    table.reg-table thead th.text-right { text-align: right; }
    table.reg-table tbody td {
        padding: 7px 10px; border: 1px solid #dde4ec; font-size: 10px; vertical-align: middle;
    }
    table.reg-table tbody tr:nth-child(even) { background: #fafbfc; }
    table.reg-table tbody td.text-right { text-align: right; font-weight: 500; }
    table.reg-table tfoot td {
        background: #0d3d56; color: #fff; font-weight: 700; padding: 8px 10px;
        border: 1px solid #0a2e40; font-size: 10px;
    }
    table.reg-table tfoot td.text-right { text-align: right; }
    .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    .date-line { font-size: 10px; color: #555; margin-top: 8px; text-align: right; }
</style>
</head>
<body>
<div class="page">
    @if($logoBase64)
    <div class="logo"><img src="{{ $logoBase64 }}" alt="Logo"></div>
    @endif

    <div class="header">
        <h1>Register of Shareholders</h1>
        <div class="company-name">{{ $client->company_name ?? '' }}</div>
        <div class="sub">
            Registration No: {{ $client->company_reg_number ?? 'N/A' }}
            &nbsp;&middot;&nbsp; Date: {{ $generatedAt->format('d F Y') }}
        </div>
    </div>

    <table class="reg-table">
        <thead>
            <tr>
                <th style="width:25px;">#</th>
                <th>Shareholder</th>
                <th>Share Class</th>
                <th>Certificate No.</th>
                <th class="text-right">Holding</th>
                <th class="text-right">Equity %</th>
                <th>Date Became Shareholder</th>
                <th>Date Ceased</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPct = 0; $totalHolding = 0; @endphp
            @foreach($directors as $i => $d)
            @php
                $shares = (int)($d->number_of_director_shares ?? 0);
                $pct = $totalShares > 0 ? round(($shares / $totalShares) * 100, 2) : 0;
                $totalPct += $pct;
                $totalHolding += $shares;
                $cert = $certificates->where('director_id', $d->id)->first();
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $d->firstname }} {{ $d->surname }}</strong></td>
                <td>{{ $shareType }}</td>
                <td>{{ $cert->certificate_number ?? '-' }}</td>
                <td class="text-right">{{ number_format($shares) }}</td>
                <td class="text-right">{{ number_format($pct, 2) }}%</td>
                <td>{{ $d->appointment_date ? \Carbon\Carbon::parse($d->appointment_date)->format('d/m/Y') : '-' }}</td>
                <td>{{ $d->resignation_date ? \Carbon\Carbon::parse($d->resignation_date)->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;">TOTALS</td>
                <td class="text-right">{{ number_format($totalHolding) }}</td>
                <td class="text-right">{{ number_format($totalPct, 2) }}%</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="date-line">
        Generated on {{ $generatedAt->format('l d F Y') }} at {{ $generatedAt->format('H:i') }}
    </div>

    <div class="footer">
        {{ $companySettings['company_name'] }} &middot; CIPC Agent Code: {{ $agentCode }}
    </div>
</div>
</body>
</html>
