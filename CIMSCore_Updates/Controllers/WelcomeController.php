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
}
