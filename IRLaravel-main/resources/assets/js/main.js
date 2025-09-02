function Main() {
}

Main.fn = {
    init: function () {
        Main.fn.showConfirmPopupSubmit.call(this);
        Main.fn.initSelect.call(this);
        Main.fn.initTag.call(this);
        // Main.fn.initCheckbox.call(this);
        Main.fn.initBulkActionChange.call(this);
        Main.fn.initItemActions.call(this);
        Main.fn.initAjaxActions.call(this);
        // Main.fn.initSort.call(this);
        // Main.fn.initPerPage.call(this);
        // Main.fn.initFilter.call(this);
        // Main.fn.initEditor.call(this);
        // Main.fn.initReadmore.call(this);
        // Main.fn.initBtnSaveAndNew.call(this);
        // Main.fn.initInputEnterSubmit.call(this);
        // Main.fn.datetimepicker.call(this);
        // Main.fn.initDateTimePicker.call(this);
        // Main.fn.previewLocalImage.call(this);
        // Main.fn.datetimeSelect.call(this);
        Main.fn.initPhoneNumberInput.call(this);
        // Main.fn.searchInList.call(this);        
        Main.fn.saveAndClose.call(this);
        Main.fn.copyFromMultiple.call(this);
        Main.fn.printElement.call(this);
    },

    printElement: function() {
        $(document).on('click', '.print-trigger', function(){
            var dest = $(this).data('dest');
            var copyDate = $(this).data('has_date');

            if(copyDate != 'none' && $('.ir-only-date-range').length && $(dest + ' '+ copyDate).length) {
                var date = $('.ir-only-date-range').val();
                $(dest).find(copyDate).empty().text(date);
            }

            $(dest).print({mediaPrint: true});
        });
    },

    showConfirmPopupSubmit: function () {
        $(document).on('click', '.show-confirm', function () {
            //Close dropdown
            $(".ir-actions").parent().removeClass('open');

            var _this = $(this);
            var title = _this.data('title');
            var yesLabel = _this.data('yes_label');
            var noLabel = _this.data('no_label');
            var closeLabel = _this.data('close_label');
            var deletedLabel = _this.data('deleted_success');

            Swal.fire({
                width: 512,
                padding: '43px 60px 30px 60px',
                title: title,
                showDenyButton: true,
                showCancelButton: false,
                focusConfirm: false,
                focusDeny: false,
                denyButtonText: yesLabel,
                confirmButtonText: noLabel,
                showCloseButton: true,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                } else if (result.isDenied) {
                    $('body').loading('toggle');

                    $.ajax({
                        type: 'DELETE',
                        url: _this.data('route'),
                        data: {
                            "_token": $("input[name='_token']").val()
                        }
                    })
                    .done(function (respon) {
                        $('body').loading('toggle');
                        
                        if (respon.success) {
                            location.reload();
                        } else {
                            if (respon.status) {
                                //Hide row
                                $("#tr-" + _this.data('id')).hide();

                                Swal.fire({
                                    title: '<span class="ir-popup-title">' + deletedLabel + '</span>',
                                    html: '<span class="ir-popup-content">' + respon.message + '</span>',
                                    width: 512,
                                    padding: '43px 60px 30px 60px',
                                    showConfirmButton: false,
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    cancelButtonText: closeLabel
                                });
                            }
                        }
                    }).fail(function () {
                        $('body').loading('toggle');
                    });
                }
            });

            return false;
        });
    },

    initSelect: function () {
        $(".js-select-multiple").map(function () {
            var placeholder = $(this).attr('placeholder');

            $(this).select2({
                placeholder: placeholder
            });
        });

        if ($('.js-select-multiple-edit').length) {
            $('.js-select-multiple-edit').map(function () {
                var dataCat = $(this).val();
                var arrCat = dataCat.split(',');
                var jsSelectMultiple = $(this).closest('div').find('.js-select-multiple');

                jsSelectMultiple.val(arrCat);
                jsSelectMultiple.trigger('change');
            });
        }
    },

    initTag: function () {
        if ($('.tags').length) {
            if ($('.tag-edit').length) {
                $('.tag-edit').map(function () {
                    var data = $(this).val();
                    var arr = data.split(',');

                    $(this).closest('.taggle-area').find('.tags').map(function () {
                        new Taggle(this, {
                            tags: arr,
                            duplicateTagClass: 'bounce',
                            placeholder: $(this).attr('data-placeholder')
                        });
                    });
                });
            } else {
                $('.tags').map(function () {
                    new Taggle(this, {
                        placeholder: $(this).attr('data-placeholder')
                    });
                });
            }
        }
    },

    initCheckbox: function () {
        $('#select_all').change(function () {
            var checkboxes = $(this).closest('table').find(':checkbox');

            if ($(this).is(':checked')) {
                checkboxes.prop('checked', true);
                $('.table tbody tr').addClass('selected_item');
            } else {
                checkboxes.prop('checked', false);
                $('.table tbody tr').removeClass('selected_item');
            }
        });

        $('.checkbox_item').change(function () {
            var parent = $(this).parent().parent().parent().parent();

            if ($(this).is(':checked')) {
                parent.addClass('selected_item');
            } else {
                parent.removeClass('selected_item');
                $('#select_all').prop('checked', false);

            }

            var length_checkbox_item = $('.checkbox_item').length;
            var length_selected_item = $('.selected_item').length;

            if (length_checkbox_item == length_selected_item) {
                $('#select_all').prop('checked', true);
            }
        });
    },

    initBulkActionChange: function () {
        // update status list checkbox
        $(document).on('click', '.bulk_action_change', function () {

            var array = new Array();

            $("input.checkbox_item:checkbox[name=checkbox]:checked").each(function () {
                array.push($(this).val());
            });

            var token = $('#data-token').val();
            var id = array.join();
            var value = $(this).attr('value');
            var dataLink = $(this).attr('data-link');
            var link = $('#' + dataLink).val();
            var key = $(this).attr('data-key');
            var text = $(this).attr('data-text');
            var title = $(this).attr('data-title');

            if (id && link && key) {
                swal({
                    title: "Are you sure you want to " + text + " " + title + "?",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonText: "Yes",
                    confirmButtonClass: "btn-danger",
                    cancelButtonText: "No"
                }, function () {
                    $('body').loading('toggle');

                    $.ajax({
                        type: 'PUT',
                        url: link,
                        data: {
                            "_token": token,
                            "key": key,
                            "value": value,
                            "ids": id
                        },
                        success: function (response) {
                            $('body').loading('toggle');
                        }
                    }).success(function (response) {
                        if (response.success) {
                            swal("Success!", response.message, "success");

                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            swal("Error!", response.message, "error");
                        }
                    }).fail(function (error) {
                        console.log('error:', error);
                    });
                });

                return false;
            } else {
                swal({
                    type: "error",
                    title: "Choose at least one " + title,
                    confirmButtonClass: "btn-danger",
                    html: true
                });
            }
        });
    },

    initItemActions: function () {
        // update status list checkbox
        $(document).on('click', '.item_actions', function () {
            var token = $('#data-token').val();
            var value = $(this).attr('data-val');
            var dataLink = $(this).attr('data-link');
            var link = $('#' + dataLink).val();
            var key = $(this).attr('data-key');
            var text = $(this).attr('data-text');
            var title = $(this).attr('data-title');
            var id = $(this).attr('data-id');

            if (id && link && key) {
                swal({
                    title: "Are you sure you want to " + text + " " + title + "?",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonText: "Yes",
                    confirmButtonClass: "btn-danger",
                    cancelButtonText: "No"
                }, function () {
                    $('body').loading('toggle');

                    $.ajax({
                        type: 'PUT',
                        url: link,
                        data: {
                            "_token": token,
                            "key": key,
                            "value": value,
                            "id": id
                        },
                        success: function (response) {
                            $('body').loading('toggle');
                        }
                    }).success(function (response) {
                        if (response.success) {
                            swal("Success!", response.message, "success");

                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            swal("Error!", response.message);
                        }
                    }).fail(function (error) {
                        console.log('error:', error);
                    });
                });
                return false;
            }
        });
    },

    initAjaxActions: function () {
        // update status list checkbox
        $(document).on('click', '[data-action="ajax-action"]', function () {
            var _this = $(this);
            var token = $('#data-token').val();
            var url = _this.attr('data-url');
            var method = _this.attr('data-method');
            var alert = _this.attr('data-alert');
            var keys = {};
            var data = {"_token": token};

            if (typeof _this.attr('data-attach') !== typeof undefined && _this.attr('data-attach') !== false) {
                data.data = _this.attr('data-attach');
            }

            if ($('#lang-keys').length) {
                var keys = JSON.parse($('#lang-keys').val());
            }

            swal({
                title: alert,
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonText: (typeof keys.yes != 'undefined' ? keys.yes : 'Yes'),
                confirmButtonClass: "btn-danger",
                cancelButtonText: (typeof keys.no != 'undefined' ? keys.no : 'No')
            }, function () {
                $('body').loading('toggle');

                $.ajax({
                    type: method,
                    url: url,
                    data: data
                }).success(function (response) {
                    if (response.success) {
                        swal({
                            title: '',
                            text: response.message,
                            type: "success",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: (typeof keys.ok != 'undefined' ? keys.ok : 'Ok')
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        swal({
                            title: '',
                            text: response.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: (typeof keys.ok != 'undefined' ? keys.ok : 'Ok')
                        });
                    }

                    $('body').loading('toggle');
                }).fail(function (error) {
                    if (error.statusCode == 500) {
                        swal({
                            title: '',
                            text: error.statusText,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: (typeof keys.ok != 'undefined' ? keys.ok : 'Ok')
                        });
                    } else {
                        swal({
                            title: '',
                            text: error.responseJSON.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: (typeof keys.ok != 'undefined' ? keys.ok : 'Ok')
                        });
                    }

                    $('body').loading('toggle');
                });
            });

            return false;
        });
    },

    initSort: function () {
        $(document).on('change', '#sort_field', function () {
            $('#form_custom_list').submit();
        });

        $(document).on('click', '.order-asc', function () {
            $(this).parent().parent().find('.type_sort').val('asc');
            $('#form_custom_list').submit();
        });

        $(document).on('click', '.order-desc', function () {
            $(this).parent().parent().find('.type_sort').val('desc');
            $('#form_custom_list').submit();
        });
    },

    initPerPage: function () {
        // limit and order
        $(document).on('change', '#limit_pagination', function () {
            $('#form_custom_list').submit();
        });
    },

    initFilter: function () {
        $(document).on('change', '.filter_header', function () {
            $('#form_custom_list').submit();
        });
    },

    initEditor: function () {
        if ($('[init="ckeditor"]').length) {
            $('[init="ckeditor"]').each(function () {
                var self = this;

                if (typeof $(self).attr('name') !== 'undefined') {
                    CKEDITOR.plugins.addExternal('youtube', '/../../assets/ckeditor-youtube-plugin/youtube/');
                    CKEDITOR.replace($(self).attr('name'), {
                        language: 'en',
                        extraPlugins: 'youtube',
                        filebrowserBrowseUrl: '/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                        filebrowserImageBrowseUrl: '/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
                    });
                } else {
                    var editor = $(self).ckeditor();
                }
            });
        }
    },

    initReadmore: function () {
        $('.readmore').map(function () {
            var openText = $(this).attr('data-text-open');
            var closeText = $(this).attr('data-text-close'),
                defaultHeight = $(this).data('height') || 200;

            $(this).readmore({
                speed: 250,
                moreLink: '<a href="#" class="btn-more">' + openText + ' <i class="fa fa-angle-double-down" aria-hidden="true"></i></a>',
                lessLink: '<a href="#" class="btn-more">' + closeText + ' <i class="fa fa-angle-double-up" aria-hidden="true"></i></a>',
                collapsedHeight: defaultHeight
            });
        });
    },

    initBtnSaveAndNew: function () {
        $(document).on('click', '.btn-save-and-new', function () {
            $(this).after('<input type="hidden" name="save_new" value="yes"/>');
            $(this).closest('form').submit();
        });
    },

    initInputEnterSubmit: function () {
        $(document).on('keypress', '.enter-submit', function (event) {
            if (event.which == 13 || event.keyCode == 13) {
                $(this).closest('form').submit();
            }
        });
    },

    initSelect2: function () {
        var selected_items = $('.selected-items');
        if (selected_items.length > 0)
            selected_items.each(function () {
                selected_items_str = $(this).val() || '';

                if (selected_items_str !== '')
                    selected_items = selected_items_str.indexOf(',') === 1 ? selected_items_str.split(',') : [selected_items_str];

                $('#' + $(this).data('select-id')).select2().val(selected_items).trigger('change');
            });
        else
            $('.select2-multiple').select2();
    },

    initDateTimePicker: function () {
        $(document).ready(function () {
            $('.datetimepicker').datetimepicker({format: 'DD/MM/YYYY'});
        });
    },

    initPhoneNumberInput: function () {
        var inputEl = document.getElementById('phone');

        // When not found
        if (inputEl === null) {
            return;
        }

        var goodKey = '0123456789+';

        var checkInputTel = function (e) {
            var key = (typeof e.which == "number") ? e.which : e.keyCode;
            var start = this.selectionStart,
                end = this.selectionEnd;

            var filtered = this.value.split('').filter(filterInput);
            this.value = filtered.join("");

            /* Prevents moving the pointer for a bad character */
            var move = (filterInput(String.fromCharCode(key)) || (key == 0 || key == 8)) ? 0 : 1;
            this.setSelectionRange(start - move, end - move);
        };

        var filterInput = function (val) {
            return (goodKey.indexOf(val) > -1);
        };

        inputEl.addEventListener('input', checkInputTel);
    },

    datetimepicker: function () {
        $('.birthday').datetimepicker({format: 'YYYY/MM/DD'});
        $('.init-datepicker').datepicker({startDate: 'now'});
    },

    capitalizeFirstLetter: function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },

    renderLocalImage: function (file) {
        var reader = new FileReader();

        reader.onload = function (event) {
            var the_url = event.target.result;
            $('#image-preview').html("<img src='" + the_url + "' />");
        };

        reader.readAsDataURL(file);
    },

    previewLocalImage: function () {
        $(".upload-image").change(function () {
            Main.fn.renderLocalImage(this.files[0]);
        });
    },

    datetimeSelect: function () {
        /*$(document).ready(function () {*/
        $('.datetimepicker').datetimepicker({format: 'DD/MM/YYYY'});

        /*var el = document.getElementById("phone");

        //---------------------------------Chỉ cho nhập số---------------------------------
        if (el) {
            el.addEventListener("keypress", function (evt) {
                if (
                    (evt.which != 8 && evt.which != 0 && evt.which < 48) ||
                    evt.which > 57
                ) {
                    evt.preventDefault();
                }
            });
        }*/
        /*});*/
    },

    saveAndClose: function () {
        $(document).on('click', '.save-and-close', function () {
            $(this).append('<input type="hidden" name="save_and_close" value="1"/>');
            $(this).closest('form').submit();
        });
    },

    searchInList: function () {
        if ($('.search-action').length) {
            $(document).on('click', '.search-action', function () {
                $(this).closest('form#form_custom_list').submit();
            });
        }
    },

    copyFromMultiple: function () {
        $(document).on('click', '[data-action="copy-post"]', function () {
            var url = $(this).attr('data-copy-action');
            $(this).closest('form').attr('action', url).submit();
        });
    },
    
    rule: function () {
        $(document).ready(function () {
            Main.fn.init.call(this);
        });
    },
};

Main.fn.rule();