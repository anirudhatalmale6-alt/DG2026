#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
rm public_html/check_app_url.php
rm public_html/check_app_url2.php
rm public_html/check_update_route.php
EOF
