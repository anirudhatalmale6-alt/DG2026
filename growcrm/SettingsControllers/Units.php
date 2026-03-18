<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for units settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Units\IndexResponse;
use App\Http\Responses\Settings\Units\CreateResponse;
use App\Http\Responses\Settings\Units\StoreResponse;
use App\Http\Responses\Settings\Units\EditResponse;
use App\Http\Responses\Settings\Units\UpdateResponse;
use App\Http\Responses\Settings\Units\DeleteResponse;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;
use Validator;

class Units extends Controller {

    /**
     * The units repository instance.
     */
    protected $unitrepo;

    public function __construct(UnitRepository $unitrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->unitrepo = $unitrepo;

    }

    /**
     * Display a listing of units
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //get units
        $units = $this->unitrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'units' => $units,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new unit
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //page settings
        $page = $this->pageSettings('create');

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created unit
     *
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'unit_name' => [
                'required',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (strip_tags($value) !== $value) {
                        return $fail(__('lang.units_no_html'));
                    }
                },
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //create the unit
        if (!$unit_id = $this->unitrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the unit object (friendly for rendering in blade template)
        $units = $this->unitrepo->search($unit_id);

        //counting rows
        $rows = $this->unitrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'units' => $units,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing a unit
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //units
        $units = $this->unitrepo->search($id);

        //not found
        if (!$unit = $units->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'unit' => $unit,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified unit
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'unit_name' => [
                'required',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (strip_tags($value) !== $value) {
                        return $fail(__('lang.units_no_html'));
                    }
                },
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update the resource
        if (!$this->unitrepo->update($id)) {
            abort(409);
        }

        //get the unit object (friendly for rendering in blade template)
        $units = $this->unitrepo->search($id);

        //reponse payload
        $payload = [
            'units' => $units,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified unit
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //check if unit is in use by any items
        if ($this->unitrepo->isInUse($id)) {
            $count = $this->unitrepo->getUsageCount($id);
            $message = __('lang.unit_cannot_be_deleted_in_use', ['count' => $count]);
            abort(409, $message);
        }

        //get record
        if (!$unit = \App\Models\Unit::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //delete the unit
        $unit->delete();

        //reponse payload
        $payload = [
            'unit_id' => $id,
        ];

        //process reponse
        return new DeleteResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     *
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.units'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        config([
            //visibility - add button
            'visibility.list_page_actions_add_button' => true,
        ]);

        //default settings
        $page += [
            'add_modal_title' => __('lang.add_product_unit'),
            'add_modal_create_url' => url('settings/units/create'),
            'add_modal_action_url' => url('settings/units/create'),
            'add_modal_action_ajax_class' => 'ajax-request',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        if ($section == 'create') {
            $page['section'] = 'create';
        }

        if ($section == 'edit') {
            $page['section'] = 'edit';
        }

        return $page;
    }

}