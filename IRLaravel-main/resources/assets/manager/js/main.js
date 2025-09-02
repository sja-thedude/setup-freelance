function MainManager() {
}

MainManager.fn = {
    init: function () {
        MainManager.fn.submitForm.call(this);
        MainManager.fn.timeNoLimit.call(this);
        MainManager.fn.uploadAvatar.call(this);
        MainManager.fn.disabledTimeNoLimit.call(this);
        MainManager.fn.switchPhysicalGift.call(this);
        MainManager.fn.getOrder.call(this);
        MainManager.fn.getJsonDataOpties.call(this);
        MainManager.fn.countOptionSelected.call(this);
        MainManager.fn.disableEmptyAccordion.call(this);
        MainManager.fn.actionOrderItem.call(this);
        MainManager.fn.ajaxGetDetail.call(this);
        MainManager.fn.ajaxShowFormCreate.call(this);
        MainManager.fn.refreshError.call(this);
        MainManager.fn.autoDetectTimeZone.call(this);
        MainManager.fn.masterOptions.call(this);
        MainManager.fn.customHandleOptiesCheck(this);
        MainManager.fn.selectpicker(this);
    },

    submitForm: function () {
        var optionValidCategory = {
            rules: {
                name: {required: true},
            },
            messages: {
                name: Lang.get('common.validation.field_required'),
            },
            highlight: function(element) {
                $(element).parents('li').addClass('error');
            }
        };

        var optionValidProduct = {
            rules: {
                name: {required: true},
                price: {required: true},
                vat_id: {required: true},
                category_id: {required: true},
            },
            messages: {
                name: Lang.get('common.validation.field_required'),
                price: Lang.get('common.validation.field_required'),
                vat_id: Lang.get('common.validation.field_required'),
                category_id: Lang.get('common.validation.field_required'),
            },
            highlight: function(element) {
                $(element).parents('li').addClass('error');
            }
        };

        var optionValidOption = {
            rules: {
                name: {required: true},
                min: {required: true},
                max: {required: true},
            },
            messages: {
                name: Lang.get('common.validation.field_required'),
                min: Lang.get('common.validation.field_required'),
                max: Lang.get('common.validation.field_required'),
            },
        };

        var optionValidGroup = {
            rules: {
                name: {customRequired: true},
                company_street: {customRequired: true},
                company_number: {customRequired: true},
                company_postcode: {customRequired: true},
                company_city: {customRequired: true},
                close_time: {
                    cutoffTimeRequired: true
                },
                receive_time: {
                    cutoffTimeRequired: true,
                    cutoffTimeValidate: true
                },
                contact_email: {
                    customRequired: true,
                    emailValidate: true
                },
                contact_surname: {customRequired: true},
                contact_name: {customRequired: true},
                contact_gsm: {
                    customRequired: true,
                    phoneValidate: true
                },
            }
        };

        var optionValidCoupon = {
            rules: {
                code: {required: true},
                promo_name: {required: true},
                max_time_all: {required: true},
                max_time_single: {required: true},
                discount: {required: function () {
                    return !$('[name="percentage"]').val()
                }},
                percentage: {
                    required: function () {
                        return !$('[name="discount"]').val()
                    }
                },
                expire_time: {required: true},
            },
            messages: {
                code: Lang.get('common.validation.field_required'),
                promo_name: Lang.get('common.validation.field_required'),
                max_time_all: Lang.get('common.validation.field_required'),
                max_time_single: Lang.get('common.validation.field_required'),
                discount: Lang.get('common.validation.field_required'),
                percentage: Lang.get('common.validation.field_required'),
                expire_time: Lang.get('common.validation.field_required'),
            },
        };

        var optionValidReward = {
            rules: {
                reward: {required: function () {
                        return !$('[name="percentage"]').val()
                    }},
                percentage: {
                    required: function () {
                        return !$('[name="reward"]').val()
                    }
                },
            },
            messages: {
                reward: Lang.get('common.validation.field_required'),
                percentage: Lang.get('common.validation.field_required'),
            },
        };

        $('#create_categories').validate(MainManager.fn.mergeJsonValid(optionValidCategory, 'create'));
        $('#update_categories').validate(MainManager.fn.mergeJsonValid(optionValidCategory, 'update'));

        $('#create_product').validate(MainManager.fn.mergeJsonValid(optionValidProduct, 'create'));
        $('#update_product').validate(MainManager.fn.mergeJsonValid(optionValidProduct, 'update'));

        $('#create_option').validate(MainManager.fn.mergeJsonValid(optionValidOption, 'create'));
        $('#update_option').validate(MainManager.fn.mergeJsonValid(optionValidOption, 'update'));

        $('#create_group').validate(MainManager.fn.mergeJsonValid(optionValidGroup, 'create'));
        $('#update_group').validate(MainManager.fn.mergeJsonValid(optionValidGroup, 'update'));

        $('#create_coupon').validate(MainManager.fn.mergeJsonValid(optionValidCoupon, 'create'));
        $('#update_coupon').validate(MainManager.fn.mergeJsonValid(optionValidCoupon, 'update'));

        $('#create_reward').validate(MainManager.fn.mergeJsonValid(optionValidReward, 'create'));
        $('#update_reward').validate(MainManager.fn.mergeJsonValid(optionValidReward, 'update'));

        $('#optie-items .name_item').each(function() {
            $(this).rules('add', {
                required: true,
                messages: {
                    required: Lang.get('option.validation.items.name_required'),
                }
            });
        });
    },

    ajaxGetDetail: function (callbackSuccess) {
        $(document).on('click', '.showItem', function (event) {
            $.ajax({
                type: 'GET',
                url: $(this).data('route'),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: callbackSuccess,
                error: function (response) {
                    console.log(response);
                }
            });
        });
    },

    ajaxShowFormCreate: function (callbackSuccess) {
        $(document).on('click', '.btnCreate', function (event) {
            $.ajax({
                type: 'GET',
                url: $(this).data('route'),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: callbackSuccess,
                error: function (response) {
                    console.log(response);
                }
            });
        });
    },

    timeNoLimit: function () {
        $(document).on('ifChecked', 'input[name=time_no_limit]', function (event) {
            MainManager.fn.disabledTimeNoLimit($(this));
        });
    },

    refreshError: function () {
        $(document).on('ifChecked', 'input[name=type]', function (event) {
            var object = $('input.error');
            object.removeClass('error');
            object.next().remove();
        });
    },

    switchPhysicalGift: function () {
        $(document).on('ifChecked', 'input[name=type]', function (event) {
            if ($(this).val() == 2) {
                $('.wrap_geldig_voor, .beloning_waarde').hide();
            } else {
                $('.wrap_geldig_voor, .beloning_waarde').show();
            }
        });
    },

    autoDetectTimeZone: function () {
        $('.auto-detect-timezone').map(function () {
            $(this).val(moment.tz.guess());
        });
    },

    mergeJsonValid: function (optionValidProduct, type) {
        $.extend(optionValidProduct, {
            onkeyup: false,
            onfocusout: false,
            submitHandler: function (form) {
                MainShared.fn.processFormByAjax(form, type);
            }
        });
        return optionValidProduct;
    },

    uploadAvatar: function () {
        $(document).on('change', ".upload-avatar", function () {
            if (this.files && this.files[0]) {

                var fileType = this.files[0]['type'];
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                if (validImageTypes.includes(fileType)) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.show-img').html('<img width="100%" height="100%" src="' + e.target.result + '"/>');
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    Swal.fire({
                        title: Lang.get('common.validation.format_img'),
                        type: "error",
                    });
                }
            }
        });
    },

    disabledTimeNoLimit: function (selector) {
        if ($(selector).val() === '0') {
            $('.days ul input').attr('readonly', 'readonly').addClass('boder-none');
            $('.days ul input[type=checkbox]').prop('disabled', true);
            $('.days ul').addClass('disable');
        }
        if ($(selector).val() === '1') {
            $('.days ul input[type=checkbox]')
                .removeAttr('disabled', true)
                .removeAttr('readonly')
                .removeClass('boder-none');

            $('.days ul').removeClass('disable');

            $('.beschikbaarheid li input[type=checkbox]').each(function(k, v) {
                var parentTime = $(v).closest('li').find('.time');
                parentTime.attr('readonly', 'readonly').addClass('boder-none');

                if ($(v).is(':checked')) {
                    parentTime.removeAttr('readonly').removeClass('boder-none');
                }
            });

            $(document).on('ifChecked', '.beschikbaarheid li input[type=checkbox]', function (event) {
                $(this).closest('li').find('.time').removeAttr('readonly').removeClass('boder-none');
            });
            $(document).on('ifUnchecked', '.beschikbaarheid li input[type=checkbox]', function (event) {
                $(this).closest('li').find('.time').attr('readonly', 'readonly').addClass('boder-none');
            });
        }
    },

    getOrder: function () {
        var arrCount = [];

        $('#newCategoryModal .ui-sortable input[type=checkbox]').each(function (k, v) {
            arrCount.push({
                id: $(v).val(),
                is_checked: $(this).is(':checked'),
            });
        });

        return arrCount;
    },

    getJsonDataOpties: function () {
        $("#newCategoryModal .ui-sortable").sortable({
            stop: function (event, ui) {
                $('input[name=orderOptions]').val(JSON.stringify(MainManager.fn.getOrder()));
            }
        }).disableSelection();

        $('#newCategoryModal .ui-sortable input[type=checkbox]').on('ifChanged', function (event) {
            $('input[name=orderOptions]').val(JSON.stringify(MainManager.fn.getOrder()));
        });
    },

    countOptionSelected: function () {
        $('#newCategoryModal .dropdown-menu input[type=checkbox]').on('ifChanged', function (event) {
            MainManager.fn.handleCountOptionSelected($(this))
        });
    },

    disableEmptyAccordion: function () {
        $(".accordion").on("accordionbeforeactivate", function (event, ui) {
            if ($.trim($(ui.newPanel).html()).length < 166) {
                event.preventDefault();
            }
        });
    },

    actionOrderItem: function () {
        $(".ui-sortable").sortable({
            cancel: '.data-item',
            stop: function (event, ui) {
                var _this = $(ui.item);
                var urlSave = _this.data('route');
                if (_this.hasClass('brick')) {
                    var gridly = _this.closest('.gridly')
                    urlSave = gridly.data('route')
                }
                var listOrder = [];
                var dropzoneBox = _this.closest('.dropzone-box')
                var galleryType = null
                if (dropzoneBox.length > 0) {
                    galleryType = dropzoneBox.data('gallery')
                }

                _this.parent().find('.row').each(function (k, v) {
                    listOrder.push($(v).data('id'));
                });

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: urlSave,
                    data: {order: listOrder, galleryType: galleryType},
                    async: false,
                    success: function (data) {
                        let previewGallery = data.data.previewGallery
                        let previewGalleryBox = $('img.preview-' + galleryType)

                        if (previewGalleryBox.length > 0) {
                            if (previewGallery) {
                                previewGalleryBox.attr('src', previewGallery)
                            } else {
                                let noImage = $('.no-image').val()
                                previewGalleryBox.attr('src', noImage)
                            }
                        }

                        console.log(data);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        }).disableSelection();
    },

    handleCountOptionSelected: function (self) {
        var arrCount = [];
        var showTextButton = $(self).parents('.dropdown-options').find('button .selected-count');

        $(self).parents('.dropdown-menu').find('input[type=checkbox]:checked').each(function (k, v) {
            arrCount.push($(v).val());
        });

        var count = arrCount.length;
        var label = showTextButton.data('extend-label');
        var maxSelect = parseInt(showTextButton.data('count-max'));

        showTextButton.text(count === maxSelect && label !== "" ? label : (count + " " + Lang.get('product.txt_selected')));
    },

    customHandleOptiesCheck: function () {
        $(document).on('click', '.iCheck-helper', function (e){
            if ($(this).parent().hasClass('disabled')) {
                return false
            }
            e.stopPropagation()
            let categoryOptions = $(this).parent().find('.categoryOptions')
            if (!$(this).parent().hasClass('checked')) {
                categoryOptions.prop('checked', true)
                $(this).parent().addClass('checked')
            } else {
                $(this).parent().removeClass('checked')
                categoryOptions.prop('checked', false)
            }

            $('input[name=orderOptions]').val(JSON.stringify(MainManager.fn.getOrder()))
            MainManager.fn.handleCountOptionSelected($(this))
        })
    },

    masterOptions: function () {
        $('#create_option input[name=master], #update_option input[name=master]').on('ifChecked', function (event) {
            $('#optie-items .master').each(function(k, v) {
                $(v).prop('checked', false).iCheck('update');
            });
            $(this).prop('checked', true).iCheck('update');
        });
    },

    selectpicker: function() {
        $('.selectpicker').selectpicker({});

        $(window).on('load', function(){
            $(".actions-btn.bs-select-all.btn.btn-default").text('@lang("common.select_all")');
            $(".actions-btn.bs-deselect-all.btn.btn-default").text('@lang("common.deselect_all")');
        });

        //on change function i need to control selected value
        $('select.selectpicker').on('change', function(){
           var selected = $('.selectpicker option:selected').val();
           var selectedCategories = $('#category_ids').val();
           var objSelector = $('#selectFeaturedProducts');
           var jsonData = objSelector.data('json');
           var selectedProducts = objSelector.val();

           // if ($('#limit_products').length && !$('#limit_products').is(':checked')) {
           //     return false;
           // }

            if (selectedCategories != null) {
                for (var i = 0; i < jsonData.length; i++) {
                    if (
                        (typeof jsonData[i] != 'undefined')
                        && (typeof jsonData[i].id != 'undefined')
                        && ($.inArray(jsonData[i].id.toString(), selectedCategories) > -1)
                    ) {
                        // Set disabled
                        jsonData[i].disabled = true;
                        for (var j = 0; j < jsonData[i].children.length; j++) {
                            jsonData[i].children[j].disabled = true;

                            // Remove product
                            if (selectedProducts != null) {
                                selectedProducts.remove(jsonData[i].children[j].id.toString());
                            }
                        }
                    } else {
                        // Remove disabled
                        delete jsonData[i].disabled;
                        for (var j = 0; j < jsonData[i].children.length; j++) {
                            delete jsonData[i].children[j].disabled;
                        }
                    }
                }
            } else {
                // Remove disabled
                for (var i = 0; i < jsonData.length; i++) {
                    delete jsonData[i].disabled;
                    for (var j = 0; j < jsonData[i].children.length; j++) {
                        delete jsonData[i].children[j].disabled;
                    }
                }

                objSelector.val([]).trigger('change.select2');
            }

            MainManager.fn.jsonData = jsonData;

            objSelector.val(selectedProducts);
            objSelector.trigger('change');
        });

        $('select.selectpicker').trigger('change');
    },

    rule: function () {
        $(document).ready(function () {
            MainManager.fn.init.call(this);
        });
    },
};

MainManager.fn.rule();
