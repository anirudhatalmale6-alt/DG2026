{{--
================================================================================
CIMS MASTER MENU - 100% SELF-CONTAINED
================================================================================
This menu is fully independent - no external CSS or JS dependencies required.
Just include it on any page: @include('cimscore::partials.cims_master_menu')

STRUCTURE:
- HTML: Menu items with 3-level dropdowns
- CSS: All styles embedded (lines 780+)
- JS: All functionality embedded (lines 950+)

TO MODIFY MENU ITEMS:
- Each main menu item is a <li> with class sd-menu-link
- Sub-menus are nested <ul> with class mm-collapse
- Icons use Font Awesome classes (fas fa-xxxx)

TO CHANGE COLORS:
- Main gradient: Search for "#0d3d56" and "#17A2B8"
- Hover accent: Search for "#20c997"
- Text colors: Search for "rgba(255,255,255"

================================================================================
--}}

{{-- MENU WRAPPER - Contains everything --}}
<div class="cims-menu-wrapper">
    <div class="cims-nav-container">
        <ul class="cims-main-menu">

            {{-- ============================================================
                 SUNSHINE - Main Menu Item 1
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-gauge-high"></i>
                    <span>Sunshine</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Firm overview, KPIs, alerts, compliance status</div>
                {{-- Dashboard Sub-menu --}}
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-building"></i> Firm Overview</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">High-level firm KPIs</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Executive Summary</a></li>
                            <li><a href="#">Revenue & Profit KPIs</a></li>
                            <li><a href="#">Utilization & Capacity</a></li>
                            <li><a href="#">Outstanding Work</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-clipboard-check"></i> Compliance Status</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Filing & deadline health</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Upcoming Deadlines</a></li>
                            <li><a href="#">Overdue Filings</a></li>
                            <li><a href="#">Compliance Heatmap</a></li>
                            <li><a href="#">Regulatory Alerts</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-heart-pulse"></i> Client Health</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Risk & overdue clients</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">At-Risk Clients</a></li>
                            <li><a href="#">SLA Performance</a></li>
                            <li><a href="#">Outstanding Queries</a></li>
                            <li><a href="#">Engagement Status</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-bell"></i> Alerts & Notifications</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Critical issues</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Critical Alerts</a></li>
                            <li><a href="#">Warning Alerts</a></li>
                            <li><a href="#">Informational Alerts</a></li>
                            <li><a href="#">Alert History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-chart-pie"></i> Analytics & Insights</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Trends & forecasting</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Trend Analysis</a></li>
                            <li><a href="#">Forecasting</a></li>
                            <li><a href="#">Custom Dashboards</a></li>
                            <li><a href="#">Export & Sharing</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 ENTITIES - Main Menu Item 2
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-building-columns"></i>
                    <span>Entities</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Clients, companies, trusts, KYC/AML</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-database"></i> CIMS Modules</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">All CIMS data modules</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="{{ Route::has('nsdcpersons.index') ? route('nsdcpersons.index') : '#' }}">NSDC Persons</a></li>
                            <li><a href="{{ Route::has('infodocs.index') ? route('infodocs.index') : '#' }}">Info Docs</a></li>
                            <li><a href="{{ Route::has('cimsdocmanager.index') ? route('cimsdocmanager.index') : '#' }}">Document Manager</a></li>
                            <li><a href="{{ Route::has('sarsrep.index') ? route('sarsrep.index') : '#' }}">SARS Representative</a></li>
                            <li><a href="/cims/dbforge">DB Forge</a></li>
                            <li><a href="{{ Route::has('users.index') ? route('users.index') : '#' }}">Users</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-users-gear"></i> Client Master</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Manage clients, addresses & persons</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="{{ Route::has('client.index') ? route('client.index') : '#' }}"><i class="fas fa-id-card-clip"></i> Maintain Clients Master</a></li>
                            <li><a href="{{ Route::has('cimsaddresses.index') ? route('cimsaddresses.index') : '#' }}"><i class="fas fa-map-location-dot"></i> Maintain Addresses</a></li>
                            <li><a href="{{ Route::has('cimspersons.index') ? route('cimspersons.index') : '#' }}"><i class="fas fa-user-pen"></i> Maintain Persons</a></li>
                            <li><a href="{{ Route::has('client.info-sheet-dashboard') ? route('client.info-sheet-dashboard') : '#' }}"><i class="fas fa-file-circle-info"></i> Client Info Sheet</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ Route::has('cimsdocgen.index') ? route('cimsdocgen.index') : '#' }}"><i class="fas fa-file-circle-plus"></i> Document Generator</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Generate documents from templates</div>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-sitemap"></i> Entity Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Companies, trusts, partnerships</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Entity List</a></li>
                            <li><a href="#">Entity Structure</a></li>
                            <li><a href="#">Directors & Shareholders</a></li>
                            <li><a href="#">Jurisdiction Details</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-handshake"></i> Engagements</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Services per client</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Active Engagements</a></li>
                            <li><a href="#">Engagement Setup</a></li>
                            <li><a href="#">Engagement Scope</a></li>
                            <li><a href="#">Engagement History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-user-shield"></i> KYC / AML</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Identity & risk checks</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Identity Verification</a></li>
                            <li><a href="#">Risk Assessment</a></li>
                            <li><a href="#">Due Diligence</a></li>
                            <li><a href="#">AML Monitoring</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-lines"></i> Client Documents</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Client-specific files</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Client Uploads</a></li>
                            <li><a href="#">Linked Documents</a></li>
                            <li><a href="#">Client Templates</a></li>
                            <li><a href="#">Document History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-comments"></i> Communication Log</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Emails, notes, calls</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Emails</a></li>
                            <li><a href="#">Notes</a></li>
                            <li><a href="#">Calls & Meetings</a></li>
                            <li><a href="#">Activity Timeline</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 ACCOUNTING - Main Menu Item 3
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Accounting</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">General ledger, AR/AP, banking, reporting</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-book"></i> General Ledger</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Chart of accounts & journals</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Chart of Accounts</a></li>
                            <li><a href="#">Journal Entries</a></li>
                            <li><a href="#">Opening Balances</a></li>
                            <li><a href="#">Adjustments</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-invoice-dollar"></i> Accounts Receivable</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Invoicing & collections</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Customer Invoices</a></li>
                            <li><a href="#">Recurring Invoices</a></li>
                            <li><a href="#">Credit Notes</a></li>
                            <li><a href="#">Collections</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-money-bill-wave"></i> Accounts Payable</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Bills & expenses</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Supplier Bills</a></li>
                            <li><a href="#">Expense Claims</a></li>
                            <li><a href="#">Payment Runs</a></li>
                            <li><a href="#">Vendor Management</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-university"></i> Banking & Reconciliation</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Bank feeds</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Bank Feeds</a></li>
                            <li><a href="#">Reconciliations</a></li>
                            <li><a href="#">Cash Transactions</a></li>
                            <li><a href="#">Exceptions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-chart-bar"></i> Financial Reports</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">P&L, Balance Sheet</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Profit & Loss</a></li>
                            <li><a href="#">Balance Sheet</a></li>
                            <li><a href="#">Cash Flow</a></li>
                            <li><a href="#">Custom Reports</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-calendar-check"></i> Period Close</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Month/year-end</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Closing Checklist</a></li>
                            <li><a href="#">Lock Period</a></li>
                            <li><a href="#">Review Notes</a></li>
                            <li><a href="#">Audit Trail</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 TAXATION - Main Menu Item 4
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-receipt"></i>
                    <span>Taxation</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Corporate, personal, VAT/GST, filings</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-building"></i> Corporate Tax</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Company tax</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Tax Computation</a></li>
                            <li><a href="#">Adjustments</a></li>
                            <li><a href="#">Losses & Credits</a></li>
                            <li><a href="#">Final Return</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-user"></i> Personal Tax</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Individual returns</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Income Sources</a></li>
                            <li><a href="#">Deductions</a></li>
                            <li><a href="#">Capital Gains</a></li>
                            <li><a href="#">Tax Return</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-percent"></i> Indirect Tax</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">VAT / GST / Sales tax</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">VAT/GST Setup</a></li>
                            <li><a href="#">Return Preparation</a></li>
                            <li><a href="#">Reconciliations</a></li>
                            <li><a href="#">Submission</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-calendar-days"></i> Tax Calendar</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Deadlines</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Filing Deadlines</a></li>
                            <li><a href="#">Payment Dates</a></li>
                            <li><a href="#">Reminder Settings</a></li>
                            <li><a href="#">Calendar Sync</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-export"></i> Tax Filings</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Submissions</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Draft Filings</a></li>
                            <li><a href="#">Review & Approval</a></li>
                            <li><a href="#">Submission Status</a></li>
                            <li><a href="#">Filing History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-lightbulb"></i> Tax Planning</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Advisory & scenarios</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Scenario Modelling</a></li>
                            <li><a href="#">Tax Optimization</a></li>
                            <li><a href="#">Advisory Notes</a></li>
                            <li><a href="#">Client Recommendations</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-invoice"></i> SARS Returns</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">SARS tax returns</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="{{ route('cimsemp201.index') }}">Employee's Tax (EMP201)</a></li>
                            <li><a href="{{ route('cimsemp201.pivot') }}">Employee's Tax (EMP501)</a></li>
                            <li><a href="{{ route('cimsemp201.statement') }}">Statement of Account (EMPSA)</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 PAYROLL - Main Menu Item 5
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-wallet"></i>
                    <span>Payroll</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Payroll runs, employees, statutory reporting</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-play-circle"></i> Payroll Runs</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Regular & off-cycle</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Pay Schedule</a></li>
                            <li><a href="#">Payroll Calculation</a></li>
                            <li><a href="#">Review & Approval</a></li>
                            <li><a href="#">Payroll History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-id-card"></i> Employees</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Employee records</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Employee Profiles</a></li>
                            <li><a href="#">Contracts & Pay</a></li>
                            <li><a href="#">Bank Details</a></li>
                            <li><a href="#">Tax Details</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-calendar-check"></i> Leave Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">PTO & absences</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Leave Requests</a></li>
                            <li><a href="#">Leave Balances</a></li>
                            <li><a href="#">Approval Workflow</a></li>
                            <li><a href="#">Holiday Calendar</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-landmark"></i> Statutory Compliance</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Taxes & social security</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Payroll Taxes</a></li>
                            <li><a href="#">Social Contributions</a></li>
                            <li><a href="#">Statutory Reports</a></li>
                            <li><a href="#">Submissions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-invoice-dollar"></i> Payslips & Reports</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Payroll outputs</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Payslip Generation</a></li>
                            <li><a href="#">Employer Reports</a></li>
                            <li><a href="#">Employee Reports</a></li>
                            <li><a href="#">Export Options</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-calendar-alt"></i> Year-End Processing</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Annual filings</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Annual Returns</a></li>
                            <li><a href="#">Employee Statements</a></li>
                            <li><a href="#">Tax Certificates</a></li>
                            <li><a href="#">Archival</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 COMPLIANCE - Main Menu Item 6
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-scale-balanced"></i>
                    <span>Compliance</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Regulatory compliance, audits, governance</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-invoice-dollar"></i> EMP201</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Monthly employer returns</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="{{ route('cimsemp201.index') }}">All EMP201</a></li>
                            <li><a href="{{ route('cimsemp201.create') }}">New EMP201</a></li>
                            <li><a href="{{ route('cimsemp201.pivot') }}">EMP201 / EMP501 Pivot</a></li>
                            <li><a href="{{ route('cimsemp201.statement') }}">Statement of Account (EMPSA)</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ Route::has('client.compliance-status') ? route('client.compliance-status') : '#' }}"><i class="fas fa-shield-alt"></i> Tax Compliance Status</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">SARS tax compliance dashboard</div>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-clipboard-list"></i> Compliance Register</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Applicable regulations</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Regulatory Mapping</a></li>
                            <li><a href="#">Compliance Status</a></li>
                            <li><a href="#">Jurisdiction Rules</a></li>
                            <li><a href="#">Evidence Library</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-tasks"></i> Compliance Tasks</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Per client/entity</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Task Assignments</a></li>
                            <li><a href="#">Due Dates</a></li>
                            <li><a href="#">Completion Tracking</a></li>
                            <li><a href="#">Escalations</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-exclamation-triangle"></i> Risk Assessments</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">AML, operational risk</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">AML Risk</a></li>
                            <li><a href="#">Financial Risk</a></li>
                            <li><a href="#">Operational Risk</a></li>
                            <li><a href="#">Risk Scoring</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-search"></i> Audits</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Internal & external</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Internal Audits</a></li>
                            <li><a href="#">External Audits</a></li>
                            <li><a href="#">Evidence Requests</a></li>
                            <li><a href="#">Audit Findings</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-gavel"></i> Policies & Governance</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Controls & procedures</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Firm Policies</a></li>
                            <li><a href="#">Client Policies</a></li>
                            <li><a href="#">Board Resolutions</a></li>
                            <li><a href="#">Governance Records</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-bug"></i> Incident Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Breaches & issues</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Incident Logging</a></li>
                            <li><a href="#">Root Cause Analysis</a></li>
                            <li><a href="#">Corrective Actions</a></li>
                            <li><a href="#">Incident History</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 CONTRACTS - Main Menu Item 7
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-file-signature"></i>
                    <span>Contracts</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Engagement letters, contracts, service levels</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-archive"></i> Contract Repository</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">All contracts</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">All Contracts</a></li>
                            <li><a href="#">Contract Metadata</a></li>
                            <li><a href="#">Version History</a></li>
                            <li><a href="#">Access Control</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-envelope-open-text"></i> Engagement Letters</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Client onboarding</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Letter Templates</a></li>
                            <li><a href="#">Scope Definition</a></li>
                            <li><a href="#">Fee Structure</a></li>
                            <li><a href="#">Signed Letters</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-handshake-angle"></i> SLA Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Service levels</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">SLA Templates</a></li>
                            <li><a href="#">SLA Metrics</a></li>
                            <li><a href="#">Performance Tracking</a></li>
                            <li><a href="#">Breach Logs</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-list-check"></i> Obligations Tracker</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Contract obligations</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Contract Obligations</a></li>
                            <li><a href="#">Compliance Deadlines</a></li>
                            <li><a href="#">Fulfilment Status</a></li>
                            <li><a href="#">Alerts</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-clock-rotate-left"></i> Renewals & Expiry</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Upcoming renewals</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Upcoming Renewals</a></li>
                            <li><a href="#">Expired Contracts</a></li>
                            <li><a href="#">Renewal Workflow</a></li>
                            <li><a href="#">Notifications</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-contract"></i> Legal Templates</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Standard agreements</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">NDAs</a></li>
                            <li><a href="#">Service Agreements</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                            <li><a href="#">Amendments</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 EMAIL - CIMS Mail
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="{{ Route::has('cimsemail.index') ? route('cimsemail.index') : '#' }}" class="cims-menu-link">
                    <i class="fas fa-envelope"></i>
                    <span>Email</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Email compose, send & templates</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="{{ Route::has('cimsemail.compose') ? route('cimsemail.compose') : '#' }}"><i class="fas fa-pen-to-square"></i> Compose Email</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Write a new email</div>
                    </li>
                    <li>
                        <a href="{{ Route::has('cimsemail.sent') ? route('cimsemail.sent') : '#' }}"><i class="fas fa-paper-plane"></i> Sent Emails</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">View sent emails</div>
                    </li>
                    <li>
                        <a href="{{ Route::has('cimsemail.drafts') ? route('cimsemail.drafts') : '#' }}"><i class="fas fa-file-pen"></i> Drafts</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Saved drafts</div>
                    </li>
                    <li>
                        <a href="{{ Route::has('cimsemail.templates') ? route('cimsemail.templates') : '#' }}"><i class="fas fa-file-code"></i> Email Templates</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Manage email templates</div>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 DOCUMENTS - Main Menu Item 8
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-folder-tree"></i>
                    <span>Documents</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Document repository, search, version control</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-database"></i> Document Repository</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">All documents</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">All Documents</a></li>
                            <li><a href="#">Folder View</a></li>
                            <li><a href="#">Metadata Tags</a></li>
                            <li><a href="#">Bulk Actions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-folder-open"></i> Client Folders</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Per-client storage</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Client Documents</a></li>
                            <li><a href="#">Engagement Documents</a></li>
                            <li><a href="#">Year-Based Folders</a></li>
                            <li><a href="#">Shared Files</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-magnifying-glass"></i> Search & OCR</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Full-text search</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Full-Text Search</a></li>
                            <li><a href="#">OCR Indexing</a></li>
                            <li><a href="#">Advanced Filters</a></li>
                            <li><a href="#">Saved Searches</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-code-branch"></i> Version Control</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Track changes</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Version History</a></li>
                            <li><a href="#">Compare Versions</a></li>
                            <li><a href="#">Rollback</a></li>
                            <li><a href="#">Change Logs</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-lock"></i> Access & Permissions</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Security settings</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Role-Based Access</a></li>
                            <li><a href="#">Sharing Rules</a></li>
                            <li><a href="#">External Access</a></li>
                            <li><a href="#">Audit Logs</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-box-archive"></i> Retention & Archiving</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Lifecycle management</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Retention Policies</a></li>
                            <li><a href="#">Archive Rules</a></li>
                            <li><a href="#">Legal Holds</a></li>
                            <li><a href="#">Secure Disposal</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 BIZZ BOX - Main Menu Item 9
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Bizz Box</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Templates, smart forms, document generation</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-alt"></i> Document Templates</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Reusable templates</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Legal Templates</a></li>
                            <li><a href="#">Accounting Templates</a></li>
                            <li><a href="#">HR Templates</a></li>
                            <li><a href="#">Compliance Templates</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-wpforms"></i> Smart Forms</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Dynamic data capture</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Data Capture Forms</a></li>
                            <li><a href="#">Conditional Fields</a></li>
                            <li><a href="#">Validation Rules</a></li>
                            <li><a href="#">Form Versions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-puzzle-piece"></i> Contract Builder</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Clause assembly</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Clause Library</a></li>
                            <li><a href="#">Clause Logic</a></li>
                            <li><a href="#">Jurisdiction Rules</a></li>
                            <li><a href="#">Review Workflow</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-shield-alt"></i> Policy Generator</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Auto-generate policies</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Policy Frameworks</a></li>
                            <li><a href="#">Regulatory Mapping</a></li>
                            <li><a href="#">Auto-Updates</a></li>
                            <li><a href="#">Approval Process</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-palette"></i> Branding & Styles</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Output formatting</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Firm Branding</a></li>
                            <li><a href="#">Client Branding</a></li>
                            <li><a href="#">Style Guides</a></li>
                            <li><a href="#">Language Settings</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-history"></i> Generated Documents</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Output history</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Draft Documents</a></li>
                            <li><a href="#">Approved Documents</a></li>
                            <li><a href="#">Signed Documents</a></li>
                            <li><a href="#">Output History</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            {{-- ============================================================
                 ADMIN - Main Menu Item 10
                 ============================================================ --}}
            <li class="cims-menu-item">
                <a href="javascript:void(0);" class="cims-menu-link">
                    <i class="fas fa-user-gear"></i>
                    <span>Admin</span>
                </a>
                <div class="sd_tooltip_teal sd-mainmenu-tooltip">Workflows, users, settings, configuration</div>
                <ul class="cims-submenu">
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-project-diagram"></i> Workflow Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Automation</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Workflow Designer</a></li>
                            <li><a href="#">Automation Rules</a></li>
                            <li><a href="#">Triggers & Actions</a></li>
                            <li><a href="#">Workflow Logs</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-tasks"></i> Task Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Firm & client tasks</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Task Lists</a></li>
                            <li><a href="#">Recurring Tasks</a></li>
                            <li><a href="#">Dependencies</a></li>
                            <li><a href="#">Escalations</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-stopwatch"></i> Time Tracking</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Billable hours</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Timesheets</a></li>
                            <li><a href="#">Billable Rates</a></li>
                            <li><a href="#">Time Approval</a></li>
                            <li><a href="#">Utilization Reports</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-file-invoice-dollar"></i> Billing & Fees</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Pricing & invoices</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Pricing Models</a></li>
                            <li><a href="#">Invoicing Rules</a></li>
                            <li><a href="#">Revenue Recognition</a></li>
                            <li><a href="#">Billing History</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-users-cog"></i> User Management</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Staff & roles</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Users</a></li>
                            <li><a href="#">Roles & Permissions</a></li>
                            <li><a href="#">Access Reviews</a></li>
                            <li><a href="#">Deactivation</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="fas fa-cogs"></i> System Settings</a>
                        <div class="sd_tooltip_teal sd-menu-tooltip">Firm configuration</div>
                        <ul class="cims-submenu-level3">
                            <li><a href="#">Firm Profile</a></li>
                            <li><a href="#">Jurisdictions</a></li>
                            <li><a href="#">Integrations</a></li>
                            <li><a href="#">Data Backup</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="cims-menu-item">
                <form action="{{ Route::has('logout') ? route('logout') : '/logout' }}" method="POST">
                    @csrf
                    <a href="#" id="logout" class="cims-menu-link" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </form>
            </li>

        </ul>
    </div>
