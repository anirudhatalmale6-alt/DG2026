<div id="bill-form-container">
    <div class="card card-body invoice-wrapper box-shadow" id="invoice-wrapper">

        <!--HEADER-->
        @if($bill->bill_type == 'invoice')
        @include('pages.bill.components.elements.invoice.header-web')
        @endif
        @if($bill->bill_type == 'estimate')
        @include('pages.bill.components.elements.estimate.header-web')
        @endif

        <!--scheduled for publishing-->
        @if($bill->bill_status == 1 && $bill->bill_publishing_type == 'scheduled')
        @if($bill->bill_publishing_scheduled_status == 'pending')
        <div class="alert alert-info m-b-0 m-t-5">@lang('lang.scheduled_publishing_info') :
            {{ runtimeDate($bill->bill_publishing_scheduled_date) }}</div>
        @endif
        @if($bill->bill_publishing_scheduled_status == 'failed')
        <div class="alert alert-danger m-b-0 m-t-5">@lang('lang.scheduled_publishing_failed_info') :
            {{ runtimeDate($bill->bill_publishing_scheduled_date) }}</div>
        @endif
        @endif

        <hr class="billing-mode-only-item">
        <div class="row">
            <!--ADDRESSES-->
            <div class="col-12 m-b-10 billing-mode-only-item">
                <!--company address-->
                <div class="pull-left">
                    <address>
                        <h3 class="x-company-name text-info">{{ config('system.settings_company_name') }}</h3>
                        <p class="text-muted m-l-5">
                            @if(config('system.settings_company_address_line_1'))
                            {{ config('system.settings_company_address_line_1') }}
                            @endif
                            @if(config('system.settings_company_city'))
                            <br /> {{ config('system.settings_company_city') }}@if(config('system.settings_company_zipcode')), {{ config('system.settings_company_zipcode') }}@endif
                            @endif
                            @if(config('system.settings_company_state'))
                            <br />{{ config('system.settings_company_state') }}
                            @endif
                            @if(config('system.settings_company_country'))
                            <br /> {{ config('system.settings_company_country') }}
                            @endif

                            <!--custom company fields-->
                            @if(config('system.settings_company_customfield_1') != '')
                            <br /> {{ config('system.settings_company_customfield_1') }}
                            @endif
                            @if(config('system.settings_company_customfield_2') != '')
                            <br /> {{ config('system.settings_company_customfield_2') }}
                            @endif
                            @if(config('system.settings_company_customfield_3') != '')
                            <br /> {{ config('system.settings_company_customfield_3') }}
                            @endif
                            @if(config('system.settings_company_customfield_4') != '')
                            <br /> {{ config('system.settings_company_customfield_4') }}
                            @endif

                            <!--module extension point-->
                            @stack('bill_position_5')
                        </p>
                    </address>
                </div>
                <!--client address-->
                <div class="pull-right text-right">
                    <address>
                        <h3 class="">{{ cleanLang(__('lang.bill_to')) }}</h3>
                        <a href="{{ url('clients/'.$bill->client_id) }}">
                            <h4 class="font-bold">{{ $bill->client_company_name }}</h4>
                        </a>
                        <p class="text-muted m-l-30">
                            @if($bill->client_billing_street)
                            {{ $bill->client_billing_street }}
                            @endif
                            @if($bill->client_billing_city)
                            <br /> {{ $bill->client_billing_city }}@if($bill->client_billing_zip), {{ $bill->client_billing_zip }}@endif
                            @endif
                            @if($bill->client_billing_state)
                            <br /> {{ $bill->client_billing_state }}
                            @endif
                            @if($bill->client_billing_country)
                            <br /> {{ $bill->client_billing_country }}
                            @endif

                            <!--custom fields-->
                            @foreach($customfields as $field)
                            @if($field->customfields_show_invoice == 'yes' && $field->customfields_status == 'enabled')
                            @php $key = $field->customfields_name; @endphp
                            @php $customfield = $bill[$key] ?? ''; @endphp
                            @if($customfield != '')
                            <br />{{ $field->customfields_title }}:
                            {{ runtimeCustomFieldsFormat($customfield, $field->customfields_datatype) }}
                            @endif
                            @endif
                            @endforeach

                            <!--module extension point-->
                            @stack('bill_position_6')
                        </p>
                    </address>
                </div>
            </div>

            <!--project title-->
            @if(config('system.settings_invoices_show_project_on_invoice') == 'yes' && $bill->project_title != '')
            <div class="col-12 m-b-10 billing-mode-only-item invoice-project-title">
                <span class="">@lang('lang.project'):</span> {{ $bill->project_title }}
                <!--module extension point-->
                @stack('bill_position_7')
            </div>
            @endif

            <!--DATES & AMOUNT DUE-->
            @if($bill->bill_type == 'invoice')
            <div class="col-12 m-b-10 billing-mode-only-item" id="invoice-dates-wrapper">
                @include('pages.bill.components.elements.invoice.dates')
                @include('pages.bill.components.elements.invoice.payments')
            </div>
            <!--module extension point-->
            @stack('bill_position_14')
            @endif
            @if($bill->bill_type == 'estimate')
            <div class="col-12 m-b-10 billing-mode-only-item" id="invoice-dates-wrapper">
                @include('pages.bill.components.elements.estimate.dates')
            </div>
            <!--module extension point-->
            @stack('bill_position_15')
            @endif

            <!--module extension point-->
            @stack('bill_position_16')

            <!--INVOICE TABLE-->
            @include('pages.bill.components.elements.main-table')

            <!--module extension point-->
            @stack('bill_position_17')

            <!--[EDITING] INVOICE LINE ITEMS BUTTONS -->
            @if(config('visibility.bill_mode') == 'editing')
            <div class="col-12">
                @include('pages.bill.components.misc.add-line-buttons')
            </div>
            @endif

            <!--module extension point-->
            @stack('bill_position_20')

            <!-- TOTAL & SUMMARY -->
            @if($bill->bill_tax_type == 'inline')
            @include('pages.bill.components.elements.totals-inline')
            @else
            @include('pages.bill.components.elements.totals-summary')
            @endif

            <!-- TAXES & DISCOUNTS -->
            @if(config('visibility.bill_mode') == 'editing')
            @include('pages.bill.components.elements.taxes-discounts')
            @endif

            <!--module extension point-->
            @stack('bill_position_23')

            <!--[VIEWING] INVOICE TERMS & MAKE PAYMENT BUTTON-->
            @if(config('visibility.bill_mode') == 'viewing')
            <div class="col-12 billing-mode-only-item">
                <!--invoice terms-->
                <div class="text-left">
                    @if($bill->bill_type == 'invoice')
                    <h4>{{ cleanLang(__('lang.invoice_terms')) }}</h4>
                    @else
                    <h4>{{ cleanLang(__('lang.estimate_terms')) }}</h4>
                    @endif
                    <div id="invoice-terms">{!! clean($bill->bill_terms) !!}</div>
                </div>
                <!--client - make a payment button-->
                @if((auth()->check() && auth()->user()->is_client) || config('visibility.public_bill_viewing'))
                <hr>
                <div class="p-t-25 invoice-pay" id="invoice-buttons-container">
                    <div class="text-right">
                        <!--[invoice] download pdf-->
                        <span>
                            @if($bill->bill_type == 'invoice')
                            <a class="btn btn-secondary btn-outline"
                                href="{{ url('/invoices/'.$bill->bill_invoiceid.'/pdf') }}" download>
                                <span><i class="mdi mdi-download"></i> {{ cleanLang(__('lang.download')) }}</span> </a>
                            @else
                            <!--[estimate] download pdf-->
                            <a class="btn btn-secondary btn-outline"
                                href="{{ url('/estimates/view/'.$bill->bill_uniqueid.'/pdf') }}">
                                <span><i class="mdi mdi-download"></i> {{ cleanLang(__('lang.download')) }}</span> </a>
                            @endif
                        </span>

                        <!--[invoice] - make payment-->
                        @if($bill->bill_type == 'invoice' && $bill->invoice_balance > 0)
                        <button class="btn btn-danger" id="invoice-make-payment-button">
                            {{ cleanLang(__('lang.make_a_payment')) }} </button>
                        @endif

                        <!--accept or decline-->
                        @if(in_array($bill->bill_status, ['new', 'revised']))
                        <!--decline-->
                        <button class="buttons-accept-decline btn btn-danger confirm-action-danger"
                            data-confirm-title="{{ cleanLang(__('lang.decline_estimate')) }}"
                            data-confirm-text="{{ cleanLang(__('lang.decline_estimate_confirm')) }}"
                            data-ajax-type="GET" data-url="{{ url('/') }}/estimates/{{ $bill->bill_uniqueid }}/decline">
                            {{ cleanLang(__('lang.decline_estimate')) }} </button>
                        <!--accept-->
                        <button class="buttons-accept-decline btn btn-success confirm-action-success"
                            data-confirm-title="{{ cleanLang(__('lang.accept_estimate')) }}"
                            data-confirm-text="{{ cleanLang(__('lang.accept_estimate_confirm')) }}" data-ajax-type="GET"
                            data-url="{{ url('/') }}/estimates/{{ $bill->bill_uniqueid }}/accept">
                            {{ cleanLang(__('lang.accept_estimate')) }} </button>
                        @endif


                    </div>
                    @endif

                </div>

                <!--module extension point-->
                @stack('bill_position_24')

                <!--payment buttons-->
                @include('pages.pay.buttons')
                @endif

                <!--module extension point-->
                @stack('bill_position_33')

                <!--[EDITING] INVOICE TERMS & MAKE PAYMENT BUTTON-->
                @if(config('visibility.bill_mode') == 'editing')
                <div class="col-12">
                    <!--invoice terms-->
                    <div class="text-left billing-mode-only-item">
                        @if($bill->bill_type == 'invoice')
                        <h4>{{ cleanLang(__('lang.invoice_terms')) }}</h4>
                        @else
                        <h4>{{ cleanLang(__('lang.estimate_terms')) }}</h4>
                        @endif
                        <textarea class="form-control form-control-sm tinymce-textarea" rows="3" name="bill_terms"
                            id="bill_terms">{!! clean($bill->bill_terms) !!}</textarea>
                    </div>
                    <!--client - make a payment button-->
                    <div class="text-right p-t-25">
                        @if($bill->bill_type == 'invoice')
                        <!--cancel-->
                        <a class="btn btn-secondary btn-sm"
                            href="{{ url('/invoices/'.$bill->bill_invoiceid) }}">@lang('lang.exit_editing_mode')</a>
                        <!--save changes-->
                        <button class="btn btn-danger btn-sm"
                            data-url="{{ url('/invoices/'.$bill->bill_invoiceid.'/edit-invoice') }}" data-type="form"
                            data-form-id="bill-form-container" data-ajax-type="post" id="billing-save-button">
                            @lang('lang.save_changes')
                        </button>
                        @else
                        <a class="btn btn-secondary btn-sm billing-mode-only-item"
                            href="{{ url('/estimates/'.$bill->bill_estimateid) }}">@lang('lang.exit_editing_mode')</a>
                        <!--save changes-->
                        <a class="btn btn-danger btn-sm" href="javascript:void(0);"
                            data-url="{{ url('/estimates/'.$bill->bill_estimateid.'/edit-estimate?estimate_mode='.request('estimate_mode')) }}"
                            data-type="form" data-form-id="bill-form-container" data-ajax-type="post"
                            data-loading-target="documents-side-panel-billing-content" data-loading-class="loading"
                            id="billing-save-button">
                            @lang('lang.save_changes')
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!--module extension point-->
                @stack('bill_position_34')
            </div>
            <!--module extension point-->
            @stack('bill_position_35')
        </div>

        <!--module extension point-->
        @stack('bill_position_36')

        <!--ADMIN ONLY NOTES-->
        @if((auth()->check() && auth()->user()->is_team) && !config('visibility.public_bill_viewing'))
        @if(config('visibility.bill_mode') == 'viewing')
        <div class="card card-body invoice-wrapper box-shadow billing-mode-only-item billing-mode-only-item"
            id="invoice-wrapper">
            <h4 class="">{{ cleanLang(__('lang.notes')) }} <span class="align-middle text-themecontrast font-16"
                    data-toggle="tooltip" title="{{ cleanLang(__('lang.not_visisble_to_client')) }}"
                    data-placement="top"><i class="ti-info-alt"></i></span></h4>
            <div>{!! clean($bill->bill_notes) !!}</div>
        </div>
        @endif
        @if(config('visibility.bill_mode') == 'editing')
        <div class="card card-body invoice-wrapper box-shadow billing-mode-only-item" id="invoice-wrapper">
            <h4 class="">{{ cleanLang(__('lang.notes')) }} <span class="align-middle text-themecontrast font-16"
                    data-toggle="tooltip" title="{{ cleanLang(__('lang.not_visisble_to_client')) }}"
                    data-placement="top"><i class="ti-info-alt"></i></span></h4>
            <div><textarea class="form-control form-control-sm tinymce-textarea" rows="3" name="bill_notes"
                    id="bill_notes">{!! clean($bill->bill_notes) !!}</textarea></div>
        </div>
        @endif
        @endif

        <!--INVOICE LOGIC-->
        @if(config('visibility.bill_mode') == 'editing')
        @include('pages.bill.components.elements.logic')
        @endif

    </div>

    <!--ELEMENTS (invoice line item)-->
    @if(config('visibility.bill_mode') == 'editing')
    <table class="hidden" id="billing-line-template-plain">
        @include('pages.bill.components.elements.line-plain')
    </table>
    <table class="hidden" id="billing-estimation-notes-template">
        @include('pages.bill.components.elements.line-estimation-notes')
    </table>
    <table class="hidden" id="billing-line-template-time">
        @include('pages.bill.components.elements.line-time')
    </table>
    <table class="hidden" id="billing-line-template-dimensions">
        @include('pages.bill.components.elements.line-dimensions')
    </table>

    <!--MODALS-->
    @include('pages.bill.components.modals.items')
    @include('pages.bill.components.modals.category-items')
    @include('pages.bill.components.modals.expenses')
    @include('pages.bill.components.timebilling.modal')
    @include('pages.bill.components.modals.bill-tasks')

    <!--[DYNAMIC INLINE SCRIPT] - Get lavarel objects and convert to javascript onject-->
    <script>
        $(document).ready(function () {
            NXINVOICE.DATA.INVOICE = $.parseJSON('{!! $bill->json !!}');
            NXINVOICE.DOM.domState();
        });
    </script>
    @endif