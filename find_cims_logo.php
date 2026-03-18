<?php
header('Content-Type: text/plain');
exec("find /usr/www/users/smartucbmh/ -maxdepth 5 -name '*cims_logo*' -o -name '*cims*logo*' 2>/dev/null", $files);
echo "FILES:\n";
foreach ($files as $f) echo "  $f\n";

exec("grep -rl 'cims_logo' /usr/www/users/smartucbmh/application/resources/ /usr/www/users/smartucbmh/application/Modules/ /usr/www/users/smartucbmh/application/config/ 2>/dev/null", $refs);
echo "\nREFERENCES:\n";
foreach ($refs as $r) echo "  $r\n";

$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');
$stmt = $pdo->query("SELECT setting_name, setting_value FROM settings WHERE setting_name LIKE '%logo%' OR setting_value LIKE '%cims%'");
echo "\nDB SETTINGS:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $s) echo "  {$s['setting_name']} = {$s['setting_value']}\n";

$imgDir = '/usr/www/users/smartucbmh/images_old/';
if (is_dir($imgDir)) {
    echo "\nimages_old/ contents:\n";
    foreach (scandir($imgDir) as $f) { if ($f!=='.'&&$f!=='..') echo "  $f\n"; }
}