</div>

{{-- ============================================================================
     GOOGLE FONTS - Poppins (same as SmartDash)
     ============================================================================ --}}
@if (!defined('POPPINS_FONT_LOADED'))
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@php define('POPPINS_FONT_LOADED', true); @endphp
@endif

{{-- ============================================================================
     EMBEDDED CSS - All menu styles, no external dependencies
     ============================================================================ --}}
<style>
/*
 * CIMS MASTER MENU - SELF-CONTAINED STYLES
 * ----------------------------------------
 * All styles are scoped to .cims-menu-wrapper to avoid conflicts
 *
 * COLOR REFERENCE:
 * - Primary Dark: #0d3d56 (dark teal)
 * - Primary Light: #17A2B8 (teal)
 * - Accent: #20c997 (green)
 * - Text Light: rgba(255,255,255,0.85)
 * - Hover Background: #e0f7fa (light cyan)
 *
 * FONT: Poppins (matches SmartDash theme)
 */

/* Reset and base styles for menu */
.cims-menu-wrapper {
    width: 100%;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.5;
    box-sizing: border-box;
}
.cims-menu-wrapper *, .cims-menu-wrapper *::before, .cims-menu-wrapper *::after {
    box-sizing: border-box;
}

/* Main navigation container - the teal gradient bar */
.cims-nav-container {
    background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
    width: 100%;
    padding: 0;
    position: relative;
    z-index: 1000;
}

