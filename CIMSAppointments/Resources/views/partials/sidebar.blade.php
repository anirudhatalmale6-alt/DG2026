{{-- Appointments Module Sidebar Navigation --}}
<div class="email-left-box email-left-body">
    <div class="generic-width px-0 mb-5 mt-4 mt-sm-0">
        <div class="p-0">
            <a href="{{ route('cimsappointments.appointments.create') }}" class="btn btn-primary btn-block">
                <i class="fas fa-plus me-2"></i>New Appointment
            </a>
        </div>

        <div class="mail-list rounded mt-4">
            <a href="{{ route('cimsappointments.dashboard') }}" class="list-group-item {{ ($activePage ?? '') == 'dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt font-18 align-middle me-2"></i> Dashboard
            </a>
            <a href="{{ route('cimsappointments.appointments.index') }}" class="list-group-item {{ ($activePage ?? '') == 'appointments' ? 'active' : '' }}">
                <i class="fas fa-calendar-check font-18 align-middle me-2"></i> All Appointments
                @if(($counts['pending'] ?? 0) > 0)
                    <span class="badge badge-warning badge-sm float-end">{{ $counts['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('cimsappointments.appointments.calendar') }}" class="list-group-item {{ ($activePage ?? '') == 'calendar' ? 'active' : '' }}">
                <i class="fas fa-calendar-alt font-18 align-middle me-2"></i> Calendar View
            </a>
        </div>

        <div class="mail-list rounded overflow-hidden mt-4">
            <div class="intro-title d-flex justify-content-between mt-0">
                <h5>Manage</h5>
            </div>
            <a href="{{ route('cimsappointments.services.index') }}" class="list-group-item {{ ($activePage ?? '') == 'services' ? 'active' : '' }}">
                <span class="icon-warning"><i class="fa fa-circle"></i></span> Services
            </a>
            <a href="{{ route('cimsappointments.staff.index') }}" class="list-group-item {{ ($activePage ?? '') == 'staff' ? 'active' : '' }}">
                <span class="icon-success"><i class="fa fa-circle"></i></span> Staff Members
            </a>
            <a href="{{ route('cimsappointments.reports.index') }}" class="list-group-item {{ ($activePage ?? '') == 'reports' ? 'active' : '' }}">
                <span class="icon-info"><i class="fa fa-circle"></i></span> Reports
            </a>
            <a href="{{ route('cimsappointments.settings.index') }}" class="list-group-item {{ ($activePage ?? '') == 'settings' ? 'active' : '' }}">
                <span class="icon-primary"><i class="fa fa-circle"></i></span> Settings
            </a>
        </div>
    </div>
</div>
