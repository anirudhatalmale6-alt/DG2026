<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for product custom fields settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Products\CustomFields;
use App\Http\Responses\Settings\Products\IndexResponse;
use App\Http\Responses\Settings\Products\UpdateResponse;
use Illuminate\Http\Request;

class ProductCustomFields extends Controller {

    public function __construct() {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('settingsMiddlewareIndex');
    }

    /**
     * Display the product custom fields settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        // Page settings
        $page = $this->pageSettings();

        // Get all 10 custom fields (ordered by ID)
        $fields = \App\Models\ProductCustomField::orderBy('items_custom_id', 'asc')->get();

        // Response payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
        ];

        // Return response
        return new IndexResponse($payload);
    }

    /**
     * Update product custom fields settings
     *
     * @param CustomFields $request
     * @return \Illuminate\Http\Response
     */
    public function update(CustomFields $request) {

        // Loop through all 10 custom fields
        for ($i = 1; $i <= 10; $i++) {

            // Get the field record
            $field = \App\Models\ProductCustomField::find($i);

            // Get submitted name value
            $field_name = request('items_custom_field_name')[$i] ?? null;

            // If field name is empty, reset everything
            if (empty(trim($field_name))) {
                $field->items_custom_field_name = null;
                $field->items_custom_field_status = 'disabled';
                $field->items_custom_field_show_on_invoice = 'no';

                // Clear all item values for this custom field
                \App\Models\Item::query()->update([
                    'item_custom_field_' . $i => null,
                ]);
            } else {
                // Update with submitted values
                $field->items_custom_field_name = $field_name;
                $field->items_custom_field_status = (request('items_custom_field_status')[$i] ?? null) == 'on' ? 'enabled' : 'disabled';
                $field->items_custom_field_show_on_invoice = (request('items_custom_field_show_on_invoice')[$i] ?? null) == 'on' ? 'yes' : 'no';
            }

            // Update creator/updater
            $field->items_custom_field_creatorid = auth()->id();

            // Save
            $field->save();
        }

        // Response payload
        $payload = [];

        // Return response
        return new UpdateResponse($payload);
    }

    /**
     * Generate page metadata
     *
     * @param string $section
     * @param array $data
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.products'),
                __('lang.custom_fields'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_general' => 'active',
            'main_menu_id' => 'active',
            'submenu_products_custom_fields' => 'active',
        ];

        return $page;
    }
}