/* Main menu - horizontal flex container */
.cims-main-menu {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    list-style: none;
    margin: 0;
    padding: 0;
}

/* Each main menu item */
.cims-menu-item {
    position: relative;
    margin: 0;
    padding: 0;
}

/* Main menu link styling */
.cims-menu-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 18px;
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.25s ease;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    position: relative;
}

/*
 * SD_TOOLTIP_TEAL - COPIED FROM SMARTDASH-FORMS.CSS
 * -------------------------------------------------
 * This is the exact tooltip styling used on the addresses page.
 * Light blue background, teal left border, info icon.
 */
.sd_tooltip_teal {
    display: none;
    position: relative;
    margin-top: 8px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #0d3d56 0%, #145369 100%);
    border-left: 4px solid #20c997;
    border-radius: 0 8px 8px 0;
    font-size: 14px;
    font-weight: 500;
    color: #ffffff;
    line-height: 1.5;
    box-shadow: 0 6px 20px rgba(13, 61, 86, 0.3), 0 3px 8px rgba(0, 0, 0, 0.2);
}
.sd_tooltip_teal::before {
    content: '\f05a';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    margin-right: 10px;
    color: #20c997;
}
.sd_tooltip_teal.show {
    display: block;
    animation: sd_tooltip_fadeIn 0.2s ease-in-out;
}
@keyframes sd_tooltip_fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Main menu tooltip positioning - appears ABOVE the menu item */
.sd-mainmenu-tooltip {
    position: absolute;
    left: 0;
    top: -50px;
    margin-top: 0;
    min-width: 200px;
    z-index: 9999;
    white-space: nowrap;
}
.sd-mainmenu-tooltip.show {
    display: block !important;
}

