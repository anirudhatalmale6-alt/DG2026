#!/bin/bash
SFTP_HOST="dedi1209.jnb2.host-h.net"
SFTP_USER="smartucbmh"
SFTP_PASS="M87Aym1ppNuZ0JOhWuG7"
REMOTE_BASE="/public_html/application/Modules/CIMSTyreDash"
LOCAL_BASE="/var/lib/freelancer/projects/40170867/CIMSTyreDash"

# Create all remote directories first
sshpass -p "$SFTP_PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$SFTP_USER@$SFTP_HOST" << EOF
-mkdir /public_html/application/Modules/CIMSTyreDash
-mkdir $REMOTE_BASE/Config
-mkdir $REMOTE_BASE/Database
-mkdir $REMOTE_BASE/Database/Migrations
-mkdir $REMOTE_BASE/Http
-mkdir $REMOTE_BASE/Http/Controllers
-mkdir $REMOTE_BASE/Models
-mkdir $REMOTE_BASE/Providers
-mkdir $REMOTE_BASE/Resources
-mkdir $REMOTE_BASE/Resources/views
-mkdir $REMOTE_BASE/Resources/views/branches
-mkdir $REMOTE_BASE/Resources/views/brands
-mkdir $REMOTE_BASE/Resources/views/catalogue
-mkdir $REMOTE_BASE/Resources/views/customers
-mkdir $REMOTE_BASE/Resources/views/dashboard
-mkdir $REMOTE_BASE/Resources/views/jobcards
-mkdir $REMOTE_BASE/Resources/views/partials
-mkdir $REMOTE_BASE/Resources/views/quotes
-mkdir $REMOTE_BASE/Resources/views/services
-mkdir $REMOTE_BASE/Resources/views/settings
-mkdir $REMOTE_BASE/Resources/views/stock
-mkdir $REMOTE_BASE/Resources/views/vehicles
-mkdir $REMOTE_BASE/Routes
-mkdir $REMOTE_BASE/Services
bye
EOF

echo "Directories created. Now uploading files..."

# Upload all files
find "$LOCAL_BASE" -type f | while read filepath; do
    relative="${filepath#$LOCAL_BASE/}"
    remote_path="$REMOTE_BASE/$relative"
    echo "Uploading: $relative"
    sshpass -p "$SFTP_PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$SFTP_USER@$SFTP_HOST" << EOF
put "$filepath" "$remote_path"
bye
EOF
done

echo "All files uploaded."
