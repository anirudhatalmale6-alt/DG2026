#!/bin/bash
export SSHPASS='M87Aym1ppNuZ0JOhWuG7'
sshpass -p "$SSHPASS" sftp -o StrictHostKeyChecking=no smartucbmh@dedi1209.jnb2.host-h.net << 'EOF'
put /var/lib/freelancer/projects/40170867/ClientMasterDirector_updated.php public_html/application/Modules/cims_pm_pro/Models/ClientMasterDirector.php
rm public_html/check_director_table.php
rm public_html/check_director_table2.php
rm public_html/check_director_table3.php
rm public_html/check_director_table4.php
rm public_html/inject_test_photo.php
rm public_html/update_photo_db.php
rm public_html/application/public/check_director_table.php
EOF