/* Level 2 sub-menu tooltip positioning - appears ABOVE the menu item */
.sd-menu-tooltip {
    position: absolute;
    left: 0;
    top: -50px;
    margin-top: 0;
    min-width: 200px;
    z-index: 9999;
    white-space: nowrap;
}
.sd-menu-tooltip.show {
    display: block !important;
}
.cims-submenu > li {
    position: relative;
}
.cims-menu-link i {
    font-size: 18px;
    color: rgba(255,255,255,0.85);
    transition: color 0.25s ease;
}
.cims-menu-link span {
    color: rgba(255,255,255,0.85);
    transition: color 0.25s ease;
}

/* Main menu hover state - light background with dark text */
.cims-menu-link:hover,
.cims-menu-item:hover > .cims-menu-link {
    background: linear-gradient(135deg, #fff 0%, #e0f7fa 100%);
    color: #000;
    border-bottom-color: #20c997;
}
.cims-menu-link:hover i,
.cims-menu-link:hover span,
.cims-menu-item:hover > .cims-menu-link i,
.cims-menu-item:hover > .cims-menu-link span {
    color: #000;
}

/* Level 2 Sub-menu (dropdown) - ELEGANT SOFT GRADIENT, AUTO WIDTH */
.cims-submenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: linear-gradient(180deg, #f0f9ff 0%, #e0f4f8 50%, #d4f1f9 100%);
    width: auto;
    min-width: max-content;
    list-style: none;
    margin: 0;
    padding: 8px 0;
    box-shadow: 0 8px 25px rgba(13, 61, 86, 0.15), 0 4px 10px rgba(23, 162, 184, 0.1);
    z-index: 1001;
    white-space: nowrap;
    border-radius: 0 0 8px 8px;
    border-top: 3px solid #20c997;
}

/* Show submenu on hover */
.cims-menu-item:hover > .cims-submenu {
    display: block;
}

/* Level 2 sub-menu items */
.cims-submenu > li {
    position: relative;
    margin: 0;
    padding: 0;
}
.cims-submenu > li > a {
    display: block;
    padding: 12px 20px;
    color: #0d3d56;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 4px solid transparent;
    white-space: nowrap;
    position: relative;
}
.cims-submenu > li > a i {
    margin-right: 10px;
    color: #17A2B8;
    transition: all 0.3s ease;
    font-size: 15px;
}

/* Level 2 hover - elegant teal accent with smooth animation */
.cims-submenu > li > a:hover {
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.15) 0%, rgba(32, 201, 151, 0.08) 100%);
    color: #0d3d56;
    border-left-color: #20c997;
    padding-left: 24px;
}
.cims-submenu > li > a:hover i {
    color: #20c997;
    transform: scale(1.1);
}

