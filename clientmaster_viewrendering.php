<?php

namespace Modules\ClientMasterNew\Listeners\MainApp;

use App\Events\MainApp\ViewComposer\ViewRendering as ViewRenderingEvent;

class ViewRendering {

    /**
     * Handle the event.
     *
     * @param  ViewRenderingEvent  $event
     * @return void
     */
    public function handle(ViewRenderingEvent $event) {
        $this->menuMain();
    }

    /**
     * Add items to the main menu
     *
     * @return void
     */
    public function menuMain() {

        // add item to main menu (team)
        $html_content = view('clientmasternew::menus.main-team')->render();
        view()->startPush('menu_main_team_10');
        echo $html_content;
        view()->stopPush();
    }
}
