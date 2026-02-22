@echo off
echo ============================================
echo  CimsDocgen - Document Generator Installer
echo ============================================
echo.

REM Check if composer is available
where composer >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Composer found! Installing PDF library...
    composer require setasign/fpdi-tcpdf
    echo.
    echo Done! Library installed via Composer.
) else (
    echo Composer not found on this PC.
    echo.
    echo MANUAL INSTALL: Copy the folders inside "vendor_files"
    echo into your Laravel project's "vendor" folder:
    echo.
    echo   vendor_files\setasign  -^>  vendor\setasign
    echo   vendor_files\tecnickcom  -^>  vendor\tecnickcom
    echo.
    echo Then add these lines to your vendor\composer\autoload_psr4.php:
    echo   'setasign\\Fpdi\\' =^> array($vendorDir . '/setasign/fpdi/src'),
    echo   'setasign\\FpdiTcpdf\\' =^> array($vendorDir . '/setasign/fpdi-tcpdf/src'),
    echo   'TCPDF' =^> array($vendorDir . '/tecnickcom/tcpdf'),
)

echo.
echo ============================================
echo  NEXT STEPS:
echo ============================================
echo  1. Copy the module folder to: Modules\CimsDocgen
echo     (Do NOT copy vendor_files or this .bat file)
echo  2. Run the SQL: create_docgen_tables.sql
echo  3. Access: /cims/docgen/
echo ============================================
pause