/* Level 3 Sub-menu (flyout to the right) - MATCHING LEVEL 2 ELEGANT STYLE */
.cims-submenu-level3 {
    display: none;
    position: absolute;
    top: -8px;
    left: calc(100% + 4px);
    background: linear-gradient(180deg, #e8f6f8 0%, #d4f1f9 50%, #c5ecf5 100%);
    min-width: max-content;
    list-style: none;
    margin: 0;
    padding: 8px 0;
    box-shadow: 0 8px 25px rgba(13, 61, 86, 0.15), 0 4px 10px rgba(23, 162, 184, 0.1);
    z-index: 1002;
    border-radius: 8px;
    border-left: 3px solid #20c997;
}

/* Show level 3 on hover */
.cims-submenu > li:hover > .cims-submenu-level3 {
    display: block;
}

/* Level 3 items */
.cims-submenu-level3 > li {
    margin: 0;
    padding: 0;
}
.cims-submenu-level3 > li > a {
    display: block;
    padding: 11px 18px;
    color: #0d3d56;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid transparent;
    white-space: nowrap;
}

/* Level 3 hover - dark teal with white text */
.cims-submenu-level3 > li > a:hover {
    background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
    color: #ffffff;
    border-left-color: #20c997;
    padding-left: 22px;
}

/* Flip level 3 to left when near right edge (added by JS) */
.cims-submenu-level3.flip-left {
    left: auto;
    right: 100%;
}

/* Responsive - stack menu on small screens */
@media (max-width: 992px) {
    .cims-main-menu {
        flex-direction: column;
    }
    .cims-submenu {
        position: relative;
        top: auto;
        left: auto;
        box-shadow: none;
        background: rgba(0,0,0,0.1);
    }
    .cims-submenu-level3 {
        position: relative;
        top: auto;
        left: auto;
        box-shadow: none;
        background: rgba(0,0,0,0.15);
    }
    .cims-submenu-level3.flip-left {
        left: auto;
        right: auto;
    }
}
</style>

{{-- ============================================================================
     EMBEDDED JAVASCRIPT - Menu functionality, no external dependencies
     ============================================================================ --}}
<script>
/*
 * CIMS MASTER MENU - SELF-CONTAINED JAVASCRIPT
 * ---------------------------------------------
 * Handles:
 * - Main menu tooltip show/hide (sd_tooltip_teal)
 * - Level 3 menu flip when near screen edge
 * - Touch device support
 *
 * No jQuery or other libraries required.
 */
document.addEventListener('DOMContentLoaded', function() {

    // Main menu links with sd_tooltip_teal tooltips
    // Tooltip shows briefly (2 seconds) then auto-hides
    var menuLinks = document.querySelectorAll('.cims-menu-link');
    menuLinks.forEach(function(link) {
        var mainTooltipTimer = null;
        link.addEventListener('mouseenter', function() {
            // Show the main menu tooltip briefly
            var tooltip = this.parentElement.querySelector('.sd-mainmenu-tooltip');
            if (tooltip) {
                tooltip.classList.add('show');
                // Auto-hide after 2 seconds
                if (mainTooltipTimer) clearTimeout(mainTooltipTimer);
                mainTooltipTimer = setTimeout(function() {
                    tooltip.classList.remove('show');
                }, 2000);
            }
        });
        link.addEventListener('mouseleave', function() {
            // Hide the main menu tooltip immediately on leave
            var tooltip = this.parentElement.querySelector('.sd-mainmenu-tooltip');
            if (tooltip) tooltip.classList.remove('show');
            if (mainTooltipTimer) clearTimeout(mainTooltipTimer);
        });
    });

    // Level 2 sub-menu links with sd_tooltip_teal tooltips
    // Tooltip shows briefly (2 seconds) then auto-hides
    var subMenuLinks = document.querySelectorAll('.cims-submenu > li > a');
    subMenuLinks.forEach(function(link) {
        var tooltipTimer = null;
        link.addEventListener('mouseenter', function() {
            // Show the level 2 tooltip briefly
            var tooltip = this.parentElement.querySelector('.sd-menu-tooltip');
            if (tooltip) {
                tooltip.classList.add('show');
                // Auto-hide after 2 seconds
                if (tooltipTimer) clearTimeout(tooltipTimer);
                tooltipTimer = setTimeout(function() {
                    tooltip.classList.remove('show');
                }, 2000);
            }
        });
        link.addEventListener('mouseleave', function() {
            // Hide the level 2 tooltip immediately on leave
            var tooltip = this.parentElement.querySelector('.sd-menu-tooltip');
            if (tooltip) tooltip.classList.remove('show');
            if (tooltipTimer) clearTimeout(tooltipTimer);
        });
    });

    // Handle level 3 menu positioning (flip to left if near edge)
    var level2Items = document.querySelectorAll('.cims-submenu > li');
    level2Items.forEach(function(item) {
        item.addEventListener('mouseenter', function() {
            var level3 = this.querySelector('.cims-submenu-level3');
            if (level3) {
                // Reset first
                level3.classList.remove('flip-left');

                // Check if menu would go off-screen
                var rect = this.getBoundingClientRect();
                var menuWidth = 200;
                var screenWidth = window.innerWidth;

                if (rect.right + menuWidth > screenWidth) {
                    level3.classList.add('flip-left');
                }
            }
        });
    });

    // Touch device support - toggle submenus on tap
    if ('ontouchstart' in window) {
        var mainLinks = document.querySelectorAll('.cims-menu-link');
        mainLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var parent = this.parentElement;
                var submenu = parent.querySelector('.cims-submenu');
                if (submenu) {
                    e.preventDefault();
                    // Close all other open menus
                    document.querySelectorAll('.cims-menu-item').forEach(function(item) {
                        if (item !== parent) {
                            item.classList.remove('touch-open');
                        }
                    });
                    parent.classList.toggle('touch-open');
                }
            });
        });

        // Add touch-open CSS rule
        var style = document.createElement('style');
        style.textContent = '.cims-menu-item.touch-open > .cims-submenu { display: block !important; }';
        document.head.appendChild(style);
    }
});
</script>
