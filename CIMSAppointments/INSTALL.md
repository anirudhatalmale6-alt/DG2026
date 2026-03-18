# CIMSAppointments Module - Installation Guide

## Quick Install

### 1. Copy Module
Copy the entire `CIMSAppointments/` folder to your Laravel project's `Modules/` directory:
```
Modules/CIMSAppointments/
```

### 2. Register the Module
Add to `modules_statuses.json`:
```json
"CIMSAppointments": true
```

### 3. Add Grow CRM Database Connection
Add to `config/database.php` in the `connections` array:
```php
'growcrm' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'grow_crm_2026',
    'username' => '5fokp_qnbo1',
    'password' => '4P9716bzm7598A',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
],
```

### 4. Run Migrations
```bash
php artisan migrate
```
This creates 7 tables:
- cims_appointments_services
- cims_appointments_staff
- cims_appointments_staff_services
- cims_appointments_availability
- cims_appointments_blocked_dates
- cims_appointments
- cims_appointments_settings (with default values)

### 5. Add to Navigation Menu
Copy the menu snippet from `Resources/views/partials/menu_snippet.blade.php` into your CIMS Master Menu blade file, inside the `<ul class="cims-main-menu">` section.

### 6. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Routes
All routes are prefixed with `/cims/appointments/` and require authentication.

| URL | Description |
|-----|-------------|
| /cims/appointments/ | Dashboard |
| /cims/appointments/list | All appointments |
| /cims/appointments/calendar | Calendar view |
| /cims/appointments/book | Book new appointment |
| /cims/appointments/services | Manage services |
| /cims/appointments/staff | Manage staff |
| /cims/appointments/reports | Reports |
| /cims/appointments/settings | Settings |

## Email Reminders (Cron Job)
To send automatic reminders, add a scheduled command or cron job that calls:
```php
app(\Modules\CIMSAppointments\Services\AppointmentEmailService::class)->processReminders();
```

## Client Sync
The module automatically:
- Creates new clients in both `client_master` AND Grow CRM `clients` table
- Links them via `client_code` in custom field ID 38
- Checks sync status bidirectionally
