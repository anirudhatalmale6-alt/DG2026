#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
get public_html/application/config/database.php /var/lib/freelancer/projects/40170867/database_config.php
get public_html/application/.env /var/lib/freelancer/projects/40170867/env_file.txt
EOF
