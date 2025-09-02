$(function () {
    var workspaceAppTheme = $('.workspace_app_theme');
    var workspaceAppMetaContainer = $('.workspace_app_meta_container');

    /*-------------------- Begin Sortable --------------------*/

    workspaceAppMetaContainer.sortable({
        itemSelector: ".workspace_app_meta_item",
        handle: ".container-sort",
        axis: "y",
        update: function (event, ui) {
            var idsInOrder = workspaceAppMetaContainer.sortable('toArray', {
                attribute: 'data-id'
            });
            var url = workspaceAppMetaContainer.data('url-orders');
            var orders = {};
            var recordId = 0;
            var recordOrder = 0;

            // Loop to push order data
            for (var i = 0; i < idsInOrder.length; i++) {
                recordId = idsInOrder[i];
                recordOrder = i + 1;

                // Add to submit order
                orders[recordId] = recordOrder;

                // Change order in hidden field
                workspaceAppMetaContainer.find('[data-id="' + recordId + '"]').find('[name="order"]').val(recordOrder);
            }

            // Change order of items
            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_method': 'PUT',
                    'orders': orders,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // var data = response.data;
                        // console.log('data:', data);
                    } else {
                        window.alert(response.message);
                    }
                },
                error: function (error) {
                    var response = error.responseJSON;

                    // Show error
                    console.log('error response:', response);
                },
            });
        },
    });

    /*-------------------- End Sortable --------------------*/

    // Save setting of item
    $(document).on('click', '.workspace_app_meta_item .btn-save-item', function (e) {
        e.preventDefault();

        var button = $(this);
        var container = button.closest('.workspace_app_meta_item');
        var url = container.data('url');
        var id = container.data('id');

        var data = {};

        if (id) {
            // PUT for update
            data['_method'] = 'PUT';
        }

        container.find(':input').each(function () {
            var input = $(this);
            var name = input.attr('name');
            var val = input.val();

            if (typeof name === 'undefined') {
                return;
            }

            // Push data item
            data[name] = val;

            if (this.type === 'checkbox') {
                data[name] = input.prop('checked') ? 1 : 0;
            }
        });

        // Clear error inputs
        container.find('.form-control').removeClass('error');

        // Update meta item
        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Hide save button
                    button.hide();

                    var data = response.data;
                    // console.log('data:', data);

                    // Update ID
                    container.data('id', data.id);
                    // container.attr('data-id', data.id);

                    //---------- Update URLs ----------

                    // Update
                    if (data.url_update) {
                        container.data('url', data.url_update);
                        // container.attr('data-url', data.url_update);
                    }

                    // Change status
                    if (data.url_change_status) {
                        container.find('.field-status').data('url', data.url_change_status);
                        // container.find('.field-status').attr('data-url', data.url_change_status);
                    }

                    // Destroy
                    if (data.url_destroy) {
                        container.find('.btn-remove-item').data('url', data.url_destroy);
                        // container.find('.btn-remove-item').attr('data-url', data.url_destroy);
                    }

                    //---------- /Update URLs ----------
                } else {
                    window.alert(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // container.addClass('error');

                if (response.errors) {
                    for (var field in response.errors) {
                        container.find('[name="' + field + '"]').addClass('error');
                    }
                }

                // Show error
                console.log('error response:', response);
            },
        });

        return false;
    });

    /**
     * Clear error input
     *
     * @param input
     */
    var clearErrorInput = function (input) {
        if ($(input).hasClass('error')) {
            $(input).removeClass('error');
        }
    };

    /**
     * Show save button
     *
     * @param input
     */
    var showSaveButton = function (input) {
        var container = $(input).closest('.workspace_app_meta_item');
        // Show save button
        container.find('.btn-save-item').show();
    };

    // Clear error highlight
    workspaceAppMetaContainer.on('keypress', ':input', function () {
        // Clear error input
        clearErrorInput($(this));

        // Show save button
        showSaveButton($(this));
    });

    // Clear error highlight
    workspaceAppMetaContainer.on('keydown', ':input', function (e) {
        var allowKeys = [
            8, // "Backspace"
            13, // "Enter"
            46, // "Delete"
        ];
        var keyCode = e.which;

        if (allowKeys.indexOf(keyCode) >= 0) {
            // Clear error input
            clearErrorInput($(this));

            // Show save button
            showSaveButton($(this));
        }
    });

    // Remove a custom item
    $(document).on('click', '.workspace_app_meta_item .btn-remove-item', function (e) {
        e.preventDefault();

        var button = $(this);
        var container = button.closest('.workspace_app_meta_item');
        var url = container.data('url');
        var id = container.data('id');

        // When not save new item
        // We only need to remove this element
        if (!id) {
            container.remove();
            return;
        }

        // Update meta item
        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                '_method': 'DELETE',
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // var data = response.data;
                    // console.log('data:', data);

                    container.remove();
                } else {
                    window.alert(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // Show error
                console.log('error response:', response);
            },
        });

        return false;
    });

    // Change status field
    workspaceAppTheme.on('change', 'input[type=checkbox].field-status', function () {
        var checkbox = $(this);
        var container = checkbox.closest('.workspace_app_meta_item');
        var checked = checkbox.prop('checked');
        var url = checkbox.data('url');

        // Clear error inputs
        container.find('.form-control').removeClass('error');

        // Toggle active item
        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                '_method': 'PUT',
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // var data = response.data;
                    // console.log('data:', data);
                } else {
                    window.alert(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // Show error
                console.log('error response:', response);
            },
        });

        // Show / hide inputs
        if (checked) {
            // Show inputs
            checkbox.closest('.workspace_app_meta_item').find('.group-field').show();
            // Show border of input in non-default type
            checkbox.closest('.workspace_app_meta_item').find('.group-function-field')
                .removeClass('hide-border');
        } else {
            // Hide inputs
            checkbox.closest('.workspace_app_meta_item').find('.group-field').hide();
            // Hide border of input in non-default type
            checkbox.closest('.workspace_app_meta_item').find('.group-function-field')
                .addClass('hide-border');
        }
    });

    // Create new item
    $('.btn-create-item').on('click', function (e) {
        e.preventDefault();

        var url = $(this).data('url');

        // Update meta item
        $.ajax({
            url: url,
            success: function (response) {
                if (response.success) {
                    var data = response.data;

                    workspaceAppMetaContainer.append(data);
                } else {
                    window.alert(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // Show error
                console.log('error response:', response);
            },
        });
    });
});