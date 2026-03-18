@extends('cimscore::layouts.master')

@section('title', 'CIMS Wizard')

@section('page_title', 'Client Information Management')

@push('styles')
<link href="/public/modules/cimscore/css/cims.css" rel="stylesheet">
<style>
    /* Wizard Tabs */
    .wizard-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .wizard-tab {
        padding: 12px 18px;
        background: #b9d6ee;
        border-radius: 4px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        border-left: 4px solid transparent;
        transition: all 0.2s ease;
    }

    .wizard-tab.active {
        border-left-color: #006aa7;
        background: #1f8dee;
        color: #fff;
    }

    .wizard-tab:hover:not(.active) {
        background: #e6f0f8;
    }

    /* Section Card */
    .section-card {
        background: #fff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,75,135,0.15);
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #004b87;
        margin-bottom: 5px;
    }

    .company-divider {
        border: none;
        height: 3px;
        background-color: #006aa7;
        margin: 15px 0 25px 0;
        border-radius: 2px;
    }

    /* Placeholder Layout */
    .placeholder-wrap {
        display: flex;
        align-items: center;
        gap: 40px;
    }

    .placeholder-text {
        flex: 1;
    }

    .placeholder-text h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    .placeholder-text p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
    }

    .placeholder-image img {
        max-width: 320px;
        opacity: 0.95;
        border-radius: 8px;
    }

    /* Wizard Footer */
    .wizard-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }

    .wizard-footer .btn {
        font-size: 1.1rem;
        padding: 10px 30px;
    }

    .wizard-footer-divider {
        border: none;
        height: 2px;
        background-color: #006aa7;
        margin: 20px 0 15px 0;
        border-radius: 2px;
    }

    .wizard-footer-note {
        text-align: center;
        font-size: 0.9rem;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .placeholder-wrap {
            flex-direction: column;
            text-align: center;
        }
        .placeholder-image img {
            max-width: 260px;
        }
    }
</style>
@endpush

@section('content')

<!-- Wizard Tabs -->
<div class="wizard-tabs" id="wizardTabs">
    <div class="wizard-tab active" data-step="company">Company Details</div>
    <div class="wizard-tab" data-step="income_tax">Income Tax</div>
    <div class="wizard-tab" data-step="vat">VAT</div>
    <div class="wizard-tab" data-step="payroll">Payroll</div>
    <div class="wizard-tab" data-step="banking">Banking</div>
    <div class="wizard-tab" data-step="contacts">Contacts</div>
    <div class="wizard-tab" data-step="address">Address</div>
    <div class="wizard-tab" data-step="directors">Directors</div>
</div>

<!-- Wizard Content -->
<div class="section-card" id="wizardContent">

    <!-- Company Details -->
    <div class="wizard-step" data-step="company">
        <h4 class="section-title">Company Details</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Company Details</h2>
                <p>
                    This section captures the <strong>core company information</strong> as per
                    <strong>CIPC COR14.3</strong> requirements and forms the foundation
                    for all statutory registrations.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Income Tax -->
    <div class="wizard-step d-none" data-step="income_tax">
        <h4 class="section-title">Income Tax</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Income Tax</h2>
                <p>
                    This section is being carefully designed to align with <strong>SARS
                    requirements</strong> and ensure your long-term compliance success.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- VAT -->
    <div class="wizard-step d-none" data-step="vat">
        <h4 class="section-title">Value Added Tax</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Value Added Tax</h2>
                <p>
                    We're building this step to help you manage <strong>VAT registrations</strong>
                    with confidence and clarity.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Payroll -->
    <div class="wizard-step d-none" data-step="payroll">
        <h4 class="section-title">Payroll ( PAYE / UIF / SDL )</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>PAYE / UIF / SDL</h2>
                <p>
                    Payroll compliance is critical — this section is being crafted
                    to support accurate <strong>PAYE, SDL, and UIF</strong> management.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Banking -->
    <div class="wizard-step d-none" data-step="banking">
        <h4 class="section-title">Banking Details</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Banking Details</h2>
                <p>
                    <strong>Secure, compliant banking information</strong> is essential.
                    This section is currently under enhancement.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Contacts -->
    <div class="wizard-step d-none" data-step="contacts">
        <h4 class="section-title">Contact Information</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Contact Information</h2>
                <p>
                    This section will allow you to capture and manage <strong>key contact
                    persons</strong> associated with the client to ensure smooth communication
                    and regulatory correspondence.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Address -->
    <div class="wizard-step d-none" data-step="address">
        <h4 class="section-title">Address / Location</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Address / Location</h2>
                <p>
                    <strong>Accurate address information</strong> is essential for compliance and
                    official correspondence. This section is being prepared to
                    support physical and postal address requirements.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

    <!-- Directors -->
    <div class="wizard-step d-none" data-step="directors">
        <h4 class="section-title">Director Information</h4>
        <hr class="company-divider">
        <div class="placeholder-wrap">
            <div class="placeholder-text">
                <h2>Director Information</h2>
                <p>
                    This section will capture <strong>director and officer details</strong> as required
                    by <strong>CIPC and SARS</strong>, forming a critical part of statutory compliance
                    and governance.
                </p>
            </div>
            <div class="placeholder-image">
                <img src="/public/modules/cimscore/images/cims_under_construction.jpg" alt="Under construction">
            </div>
        </div>
    </div>

</div>

<!-- Wizard Footer -->
<div class="wizard-footer">
    <button class="btn btn-outline-secondary btn-lg" id="prevBtn">Back</button>
    <button class="btn btn-primary btn-lg" id="nextBtn">Next</button>
</div>

<hr class="wizard-footer-divider">

<div class="wizard-footer-note">
    &copy; SmartDash Core — Developed by Nexsus Business Solutions ( 064 507 2274 ) - All rights reserved.
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.wizard-tab');
    const steps = document.querySelectorAll('.wizard-step');
    let currentIndex = 0;

    function showStep(index) {
        tabs.forEach(t => t.classList.remove('active'));
        steps.forEach(s => s.classList.add('d-none'));

        tabs[index].classList.add('active');
        steps[index].classList.remove('d-none');
        currentIndex = index;
    }

    // Tab navigation
    tabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => showStep(idx));
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentIndex < tabs.length - 1) showStep(currentIndex + 1);
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentIndex > 0) showStep(currentIndex - 1);
    });

    // Initialize
    showStep(0);
});
</script>
@endpush
