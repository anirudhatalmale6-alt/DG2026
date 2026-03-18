/**
 * SmartDash Global Date Configuration
 * Uses Bootstrap Material DatePicker (requires Moment.js)
 * South African date standards:
 * - Display format: ddd, D MMM YYYY (e.g., "Wed, 12 Feb 2020") - Human readable
 * - Database format: YYYY-MM-DD (e.g., "2020-02-12") - For form submission
 *
 * Usage:
 * Add these classes to date inputs:
 * - .sd_datepicker_max  : Only past dates allowed (maxDate: today)
 * - .sd_datepicker      : No date restrictions
 * - .sd_datepicker_all  : Any date allowed
 *
 * Also supports legacy flatpickr classes (auto-converts):
 * - .datepicker-past    -> sd_datepicker_max behaviour
 * - .datepicker-future  -> sd_datepicker behaviour
 * - .datepicker-any     -> sd_datepicker_all behaviour
 *
 * HTML pattern (preferred):
 *   <input type="hidden" id="my_date" name="my_date" value="2020-02-12">
 *   <input type="text" id="my_date_display" class="form-control sd_datepicker_max" placeholder="Select date" readonly>
 *
 * Simple pattern (legacy - auto-converted):
 *   <input type="text" id="my_date" name="my_date" class="form-control datepicker-past" value="2020-02-12">
 */

var SmartDashDates = {
    formats: {
        display: 'ddd, D MMM YYYY',
        database: 'YYYY-MM-DD'
    },

    toDisplay: function(date) {
        if (!date) return '';
        var d = new Date(date);
        if (isNaN(d.getTime())) return '';
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    },

    toDatabase: function(date) {
        if (!date) return '';
        var d = new Date(date);
        if (isNaN(d.getTime())) return '';
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    },

    _initElement: function($display, options) {
        var fieldName = $display.attr('id').replace('_display', '');
        var $hidden = $('#' + fieldName);

        if ($hidden.length && $hidden.val()) {
            $display.val(SmartDashDates.toDisplay($hidden.val()));
        }

        var config = {
            weekStart: 0,
            time: false,
            format: SmartDashDates.formats.display,
            clearButton: true
        };
        if (options && options.maxDate) config.maxDate = options.maxDate;

        $display.bootstrapMaterialDatePicker(config).on('change', function(e, date) {
            if ($hidden.length) {
                $hidden.val(date ? SmartDashDates.toDatabase(date) : '');
            }
        });
    },

    init: function() {
        if (!$.fn.bootstrapMaterialDatePicker) return;

        // Standard classes
        $('.sd_datepicker_max').each(function() {
            if (!$(this).data('plugin_bootstrapMaterialDatePicker'))
                SmartDashDates._initElement($(this), { maxDate: new Date() });
        });
        $('.sd_datepicker').each(function() {
            if (!$(this).data('plugin_bootstrapMaterialDatePicker'))
                SmartDashDates._initElement($(this), {});
        });
        $('.sd_datepicker_all').each(function() {
            if (!$(this).data('plugin_bootstrapMaterialDatePicker'))
                SmartDashDates._initElement($(this), {});
        });

        // Legacy flatpickr class support
        SmartDashDates._initLegacy('.datepicker-past', { maxDate: new Date() });
        SmartDashDates._initLegacy('.datepicker-future', {});
        SmartDashDates._initLegacy('.datepicker-any', {});
    },

    _initLegacy: function(selector, options) {
        $(selector).each(function() {
            var $input = $(this);
            if ($input.data('plugin_bootstrapMaterialDatePicker')) return;
            if ($input.attr('id') && $input.attr('id').indexOf('_display') > -1) return;

            var inputId = $input.attr('id') || $input.attr('name');
            if (!inputId) return;

            var $hidden = $('<input type="hidden">');
            $hidden.attr('name', $input.attr('name'));
            $hidden.attr('id', inputId);
            $hidden.val($input.val());

            $input.attr('id', inputId + '_display');
            $input.removeAttr('name');
            $input.prop('readonly', true);

            if ($hidden.val()) {
                $input.val(SmartDashDates.toDisplay($hidden.val()));
            }

            $input.before($hidden);

            var config = {
                weekStart: 0,
                time: false,
                format: SmartDashDates.formats.display,
                clearButton: true
            };
            if (options && options.maxDate) config.maxDate = options.maxDate;

            $input.bootstrapMaterialDatePicker(config).on('change', function(e, date) {
                $hidden.val(date ? SmartDashDates.toDatabase(date) : '');
            });
        });
    },

    today: function() { return SmartDashDates.toDisplay(new Date()); },

    isPast: function(dateStr) {
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return false;
        var today = new Date(); today.setHours(0,0,0,0);
        return d < today;
    },

    isFuture: function(dateStr) {
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return false;
        var today = new Date(); today.setHours(0,0,0,0);
        return d > today;
    }
};

$(document).ready(function() {
    SmartDashDates.init();
});
