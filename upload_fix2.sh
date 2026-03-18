#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
put /var/lib/freelancer/projects/40170867/fix_person_photo_back.php public_html/fix_person_photo_back.php
EOF
