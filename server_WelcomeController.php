<?php

namespace Modules\CIMSCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WelcomeController extends Controller
{
    /**
     * Display the CIMS Welcome page - clean white page with POPIA consent
     */
    public function index()
    {
        return view('cimscore::partials.cims_master_welcome');
    }

    /**
     * Display the CIMS Wizard page - with menu and wizard tabs
     */
    public function wizard()
    {
        return view('cimscore::pages.wizard');
    }

    /**
     * Display the CIMS Main Landing page - full dashboard with header, menu, footer
     */
    public function landing()
    {
        return view('cimscore::partials.cims_master_landing');
    }

    /**
     * Display the Hello Sunshine page
     */
    public function hello()
    {
        return view('cimscore::pages.hello_sunshine');
    }

    /**
     * Display the CIMS Client Master page
     */
    public function clientmaster()
    {
        return view('cimscore::pages.cims_clientmaster');
    }

    /**
     * Display the CIMS Client Portal page
     */
    public function clientportal()
    {
        return view('cimscore::pages.cims_client_portal');
    }

    /**
     * Clear all caches - blade views, bootstrap, OPcache, and bump asset version
     */
    public function clearCache()
    {
        $cleared = [];

        // 1. Clear compiled blade views
        $viewsPath = storage_path('framework/views');
        if (is_dir($viewsPath)) {
            $files = glob($viewsPath . '/*.php');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file) && @unlink($file)) $count++;
            }
            $cleared[] = "Blade views: $count files cleared";
        }

        // 2. Clear bootstrap cache
        $bootstrapPath = base_path('bootstrap/cache');
        foreach (['routes-v7.php', 'config.php', 'packages.php', 'services.php'] as $f) {
            $p = $bootstrapPath . '/' . $f;
            if (file_exists($p) && @unlink($p)) {
                $cleared[] = "Bootstrap: $f deleted";
            }
        }

        // 3. Try to reset OPcache
        if (function_exists('opcache_reset')) {
            @opcache_reset();
            $cleared[] = 'OPcache: reset attempted';
        }

        // 4. Bump the asset cache version (forces browser to load fresh JS/CSS)
        $versionFile = storage_path('cache_version.txt');
        $newVersion = time();
        file_put_contents($versionFile, $newVersion);
        $cleared[] = "Asset version: bumped to $newVersion";

        return redirect()->back()->with('success', 'All caches cleared successfully')->with('swal_action', 'cleared')->with('swal_name', 'System Cache');
    }
}
