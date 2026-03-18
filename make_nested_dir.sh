#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
ls public_html/storage/storage/
mkdir public_html/storage/storage
ls public_html/storage/storage/
mkdir public_html/storage/storage/profile_photos
put /var/lib/freelancer/projects/40170867/test_profile_photo.jpg public_html/storage/storage/profile_photos/test_profile_photo.jpg
ls public_html/storage/storage/profile_photos/
EOF
