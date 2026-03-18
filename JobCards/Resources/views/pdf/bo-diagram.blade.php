<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #333; }
    .page { padding: 30px 40px; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #0d3d56; padding-bottom: 15px; }
    .header h1 { font-size: 18px; font-weight: 700; color: #0d3d56; text-transform: uppercase; letter-spacing: 1px; }
    .header .company-name { font-size: 14px; color: #17A2B8; font-weight: 600; margin-top: 6px; }
    .header .sub { font-size: 10px; color: #666; margin-top: 4px; }
    .logo { text-align: center; margin-bottom: 10px; }
    .logo img { max-height: 50px; }
    .diagram { text-align: center; margin: 30px 0; }
    .company-box {
        display: inline-block; background: #0d3d56; color: #fff; padding: 20px 40px;
        border-radius: 10px; font-size: 14px; font-weight: 700; text-align: center;
        min-width: 300px; position: relative;
    }
    .company-box .reg { font-size: 10px; font-weight: 400; opacity: 0.8; margin-top: 4px; }
    .connector { text-align: center; margin: 10px 0; }
    .connector .line { display: inline-block; width: 2px; height: 40px; background: #0d3d56; }
    .owners-grid { width: 100%; margin-top: 10px; }
    .owners-grid td { vertical-align: top; text-align: center; padding: 8px; }
    .owner-card {
        display: inline-block; border: 2px solid #17A2B8; border-radius: 10px;
        padding: 15px 20px; text-align: center; min-width: 160px; background: #fff;
    }
    .owner-card .icon {
        font-size: 30px; color: #17A2B8; margin-bottom: 6px;
    }
    .owner-card .name { font-size: 11px; font-weight: 700; color: #0d3d56; }
    .owner-card .id-num { font-size: 9px; color: #666; margin-top: 2px; }
    .owner-card .pct {
        display: inline-block; margin-top: 8px; background: #17A2B8; color: #fff;
        padding: 4px 14px; border-radius: 12px; font-size: 12px; font-weight: 700;
    }
    .owner-card .type { font-size: 8px; color: #999; text-transform: uppercase; margin-top: 4px; }
    .owner-connector { text-align: center; }
    .owner-connector .line { display: inline-block; width: 2px; height: 25px; background: #17A2B8; }
    .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
</style>
</head>
<body>
<div class="page">
    @if($logoBase64)
    <div class="logo"><img src="{{ $logoBase64 }}" alt="Logo"></div>
    @endif

    <div class="header">
        <h1>Beneficial Ownership Diagram</h1>
        <div class="company-name">{{ $client->company_name ?? '' }}</div>
        <div class="sub">
            Registration No: {{ $client->company_reg_number ?? 'N/A' }}
            &nbsp;&middot;&nbsp; Date: {{ $generatedAt->format('d F Y') }}
        </div>
    </div>

    <div class="diagram">
        <!-- Company Box -->
        <div class="company-box">
            &#127970; {{ $client->company_name ?? '' }}
            <div class="reg">Reg: {{ $client->company_reg_number ?? 'N/A' }}</div>
            <div class="reg">Total Shares: {{ number_format($totalShares) }} {{ $shareType }}</div>
        </div>

        <!-- Connector line down -->
        <div class="connector"><div class="line"></div></div>

        <!-- Owners -->
        <table class="owners-grid" cellpadding="0" cellspacing="0">
            <tr>
                @foreach($directors as $d)
                <td>
                    <div class="owner-connector"><div class="line"></div></div>
                    @php
                        $shares = (int)($d->number_of_director_shares ?? 0);
                        $pct = $totalShares > 0 ? round(($shares / $totalShares) * 100, 2) : 0;
                    @endphp
                    <div class="owner-card">
                        <div class="icon">&#128100;</div>
                        <div class="name">{{ $d->firstname }} {{ $d->surname }}</div>
                        <div class="id-num">ID: {{ $d->identity_number ?? 'N/A' }}</div>
                        <div class="pct">{{ number_format($pct, 2) }}%</div>
                        <div class="type">{{ $d->director_type_name ?? 'Director' }}</div>
                    </div>
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ $companySettings['company_name'] }} &middot; CIPC Agent Code: {{ $agentCode }}
        &middot; Generated on {{ $generatedAt->format('d F Y') }}
    </div>
</div>
</body>
</html>
