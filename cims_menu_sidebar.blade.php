<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
        <style>
            /* Sidebar submenus collapsed by default */
            .cims-sidebar-menu .metismenu > li > ul {
                display: none;
            }
            .cims-sidebar-menu .metismenu > li.mm-active > ul {
                display: block;
            }
        </style>
            <!-- Dashboard -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-gauge-high"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Executive Summary</a></li>
                    <li><a href="#">Compliance Status</a></li>
                    <li><a href="#">Client Health</a></li>
                    <li><a href="#">Alerts</a></li>
                    <li><a href="#">Analytics</a></li>
                </ul>
            </li>
            <!-- Entities / CIMS Modules -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-building-columns"></i>
                    <span class="nav-text">Entities</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="/cims/persons"><i class="fas fa-users me-2"></i>Persons</a></li>
                    <li><a href="/cims/addresses"><i class="fas fa-map-marker-alt me-2"></i>Addresses</a></li>
                    <li><a href="/cims/clients"><i class="fas fa-building me-2"></i>Companies/Clients</a></li>
                    <li><a href="/cims/info-docs"><i class="fas fa-file-alt me-2"></i>Info Docs</a></li>
                    <li><a href="/cims/docmanager"><i class="fas fa-folder-open me-2"></i>Document Manager</a></li>
                </ul>
            </li>
            <!-- Accounting -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Accounting</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">General Ledger</a></li>
                    <li><a href="#">Accounts Receivable</a></li>
                    <li><a href="#">Accounts Payable</a></li>
                    <li><a href="#">Banking</a></li>
                    <li><a href="#">Financial Reports</a></li>
                </ul>
            </li>
            <!-- Taxation -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-receipt"></i>
                    <span class="nav-text">Taxation</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Corporate Tax</a></li>
                    <li><a href="#">Personal Tax</a></li>
                    <li><a href="#">VAT/GST</a></li>
                    <li><a href="#">Tax Calendar</a></li>
                    <li><a href="#">Tax Filings</a></li>
                </ul>
            </li>
            <!-- Payroll -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Payroll</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Payroll Runs</a></li>
                    <li><a href="#">Employees</a></li>
                    <li><a href="#">Leave Management</a></li>
                    <li><a href="#">Payslips</a></li>
                    <li><a href="/cims/payroll/paye-calculator"><i class="fas fa-calculator me-2"></i>PAYE Calculator</a></li>
                </ul>
            </li>
            <!-- Compliance -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-scale-balanced"></i>
                    <span class="nav-text">Compliance</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Compliance Register</a></li>
                    <li><a href="#">Risk Assessments</a></li>
                    <li><a href="#">Audits</a></li>
                    <li><a href="#">Policies</a></li>
                </ul>
            </li>
            <!-- Contracts -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-file-signature"></i>
                    <span class="nav-text">Contracts</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Contract Repository</a></li>
                    <li><a href="#">Engagement Letters</a></li>
                    <li><a href="#">SLA Management</a></li>
                    <li><a href="#">Renewals</a></li>
                </ul>
            </li>
            <!-- Documents -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-folder-tree"></i>
                    <span class="nav-text">Documents</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Document Repository</a></li>
                    <li><a href="#">Client Folders</a></li>
                    <li><a href="#">Search</a></li>
                    <li><a href="#">Archive</a></li>
                </ul>
            </li>
            <!-- Bizz Box -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-boxes-stacked"></i>
                    <span class="nav-text">Bizz Box</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Document Templates</a></li>
                    <li><a href="#">Smart Forms</a></li>
                    <li><a href="#">Contract Builder</a></li>
                    <li><a href="#">Generated Docs</a></li>
                </ul>
            </li>
            <!-- Admin -->
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-user-gear"></i>
                    <span class="nav-text">Admin</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Workflows</a></li>
                    <li><a href="#">Task Management</a></li>
                    <li><a href="#">Time Tracking</a></li>
                    <li><a href="#">Users</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </li>

            <!-- Website Preview -->
            <li>
                <a href="/smartweigh-website.html" target="_blank" aria-expanded="false">
                    <i class="fas fa-globe"></i>
                    <span class="nav-text">Website</span>
                </a>
            </li>
        </ul>

        <div class="copyright">
            <p><strong>CIMS</strong> &copy; {{ date('Y') }}</p>
            <p class="fs-12">by ATP Solutions</p>
        </div>
    </div>
</div>
