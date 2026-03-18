<?php
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<pre>\n=== UPLOAD DEBUG ===\n\n";

// 1. PHP upload settings
echo "=== PHP Upload Settings ===\n";
echo "  upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "  post_max_size: " . ini_get('post_max_size') . "\n";
echo "  max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "  upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: '(system default)') . "\n";
echo "  sys_temp_dir: " . sys_get_temp_dir() . "\n";
echo "  temp dir writable: " . (is_writable(sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "  temp dir exists: " . (is_dir(sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "  file_uploads: " . ini_get('file_uploads') . "\n";
echo "  max_input_time: " . ini_get('max_input_time') . "\n";
echo "  max_execution_time: " . ini_get('max_execution_time') . "\n";

// 2. Check recent log entries for upload errors
echo "\n=== Recent FPM Log (last upload-related entries) ===\n";
$logPath = storage_path('logs/laravel-fpm-fcgi.log');
if (file_exists($logPath)) {
    // Read last 50000 bytes to find recent upload errors
    $fp = fopen($logPath, 'r');
    fseek($fp, -50000, SEEK_END);
    $content = fread($fp, 50000);
    fclose($fp);

    $lines = explode("\n", $content);
    $relevantLines = [];
    foreach ($lines as $line) {
        if (preg_match('/upload|income_tax|cor_cert|document.*fail|ITAX|file_size|handleDocument|uploadDocument|FAILED|Error updating/i', $line)) {
            $relevantLines[] = substr($line, 0, 500);
        }
    }
    if (empty($relevantLines)) {
        echo "  No upload-related errors found in last 50000 bytes\n";
    } else {
        foreach (array_slice($relevantLines, -20) as $line) {
            echo "  " . htmlspecialchars($line) . "\n\n";
        }
    }
}

// 3. Test form - simple file upload test
echo "\n=== Upload Test Form ===\n";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "Upload received!\n";
    echo "  Error code: " . $_FILES['test_file']['error'] . "\n";
    echo "  Error meaning: ";
    $errors = [
        UPLOAD_ERR_OK => 'OK (no error)',
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
    ];
    echo ($errors[$_FILES['test_file']['error']] ?? 'Unknown error') . "\n";
    echo "  Name: " . $_FILES['test_file']['name'] . "\n";
    echo "  Size: " . $_FILES['test_file']['size'] . "\n";
    echo "  Tmp name: " . $_FILES['test_file']['tmp_name'] . "\n";
    echo "  Tmp exists: " . (file_exists($_FILES['test_file']['tmp_name']) ? 'YES' : 'NO') . "\n";
    echo "  Type: " . $_FILES['test_file']['type'] . "\n";

    // Try to move it
    $dest = sys_get_temp_dir() . '/upload_test_' . time() . '.pdf';
    if (move_uploaded_file($_FILES['test_file']['tmp_name'], $dest)) {
        echo "  Move test: SUCCESS to $dest\n";
        unlink($dest);
    } else {
        echo "  Move test: FAILED\n";
    }
} else {
    echo "  (POST a file to test)\n";
}

// 4. Check Laravel validation messages for 'uploaded' rule
echo "\n=== Laravel 'uploaded' validation message ===\n";
$messages = require base_path('vendor/laravel/framework/src/Illuminate/Validation/Concerns/../../../lang/en/validation.php');
echo "  'uploaded' message: " . ($messages['uploaded'] ?? 'NOT FOUND') . "\n";
echo "  'file' message: " . ($messages['file'] ?? 'NOT FOUND') . "\n";

echo "\n=== DONE ===\n</pre>";
