@props([
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => 'Select date',
    'required' => false,
    'class' => '',
])

@php
    $componentId = $id ?? $name;
    $displayId = $componentId . '_display';
@endphp

<div class="mb-3">
    @if($label)
    <label for="{{ $displayId }}" class="form-label">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>
    @endif

    <input type="hidden" id="{{ $componentId }}" name="{{ $name }}" value="{{ old($name, $value) }}">
    <input
        type="text"
        id="{{ $displayId }}"
        class="form-control sd_datepicker datepicker-all-{{ $componentId }} {{ $class }}"
        placeholder="{{ $placeholder }}"
        readonly
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
</div>

@push('scripts')
<script>
$(function() {
    var $display = $('#{{ $displayId }}');
    var $hidden = $('#{{ $componentId }}');

    // Format date for display (Tue, 10 Jan 2026)
    function formatDateDisplay_{{ str_replace(['-', '.'], '_', $componentId) }}(date) {
        if (!date) return '';
        var d = new Date(date);
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    // Format date for database (YYYY-MM-DD)
    function formatDateDB_{{ str_replace(['-', '.'], '_', $componentId) }}(date) {
        if (!date) return '';
        var d = new Date(date);
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    // Set initial value
    if ($hidden.val()) {
        $display.val(formatDateDisplay_{{ str_replace(['-', '.'], '_', $componentId) }}($hidden.val()));
    }

    // Initialize datepicker with no restrictions (all dates allowed)
    if ($.fn.bootstrapMaterialDatePicker) {
        $display.bootstrapMaterialDatePicker({
            weekStart: 0,
            time: false,
            format: 'ddd, D MMM YYYY',
            clearButton: true
        }).on('change', function(e, date) {
            $hidden.val(date ? formatDateDB_{{ str_replace(['-', '.'], '_', $componentId) }}(date) : '');
        });
    }

    // Global function to set value dynamically
    window.setDatepickerValue_{{ str_replace(['-', '.'], '_', $componentId) }} = function(dateValue) {
        if (!dateValue) {
            $hidden.val('');
            $display.val('');
            return;
        }
        $hidden.val(dateValue);
        $display.val(formatDateDisplay_{{ str_replace(['-', '.'], '_', $componentId) }}(dateValue));
    };
});
</script>
@endpush
