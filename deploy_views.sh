#!/bin/bash
HOST="dedi1209.jnb2.host-h.net"
USER="smartucbmh"
PASS="M87Aym1ppNuZ0JOhWuG7"
REMOTE_BASE="public_html/application/Modules/CIMS_PAYROLL/Resources/views/payroll"
LOCAL_BASE="payroll/Resources/views/payroll"

# Upload all view files via SFTP
sshpass -p "$PASS" sftp -oBatchMode=no -oStrictHostKeyChecking=no "$USER@$HOST" << EOF
# Dashboard
put ${LOCAL_BASE}/dashboard.blade.php ${REMOTE_BASE}/dashboard.blade.php

# Companies
put ${LOCAL_BASE}/companies/index.blade.php ${REMOTE_BASE}/companies/index.blade.php
put ${LOCAL_BASE}/companies/form.blade.php ${REMOTE_BASE}/companies/form.blade.php

# Employees
put ${LOCAL_BASE}/employees/index.blade.php ${REMOTE_BASE}/employees/index.blade.php
put ${LOCAL_BASE}/employees/form.blade.php ${REMOTE_BASE}/employees/form.blade.php

# Income Types
put ${LOCAL_BASE}/income-types/index.blade.php ${REMOTE_BASE}/income-types/index.blade.php

# Deduction Types
put ${LOCAL_BASE}/deduction-types/index.blade.php ${REMOTE_BASE}/deduction-types/index.blade.php

# Contribution Types
put ${LOCAL_BASE}/contribution-types/index.blade.php ${REMOTE_BASE}/contribution-types/index.blade.php

# Tax Tables
put ${LOCAL_BASE}/tax-tables/index.blade.php ${REMOTE_BASE}/tax-tables/index.blade.php

bye
EOF

echo "Upload exit code: $?"
