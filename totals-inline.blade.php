<div class="col-12" id="bill-totals-wrapper">

    <!--FILE ATTACHEMENTS-->
    @if(config('visibility.bill_files_section'))
    <div class="pull-left m-t-30 text-left bill-file-attachments">
        <h6>@lang('lang.attachments')</h6>
        <div class="bill-file-attachments-wrapper" id="bill-file-attachments-wrapper">

            @foreach($files as $file)
            @include('pages.bill.components.elements.attachment')
            @endforeach
            <!--add attachments-->
            @if(config('visibility.bill_mode') == 'editing' && (auth()->check() && auth()->user()->role->role_estimates
            >= 3))
            <div class="x-add-file-button">
                <button type="button" id="bill-file-attachments-upload-button"
                    class="btn waves-effect waves-light btn-rounded btn-xs btn-danger">@lang('lang.add_file_attachments')</button>
            </div>
            @endif
        </div>
        <!--dropzone-->
        <!--fileupload-->
        @if(auth()->check() && auth()->user()->role->role_estimates >= 3)
        <div class="form-group row hidden" id="bill-file-attachments-dropzone-wrapper">
            <div class="col-12">
                <div class="dropzone dz-clickable fileupload_bills" id="fileupload_bills"
                    data-upload-url="{{ runtimeURLBillAttachFiles($bill) }}">
                    <div class="dz-default dz-message">
                        <i class="icon-Upload-toCloud"></i>
                        <span>@lang('lang.drag_drop_file')</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                        id="bill-file-attachments-close-button">
                        <i class="ti-close"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif
        <!--#fileupload-->
    </div>
    @endif

    <!--module extension point-->
    @stack('bill_position_37')

    <!--total amounts - INLINE MODE-->
    <div class="pull-right m-t-30 text-right">

        <!--module extension point-->
        @stack('bill_position_38')

        <table class="invoice-total-table">

            <!--subtotal before discounts-->
            <tbody id="billing-table-section-subtotal">
                <tr>
                    <td>{{ cleanLang(__('lang.subtotal')) }}</td>
                    <td id="billing-subtotal-figure">
                        <span>{!! runtimeMoneyFormatPDF($bill->bill_subtotal_before_discount ?? $bill->bill_subtotal)
                            !!}</span>
                    </td>
                </tr>
            </tbody>

            <!--module extension point-->
            @stack('bill_position_39')

            <!--line discounts total-->
            <tbody id="billing-table-section-line-discounts">
                <tr id="billing-line-discounts-container">
                    <td>{{ cleanLang(__('lang.discounts')) }}</td>
                    <td id="billing-line-discounts-total">
                        <span>-{!! runtimeMoneyFormatPDF($bill->bill_discount_amount ?? 0) !!}</span>
                    </td>
                </tr>
            </tbody>

            <!--module extension point-->
            @stack('bill_position_40')

            <!--subtotal after discounts-->
            <tbody id="billing-table-section-subtotal-after-discount">
                <tr>
                    <td>{{ cleanLang(__('lang.subtotal')) }} <span class="x-small">(@lang('lang.after_discount'))</span>
                    </td>
                    <td id="billing-subtotal-after-discount-figure">
                        <span>{!! runtimeMoneyFormatPDF($bill->bill_subtotal) !!}</span>
                    </td>
                </tr>
            </tbody>

            <!--module extension point-->
            @stack('bill_position_41')

            <!--taxes (inline)-->
            <tbody id="billing-table-section-tax" class="{{ $bill->visibility_tax_row }}">
                <tr class="billing-sums-tax-container">
                    <td>@lang('lang.tax')</td>
                    <td id="billing-tax-total-figure">
                        <span>{!! runtimeMoneyFormatPDF($bill->bill_tax_total_amount) !!}</span>
                    </td>
                </tr>
            </tbody>

            <!--module extension point-->
            @stack('bill_position_42')

            <!--adjustment & invoice total-->
            <tbody id="invoice-table-section-total">

                <!--module extension point-->
                @stack('bill_position_43')

                <!--adjustment-->
                <tr class="billing-adjustment-container {{ $bill->visibility_adjustment_row }}"
                    id="billing-adjustment-container">
                    <td class="p-t-15 billing-adjustment-text" id="billing-adjustment-container-description">
                        {{ $bill->bill_adjustment_description }}</td>
                    <td class="p-t-15 billing-adjustment-text">
                        <span id="billing-adjustment-container-amount">{!!
                            runtimeMoneyFormatPDF($bill->bill_adjustment_amount) !!}</span>
                    </td>
                </tr>

                <!--module extension point-->
                @stack('bill_position_44')

                <!--total-->
                <tr class="text-themecontrast" id="billing-sums-total-container">
                    <td class="billing-sums-total">{{ cleanLang(__('lang.total')) }}</td>
                    <td id="billing-sums-total">
                        <span>{!! runtimeMoneyFormatPDF($bill->bill_final_amount) !!}</span>
                    </td>
                </tr>
                <!--module extension point-->
                @stack('bill_position_45')
            </tbody>
            <!--module extension point-->
            @stack('bill_position_46')

        </table>

        <!--module extension point-->
        @stack('bill_position_47')

    </div>

    <!--module extension point-->
    @stack('bill_position_48')

</div>

<!--module extension point-->
@stack('bill_position_49')