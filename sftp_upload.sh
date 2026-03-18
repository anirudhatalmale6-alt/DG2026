#!/bin/bash
# Upload CIMSAppointments module to server
SFTP_PASS='M87Aym1ppNuZ0JOhWuG7'
SFTP_HOST='smartucbmh@dedi1209.jnb2.host-h.net'
LOCAL_BASE="/var/lib/freelancer/projects/40170867/CIMSAppointments"
REMOTE_BASE="public_html/application/Modules/CIMSAppointments"

# Create directory structure first
sshpass -p "$SFTP_PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$SFTP_HOST" <<EOF
mkdir $REMOTE_BASE
mkdir ${REMOTE_BASE}/Config
mkdir ${REMOTE_BASE}/Database
mkdir ${REMOTE_BASE}/Database/Migrations
mkdir ${REMOTE_BASE}/Http
mkdir ${REMOTE_BASE}/Http/Controllers
mkdir ${REMOTE_BASE}/Models
mkdir ${REMOTE_BASE}/Providers
mkdir ${REMOTE_BASE}/Resources
mkdir ${REMOTE_BASE}/Resources/views
mkdir ${REMOTE_BASE}/Resources/views/appointments
mkdir ${REMOTE_BASE}/Resources/views/partials
mkdir ${REMOTE_BASE}/Resources/views/services
mkdir ${REMOTE_BASE}/Resources/views/settings
mkdir ${REMOTE_BASE}/Resources/views/staff
mkdir ${REMOTE_BASE}/Routes
mkdir ${REMOTE_BASE}/Services
EOF

echo "=== Directories created ==="

# Upload files
sshpass -p "$SFTP_PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$SFTP_HOST" <<EOF
put ${LOCAL_BASE}/module.json ${REMOTE_BASE}/module.json
put ${LOCAL_BASE}/Config/config.php ${REMOTE_BASE}/Config/config.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000001_create_cims_appointments_services_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000001_create_cims_appointments_services_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000002_create_cims_appointments_staff_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000002_create_cims_appointments_staff_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000003_create_cims_appointments_staff_services_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000003_create_cims_appointments_staff_services_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000004_create_cims_appointments_availability_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000004_create_cims_appointments_availability_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000005_create_cims_appointments_blocked_dates_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000005_create_cims_appointments_blocked_dates_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000006_create_cims_appointments_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000006_create_cims_appointments_table.php
put ${LOCAL_BASE}/Database/Migrations/2026_03_18_000007_create_cims_appointments_settings_table.php ${REMOTE_BASE}/Database/Migrations/2026_03_18_000007_create_cims_appointments_settings_table.php
put ${LOCAL_BASE}/Http/Controllers/AppointmentController.php ${REMOTE_BASE}/Http/Controllers/AppointmentController.php
put ${LOCAL_BASE}/Http/Controllers/DashboardController.php ${REMOTE_BASE}/Http/Controllers/DashboardController.php
put ${LOCAL_BASE}/Http/Controllers/ReportController.php ${REMOTE_BASE}/Http/Controllers/ReportController.php
put ${LOCAL_BASE}/Http/Controllers/ServiceController.php ${REMOTE_BASE}/Http/Controllers/ServiceController.php
put ${LOCAL_BASE}/Http/Controllers/SettingsController.php ${REMOTE_BASE}/Http/Controllers/SettingsController.php
put ${LOCAL_BASE}/Http/Controllers/StaffController.php ${REMOTE_BASE}/Http/Controllers/StaffController.php
put ${LOCAL_BASE}/Models/Appointment.php ${REMOTE_BASE}/Models/Appointment.php
put ${LOCAL_BASE}/Models/AppointmentService.php ${REMOTE_BASE}/Models/AppointmentService.php
put ${LOCAL_BASE}/Models/AppointmentSetting.php ${REMOTE_BASE}/Models/AppointmentSetting.php
put ${LOCAL_BASE}/Models/AppointmentStaff.php ${REMOTE_BASE}/Models/AppointmentStaff.php
put ${LOCAL_BASE}/Models/BlockedDate.php ${REMOTE_BASE}/Models/BlockedDate.php
put ${LOCAL_BASE}/Models/StaffAvailability.php ${REMOTE_BASE}/Models/StaffAvailability.php
put ${LOCAL_BASE}/Providers/CIMSAppointmentsServiceProvider.php ${REMOTE_BASE}/Providers/CIMSAppointmentsServiceProvider.php
put ${LOCAL_BASE}/Providers/RouteServiceProvider.php ${REMOTE_BASE}/Providers/RouteServiceProvider.php
put ${LOCAL_BASE}/Resources/views/appointments/calendar.blade.php ${REMOTE_BASE}/Resources/views/appointments/calendar.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/create.blade.php ${REMOTE_BASE}/Resources/views/appointments/create.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/dashboard.blade.php ${REMOTE_BASE}/Resources/views/appointments/dashboard.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/edit.blade.php ${REMOTE_BASE}/Resources/views/appointments/edit.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/index.blade.php ${REMOTE_BASE}/Resources/views/appointments/index.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/reports.blade.php ${REMOTE_BASE}/Resources/views/appointments/reports.blade.php
put ${LOCAL_BASE}/Resources/views/appointments/show.blade.php ${REMOTE_BASE}/Resources/views/appointments/show.blade.php
put ${LOCAL_BASE}/Resources/views/partials/menu_snippet.blade.php ${REMOTE_BASE}/Resources/views/partials/menu_snippet.blade.php
put ${LOCAL_BASE}/Resources/views/partials/sidebar.blade.php ${REMOTE_BASE}/Resources/views/partials/sidebar.blade.php
put ${LOCAL_BASE}/Resources/views/services/create.blade.php ${REMOTE_BASE}/Resources/views/services/create.blade.php
put ${LOCAL_BASE}/Resources/views/services/edit.blade.php ${REMOTE_BASE}/Resources/views/services/edit.blade.php
put ${LOCAL_BASE}/Resources/views/services/index.blade.php ${REMOTE_BASE}/Resources/views/services/index.blade.php
put ${LOCAL_BASE}/Resources/views/settings/index.blade.php ${REMOTE_BASE}/Resources/views/settings/index.blade.php
put ${LOCAL_BASE}/Resources/views/staff/create.blade.php ${REMOTE_BASE}/Resources/views/staff/create.blade.php
put ${LOCAL_BASE}/Resources/views/staff/edit.blade.php ${REMOTE_BASE}/Resources/views/staff/edit.blade.php
put ${LOCAL_BASE}/Resources/views/staff/index.blade.php ${REMOTE_BASE}/Resources/views/staff/index.blade.php
put ${LOCAL_BASE}/Resources/views/staff/show.blade.php ${REMOTE_BASE}/Resources/views/staff/show.blade.php
put ${LOCAL_BASE}/Routes/web.php ${REMOTE_BASE}/Routes/web.php
put ${LOCAL_BASE}/Services/AppointmentEmailService.php ${REMOTE_BASE}/Services/AppointmentEmailService.php
put ${LOCAL_BASE}/Services/ClientSyncService.php ${REMOTE_BASE}/Services/ClientSyncService.php
put ${LOCAL_BASE}/Services/SlotService.php ${REMOTE_BASE}/Services/SlotService.php
put ${LOCAL_BASE}/INSTALL.md ${REMOTE_BASE}/INSTALL.md
EOF

echo "=== Files uploaded ==="
