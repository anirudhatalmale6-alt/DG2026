/**
 * CIMS Master SweetAlert Functions
 * Centralized SweetAlert2 popups for consistent system-wide messaging
 * 
 * Functions:
 *   cimsSuccess(action, name)         - Success message (10s timer, OK button)
 *   cimsError(message)                - Error message (OK button, no timer)
 *   cimsConfirmDelete(formId, name)   - Delete confirmation (Yes Delete / No buttons)
 */

var CIMSAlert = CIMSAlert || {};

/**
 * Success message popup
 * @param {string} action - What was done: 'created', 'updated', 'deleted', 'saved'
 * @param {string} name   - The item name to display in bold
 */
CIMSAlert.success = function(action, name) {
    action = action || 'saved';
    var title = action.charAt(0).toUpperCase() + action.slice(1) + '!';
    var html = name
        ? 'The item<br><span style="display:block;font-size:18px;font-weight:700;color:#1a1a1a;margin:8px 0;">' + name + '</span>has been ' + action + ' successfully.'
        : 'The item has been ' + action + ' successfully.';

    Swal.fire({
        title: title,
        html: html,
        icon: 'success',
        confirmButtonText: '<i class="fa fa-check"></i> OK',
        customClass: {
            confirmButton: 'btn button_master_ok'
        },
        buttonsStyling: false,
        timer: 10000,
        timerProgressBar: true
    });
};

/**
 * Error message popup
 * @param {string} message - The error message to display
 */
CIMSAlert.error = function(message) {
    Swal.fire({
        title: 'Error!',
        html: message,
        icon: 'error',
        confirmButtonText: '<i class="fa fa-check"></i> OK',
        customClass: {
            confirmButton: 'btn button_master_ok'
        },
        buttonsStyling: false
    });
};

/**
 * Delete confirmation popup
 * @param {string} formId - The ID of the delete form to submit
 * @param {string} name   - The item name to display in bold
 */
CIMSAlert.confirmDelete = function(formId, name) {
    var html = 'Are you sure you want to delete';
    if (name) {
        html += '<br><span style="display:block;font-size:18px;font-weight:700;color:#1a1a1a;margin:8px 0;">' + name + '</span>';
    }
    html += 'This action cannot be undone.<br><br><span style="font-size:13px;color:#666;">Type <strong>DELETE</strong> below to confirm.</span>';

    Swal.fire({
        title: 'Confirm Delete',
        html: html,
        icon: 'warning',
        input: 'text',
        inputPlaceholder: 'Type DELETE to confirm',
        inputAttributes: {
            autocomplete: 'off',
            style: 'text-align:center;font-size:16px;font-weight:600;letter-spacing:1px;'
        },
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-trash"></i> Delete',
        cancelButtonText: '<i class="fa fa-times"></i> Close',
        customClass: {
            confirmButton: 'btn button_master_delete',
            cancelButton: 'btn button_master_close_blue'
        },
        buttonsStyling: false,
        preConfirm: function(value) {
            if (value !== 'DELETE') {
                Swal.showValidationMessage('Please type the word DELETE to confirm');
                return false;
            }
            return true;
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
};
