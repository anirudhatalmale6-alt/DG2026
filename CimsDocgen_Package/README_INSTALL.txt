============================================
 DOCUMENT GENERATOR MODULE - INSTALLATION
============================================

STEP 1: Install PDF Library
----------------------------
Option A (Recommended - if you have Composer):
  Open Command Prompt, go to your Laravel project folder, and run:
    composer require setasign/fpdi-tcpdf

Option B (Manual - no Composer needed):
  Copy these 2 folders from the "vendor_files" folder in this zip:
    vendor_files\setasign    -->  your_project\vendor\setasign
    vendor_files\tecnickcom  -->  your_project\vendor\tecnickcom

  If setasign/tecnickcom folders already exist in vendor, just overwrite.


STEP 2: Copy Module
--------------------
  Copy everything EXCEPT "vendor_files", "INSTALL_WINDOWS.bat", and this file
  into: your_project\Modules\CimsDocgen\

  Your folder structure should look like:
    Modules\CimsDocgen\
      ├── Config\
      ├── Database\
      ├── Http\
      ├── Models\
      ├── Providers\
      ├── Resources\
      ├── Routes\
      ├── Services\
      ├── module.json
      └── create_docgen_tables.sql


STEP 3: Run SQL Script
-----------------------
  Open phpMyAdmin (or your MySQL tool)
  Select your database
  Go to SQL tab
  Paste the contents of: create_docgen_tables.sql
  Click "Go" to run it


STEP 4: Access the Module
--------------------------
  Open your browser and go to:
    http://your-domain.com/cims/docgen/

  You should see the Document Generator dashboard.


PAGES:
  /cims/docgen/           - Document list
  /cims/docgen/generate   - Generate new document
  /cims/docgen/templates  - Manage templates
  /cims/docgen/settings   - General settings
  /cims/docgen/smtp       - Email settings


SUPPORT:
  Message me on Freelancer if you need any help!
