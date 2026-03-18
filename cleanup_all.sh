#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
rm public_html/test_asset_url.php
rm public_html/test_laravel_asset.php
rm public_html/fix_person_photo_back.php
EOF
