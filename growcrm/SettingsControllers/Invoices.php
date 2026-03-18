<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for estimates settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Invoices\IndexResponse;
use App\Http\Responses\Settings\Invoices\UpdateResponse;
use App\Http\Responses\Settings\Invoices\StatusesResponse;
use App\Http\Responses\Settings\Invoices\EditStatusResponse;
use App\Http\Responses\Settings\Invoices\UpdateStatusResponse;
use App\Http\Responses\Settings\Invoices\CreateStatusResponse;
use App\Http\Responses\Settings\Invoices\StoreStatusResponse;
use App\Http\Responses\Settings\Invoices\moveResponse;
use App\Http\Responses\Settings\Invoices\MoveUpdateResponse;
use App\Http\Responses\Settings\Invoices\DestroyStatusResponse;
use App\Repositories\SettingsRepository;
use App\Repositories\InvoiceStatusRepository;
use DB;
use Illuminate\Http\Request;
use Validator;

class Invoices extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;
    protected $statusrepo;

    public function __construct(SettingsRepository $settingsrepo, InvoiceStatusRepository $statusrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;
        $this->statusrepo = $statusrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);
        $settings2 = \App\Models\Settings2::find(1);

        $query = DB::select("SHOW TABLE STATUS LIKE 'invoices'");
        $next_id = $query[0]->Auto_increment;

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'settings2' => $settings2,
            'next_id' => $next_id,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //validate (add specific error messages, per field name, in validation.lang)
        $validator = Validator::make(request()->all(), [
            'settings_invoices_recurring_grace_period' => 'required',
            'settings2_invoices_due_days' => 'required|numeric|min:1',
            'settings2_invoices_second_reminder_days' => 'required|numeric|min:0',
            'settings2_invoices_third_reminder_days' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $second = request('settings2_invoices_second_reminder_days');
                    if ($value > 0 && $second > 0 && $value <= $second) {
                        return $fail(__('lang.third_reminder_must_be_greater'));
                    }
                },
            ],
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //are we updating next ID
        if (request()->filled('next_id')) {
            if (request('next_id') != request('next_id_current')) {
                DB::select("ALTER TABLE invoices AUTO_INCREMENT = " . request('next_id'));
            }
        }

        //update
        if (!$this->settingsrepo->updateInvoiceSettings()) {
            abort(409);
        }

        //additional updates
        \App\Models\Settings2::where('settings2_id', 1)
            ->update([
                'settings2_bills_pdf_css' => request('settings2_bills_pdf_css'),
                'settings2_dompdf_fonts' => request('settings2_dompdf_fonts'),
                'settings2_invoices_due_days' => request('settings2_invoices_due_days'),
                'settings2_invoices_default_mode' => request('settings2_invoices_default_mode'),
                'settings2_invoices_show_long_description' => request('settings2_invoices_show_long_description'),
                'settings2_invoices_second_reminder_days' => request('settings2_invoices_second_reminder_days'),
                'settings2_invoices_third_reminder_days' => request('settings2_invoices_third_reminder_days'),
            ]);

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display invoice statuses
     *
     * @return \Illuminate\Http\Response
     */
    public function statuses() {

        //crumbs, page data & stats
        $page = $this->pageSettings('statuses');

        $statuses = $this->statusrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'statuses' => $statuses,
        ];

        //show the view
        return new StatusesResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function editStatus($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //invoice statuses
        $statuses = $this->statusrepo->search($id);

        //not found
        if (!$status = $statuses->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'status' => $status,
        ];

        //response
        return new EditStatusResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus($id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'invoicestatus_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\InvoiceStatus::where('invoicestatus_title', $value)
                        ->where('invoicestatus_id', '!=', request()->route('id'))
                        ->exists()) {
                        return $fail(__('lang.invoice_status_already_exists'));
                    }
                }],
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
        if (!$this->statusrepo->update($id)) {
            abort(409);
        }

        //get the status object (friendly for rendering in blade template)
        $statuses = $this->statusrepo->search($id);

        //reponse payload
        $payload = [
            'statuses' => $statuses,
        ];

        //process reponse
        return new UpdateStatusResponse($payload);

    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function createStatus() {

        //page settings
        $page = $this->pageSettings();
        $page['default_color'] = 'checked';

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new CreateStatusResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function storeStatus() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'invoicestatus_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\InvoiceStatus::where('invoicestatus_title', $value)
                        ->exists()) {
                        return $fail(__('lang.invoice_status_already_exists'));
                    }
                }],
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

        //get the last row (order by position - desc)
        if ($last = \App\Models\InvoiceStatus::orderBy('invoicestatus_position', 'desc')->first()) {
            $position = $last->invoicestatus_position + 1;
        } else {
            //default position
            $position = 2;
        }

        //create the status
        if (!$invoicestatus_id = $this->statusrepo->create($position)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the status object (friendly for rendering in blade template)
        $statuses = $this->statusrepo->search($invoicestatus_id);

        //reponse payload
        $payload = [
            'statuses' => $statuses,
        ];

        //process reponse
        return new StoreStatusResponse($payload);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function move($id) {

        //page settings
        $page = $this->pageSettings();

        //invoice statuses
        $statuses = \App\Models\InvoiceStatus::get();

        //reponse payload
        $payload = [
            'page' => $page,
            'statuses' => $statuses,
        ];

        //response
        return new moveResponse($payload);
    }

    /**
     * Move invoices from one status to another
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateMove($id) {

        //page settings
        $page = $this->pageSettings();

        //move the invoices and set trigger to manual
        \App\Models\Invoice::where('bill_status', $id)->update([
            'bill_status' => request('invoices_status'),
            'bill_status_trigger' => 'manual'
        ]);

        //invoice statuses
        $statuses = $this->statusrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'statuses' => $statuses,
        ];

        //response
        return new MoveUpdateResponse($payload);
    }

    /**
     * Update invoice statuses position
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateStagePositions() {

        //reposition each invoice status
        $i = 1;
        foreach (request('sort-stages') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\InvoiceStatus::where('invoicestatus_id', $id)->update(['invoicestatus_position' => $i]);
            }
            $i++;
        }

        //retun simple success json
        return response()->json('success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroyStatus($id) {

        //get record
        if (!\App\Models\InvoiceStatus::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get it in useful format
        $statuses = $this->statusrepo->search($id);
        $status = $statuses->first();

        //validation: system default
        if ($status->invoicestatus_system_default == 'yes') {
            abort(409, __('lang.you_cannot_delete_system_default_item'));
        }

        //validation: has invoices
        if ($status->count_invoices != 0) {
            abort(409, __('lang.status_has_invoices_cannot_delete'));
        }

        //delete the status
        $status->delete();

        //reponse payload
        $payload = [
            'status_id' => $id,
        ];

        //process reponse
        return new DestroyStatusResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.sales'),
                __('lang.invoices'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        config([
            //visibility - add button
            'visibility.list_page_actions_add_button' => true,
        ]);

        //create new resource
        if ($section == 'statuses') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.invoices'),
                __('lang.invoice_statuses'),
            ];
            $page += [
                'add_modal_title' => __('lang.add_new_invoice_status'),
                'add_modal_create_url' => url('settings/invoices/statuses/create'),
                'add_modal_action_url' => url('settings/invoices/statuses/create'),
                'add_modal_action_ajax_class' => 'ajax-request',
                'add_modal_action_ajax_loading_target' => 'commonModalBody',
                'add_modal_action_method' => 'POST',
            ];
        }

        return $page;
    }

}
