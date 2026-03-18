/**
 * SmartDash Global Date Configuration
 * South African date standards:
 * - Display format: D, j M Y (e.g., "Mon, 21 Jun 2022") - Human readable (altFormat)
 * - Database format: Y-m-d (e.g., "2022-06-21") - For form submission (dateFormat)
 *
 * Usage:
 * Add these classes to date inputs:
 * - .datepicker-past    : Only past dates allowed (max: today)
 * - .datepicker-future  : Future dates allowed (no max)
 * - .datepicker-any     : Any date allowed
 */

var SmartDashDates = {
    // SA Date Formats
    formats: {
        display: 'D, j M Y',      // Mon, 21 Jun 2022 (shown to user via altInput)
        database: 'Y-m-d',        // 2022-06-21 (submitted by form)
        long: 'D, j M Y'          // Mon, 21 Jun 2022 (short day & month)
    },

    // Initialize all datepickers on the page
    init: function() {
        var displayFormat = SmartDashDates.formats.display;
        var dbFormat = SmartDashDates.formats.database;

        // Past dates only (Date of Issue, Date of Death, Bank Date Opened, etc.)
        if ($('.datepicker-past').length) {
            $('.datepicker-past').flatpickr({
                dateFormat: dbFormat,
                altInput: true,
                altFormat: displayFormat,
                allowInput: true,
                maxDate: 'today'
            });
        }

        // Future dates allowed (Passport Expiry, Contract End Dates, etc.)
        if ($('.datepicker-future').length) {
            $('.datepicker-future').flatpickr({
                dateFormat: dbFormat,
                altInput: true,
                altFormat: displayFormat,
                allowInput: true
            });
        }

        // Any date (general purpose)
        if ($('.datepicker-any').length) {
            $('.datepicker-any').flatpickr({
                dateFormat: dbFormat,
                altInput: true,
                altFormat: displayFormat,
                allowInput: true
            });
        }
    },

    // Format date to display format (Mon, 21 Jun 2022)
    toDisplay: function(date) {
        if (!date) return '';
        var d = new Date(date);
        if (isNaN(d.getTime())) return '';
        var options = {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        };
        return d.toLocaleDateString('en-GB', options);
    },

    // Format date to SA long format with day name (Mon, 21 Jun 2022)
    toLong: function(date) {
        if (!date) return '';
        var d = new Date(date);
        if (isNaN(d.getTime())) return '';
        var options = {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        };
        return d.toLocaleDateString('en-GB', options);
    },

    // Format date to database format (yyyy-mm-dd)
    toDatabase: function(dateStr) {
        if (!dateStr) return null;
        // Try to parse the display format (21 June 2022)
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return null;
        var year = d.getFullYear();
        var month = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return year + '-' + month + '-' + day;
    },

    // Parse display format to Date object
    parse: function(dateStr) {
        if (!dateStr) return null;
        return new Date(dateStr);
    },

    // Get today's date in display format
    today: function() {
        return SmartDashDates.toDisplay(new Date());
    },

    // Check if date is in the past
    isPast: function(dateStr) {
        var date = SmartDashDates.parse(dateStr);
        if (!date) return false;
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        return date < today;
    },

    // Check if date is in the future
    isFuture: function(dateStr) {
        var date = SmartDashDates.parse(dateStr);
        if (!date) return false;
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        return date > today;
    }
};

// Auto-initialize when document is ready
$(document).ready(function() {
    SmartDashDates.init();
});
