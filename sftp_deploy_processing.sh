#!/bin/bash
HOST="dedi1209.jnb2.host-h.net"
USER="smartucbmh"
PASS="M87Aym1ppNuZ0JOhWuG7"
LOCAL="/var/lib/freelancer/projects/40170867"
REMOTE="/public_html/application"

sshpass -p "$PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$USER@$HOST" << EOF
# Models
cd ${REMOTE}/Modules/CIMS_PAYROLL/Models
put ${LOCAL}/payroll/Models/PayrollEmployee.php
put ${LOCAL}/payroll/Models/PayrollMedicalAid.php
put ${LOCAL}/payroll/Models/PayrollPrivateRA.php

# Controller
cd ${REMOTE}/Modules/CIMS_PAYROLL/Http/Controllers
put ${LOCAL}/payroll/Http/Controllers/PayrollController.php

# Routes
cd ${REMOTE}/Modules/CIMS_PAYROLL/Routes
put ${LOCAL}/payroll/Routes/web.php

# Views - processing page
cd ${REMOTE}/Modules/CIMS_PAYROLL/Resources/views/payroll
put ${LOCAL}/payroll/Resources/views/payroll/processing.blade.php

# Views - payslip template
cd ${REMOTE}/Modules/CIMS_PAYROLL/Resources/views/payroll/payslips
put ${LOCAL}/payroll/Resources/views/payroll/payslips/pdf-template.blade.php

# Services
cd ${REMOTE}/Modules/CIMS_PAYROLL/Services
put ${LOCAL}/payroll/Services/PayslipPdfGenerator.php

# Menu
cd ${REMOTE}/Modules/CIMSCore/Resources/views/partials
put ${LOCAL}/actual_live_menu.blade.php cims_master_menu.blade.php

# Migration script (in public_html root)
cd /public_html
put ${LOCAL}/payroll/migrate_processing.php

bye
EOF
echo "SFTP upload complete"
