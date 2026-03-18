#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
get /usr/www/users/smartucbmh/public_html/application/Modules/cims_pm_pro/Models/ClientMasterDirector.php /var/lib/freelancer/projects/40170867/ClientMasterDirector.php
EOF
