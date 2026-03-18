#!/bin/bash
set -e

SERVER="dedi1209.jnb2.host-h.net"
USER="smartucbmh"
PASS="M87Aym1ppNuZ0JOhWuG7"
LOCAL="/var/lib/freelancer/projects/40170867"
REMOTE="public_html/application"

SFTP_CMD="sshpass -p '$PASS' sftp -oBatchMode=no -oStrictHostKeyChecking=no $USER@$SERVER"

echo "=== 1. Uploading migration files ==="
echo "put ${LOCAL}/empsa_ref/CIMS_EMP201/Database/Migrations/2026_03_15_000001_create_sars_status_table.php ${REMOTE}/Modules/CIMS_EMP201/Database/Migrations/2026_03_15_000001_create_sars_status_table.php" | $SFTP_CMD

echo "put ${LOCAL}/empsa_ref/CIMS_EMP201/Database/Migrations/2026_03_15_000002_add_emp201_status_and_approved_by_to_declarations.php ${REMOTE}/Modules/CIMS_EMP201/Database/Migrations/2026_03_15_000002_add_emp201_status_and_approved_by_to_declarations.php" | $SFTP_CMD

echo "=== 2. Uploading SarsStatus model ==="
echo "put ${LOCAL}/empsa_ref/CIMS_EMP201/Models/SarsStatus.php ${REMOTE}/Modules/CIMS_EMP201/Models/SarsStatus.php" | $SFTP_CMD

echo "=== 3. Uploading updated Emp201Declaration model ==="
echo "put ${LOCAL}/empsa_ref/CIMS_EMP201/Models/Emp201Declaration.php ${REMOTE}/Modules/CIMS_EMP201/Models/Emp201Declaration.php" | $SFTP_CMD

echo "=== 4. Uploading updated controller ==="
echo "put ${LOCAL}/empsa_ref/CIMS_EMP201/Http/Controllers/Emp201Controller.php ${REMOTE}/Modules/CIMS_EMP201/Http/Controllers/Emp201Controller.php" | $SFTP_CMD

echo "=== 5. Uploading updated form blade ==="
echo "put ${LOCAL}/emp201_form.blade.php ${REMOTE}/Modules/CIMS_EMP201/Resources/views/emp201/form.blade.php" | $SFTP_CMD

echo "=== All files uploaded ==="
