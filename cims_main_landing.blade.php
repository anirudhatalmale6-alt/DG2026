<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CIMS 3000 - Main Landing</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- MetisMenu CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/metismenu/dist/metisMenu.min.css">

    <style>
        :root {
            --cims-teal: #17A2B8;
            --cims-dark: #0d3d56;
            --cims-light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #fff;
        }

        /* Main content area */
        .cims-main-content {
            flex: 1;
            padding: 40px 30px;
            background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        }

        .cims-welcome-section {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .cims-welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--cims-dark);
            margin-bottom: 15px;
        }

        .cims-welcome-subtitle {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 40px;
        }

        .cims-welcome-subtitle strong {
            color: var(--cims-teal);
        }

        /* Feature cards */
        .cims-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .cims-feature-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--cims-teal);
            transition: all 0.3s ease;
        }

        .cims-feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .cims-feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--cims-dark) 0%, var(--cims-teal) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .cims-feature-icon i {
            font-size: 1.5rem;
            color: #fff;
        }

        .cims-feature-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--cims-dark);
            margin-bottom: 12px;
        }

        .cims-feature-card p {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.6;
        }

        /* Quick action buttons */
        .cims-quick-actions {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }

        .cims-quick-actions h4 {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 20px;
        }

        .cims-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            margin: 5px;
            background: var(--cims-dark);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .cims-action-btn:hover {
            background: var(--cims-teal);
            color: #fff;
            transform: translateY(-2px);
        }

        .cims-action-btn i {
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cims-welcome-title {
                font-size: 1.8rem;
            }

            .cims-welcome-subtitle {
                font-size: 1.1rem;
            }

            .cims-features {
                grid-template-columns: 1fr;
            }
        }

        /* =============================================
           HORIZONTAL MENU STYLES
           Force the menu to display horizontally
        ============================================= */
        [data-layout="horizontal"] .dlabnav {
            width: 100% !important;
            position: relative !important;
            left: 0 !important;
            height: auto !important;
            min-height: auto !important;
        }

        [data-layout="horizontal"] .dlabnav .dlabnav-scroll {
            overflow: visible !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu > li {
            display: inline-block !important;
            position: relative !important;
            margin: 0 !important;
            list-style: none !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu > li > a {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 15px 18px !important;
            color: rgba(255,255,255,0.9) !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu > li > a i {
            font-size: 16px !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu > li > a .nav-text {
            color: inherit !important;
        }

        /* Dropdown menus */
        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse {
            display: none !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%) !important;
            min-width: 220px !important;
            z-index: 9999 !important;
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse.mm-show,
        [data-layout="horizontal"] .dlabnav .metismenu li:hover > .mm-collapse {
            display: block !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse li {
            list-style: none !important;
            margin: 0 !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse li a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 12px 20px !important;
            color: rgba(255,255,255,0.85) !important;
            text-decoration: none !important;
            font-size: 13px !important;
            white-space: nowrap !important;
        }

        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse li a:hover {
            background: linear-gradient(135deg, #0a2d3d 0%, #0d4a5e 100%) !important;
            color: #fff !important;
            border-bottom: 3px solid #20c997 !important;
        }

        /* Third level dropdown */
        [data-layout="horizontal"] .dlabnav .metismenu .mm-collapse .mm-collapse {
            top: 0 !important;
            left: 100% !important;
        }

        /* Hide copyright in horizontal mode */
        [data-layout="horizontal"] .dlabnav .copyright {
            display: none !important;
        }

        /* Hide tooltips by default */
        .sd_tooltip_teal,
        .sd-menu-tooltip,
        .sd-mainmenu-tooltip {
            display: none !important;
        }
    </style>
</head>
<body data-layout="horizontal">

<!-- CIMS Notification System -->
<script>
    var CIMS = CIMS || {};
    CIMS.notify = function(message, type, duration) {
        type = type || 'success';
        duration = duration || 3000;
        var existing = document.querySelector('.cims-notify');
        if (existing) existing.remove();
        var icons = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        var colors = { success: '#17A2B8', error: '#dc3545', warning: '#ffc107', info: '#0d3d56' };
        var notify = document.createElement('div');
        notify.className = 'cims-notify cims-notify-' + type;
        notify.innerHTML = '<i class="fas ' + icons[type] + '"></i> <span>' + message + '</span>';
        notify.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;padding:15px 25px;border-radius:6px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.15);display:flex;align-items:center;gap:12px;font-size:14px;font-weight:500;color:#333;border-left:4px solid ' + colors[type] + ';animation:cimsSlideIn 0.3s ease;';
        notify.querySelector('i').style.cssText = 'font-size:20px;color:' + colors[type] + ';';
        if (!document.getElementById('cims-notify-styles')) {
            var style = document.createElement('style');
            style.id = 'cims-notify-styles';
            style.textContent = '@keyframes cimsSlideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes cimsSlideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}';
            document.head.appendChild(style);
        }
        document.body.appendChild(notify);
        setTimeout(function() {
            notify.style.animation = 'cimsSlideOut 0.3s ease forwards';
            setTimeout(function() { notify.remove(); }, 300);
        }, duration);
    };
</script>

<!-- CIMS Header -->
@include('cimscore::partials.cims_header')

<!-- CIMS Horizontal Menu -->
@include('cimscore::partials.cims_menu_horizontal')

<!-- Main Content -->
<div class="cims-main-content">
    <div class="cims-welcome-section">
        <h1 class="cims-welcome-title">Welcome to CIMS 3000</h1>
        <p class="cims-welcome-subtitle">
            <strong>Client Information Management System</strong> — Your complete solution for accounting, taxation, payroll, and compliance management
        </p>

        <!-- Feature Cards -->
        <div class="cims-features">
            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-building-columns"></i>
                </div>
                <h3>Entity Management</h3>
                <p>Manage clients, companies, trusts, and all entity relationships in one centralized system.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Accounting & Finance</h3>
                <p>Full general ledger, accounts receivable/payable, banking reconciliation, and financial reporting.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3>Taxation</h3>
                <p>Corporate and personal tax, VAT/GST management, tax calendar, filings, and planning tools.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Payroll</h3>
                <p>Complete payroll processing, employee management, leave tracking, and statutory compliance.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-scale-balanced"></i>
                </div>
                <h3>Compliance</h3>
                <p>Regulatory compliance tracking, risk assessments, audits, and governance management.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-folder-tree"></i>
                </div>
                <h3>Document Management</h3>
                <p>Secure document storage, version control, OCR search, and retention policies.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3>Contracts</h3>
                <p>Engagement letters, contracts, SLA management, obligations tracking, and renewals.</p>
            </div>

            <div class="cims-feature-card">
                <div class="cims-feature-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <h3>Bizz Box</h3>
                <p>Automated document and contract generation with smart forms and policy templates.</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="cims-quick-actions">
            <h4>Quick Actions</h4>
            <a href="/cims/persons" class="cims-action-btn">
                <i class="fas fa-user"></i> Manage Persons
            </a>
            <a href="/cims/addresses" class="cims-action-btn">
                <i class="fas fa-map-marker-alt"></i> Manage Addresses
            </a>
            <a href="/cims/clientmaster" class="cims-action-btn">
                <i class="fas fa-building"></i> Manage Clients
            </a>
            <a href="/cims/welcome" class="cims-action-btn">
                <i class="fas fa-play-circle"></i> Start Wizard
            </a>
        </div>
    </div>
</div>

<!-- CIMS Footer -->
@include('cimscore::partials.cims_footer')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/metismenu/dist/metisMenu.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize MetisMenu for dropdowns
        if (typeof metisMenu !== 'undefined') {
            new metisMenu('#menu');
        }
    });
</script>

</body>
</html>
