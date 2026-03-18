@extends('layouts.default')

@section('title', 'CIMS 3000 - Main Landing')

@push('styles')
<style>
    :root {
        --cims-teal: #17A2B8;
        --cims-dark: #0d3d56;
        --cims-light: #f8f9fa;
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
</style>
@endpush

@section('content')
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
@endsection
