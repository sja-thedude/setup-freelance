/**
 * Convert line breaks to br in jQuery
 *
 * @link https://itsolutionstuff.com/post/how-to-convert-line-breaks-to-br-in-jquery-example.html
 * @param str
 * @param is_xhtml
 * @returns {string}
 */
function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br/>' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

/**
 * Show bootstrap alert message
 *
 * @param {String} container Container selector to do with jQuery
 * @param {String} message
 * @param {String=} type Alert type.<br>
 *     Allow values: success, danger. Default = 'success'
 * @param {Boolean=} scrollToTop Allow to scroll to top or not.<br>
 *     Allow values: true, false. Default = false.
 */
function showBootstrapAlert(container, message, type, scrollToTop) {
    if (typeof type === 'undefined') {
        type = 'success';
    }

    if (typeof scrollToTop === 'undefined') {
        scrollToTop = false;
    }

    let responseBox = $(container);

    // Parse message
    message = (message + '').trim();

    // Replace message if not empty
    if (message !== '') {
        responseBox.html(message);
    }

    responseBox.removeClass('alert alert-success alert-danger')

    // Display type
    if (type === 'success') {
        responseBox.addClass('alert alert-success')
    } else if (type === 'danger') {
        responseBox.addClass('alert alert-danger')
    } else {
        //
    }

    // Show message
    responseBox.show();

    // Scroll to top
    if (scrollToTop) {
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    }

}

/**
 * Hide bootstrap alert message
 *
 * @param {String} container Container selector to do with jQuery
 */
function hideBootstrapAlert(container) {
    let responseBox = $(container);
    responseBox.removeClass('alert alert-success alert-danger');
}

/**
 * Clear all bad inputs in the form
 *
 * @param container
 */
function clearError(container) {
    container.find('.bad').removeClass('bad');
}

/**
 * Process errors from ajax error
 *
 * @param {jQuery} container
 * @param {jqXHR} jqXHR
 * @param {String=} textStatus
 * @param {String=} errorThrown
 */
function processError(container, jqXHR, textStatus, errorThrown) {
    /**
     * Error 422 Ajax Post using Laravel
     * @link https://stackoverflow.com/a/49021074
     */
    if (jqXHR.status === 422) {
        let response = $.parseJSON(jqXHR.responseText);

        // Change container by index
        let data = response.data;

        if (data && data.index) {
            let idxContainer = container.find('.itemHour').eq(data.index);

            if (idxContainer.length > 0) {
                container = idxContainer;
            }
        }

        // Parse error list
        let errors = response.errors;

        // Parse from data if errors response from data
        if (!errors && response.data) {
            errors = response.data;
        }

        let message = '';

        /*$.each(response, function (key, value) {
            if ($.isPlainObject(value)) {
                $.each(value, function (key, value) {
                    message += value + "<br/>";
                });
            }
        });*/

        // Check all errors
        $.each(errors, function (field, arrError) {
            message += arrError + "<br/>";

            // Highlight input
            container.find(':input[data-field="' + field + '"]').parent().addClass('bad');

            // Highlight array input
            // Check by laravel validation format
            // @link https://laravel.com/docs/5.5/validation#validating-arrays
            let arrField = field.split('.');

            if (arrField.length > 1) {
                if (arrField.length === 3) {
                    // Array list name
                    let field0 = arrField[0];
                    // Number of index
                    let field1 = arrField[1];
                    // Field name in array list
                    let field2 = arrField[2];

                    // Normal case: person.*.email
                    container
                        .find(':input[data-field="' + field0 + '[]' + '[' + field2 + ']' + '"]')
                        .eq(field1) // Index of field
                        .parent() // Mark bad in parent container
                        .addClass('bad'); // Add bad class to mark error

                    // Special case: person.email.*
                    if (!isNaN(field2)) {
                        container
                            .find(':input[data-field="' + field0 + '[' + field1 + ']' + '[]' + '"]')
                            .eq(field2)
                            .parent()
                            .addClass('bad');
                        container
                            .find(':input[data-field="' + field0 + '[]' + '[' + field1 + ']' + '"]')
                            .eq(field2)
                            .parent()
                            .addClass('bad');
                    }

                }
            }
        });

        if (message !== '') {
            showBootstrapAlert('#response', message, 'danger', true);
        }
    }
}

/**
 * Determine the mobile operating system.
 * This function returns one of 'iOS', 'Android', 'Windows Phone', or 'unknown'.
 * @link https://stackoverflow.com/a/21742107/2809971
 * @returns {String}
 */
function getMobileOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    return "unknown";
}
