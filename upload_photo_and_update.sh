#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
mkdir public_html/application/storage/app/public/profile_photos
put /var/lib/freelancer/projects/40170867/test_profile_photo.jpg public_html/application/storage/app/public/profile_photos/test_profile_photo.jpg
mkdir public_html/storage/profile_photos
put /var/lib/freelancer/projects/40170867/test_profile_photo.jpg public_html/storage/profile_photos/test_profile_photo.jpg
mkdir public_html/application/public/storage/profile_photos
put /var/lib/freelancer/projects/40170867/test_profile_photo.jpg public_html/application/public/storage/profile_photos/test_profile_photo.jpg
EOF
