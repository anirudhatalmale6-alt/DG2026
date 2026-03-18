#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
get public_html/application/Modules/CIMSPersons/Http/Controllers/PersonController.php /var/lib/freelancer/projects/40170867/PersonController_server.php
EOF
