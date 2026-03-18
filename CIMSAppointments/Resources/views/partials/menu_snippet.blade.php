{{--
================================================================================
APPOINTMENTS MENU SNIPPET
================================================================================
Add this inside the CIMS Master Menu <ul class="cims-main-menu">
Paste it alongside the other menu items (Entities, Taxes, Communications, etc.)
================================================================================
--}}

{{-- APPOINTMENTS --}}
<li class="cims-menu-item">
    <a href="javascript:void(0);" class="cims-menu-link">
        <i class="fas fa-calendar-check"></i>
        <span>Appointments</span>
    </a>
    <div class="sd_tooltip_teal sd-mainmenu-tooltip">Manage appointments, scheduling & bookings</div>
    <ul class="cims-submenu">
        <li>
            <a href="{{ Route::has('cimsappointments.dashboard') ? route('cimsappointments.dashboard') : '#' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Appointment overview and today's schedule</div>
        </li>
        <li>
            <a href="{{ Route::has('cimsappointments.appointments.create') ? route('cimsappointments.appointments.create') : '#' }}">
                <i class="fas fa-calendar-plus"></i> Book Appointment
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Create a new appointment booking</div>
        </li>
        <li>
            <a href="{{ Route::has('cimsappointments.appointments.index') ? route('cimsappointments.appointments.index') : '#' }}">
                <i class="fas fa-list"></i> All Appointments
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">View and manage all appointments</div>
        </li>
        <li>
            <a href="{{ Route::has('cimsappointments.appointments.calendar') ? route('cimsappointments.appointments.calendar') : '#' }}">
                <i class="fas fa-calendar-alt"></i> Calendar View
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Monthly/weekly calendar view</div>
        </li>
        <li>
            <a href="javascript:void(0);"><i class="fas fa-cogs"></i> Manage</a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Services, staff & settings</div>
            <ul class="cims-submenu-level3">
                <li><a href="{{ Route::has('cimsappointments.services.index') ? route('cimsappointments.services.index') : '#' }}"><i class="fas fa-concierge-bell"></i> Services</a></li>
                <li><a href="{{ Route::has('cimsappointments.staff.index') ? route('cimsappointments.staff.index') : '#' }}"><i class="fas fa-users"></i> Staff Members</a></li>
                <li><a href="{{ Route::has('cimsappointments.reports.index') ? route('cimsappointments.reports.index') : '#' }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="{{ Route::has('cimsappointments.settings.index') ? route('cimsappointments.settings.index') : '#' }}"><i class="fas fa-wrench"></i> Settings</a></li>
            </ul>
        </li>
    </ul>
</li>
