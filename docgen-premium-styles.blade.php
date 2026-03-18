{{-- Document Generator Premium Styles - Teal Theme (#17A2B8) --}}
{{-- Usage: @include('CIMSDocumentGenerator::partials.premium-styles') or @push('styles') in layouts --}}

<style>
/* ==========================================================================
   DOCUMENT GENERATOR - PREMIUM TEAL THEME STYLESHEET
   ==========================================================================
   Theme Colors:
     Primary Teal:    #17A2B8
     Dark Teal:       #138496
     Darkest Teal:    #117a8b
     Dark Text:       #0d3d56
     Light BG:        #f8fdfe / #eaf7f9
     Gradient:        linear-gradient(135deg, #17A2B8, #138496)
   ========================================================================== */


/* ==========================================================================
   1. CSS CUSTOM PROPERTIES (Variables)
   ========================================================================== */
:root {
    --docgen-primary: #17A2B8;
    --docgen-primary-dark: #138496;
    --docgen-primary-darkest: #117a8b;
    --docgen-text-dark: #0d3d56;
    --docgen-text-muted: #5a7d8a;
    --docgen-gradient: linear-gradient(135deg, #17A2B8, #138496);
    --docgen-gradient-hover: linear-gradient(135deg, #138496, #117a8b);
    --docgen-gradient-light: linear-gradient(135deg, #f8fdfe 0%, #eaf7f9 100%);
    --docgen-shadow: 0 4px 15px rgba(23, 162, 184, 0.15);
    --docgen-shadow-hover: 0 8px 25px rgba(23, 162, 184, 0.3);
    --docgen-shadow-strong: 0 4px 15px rgba(23, 162, 184, 0.3);
    --docgen-border-radius: 10px;
    --docgen-border-radius-sm: 8px;
    --docgen-border-radius-xs: 6px;
    --docgen-transition: all 0.3s ease;
}


/* ==========================================================================
   2. BREADCRUMB BAR
   ========================================================================== */
.docgen-breadcrumb-bar {
    background: var(--docgen-gradient);
    padding: 14px 25px;
    border-radius: var(--docgen-border-radius-sm);
    margin-bottom: 25px;
    box-shadow: var(--docgen-shadow);
}

.docgen-breadcrumb-bar .breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0;
}

.docgen-breadcrumb-bar .breadcrumb-item,
.docgen-breadcrumb-bar .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.85);
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
    transition: var(--docgen-transition);
}

.docgen-breadcrumb-bar .breadcrumb-item a:hover {
    color: #fff;
}

.docgen-breadcrumb-bar .breadcrumb-item.active {
    color: #fff;
    font-weight: 600;
}

.docgen-breadcrumb-bar .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.6);
    content: "\f105";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    font-size: 12px;
}


/* ==========================================================================
   3. PAGE HEADERS
   ========================================================================== */
.docgen-page-header {
    background: var(--docgen-gradient-light);
    border-left: 4px solid var(--docgen-primary);
    padding: 20px 25px;
    margin-bottom: 25px;
    border-radius: 0 var(--docgen-border-radius-sm) var(--docgen-border-radius-sm) 0;
}

.docgen-page-header h2,
.docgen-page-header h3 {
    color: var(--docgen-text-dark);
    font-weight: 700;
    margin: 0;
    font-size: 22px;
}

.docgen-page-header p {
    color: var(--docgen-text-muted);
    margin: 5px 0 0 0;
    font-size: 14px;
}

.docgen-page-title {
    color: var(--docgen-text-dark);
    font-weight: 700;
    font-size: 24px;
    margin-bottom: 5px;
}

.docgen-page-subtitle {
    color: var(--docgen-text-muted);
    font-size: 14px;
    font-weight: 400;
}


/* ==========================================================================
   4. CARDS - General Card Styling
   ========================================================================== */
.docgen-card {
    border: 1px solid rgba(23, 162, 184, 0.2);
    border-radius: var(--docgen-border-radius);
    transition: var(--docgen-transition);
    background: #fff;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.docgen-card:hover {
    box-shadow: var(--docgen-shadow-hover);
    transform: translateY(-3px);
    border-color: var(--docgen-primary);
}

.docgen-card .card-header,
.docgen-card-header {
    background: var(--docgen-gradient);
    color: #fff;
    padding: 15px 20px;
    border-bottom: none;
    font-weight: 600;
    font-size: 16px;
}

.docgen-card .card-header h4,
.docgen-card .card-header h5,
.docgen-card-header h4,
.docgen-card-header h5 {
    color: #fff;
    margin: 0;
    font-weight: 600;
}

.docgen-card .card-header .badge,
.docgen-card-header .badge {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-weight: 600;
}

.docgen-card .card-body {
    padding: 20px;
}

/* Card with light header variant */
.docgen-card-light .card-header,
.docgen-card-header-light {
    background: var(--docgen-gradient-light);
    color: var(--docgen-text-dark);
    border-bottom: 1px solid rgba(23, 162, 184, 0.15);
}

.docgen-card-light .card-header h4,
.docgen-card-light .card-header h5,
.docgen-card-header-light h4,
.docgen-card-header-light h5 {
    color: var(--docgen-text-dark);
}

/* Existing .card override for DocGen pages */
.docgen-wrapper .card {
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: var(--docgen-border-radius);
    transition: var(--docgen-transition);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.docgen-wrapper .card:hover {
    box-shadow: var(--docgen-shadow);
}

.docgen-wrapper .card .card-header {
    background: var(--docgen-gradient);
    color: #fff;
    border-bottom: none;
    padding: 15px 20px;
    font-weight: 600;
}

.docgen-wrapper .card .card-header * {
    color: #fff;
}


/* ==========================================================================
   5. STAT CARDS (Dashboard Stats)
   ========================================================================== */
.docgen-stat-card {
    border: 1px solid rgba(23, 162, 184, 0.2);
    border-radius: var(--docgen-border-radius);
    padding: 22px 20px;
    text-align: center;
    transition: var(--docgen-transition);
    background: #fff;
    position: relative;
    overflow: hidden;
}

.docgen-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--docgen-gradient);
}

