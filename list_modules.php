<?php
$modulesDir = __DIR__ . '/Modules';
$modules = [];
if (is_dir($modulesDir)) {
    foreach (scandir($modulesDir) as $mod) {
        if ($mod === '.' || $mod === '..') continue;
        $modPath = $modulesDir . '/' . $mod;
        if (!is_dir($modPath)) continue;
        $info = ['name' => $mod, 'files' => []];
        // Check for module.json
        if (file_exists($modPath . '/module.json')) {
            $info['module_json'] = json_decode(file_get_contents($modPath . '/module.json'), true);
        }
        // List controllers
        $ctrlDir = $modPath . '/Http/Controllers';
        if (is_dir($ctrlDir)) {
            $info['controllers'] = array_values(array_diff(scandir($ctrlDir), ['.', '..']));
        }
        // List routes
        $routesFile = $modPath . '/Routes/web.php';
        if (file_exists($routesFile)) {
            $info['routes'] = file_get_contents($routesFile);
        }
        // List views
        $viewsDir = $modPath . '/Resources/views';
        if (is_dir($viewsDir)) {
            $info['views'] = [];
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $info['views'][] = str_replace($viewsDir . '/', '', $file->getPathname());
                }
            }
        }
        // List models
        $modelsDir = $modPath . '/Models';
        if (is_dir($modelsDir)) {
            $info['models'] = array_values(array_diff(scandir($modelsDir), ['.', '..']));
        }
        // List migrations
        $migDir = $modPath . '/Database/Migrations';
        if (is_dir($migDir)) {
            $info['migrations'] = array_values(array_diff(scandir($migDir), ['.', '..']));
        }
        $modules[] = $info;
    }
}
header('Content-Type: application/json');
echo json_encode($modules, JSON_PRETTY_PRINT);
