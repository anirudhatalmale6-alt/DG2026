@foreach($lineitems as $lineitem)
<tr>
    <!--description-->
    <td class="x-description">
        <div>
            {{ $lineitem->lineitem_description }}
        </div>
        @if(config('system.settings2_invoices_show_long_description') == 'yes' &&
        !empty($lineitem->lineitem_long_description))
        <div class="x-long-description opacity-8"><small>{{ $lineitem->lineitem_long_description }}</small></div>
        @endif
        @if($lineitem->item->has_enabled_custom_fields)
        <div class="x-product-custom-fields">
            @foreach($lineitem->item->enabled_custom_fields as $custom_field)
            <div class="x-each-product-custom-fields">
                <small><span class="font-weight-500">{{ $custom_field['name'] }}:</span>
                    {{ $custom_field['value'] }}</small>
            </div>
            @endforeach
        </div>
        @endif
    </td>

    <!--quantity - [plain]-->
    @if($lineitem->lineitem_type == 'plain')
    <td class="x-quantity">{{ $lineitem->lineitem_quantity }}</td>
    @endif

    <!--quantity -[time]-->
    @if($lineitem->lineitem_type == 'time')
    <td class="x-quantity">
        @if($lineitem->lineitem_time_hours > 0)
        {{ $lineitem->lineitem_time_hours }}{{ strtolower(__('lang.hrs')) }}&nbsp;
        @endif
        @if($lineitem->lineitem_time_minutes > 0)
        {{ $lineitem->lineitem_time_minutes }}{{ strtolower(__('lang.mins')) }}
        @endif
    </td>
    @endif

    <!--quantity - [dimensions]-->
    @if($lineitem->lineitem_type == 'dimensions')
    <td class="x-quantity">{{ $lineitem->lineitem_quantity }}</td>
    @endif

    <!--unit price-->
    <td class="x-unit">{{ $lineitem->lineitem_unit }}</td>
    <!--rate-->
    <td class="x-rate" style="text-align: right;">{{ runtimeNumberFormat($lineitem->lineitem_rate) }}</td>
    <!--discount-->
    <td style="display:none;"
        class="x-discount bill_col_discount {{ runtimeVisibility('invoice-column-inline-discount', $bill->bill_tax_type) }}">
        @if($lineitem->lineitem_discount_type == 'fixed')
        {{ runtimeMoneyFormat($lineitem->lineitem_discount_value) }}
        @elseif($lineitem->lineitem_discount_type == 'percentage')
        {{ runtimeNumberFormat($lineitem->lineitem_discount_value) }}%
        @else
        {{ runtimeMoneyFormat(0) }}
        @endif
    </td>
    <!--tax-->
    <td style="display:none;" class="x-tax {{ runtimeVisibility('invoice-column-inline-tax', $bill->bill_tax_type) }}">
        @foreach($lineitem->taxes as $tax)
        @if($tax->tax_rate == '0.00')
        0.00
        @else
        @php $lineitem_tax = (($lineitem->lineitem_total - $lineitem->lineitem_discount_amount) * $tax->tax_rate)/100;
        @endphp
        <div>{{ runtimeNumberFormat($lineitem_tax) }}</div>
        <div class="tax-subtext">(<small>{{ $tax->tax_name }}</small> - {{ runtimeDecimalFloat($tax->tax_rate) }}%)
        </div>
        @endif
        @endforeach
    </td>
    <!--total-->
    <td class="x-total" style="text-align: right;">{{ runtimeNumberFormat($lineitem->lineitem_total) }}</td>
</tr>
@endforeach