.docgen-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--docgen-shadow-hover);
    border-color: var(--docgen-primary);
}

.docgen-stat-card .docgen-stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: var(--docgen-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 22px;
    color: var(--docgen-primary);
    transition: var(--docgen-transition);
}

.docgen-stat-card:hover .docgen-stat-icon {
    background: var(--docgen-gradient);
    color: #fff;
    transform: scale(1.1);
}

.docgen-stat-card .docgen-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--docgen-text-dark);
    line-height: 1.2;
    margin-bottom: 4px;
}

.docgen-stat-card .docgen-stat-label {
    font-size: 13px;
    color: var(--docgen-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Stat card color variants (all teal-based) */
.docgen-stat-card.stat-primary::before { background: linear-gradient(135deg, #17A2B8, #138496); }
.docgen-stat-card.stat-secondary::before { background: linear-gradient(135deg, #20c997, #17a689); }
.docgen-stat-card.stat-tertiary::before { background: linear-gradient(135deg, #0d8fa7, #0a7890); }
.docgen-stat-card.stat-quaternary::before { background: linear-gradient(135deg, #1dc4db, #17A2B8); }

.docgen-stat-card.stat-secondary .docgen-stat-icon { color: #20c997; }
.docgen-stat-card.stat-secondary:hover .docgen-stat-icon { background: linear-gradient(135deg, #20c997, #17a689); color: #fff; }

.docgen-stat-card.stat-tertiary .docgen-stat-icon { color: #0d8fa7; }
.docgen-stat-card.stat-tertiary:hover .docgen-stat-icon { background: linear-gradient(135deg, #0d8fa7, #0a7890); color: #fff; }

.docgen-stat-card.stat-quaternary .docgen-stat-icon { color: #1dc4db; }
.docgen-stat-card.stat-quaternary:hover .docgen-stat-icon { background: linear-gradient(135deg, #1dc4db, #17A2B8); color: #fff; }


/* ==========================================================================
   6. BUTTONS
   ========================================================================== */

/* Primary Button - Teal Gradient */
.docgen-btn-primary,
.docgen-wrapper .btn-primary {
    background: var(--docgen-gradient) !important;
    border: none !important;
    color: #fff !important;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    box-shadow: var(--docgen-shadow-strong);
    transition: var(--docgen-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.docgen-btn-primary:hover,
.docgen-btn-primary:focus,
.docgen-wrapper .btn-primary:hover,
.docgen-wrapper .btn-primary:focus {
    background: var(--docgen-gradient-hover) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: var(--docgen-shadow-hover) !important;
    text-decoration: none;
}

.docgen-btn-primary:active,
.docgen-wrapper .btn-primary:active {
    transform: translateY(0);
    box-shadow: var(--docgen-shadow) !important;
}

/* Large Primary Button (e.g., "Generate Document", "New Template") */
.docgen-btn-primary-lg {
    background: var(--docgen-gradient) !important;
    border: none !important;
    color: #fff !important;
    padding: 12px 28px;
    font-size: 16px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    box-shadow: var(--docgen-shadow-strong);
    transition: var(--docgen-transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.docgen-btn-primary-lg:hover {
    background: var(--docgen-gradient-hover) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: var(--docgen-shadow-hover) !important;
}

/* Secondary Button - Outline Teal */
.docgen-btn-secondary,
.docgen-wrapper .btn-outline-info {
    background: transparent !important;
    border: 2px solid var(--docgen-primary) !important;
    color: var(--docgen-primary) !important;
    padding: 9px 22px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    transition: var(--docgen-transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.docgen-btn-secondary:hover,
.docgen-btn-secondary:focus,
.docgen-wrapper .btn-outline-info:hover,
.docgen-wrapper .btn-outline-info:focus {
    background: var(--docgen-gradient) !important;
    border-color: var(--docgen-primary) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: var(--docgen-shadow-strong) !important;
}

/* Back / Cancel Button */
.docgen-btn-back {
    background: #f8f9fa !important;
    border: 2px solid #dee2e6 !important;
    color: #6c757d !important;
    padding: 9px 22px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    transition: var(--docgen-transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.docgen-btn-back:hover {
    background: #e9ecef !important;
    border-color: #ced4da !important;
    color: #495057 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
}

/* Danger Button */
.docgen-btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    border: none !important;
    color: #fff !important;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    transition: var(--docgen-transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.docgen-btn-danger:hover {
    background: linear-gradient(135deg, #c82333, #bd2130) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.35) !important;
}

/* Success Button (for "Email" or "Send" actions) */
.docgen-btn-success {
    background: linear-gradient(135deg, #28a745, #218838) !important;
    border: none !important;
    color: #fff !important;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--docgen-border-radius-sm);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: var(--docgen-transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.docgen-btn-success:hover {
    background: linear-gradient(135deg, #218838, #1e7e34) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.35) !important;
}

/* Icon-only Button */
.docgen-btn-icon {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--docgen-border-radius-xs);
    transition: var(--docgen-transition);
    border: 1px solid rgba(23, 162, 184, 0.2);
    background: #fff;
    color: var(--docgen-primary);
}

.docgen-btn-icon:hover {
    background: var(--docgen-gradient);
    color: #fff;
    border-color: var(--docgen-primary);
    transform: translateY(-2px);
    box-shadow: var(--docgen-shadow);
}

/* Button Group */
.docgen-btn-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}


/* ==========================================================================
   7. TABLES
   ========================================================================== */
.docgen-table-wrapper {
    border-radius: var(--docgen-border-radius);
    overflow: hidden;
    border: 1px solid rgba(23, 162, 184, 0.15);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    background: #fff;
}

.docgen-table {
    margin-bottom: 0;
    width: 100%;
}

.docgen-table thead th {
    background: var(--docgen-gradient);
    color: #fff !important;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border: none;
    white-space: nowrap;
    vertical-align: middle;
}

.docgen-table thead th:first-child {
    border-radius: 0;
}

.docgen-table thead th:last-child {
    border-radius: 0;
}

.docgen-table tbody td {
    padding: 12px 16px;
    font-size: 14px;
    color: #495057;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    transition: var(--docgen-transition);
}

.docgen-table tbody tr {
    transition: var(--docgen-transition);
}

.docgen-table tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.04);
}

.docgen-table tbody tr:hover td {
    color: var(--docgen-text-dark);
}

.docgen-table tbody tr:last-child td {
    border-bottom: none;
}

/* Clickable table rows */
.docgen-table tbody tr.docgen-clickable-row {
    cursor: pointer;
}

.docgen-table tbody tr.docgen-clickable-row:hover {
    background-color: rgba(23, 162, 184, 0.06);
    box-shadow: inset 3px 0 0 var(--docgen-primary);
}

/* Table links */
.docgen-table a {
    color: var(--docgen-text-dark);
    font-weight: 600;
    text-decoration: none;
    transition: var(--docgen-transition);
}

.docgen-table a:hover {
    color: var(--docgen-primary);
}

/* Table with existing Bootstrap classes override */
.docgen-wrapper .table thead th {
    background: var(--docgen-gradient);
    color: #fff !important;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    padding: 14px 16px;
}

.docgen-wrapper .table tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.04);
}


/* ==========================================================================
   8. FORMS
   ========================================================================== */
.docgen-form-group {
    margin-bottom: 20px;
}

.docgen-form-group label,
.docgen-wrapper .form-group label {
    font-weight: 600;
    color: var(--docgen-text-dark);
    font-size: 14px;
    margin-bottom: 8px;
    display: block;
}

.docgen-form-group .form-control,
.docgen-wrapper .form-control {
    border: 2px solid #e0e6ed;
    border-radius: var(--docgen-border-radius-sm);
    padding: 10px 15px;
    font-size: 14px;
    color: #495057;
    transition: var(--docgen-transition);
    background: #fff;
    height: auto;
}

.docgen-form-group .form-control:focus,
.docgen-wrapper .form-control:focus {
    border-color: var(--docgen-primary);
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
    outline: none;
}

.docgen-form-group .form-control::placeholder {
    color: #adb5bd;
    font-style: italic;
}

/* Select / Dropdown styling */
.docgen-form-group select.form-control,
.docgen-wrapper select.form-control {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2317A2B8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

/* Select2 Override for DocGen pages */
.docgen-wrapper .select2-container--default .select2-selection--single {
    border: 2px solid #e0e6ed;
    border-radius: var(--docgen-border-radius-sm);
    height: 44px;
    padding: 6px 15px;
    transition: var(--docgen-transition);
}

.docgen-wrapper .select2-container--default .select2-selection--single:focus,
.docgen-wrapper .select2-container--default.select2-container--focus .select2-selection--single,
.docgen-wrapper .select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--docgen-primary);
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
}

.docgen-wrapper .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: var(--docgen-gradient);
    color: #fff;
}

/* Textarea */
.docgen-form-group textarea.form-control,
.docgen-wrapper textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

/* Required field indicator */
.docgen-required::after {
    content: ' *';
    color: #dc3545;
    font-weight: 700;
}

/* Form section separator */
.docgen-form-section {
    border-bottom: 2px solid rgba(23, 162, 184, 0.1);
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.docgen-form-section-title {
    color: var(--docgen-text-dark);
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.docgen-form-section-title i {
    color: var(--docgen-primary);
    font-size: 18px;
}

/* Inline form help text */
.docgen-form-help {
    font-size: 12px;
    color: #8c9bab;
    margin-top: 5px;
    font-style: italic;
}


/* ==========================================================================
   9. TABS
   ========================================================================== */
.docgen-tabs .nav-tabs {
    border-bottom: 2px solid rgba(23, 162, 184, 0.15);
    margin-bottom: 20px;
}

.docgen-tabs .nav-tabs .nav-link {
    color: var(--docgen-text-muted);
    font-weight: 600;
    font-size: 14px;
    padding: 12px 22px;
    border: none;
    border-bottom: 3px solid transparent;
    transition: var(--docgen-transition);
    background: transparent;
    border-radius: 0;
    margin-bottom: -2px;
}

.docgen-tabs .nav-tabs .nav-link:hover {
    color: var(--docgen-primary);
    border-bottom-color: rgba(23, 162, 184, 0.4);
    background: rgba(23, 162, 184, 0.03);
}

.docgen-tabs .nav-tabs .nav-link.active {
    color: var(--docgen-primary);
    border-bottom-color: var(--docgen-primary);
    background: transparent;
    font-weight: 700;
}

.docgen-tabs .nav-tabs .nav-link .badge {
    background: var(--docgen-gradient);
    color: #fff;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: 6px;
    font-weight: 600;
}

.docgen-tabs .nav-tabs .nav-link.active .badge {
    background: var(--docgen-gradient);
}

/* Pill-style tabs variant */
.docgen-tabs-pills .nav-pills .nav-link {
    color: var(--docgen-text-muted);
    font-weight: 600;
    font-size: 14px;
    padding: 10px 20px;
    border-radius: var(--docgen-border-radius-sm);
    transition: var(--docgen-transition);
    margin-right: 8px;
    border: 2px solid transparent;
}

.docgen-tabs-pills .nav-pills .nav-link:hover {
    background: rgba(23, 162, 184, 0.06);
    color: var(--docgen-primary);
    border-color: rgba(23, 162, 184, 0.2);
}

.docgen-tabs-pills .nav-pills .nav-link.active {
    background: var(--docgen-gradient);
    color: #fff;
    border-color: var(--docgen-primary);
    box-shadow: var(--docgen-shadow-strong);
}


/* ==========================================================================
   10. BADGES & STATUS PILLS
   ========================================================================== */
.docgen-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: var(--docgen-transition);
}

.docgen-badge-generated,
.docgen-badge-success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.docgen-badge-emailed,
.docgen-badge-info {
    background: rgba(23, 162, 184, 0.1);
    color: var(--docgen-primary);
    border: 1px solid rgba(23, 162, 184, 0.2);
}

.docgen-badge-draft,
.docgen-badge-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #d69e2e;
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.docgen-badge-error,
.docgen-badge-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.docgen-badge-pending {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
}

.docgen-badge-primary {
    background: rgba(23, 162, 184, 0.1);
    color: var(--docgen-primary);
    border: 1px solid rgba(23, 162, 184, 0.2);
}

/* Dot indicator before badge text */
.docgen-badge::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.docgen-badge-generated::before,
.docgen-badge-success::before { background: #28a745; }
.docgen-badge-emailed::before,
.docgen-badge-info::before { background: var(--docgen-primary); }
.docgen-badge-draft::before,
.docgen-badge-warning::before { background: #d69e2e; }
.docgen-badge-error::before,
.docgen-badge-danger::before { background: #dc3545; }
.docgen-badge-pending::before { background: #6c757d; }
.docgen-badge-primary::before { background: var(--docgen-primary); }


/* ==========================================================================
   11. SEARCH BAR
   ========================================================================== */
.docgen-search {
    position: relative;
    margin-bottom: 20px;
}

.docgen-search .form-control {
    border: 2px solid var(--docgen-primary);
    border-radius: var(--docgen-border-radius-sm) 0 0 var(--docgen-border-radius-sm);
    padding: 10px 18px;
    font-size: 14px;
    height: auto;
    transition: var(--docgen-transition);
}

.docgen-search .form-control:focus {
    border-color: var(--docgen-primary);
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
}

.docgen-search .input-group-text,
.docgen-search .input-group-append .btn {
    background: var(--docgen-gradient);
    border: 2px solid var(--docgen-primary);
    border-left: none;
    border-radius: 0 var(--docgen-border-radius-sm) var(--docgen-border-radius-sm) 0;
    color: #fff;
    padding: 10px 18px;
    cursor: pointer;
    transition: var(--docgen-transition);
}

.docgen-search .input-group-text:hover,
.docgen-search .input-group-append .btn:hover {
    background: var(--docgen-gradient-hover);
}

/* Standalone search input (no button) */
.docgen-search-standalone .form-control {
    border: 2px solid var(--docgen-primary);
    border-radius: var(--docgen-border-radius-sm);
    padding: 10px 18px 10px 42px;
    font-size: 14px;
    transition: var(--docgen-transition);
}

.docgen-search-standalone::before {
    content: "\f002";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--docgen-primary);
    font-size: 14px;
    z-index: 5;
}

/* Filter bar */
.docgen-filter-bar {
    background: var(--docgen-gradient-light);
    border: 1px solid rgba(23, 162, 184, 0.12);
    border-radius: var(--docgen-border-radius);
    padding: 18px 20px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
}

.docgen-filter-bar .form-group {
    margin-bottom: 0;
    flex: 1;
    min-width: 180px;
}

.docgen-filter-bar label {
    font-size: 12px;
    font-weight: 600;
    color: var(--docgen-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}


/* ==========================================================================
   12. ALERT MESSAGES
   ========================================================================== */
.docgen-alert {
    border-radius: var(--docgen-border-radius-sm);
    padding: 15px 20px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    border: none;
    margin-bottom: 20px;
}

.docgen-alert i {
    font-size: 18px;
    margin-top: 1px;
    flex-shrink: 0;
}

.docgen-alert-success {
    background: rgba(40, 167, 69, 0.08);
    color: #1e7e34;
    border-left: 4px solid #28a745;
}

.docgen-alert-danger {
    background: rgba(220, 53, 69, 0.08);
    color: #bd2130;
    border-left: 4px solid #dc3545;
}

.docgen-alert-warning {
    background: rgba(255, 193, 7, 0.08);
    color: #b8860b;
    border-left: 4px solid #ffc107;
}

.docgen-alert-info {
    background: rgba(23, 162, 184, 0.08);
    color: var(--docgen-primary-darkest);
    border-left: 4px solid var(--docgen-primary);
}


/* ==========================================================================
   13. PAGINATION
   ========================================================================== */
.docgen-pagination .pagination {
    gap: 5px;
}

.docgen-pagination .page-item .page-link {
    border: 1px solid #e0e6ed;
    color: var(--docgen-text-muted);
    font-weight: 600;
    font-size: 14px;
    padding: 8px 14px;
    border-radius: var(--docgen-border-radius-xs) !important;
    transition: var(--docgen-transition);
    margin: 0;
}

.docgen-pagination .page-item .page-link:hover {
    background: rgba(23, 162, 184, 0.06);
    border-color: var(--docgen-primary);
    color: var(--docgen-primary);
    transform: translateY(-1px);
}

.docgen-pagination .page-item.active .page-link {
    background: var(--docgen-gradient);
    border-color: var(--docgen-primary);
    color: #fff;
    box-shadow: var(--docgen-shadow);
}

.docgen-pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #e9ecef;
    color: #ced4da;
}


/* ==========================================================================
   14. SIGNATURE PREVIEW AREAS
   ========================================================================== */
.docgen-signature-preview {
    border: 2px dashed rgba(23, 162, 184, 0.3);
    border-radius: var(--docgen-border-radius);
    padding: 20px;
    text-align: center;
    background: var(--docgen-gradient-light);
    transition: var(--docgen-transition);
    position: relative;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.docgen-signature-preview:hover {
    border-color: var(--docgen-primary);
    box-shadow: var(--docgen-shadow);
}

.docgen-signature-preview img {
    max-height: 100px;
    max-width: 100%;
    object-fit: contain;
}

.docgen-signature-preview .docgen-signature-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--docgen-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 10px;
}

.docgen-signature-preview .docgen-signature-placeholder {
    color: var(--docgen-text-muted);
    font-size: 14px;
}

.docgen-signature-preview .docgen-signature-placeholder i {
    font-size: 28px;
    color: rgba(23, 162, 184, 0.4);
    display: block;
    margin-bottom: 8px;
}

/* Signature row layout */
.docgen-signature-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 15px;
}

@media (max-width: 576px) {
    .docgen-signature-row {
        grid-template-columns: 1fr;
    }
}


/* ==========================================================================
   15. AUDIT TIMELINE
   ========================================================================== */
.docgen-timeline {
    position: relative;
    padding-left: 35px;
}

.docgen-timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--docgen-primary), rgba(23, 162, 184, 0.15));
    border-radius: 2px;
}

.docgen-timeline-item {
    position: relative;
    padding-bottom: 25px;
    padding-left: 15px;
}

.docgen-timeline-item:last-child {
    padding-bottom: 0;
}

.docgen-timeline-item::before {
    content: '';
    position: absolute;
    left: -28px;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--docgen-gradient);
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px var(--docgen-primary);
    z-index: 2;
}

.docgen-timeline-item:first-child::before {
    width: 18px;
    height: 18px;
    left: -30px;
    top: 2px;
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.2), 0 0 0 2px var(--docgen-primary);
}

.docgen-timeline-item .docgen-timeline-title {
    font-weight: 600;
    color: var(--docgen-text-dark);
    font-size: 14px;
    margin-bottom: 4px;
}

.docgen-timeline-item .docgen-timeline-meta {
    font-size: 12px;
    color: var(--docgen-text-muted);
    display: flex;
    align-items: center;
    gap: 12px;
}

.docgen-timeline-item .docgen-timeline-meta i {
    color: var(--docgen-primary);
    font-size: 11px;
}

.docgen-timeline-item .docgen-timeline-description {
    font-size: 13px;
    color: #6c757d;
    margin-top: 6px;
    padding: 10px 14px;
    background: var(--docgen-gradient-light);
    border-radius: var(--docgen-border-radius-xs);
    border-left: 3px solid rgba(23, 162, 184, 0.3);
}

/* Timeline icon variants */
.docgen-timeline-item.timeline-created::before { background: var(--docgen-gradient); }
.docgen-timeline-item.timeline-emailed::before { background: linear-gradient(135deg, #28a745, #218838); box-shadow: 0 0 0 2px #28a745; }
.docgen-timeline-item.timeline-viewed::before { background: linear-gradient(135deg, #6f42c1, #5a34a8); box-shadow: 0 0 0 2px #6f42c1; }
.docgen-timeline-item.timeline-error::before { background: linear-gradient(135deg, #dc3545, #c82333); box-shadow: 0 0 0 2px #dc3545; }


/* ==========================================================================
   16. PDF PREVIEW FRAME
   ========================================================================== */
.docgen-pdf-preview {
    border: 2px solid rgba(23, 162, 184, 0.25);
    border-radius: var(--docgen-border-radius);
    overflow: hidden;
    background: #f5f5f5;
    position: relative;
    box-shadow: var(--docgen-shadow);
}

.docgen-pdf-preview iframe {
    width: 100%;
    min-height: 600px;
    border: none;
    display: block;
}

.docgen-pdf-preview-header {
    background: var(--docgen-gradient);
    color: #fff;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 600;
    font-size: 14px;
}

.docgen-pdf-preview-header .docgen-pdf-actions {
    display: flex;
    gap: 8px;
}

.docgen-pdf-preview-header .docgen-pdf-actions a,
.docgen-pdf-preview-header .docgen-pdf-actions button {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 5px 12px;
    border-radius: var(--docgen-border-radius-xs);
    font-size: 13px;
    font-weight: 500;
    transition: var(--docgen-transition);
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.docgen-pdf-preview-header .docgen-pdf-actions a:hover,
.docgen-pdf-preview-header .docgen-pdf-actions button:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* PDF placeholder / loading */
.docgen-pdf-placeholder {
    padding: 60px 30px;
    text-align: center;
    color: var(--docgen-text-muted);
}

.docgen-pdf-placeholder i {
    font-size: 48px;
    color: rgba(23, 162, 184, 0.3);
    margin-bottom: 15px;
    display: block;
}


/* ==========================================================================
   17. EMPTY STATE
   ========================================================================== */
.docgen-empty-state {
    text-align: center;
    padding: 60px 30px;
    color: var(--docgen-text-muted);
}

.docgen-empty-state i {
    font-size: 56px;
    color: rgba(23, 162, 184, 0.25);
    margin-bottom: 20px;
    display: block;
}

.docgen-empty-state h4 {
    color: var(--docgen-text-dark);
    font-weight: 700;
    font-size: 20px;
    margin-bottom: 10px;
}

.docgen-empty-state p {
    color: var(--docgen-text-muted);
    font-size: 15px;
    max-width: 420px;
    margin: 0 auto 25px;
    line-height: 1.6;
}

.docgen-empty-state .docgen-btn-primary {
    padding: 12px 28px;
    font-size: 15px;
}


/* ==========================================================================
   18. ACTION DROPDOWN MENUS
   ========================================================================== */
.docgen-dropdown .dropdown-toggle {
    background: #fff;
    border: 1px solid rgba(23, 162, 184, 0.25);
    color: var(--docgen-primary);
    padding: 6px 14px;
    border-radius: var(--docgen-border-radius-xs);
    font-size: 13px;
    font-weight: 600;
    transition: var(--docgen-transition);
}

.docgen-dropdown .dropdown-toggle:hover,
.docgen-dropdown .dropdown-toggle:focus,
.docgen-dropdown.show .dropdown-toggle {
    background: var(--docgen-gradient);
    color: #fff;
    border-color: var(--docgen-primary);
    box-shadow: var(--docgen-shadow);
}

.docgen-dropdown .dropdown-toggle::after {
    margin-left: 6px;
}

.docgen-dropdown .dropdown-menu {
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: var(--docgen-border-radius-sm);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    margin-top: 5px;
    min-width: 180px;
    animation: docgenDropdownFadeIn 0.2s ease;
}

.docgen-dropdown .dropdown-menu .dropdown-item {
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 500;
    color: #495057;
    transition: var(--docgen-transition);
    display: flex;
    align-items: center;
    gap: 10px;
}

.docgen-dropdown .dropdown-menu .dropdown-item i {
    font-size: 14px;
    width: 18px;
    text-align: center;
    color: var(--docgen-primary);
}

.docgen-dropdown .dropdown-menu .dropdown-item:hover {
    background: rgba(23, 162, 184, 0.06);
    color: var(--docgen-primary);
}

.docgen-dropdown .dropdown-menu .dropdown-item:active {
    background: var(--docgen-gradient);
    color: #fff;
}

.docgen-dropdown .dropdown-menu .dropdown-item:active i {
    color: #fff;
}

.docgen-dropdown .dropdown-menu .dropdown-divider {
    border-top-color: rgba(23, 162, 184, 0.1);
    margin: 6px 0;
}

/* Danger item in dropdown */
.docgen-dropdown .dropdown-menu .dropdown-item-danger {
    color: #dc3545;
}

.docgen-dropdown .dropdown-menu .dropdown-item-danger i {
    color: #dc3545;
}

.docgen-dropdown .dropdown-menu .dropdown-item-danger:hover {
    background: rgba(220, 53, 69, 0.06);
    color: #bd2130;
}

@keyframes docgenDropdownFadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}


/* ==========================================================================
   19. DETAIL VIEW (Show Page)
   ========================================================================== */
.docgen-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.docgen-detail-item {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.docgen-detail-item:last-child {
    border-bottom: none;
}

.docgen-detail-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--docgen-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.docgen-detail-value {
    font-size: 15px;
    color: var(--docgen-text-dark);
    font-weight: 500;
}

@media (max-width: 768px) {
    .docgen-detail-grid {
        grid-template-columns: 1fr;
    }
}

/* Detail view action bar */
.docgen-action-bar {
    background: var(--docgen-gradient-light);
    border: 1px solid rgba(23, 162, 184, 0.12);
    border-radius: var(--docgen-border-radius);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.docgen-action-bar-title {
    font-weight: 700;
    color: var(--docgen-text-dark);
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.docgen-action-bar-title i {
    color: var(--docgen-primary);
}


/* ==========================================================================
   20. SETTINGS PAGE
   ========================================================================== */
.docgen-settings-nav {
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: var(--docgen-border-radius);
    overflow: hidden;
}

.docgen-settings-nav .nav-link {
    padding: 14px 20px;
    font-weight: 600;
    font-size: 14px;
    color: #6c757d;
    border-bottom: 1px solid #f0f0f0;
    transition: var(--docgen-transition);
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 0;
}

.docgen-settings-nav .nav-link:last-child {
    border-bottom: none;
}

.docgen-settings-nav .nav-link i {
    color: var(--docgen-primary);
    width: 20px;
    text-align: center;
}

.docgen-settings-nav .nav-link:hover {
    background: rgba(23, 162, 184, 0.04);
    color: var(--docgen-text-dark);
}

.docgen-settings-nav .nav-link.active {
    background: var(--docgen-gradient);
    color: #fff;
    border-bottom-color: transparent;
}

.docgen-settings-nav .nav-link.active i {
    color: #fff;
}

/* Settings panel */
.docgen-settings-panel {
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: var(--docgen-border-radius);
    padding: 25px;
    background: #fff;
}

.docgen-settings-panel h5 {
    color: var(--docgen-text-dark);
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid rgba(23, 162, 184, 0.1);
}


/* ==========================================================================
   21. TEMPLATE CARD (for Templates Index)
   ========================================================================== */
.docgen-template-card {
    border: 1px solid rgba(23, 162, 184, 0.15);
    border-radius: var(--docgen-border-radius);
    transition: var(--docgen-transition);
    background: #fff;
    overflow: hidden;
    cursor: pointer;
}

.docgen-template-card:hover {
    border-color: var(--docgen-primary);
    box-shadow: var(--docgen-shadow-hover);
    transform: translateY(-3px);
}

.docgen-template-card .docgen-template-icon {
    background: var(--docgen-gradient-light);
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(23, 162, 184, 0.1);
    transition: var(--docgen-transition);
}

.docgen-template-card .docgen-template-icon i {
    font-size: 32px;
    color: var(--docgen-primary);
    transition: var(--docgen-transition);
}

.docgen-template-card:hover .docgen-template-icon {
    background: var(--docgen-gradient);
}

.docgen-template-card:hover .docgen-template-icon i {
    color: #fff;
}

.docgen-template-card .docgen-template-body {
    padding: 18px;
}

.docgen-template-card .docgen-template-name {
    font-weight: 600;
    color: var(--docgen-text-dark);
    font-size: 15px;
    margin-bottom: 5px;
}

.docgen-template-card .docgen-template-category {
    font-size: 12px;
    color: var(--docgen-text-muted);
    font-weight: 500;
}


/* ==========================================================================
   22. LOADING STATES & SPINNERS
   ========================================================================== */
.docgen-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    gap: 15px;
}

.docgen-spinner {
    width: 42px;
    height: 42px;
    border: 4px solid rgba(23, 162, 184, 0.15);
    border-top-color: var(--docgen-primary);
    border-radius: 50%;
    animation: docgenSpin 0.8s linear infinite;
}

@keyframes docgenSpin {
    to { transform: rotate(360deg); }
}

.docgen-loading-text {
    color: var(--docgen-text-muted);
    font-size: 14px;
    font-weight: 500;
}

/* Skeleton loading */
.docgen-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: docgenShimmer 1.5s ease-in-out infinite;
    border-radius: var(--docgen-border-radius-xs);
}

@keyframes docgenShimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.docgen-skeleton-text {
    height: 14px;
    margin-bottom: 8px;
    width: 80%;
}

.docgen-skeleton-title {
    height: 20px;
    margin-bottom: 12px;
    width: 50%;
}


/* ==========================================================================
   23. MODAL OVERRIDES
   ========================================================================== */
.docgen-modal .modal-header {
    background: var(--docgen-gradient);
    color: #fff;
    border-bottom: none;
    padding: 16px 24px;
    border-radius: var(--docgen-border-radius) var(--docgen-border-radius) 0 0;
}

.docgen-modal .modal-header .modal-title {
    font-weight: 600;
    font-size: 17px;
    color: #fff;
}

.docgen-modal .modal-header .close,
.docgen-modal .modal-header .btn-close {
    color: #fff;
    opacity: 0.8;
    text-shadow: none;
}

.docgen-modal .modal-header .close:hover,
.docgen-modal .modal-header .btn-close:hover {
    opacity: 1;
}

.docgen-modal .modal-body {
    padding: 24px;
}

.docgen-modal .modal-footer {
    border-top: 1px solid rgba(23, 162, 184, 0.1);
    padding: 16px 24px;
}

.docgen-modal .modal-content {
    border: none;
    border-radius: var(--docgen-border-radius);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}


/* ==========================================================================
   24. TOOLTIP OVERRIDES
   ========================================================================== */
.docgen-wrapper .tooltip-inner {
    background: var(--docgen-text-dark);
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: var(--docgen-border-radius-xs);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.docgen-wrapper .tooltip .arrow::before {
    border-top-color: var(--docgen-text-dark);
}


/* ==========================================================================
   25. PROGRESS BAR
   ========================================================================== */
.docgen-progress {
    height: 8px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.docgen-progress-bar {
    height: 100%;
    border-radius: 10px;
    background: var(--docgen-gradient);
    transition: width 0.6s ease;
}


/* ==========================================================================
   26. INFO BOXES / CALLOUTS
   ========================================================================== */
.docgen-callout {
    border-left: 4px solid var(--docgen-primary);
    background: var(--docgen-gradient-light);
    border-radius: 0 var(--docgen-border-radius-sm) var(--docgen-border-radius-sm) 0;
    padding: 16px 20px;
    margin-bottom: 20px;
}

.docgen-callout-title {
    font-weight: 700;
    color: var(--docgen-text-dark);
    font-size: 14px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.docgen-callout-title i {
    color: var(--docgen-primary);
}

.docgen-callout-text {
    font-size: 13px;
    color: var(--docgen-text-muted);
    margin: 0;
    line-height: 1.5;
}


/* ==========================================================================
   27. SWITCH / TOGGLE
   ========================================================================== */
.docgen-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
}

.docgen-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.docgen-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #ccc;
    transition: var(--docgen-transition);
    border-radius: 26px;
}

.docgen-switch-slider::before {
    content: "";
    position: absolute;
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    transition: var(--docgen-transition);
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.docgen-switch input:checked + .docgen-switch-slider {
    background: var(--docgen-gradient);
}

.docgen-switch input:checked + .docgen-switch-slider::before {
    transform: translateX(22px);
}

.docgen-switch input:focus + .docgen-switch-slider {
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.2);
}


/* ==========================================================================
   28. FLOATING ACTION BUTTON
   ========================================================================== */
.docgen-fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--docgen-gradient);
    color: #fff;
    border: none;
    font-size: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
    cursor: pointer;
    transition: var(--docgen-transition);
    z-index: 1050;
}

.docgen-fab:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(23, 162, 184, 0.5);
}


/* ==========================================================================
   29. RESPONSIVE UTILITIES
   ========================================================================== */
@media (max-width: 992px) {
    .docgen-breadcrumb-bar {
        padding: 12px 18px;
    }

    .docgen-stat-card {
        padding: 18px 15px;
    }

    .docgen-stat-card .docgen-stat-value {
        font-size: 24px;
    }

    .docgen-filter-bar {
        padding: 15px;
    }

    .docgen-action-bar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .docgen-btn-group {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .docgen-page-header h2,
    .docgen-page-header h3 {
        font-size: 18px;
    }

    .docgen-table thead th {
        font-size: 12px;
        padding: 10px 12px;
    }

    .docgen-table tbody td {
        font-size: 13px;
        padding: 10px 12px;
    }

    .docgen-pdf-preview iframe {
        min-height: 400px;
    }

    .docgen-timeline {
        padding-left: 28px;
    }

    .docgen-tabs .nav-tabs .nav-link {
        padding: 10px 14px;
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    .docgen-breadcrumb-bar {
        border-radius: var(--docgen-border-radius-xs);
        padding: 10px 15px;
    }

    .docgen-btn-primary-lg {
        width: 100%;
        justify-content: center;
    }

    .docgen-empty-state {
        padding: 40px 20px;
    }

    .docgen-empty-state i {
        font-size: 42px;
    }

    .docgen-empty-state h4 {
        font-size: 17px;
    }
}


/* ==========================================================================
   30. ANIMATION UTILITIES
   ========================================================================== */
.docgen-fade-in {
    animation: docgenFadeIn 0.3s ease;
}

@keyframes docgenFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.docgen-slide-up {
    animation: docgenSlideUp 0.4s ease;
}

@keyframes docgenSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}


/* ==========================================================================
   31. PRINT STYLES
   ========================================================================== */
@media print {
    .docgen-breadcrumb-bar,
    .docgen-fab,
    .docgen-btn-group,
    .docgen-action-bar,
    .docgen-filter-bar,
    .docgen-dropdown {
        display: none !important;
    }

    .docgen-card {
        border: 1px solid #ddd;
        box-shadow: none;
    }

    .docgen-card:hover {
        transform: none;
        box-shadow: none;
    }

    .docgen-table thead th {
        background: #f5f5f5 !important;
        color: #333 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}


/* ==========================================================================
   32. SCROLLBAR STYLING (Webkit)
   ========================================================================== */
.docgen-wrapper ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.docgen-wrapper ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.docgen-wrapper ::-webkit-scrollbar-thumb {
    background: rgba(23, 162, 184, 0.3);
    border-radius: 10px;
    transition: var(--docgen-transition);
}

.docgen-wrapper ::-webkit-scrollbar-thumb:hover {
    background: rgba(23, 162, 184, 0.5);
}


/* ==========================================================================
   33. CHECKBOX & RADIO CUSTOM STYLES
   ========================================================================== */
.docgen-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #495057;
}

.docgen-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: var(--docgen-primary);
    cursor: pointer;
}

.docgen-radio input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: var(--docgen-primary);
    cursor: pointer;
}


/* ==========================================================================
   34. FILE UPLOAD / DRAG-DROP ZONE
   ========================================================================== */
.docgen-upload-zone {
    border: 2px dashed rgba(23, 162, 184, 0.3);
    border-radius: var(--docgen-border-radius);
    padding: 35px 20px;
    text-align: center;
    background: var(--docgen-gradient-light);
    transition: var(--docgen-transition);
    cursor: pointer;
}

.docgen-upload-zone:hover,
.docgen-upload-zone.dragover {
    border-color: var(--docgen-primary);
    background: rgba(23, 162, 184, 0.04);
    box-shadow: var(--docgen-shadow);
}

.docgen-upload-zone i {
    font-size: 36px;
    color: rgba(23, 162, 184, 0.4);
    margin-bottom: 12px;
    display: block;
    transition: var(--docgen-transition);
}

.docgen-upload-zone:hover i {
    color: var(--docgen-primary);
    transform: scale(1.1);
}

.docgen-upload-zone p {
    color: var(--docgen-text-muted);
    font-size: 14px;
    margin: 0;
}

.docgen-upload-zone .docgen-upload-link {
    color: var(--docgen-primary);
    font-weight: 600;
    text-decoration: underline;
}


/* ==========================================================================
   35. MISC / UTILITY CLASSES
   ========================================================================== */

/* Teal text */
.docgen-text-primary { color: var(--docgen-primary) !important; }
.docgen-text-dark { color: var(--docgen-text-dark) !important; }
.docgen-text-muted { color: var(--docgen-text-muted) !important; }

/* Teal backgrounds */
.docgen-bg-primary { background: var(--docgen-gradient) !important; color: #fff !important; }
.docgen-bg-light { background: var(--docgen-gradient-light) !important; }

/* Dividers */
.docgen-divider {
    border: none;
    height: 2px;
    background: linear-gradient(to right, var(--docgen-primary), rgba(23, 162, 184, 0.05));
    margin: 25px 0;
    border-radius: 2px;
}

/* Count / number highlight */
.docgen-count {
    background: var(--docgen-gradient);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 20px;
    display: inline-block;
    min-width: 24px;
    text-align: center;
}

/* Truncate long text */
.docgen-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 250px;
}

/* Section spacing */
.docgen-section {
    margin-bottom: 30px;
}

/* No list style */
.docgen-list-unstyled {
    list-style: none;
    padding: 0;
    margin: 0;
}

.docgen-list-unstyled li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #495057;
}

.docgen-list-unstyled li:last-child {
    border-bottom: none;
}

/* Hover underline for links */
.docgen-link {
    color: var(--docgen-primary);
    text-decoration: none;
    font-weight: 600;
    transition: var(--docgen-transition);
    position: relative;
}

.docgen-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -2px;
    left: 0;
    background: var(--docgen-primary);
    transition: width 0.3s ease;
}

.docgen-link:hover {
    color: var(--docgen-primary-dark);
}

.docgen-link:hover::after {
    width: 100%;
}

</style>
