function MainWeb() {
}

var flag = false;

MainWeb.fn = {
    init: function () {
        MainWeb.fn.convertUtcToLocalTime.call(this);
        MainWeb.fn.autoDetectTimeZone.call(this);
        MainWeb.fn.submitCartStep1.call(this);
        MainWeb.fn.cartCouponCode.call(this);
        MainWeb.fn.deleteCouponDiscount.call(this);
        MainWeb.fn.deleteItemInCart.call(this);
        MainWeb.fn.calculationFinalPriceCart.call(this);
        MainWeb.fn.roundFloatPrice.call(this);
        MainWeb.fn.changeNumberProduct.call(this);
        MainWeb.fn.getTimeslot.call(this);
        MainWeb.fn.initTimeslotCarousel.call(this);
        MainWeb.fn.web.call(this);
        MainWeb.fn.notifications.call(this);
        MainWeb.fn.notificationDetail.call(this);
        MainWeb.fn.submitCreateCart.call(this);
        MainWeb.fn.switchAfhaalLevering.call(this);
        MainWeb.fn.switchLevering.call(this);
        MainWeb.fn.switchSearchGroup.call(this);
        MainWeb.fn.autocompleteGroup.call(this);
        MainWeb.fn.btnModalSugesstProduct.call(this);
        MainWeb.fn.clientFillAddress.call(this);
        MainWeb.fn.showHideFeeShip.call(this);
        MainWeb.fn.productFavourite.call(this);
        MainWeb.fn.uploadAvatar.call(this);
        MainWeb.fn.updateProfile.call(this);
        MainWeb.fn.orderDetail.call(this);
        MainWeb.fn.showPopup.call(this);
        MainWeb.fn.closePopup.call(this);
        MainWeb.fn.cartRedeem.call(this);
        MainWeb.fn.deleteRedeemDiscount.call(this);
        MainWeb.fn.checkedTimeslot.call(this);
        MainWeb.fn.showHideSubtotal.call(this);
        MainWeb.fn.keyupGSM.call(this);
        MainWeb.fn._openPopup.call(this);
        MainWeb.fn._closePopup.call(this);
        MainWeb.fn.autoCheckedRadioAddress.call(this);
        MainWeb.fn.redirectOntdekOns.call(this);
        MainWeb.fn.redirectFromContact.call(this);
        MainWeb.fn.validateMandatory.call(this);
        MainWeb.fn.autoLoginAfterRegister.call(this);
        MainWeb.fn.redirectWhenGroupInactive.call(this);
        MainWeb.fn.correctDeliveryInfo.call(this);
        MainWeb.fn.handleRestaurantBox.call(this);
        MainWeb.fn.handleSelection.call(this);
        MainWeb.fn.stickyHeader.call(this);
        MainWeb.fn.handleMyProfileUser.call(this);
        MainWeb.fn.handleSearchMenuButton.call(this);
        MainWeb.fn.handleResizeProfileUser.call(this);
        MainWeb.fn.handleAccountBottomMenu.call(this);
        MainWeb.fn.handleShoppingBottomButton.call(this);
        MainWeb.fn.handleMobileHeaderBackButton.call(this);
        MainWeb.fn.handleLoyaltiesButton.call(this);
        MainWeb.fn.handleShowForgotPasswordEvent.call(this);
        MainWeb.fn.handleShowRegisterMobileEvent.call(this);
        MainWeb.fn.getIdCategoryAndScroll.call(this);
        MainWeb.fn.handleMessagesInMobile.call(this);
        MainWeb.fn.stickyHeaderProductCategory.call(this);
        MainWeb.fn.toggleMapInformation.call(this);
        MainWeb.fn.mSwitchLevering.call(this);
        MainWeb.fn.handleOrderHistory.call(this);
        MainWeb.fn.handleLoginMobileHomepage.call(this);
        MainWeb.fn.closeAddressBox.call(this);
        MainWeb.fn.loadingPage.call(this);
        MainWeb.fn.keyPressMobile.call(this);
        MainWeb.fn.triggerShowProfile.call(this);
    },

    convertUtcToLocalTime: function () {
        if ($('.time-convert').length) {
            $('.time-convert').map(function () {
                var format = $(this).data('format');
                var utcTime = $(this).data('datetime');
                var localTime = moment.utc(utcTime).local().format(format);

                $(this).empty().text(localTime);
            });
        }
    },

    autoDetectTimeZone: function () {
        $('.auto-detect-timezone').map(function () {
            $(this).val(moment.tz.guess());
        });
    },

    submitCartStep1: function () {
        $(document).on('click', '#btn-andere', function (e) {
            e.preventDefault();

            var classData = $(this).attr('data-class');
            var isDelTrigger = parseInt($('#parentCart input[name=is_trigger_del]').val());

            if (classData && classData.trim() === "btn-show-login-modal" && isDelTrigger !== 1) {
                let currentWidth = $(window).width()
                if (currentWidth <= 768) {
                    MainWeb.fn.handleLoginPopup.call(this)
                } else {
                    $('#modalLogin').removeClass('hidden')
                }

                return false;
            } else if (!flag && classData && classData.trim() === "btn-show-update-gsm") {
                $('.update-gsm').removeClass('hidden');
                return false;
            }

            var form = $(this).parents('form');
            var step = parseInt(form.find('input[name=step]').val());

            if (parseInt(form.find('input[name=numberCategory]').val()) === 1 && step === 1 && isDelTrigger !== 1) {
                $('#modalProductSuggesstion').removeClass('hidden').show();
            } else {
                $('#modalProductSuggesstion').addClass('hidden').hide();
                form.submit();
            }
        });
    },

    roundFloatPrice: function (num, isStr = false) {
        var result = (Math.round(MainWeb.fn.strip(num * 100)) / 100).toFixed(2);
        return isStr ? result : parseFloat(result);
    },

    changeNumberProduct: function () {
        $(document).on('change keyup', '.minute-content input', function () {
            MainWeb.fn.calculationFinalPriceCart.call(this);
        })
    },

    calculationFinalPriceCart: function () {
        var totalPrice = 0;
        var priceDiscountCoupon = 0;
        var priceDiscountRedeem = 0;
        var priceDiscountGroup  = 0;
        var couponDiscountWrap = $('#parentCart .couponDiscount');
        var redeemDiscountWrap = $('#parentCart .redeemDiscount');
        var groupDiscountWrap = $('#parentCart .groupDiscount');
        var couponDiscount = parseFloat($('input[name=coupon_discount]').val());
        var redeemDiscount = $("#wrapRedeem").length ? parseFloat($('#wrapRedeem a').attr('data-discount').trim()) : parseFloat(0);
        var isDeleveringPriceMin = $('input[name=isDeleveringPriceMin]');
        var groupId = $('[name=groupId]').val()
        var groupDiscount = parseFloat($('[name=group_discount]').val())
        var applicableDiscountProducts = $('input[name="discountProducts"]').val()
        if (applicableDiscountProducts) {
            applicableDiscountProducts = JSON.parse(applicableDiscountProducts)
        }
        var totalApplicablePrice = 0

        $('#parentCart .wrapForProduct').each(function (k, v) {
            var number = parseInt($(v).find('.minute-content input').val());
            var priceUnitProduct = $(v).find('input[name=priceUnitProduct]').val();
            var totalPriceOption = 0;
            var productId = parseInt($(v).find('.listProductInCart').val())

            $(v).find('.row-table.no-border').each(function (k, v) {
                var priceUnitOption = $(v).find('.priceUnitOption').val();
                var priceOption = parseFloat(priceUnitOption) * number;

                totalPriceOption = totalPriceOption + priceOption;

                $(v).find('.price.option').text(MainWeb.fn.roundFloatPrice(priceOption));
            });

            var priceOfProduct = parseFloat(totalPriceOption) + number * parseFloat(priceUnitProduct);
            priceOfProduct = priceOfProduct > 0 ? priceOfProduct : 0;

            if ($.inArray(productId, applicableDiscountProducts) > -1) {
                totalApplicablePrice += priceOfProduct
            }

            totalPrice = totalPrice + priceOfProduct;

            $(v).find('.price.product').text(MainWeb.fn.roundFloatPrice(priceOfProduct, 1));
        });

        var couponId = $('input[name=coupon_id]').val();
        if (couponId && !isNaN(couponDiscount)) {
            if ($('[name="coupon_percentage"]').val() > 0) {
                couponDiscount = totalApplicablePrice * parseFloat($('[name="coupon_percentage"]').val()) / 100
                couponDiscount = MainWeb.fn.roundFloatPrice(couponDiscount)
            }

            priceDiscountCoupon = couponDiscount > totalApplicablePrice ? totalApplicablePrice : couponDiscount;
        }

        // Caculate redeem
        var redeemId = $('input[name=redeemId]').val();
        if (redeemId && !isNaN(redeemDiscount)) {
            if ($('[name="redeem_percentage"]').val() > 0) {
                redeemDiscount = totalApplicablePrice * parseFloat($('[name="redeem_percentage"]').val()) / 100
                redeemDiscount = MainWeb.fn.roundFloatPrice(redeemDiscount)
            }

            priceDiscountRedeem = redeemDiscount > totalApplicablePrice ? totalApplicablePrice : redeemDiscount;
        }

        if (!(redeemId && !isNaN(redeemDiscount)) && !(couponId && !isNaN(couponDiscount)) && (groupId && !isNaN(groupDiscount))) {
            if ($('[name="group_percentage"]').val() > 0) {
                groupDiscount = totalApplicablePrice * parseFloat($('[name="group_percentage"]').val()) / 100
                groupDiscount = MainWeb.fn.roundFloatPrice(groupDiscount)
            }

            priceDiscountGroup = groupDiscount > totalApplicablePrice ? totalApplicablePrice : groupDiscount;
        }

        $('#parentCart .totalPriceOld').text(MainWeb.fn.roundFloatPrice(totalPrice, 1));

        var feeShip = parseFloat($('#fee .leverkosten').text().trim());
        var priceMin = parseFloat($('.priceMin').text().trim());
        var totalPriceOld = parseFloat($('.totalPriceOld').text().trim());
        var totalPriceFinal = MainWeb.fn.roundFloatPrice(totalPrice -
            (priceDiscountCoupon + priceDiscountRedeem + priceDiscountGroup) + (isNaN(feeShip) ? 0 : feeShip));

        couponDiscountWrap.text(MainWeb.fn.roundFloatPrice(priceDiscountCoupon, 1));
        redeemDiscountWrap.text(MainWeb.fn.roundFloatPrice(priceDiscountRedeem, 1));
        groupDiscountWrap.text(MainWeb.fn.roundFloatPrice(priceDiscountGroup, 1));

        $('#parentCart .totalPriceFinal').text(totalPriceFinal > 0 ? MainWeb.fn.roundFloatPrice(totalPriceFinal, 1) : 0.00);
        $('#parentCart input[name=redeem_discount]').val(priceDiscountRedeem);

        if (priceMin > totalPriceOld) {
            isDeleveringPriceMin.val(0);
        }

        if ($('#parentCart input[name=tab]').val() === "levering") {
            if (!$('#parentCart input[name=groupId]').val()) {
                $('#parentCart .wrapSubTotal').removeClass('hiddenSubtotal').show();
            }
        } else {
            if (!redeemId && !couponId && !groupId) {
                $('#parentCart .wrapSubTotal').addClass('hiddenSubtotal').hide();
            }
        }

        MainWeb.fn.checkDisableButtonSubmit.call(this);

        MainWeb.fn.showHideSubtotal.call(this);
    },

    deleteItemInCart: function () {
        $(document).on('click', '.wrapForProduct .delete', function () {
            flag = true;
            var btnSubmit = $('#parentCart #btn-andere');
            var type = $(this).data('type');
            var shopingCart = $(this).parents('.shopping-cart');

            $('#parentCart input[name=is_trigger_del]').val(1);

            if (type === "product") {
                $(this).parents('.wrapForProduct').remove();
            }

            if (shopingCart.find('.wrapForProduct').length === 0) {
                $('.deleteCouponDiscount').trigger('click');
                $('.deleteRedeemDiscount').trigger('click');
            }

            MainWeb.fn.calculationFinalPriceCart.call(this);

            MainWeb.fn.checkDisableButtonSubmit.call(this);

            MainWeb.fn.showHideFeeShip.call(this);

            btnSubmit.removeAttr('disabled').trigger('click');
        });
    },

    showHideFeeShip: function () {
        var feeContainer = $('#fee');
        var tabContainer = $('input[name=tab]');
        if (tabContainer.length === 0) {
            tabContainer = $('input[name=tab]');
        }
        var wrapSubTotal  = $('#parentCart .wrapSubTotal');
        var priceMin      = parseFloat($('.priceMin').text().trim());
        var totalPriceOld = parseFloat($('.totalPriceOld').text().trim());
        var priceToFree   = parseFloat($('input[name=price_to_free]').val());
        var feeCart       = $('#parentCart input[name=feeCart]').val();
        var groupId       = $('#parentCart input[name=groupId]').val();

        if (!groupId) {
            groupId       = $('.parentCart input[name=groupId]').val();
        }
        if (totalPriceOld < priceMin) {
            feeContainer.find('.leverkosten').text(0);
            feeContainer.hide();
            wrapSubTotal.addClass('hiddenSubtotal').hide();
        } else {
            if (!groupId) {
                feeContainer.show();
                wrapSubTotal.removeClass('hiddenSubtotal').show();
            }

            if (totalPriceOld >= priceToFree || groupId) {
                feeContainer.find('.leverkosten').text(0);
            } else {
                if (typeof feeCart != 'undefined') {
                    feeContainer.find('.leverkosten').text(parseFloat(feeCart).toFixed(2));
                } else {
                    feeContainer.find('.leverkosten').text(feeCart);
                }
            }
        }

        if (tabContainer.val() === "afhaal" || !tabContainer.val()) {
            feeContainer.hide();
        }

        if (tabContainer.val() === "levering") {
            if (totalPriceOld >= priceToFree) {
                $('.message-free').show();
            } else {
                $('.message-free').hide();
            }

            if (groupId) {
                $('.message-free').hide();
            }
        }

        MainWeb.fn.calculationFinalPriceCart.call(this);
    },

    showHideSubtotal: function () {
        if ($('.wrapSubTotal').hasClass('hiddenSubtotal')) {
            $('.wrapSubTotal').hide();
        } else {
            $('.wrapSubTotal').show();
        }
    },

    clientFillAddress: function () {
        $(document).on('change', '.use-maps input[name=address_type]', function () {
            var container = $(this).closest('.wrap-step, .wrap-popup-card');
            var addressType = $("input[type=radio][name=address_type]:checked").val();
            var lat = $('input[name=lat]').val();
            var long = $('input[name=long]').val();
            var locationInput = container.find('.location').val();

            if ((addressType == 1 || typeof addressType == "undefined") && (lat == 0 || long == 0 || locationInput == "")) {
                container.find('.btn-order, .btn-pr-custom').addClass('disableBtn').prop('disabled', true);
            } else {
                container.find('.btn-order, .btn-pr-custom').removeClass('disableBtn').prop('disabled', false);
            }

            if(addressType == 0) {
                container.find('.btn-order, .btn-pr-custom').removeClass('disableBtn').prop('disabled', false);
            }
        });

        $(document).on('keypress change', '.use-maps input[name=address]', function () {
            var container = $(this).closest('.wrap-step, .wrap-popup-card');
            var addressType = $("input[type=radio][name=address_type]:checked").val();
            var lat = $('input[name=lat]').val();
            var long = $('input[name=long]').val();
            var locationInput = container.find('.location').val();

            if ((addressType == 1 || typeof addressType == "undefined") && (lat == 0 || long == 0 || locationInput == "")) {
                container.find('.btn-order, .btn-pr-custom').addClass('disableBtn').prop('disabled', true);
            }
        });

        $(document).on('click', '.use-maps .place-results .select-address', function () {
            var container = $(this).closest('.wrap-step, .wrap-popup-card');
            var addressType = $("input[type=radio][name=address_type]:checked").val();
            var lat = $('input[name=lat]').val();
            var long = $('input[name=long]').val();
            var locationInput = container.find('.location').val();

            if (!((addressType == 1 || typeof addressType == "undefined") && (lat == 0 || long == 0 || locationInput == ""))) {
                container.find('.btn-order, .btn-pr-custom').removeClass('disableBtn').prop('disabled', false);
            }
        });
    },

    btnModalSugesstProduct: function () {
        $(document).on('click', '.btnModalSugesstProduct', function () {
            $('#formCartStep1').submit();
        });
    },

    checkDisableButtonSubmit: function () {
        var countProductError    = 0;
        var isDeleveringPriceMin = parseInt($('input[name=isDeleveringPriceMin]').val());
        var priceMin             = parseFloat($('.priceMin').text().trim());
        var totalPriceOld        = parseFloat($('.totalPriceOld').text().trim());
        var groupId              = $('#parentCart input[name=group_id]').val();

        $('#parentCart .wrapForProduct').each(function (k, v) {
            countProductError += $(v).find('.row-table.row-error').length;
        });

        if ($('input[name=tab]').val() === "levering") {
            if (countProductError === 0 && (isDeleveringPriceMin === 1 || priceMin <= totalPriceOld)) {
                $('#parentCart #btn-andere')
                    .removeAttr('disabled')
                    .removeClass('btn-andere-gray')
                    .addClass('btn-andere');
                $('#parentCart .error').remove();
                $('#parentCart .error-delevering').hide();
                $('#parentCart .steps-wrap').show();
            } else {
                $('#parentCart #btn-andere')
                    .removeClass('btn-andere')
                    .addClass('btn-andere');
                $('#parentCart .error-delevering').show();
                $('#parentCart .steps-wrap').hide();
                $('#parentCart #btn-andere').prop('disabled', true);
            }

            if (groupId) {
                $('#parentCart .error-delevering').hide();
            }
        }

        if ($('input[name=tab]').val() === "afhaal" && countProductError === 0) {
            $('#parentCart #btn-andere')
                .removeAttr('disabled')
                .removeClass('btn-andere-gray')
                .addClass('btn-andere');
            $('#parentCart .error').remove();
            $('#parentCart .error-delevering').hide();
            $('#parentCart .steps-wrap').show();
        }
    },

    deleteCouponDiscount: function () {
        $(document).on('click', '.deleteCouponDiscount', function () {
            var parentContainer = $('#parentCart');
            var totalPriceOld = parentContainer.find('.totalPriceOld').text().trim();
            var feeShip = parseFloat($('#fee .leverkosten').text().trim());
            var totalPriceFinal = parseFloat(totalPriceOld) + feeShip;

            parentContainer.find('.formInputCoupon').show();
            parentContainer.find('input[name=coupon_id]').val(null);
            parentContainer.find('input[name=coupon_code]').val(null);
            parentContainer.find('input[name=coupon_discount]').val(null);

            if (!feeShip) {
                parentContainer.find('.wrapSubTotal').addClass('hiddenSubtotal').hide();
            }

            parentContainer.find('.wrapCouponCode').hide();
            parentContainer.find('.totalPriceFinal').text(MainWeb.fn.roundFloatPrice(totalPriceFinal, 1));
            parentContainer.find('.couponDiscount').text(0);

            // if (parentContainer.find('#wrapRedeem a').attr('data-id')) {
            //     parentContainer.find('#wrapRedeem').show();
            // }

            MainWeb.fn.calculationFinalPriceCart.call(this);

            parentContainer.find('input[name=is_trigger_del]').val(1);
            parentContainer.find('#btn-andere').removeAttr('disabled').trigger('click');
        });
    },

    cartCouponCode: function () {
        $(document).on('click', '.btn-submit-coupon', function () {
            var listProduct = [];
            var parentContainer = $(this).parents('#parentCart');
            var container = $(this).parent();
            var inputCode = container.find('input[name=coupon_code]');
            var inputListProduct = parentContainer.find('.listProductInCart');
            var userId = parentContainer.find('input[name=user_id]').val();
            var code = btoa(inputCode.val());
            var url = inputCode.data('route').replace('-coupon_code-', code ? code : "blank");
            var currentTotalPrice = parentContainer.find('.totalPriceFinal').text().trim();
            var priceMinOfProduct = parseFloat($('input[name=priceMinOfProduct]').val());
            var cartId = $('input[name="cart_id"]').val()

            inputListProduct.each(function (k, v) {
                listProduct.push($(v).val());
            });

            $.ajax({
                type: 'GET',
                url: url + "&user_id=" + userId + "&product_id=" + listProduct + "&cart_id=" + cartId,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (parentContainer.find('#wrapRedeem a').attr('data-id')) {
                        parentContainer.find('#wrapRedeem').show();
                    }

                    if ($('.wrapGroupDiscount').is(":visible")) {
                        $('.wrapGroupDiscount').hide()
                    }

                    if (response.code === 422) {
                        container.find('.errors').text(response.message).show();
                        container.find('input').addClass('error-input');

                    } else {
                        container.find('.errors').text("").hide();
                        container.find('input').removeClass('error-input');

                        if (response.code === 200) {
                            var oldRedeemDiscount = parseFloat(parentContainer.find('.wrapRedeemDiscount .redeemDiscount').text().trim());
                            var priceDiscount = response.data.totalDiscount
                            currentTotalPrice = parseFloat(currentTotalPrice) + oldRedeemDiscount;

                            var totalPriceFinal = MainWeb.fn.roundFloatPrice(currentTotalPrice - priceDiscount);
                            container.hide();
                            parentContainer.find('.wrapCouponCode').show();
                            parentContainer.find('.couponDiscount').text(MainWeb.fn.roundFloatPrice(priceDiscount, 1));
                            parentContainer.find('.totalPriceFinal').text(totalPriceFinal > 0 ? MainWeb.fn.roundFloatPrice(totalPriceFinal, 1) : 0.00);
                            parentContainer.find('.totalPriceOld').text(MainWeb.fn.roundFloatPrice(currentTotalPrice, 1));
                            parentContainer.find('input[name=coupon_id]').val(response.data.id);
                            parentContainer.find('input[name=submitByCoupon]').val(response.data.id);
                            parentContainer.find('input[name=coupon_discount]').val(response.data.discount);

                            // Check redeeem
                            parentContainer.find('input[name=redeem_discount]').val(null);
                            parentContainer.find('input[name=redeem_history_id]').val(null);
                            parentContainer.find('.wrapRedeemDiscount').hide();
                            parentContainer.find('.wrapRedeemDiscount .redeemDiscount').text(0);
                            parentContainer.find('.wrapSubTotal').removeClass('hiddenSubtotal').show();

                            $('#formCartStep1').submit();
                        }
                    }

                    parentContainer.find('#wrapRedeem').hide();
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    },

    cartRedeem: function () {
        $(document).on('click', '#wrapRedeem a', function () {
            var parentContainer = $('#parentCart');
            var redeemDiscount = $(this).data('discount');
            var redeemHistoryId = $(this).data('id');

            parentContainer.find('input[name=redeem_discount]').val(redeemDiscount);
            parentContainer.find('input[name=redeem_history_id]').val(redeemHistoryId);
            parentContainer.find('input[name=redeemId]').val(redeemHistoryId);
            parentContainer.find('.wrapRedeemDiscount .redeemDiscount').text(redeemDiscount);
            parentContainer.find('.wrapRedeemDiscount').attr('data-show', 1).show();
            parentContainer.find('#wrapRedeem').hide();
            parentContainer.find('.wrapSubTotal').removeClass('hiddenSubtotal').show();
            if ($('.wrapGroupDiscount').is(":visible")) {
                $('.wrapGroupDiscount').hide()
            }

            MainWeb.fn.calculationFinalPriceCart.call(this);

            parentContainer.find('input[name=is_trigger_del]').val(1);
            parentContainer.find('#btn-andere').removeAttr('disabled').trigger('click');
        });
    },

    deleteRedeemDiscount: function () {
        $(document).on('click', '.deleteRedeemDiscount', function () {
            var parentContainer = $('#parentCart');

            parentContainer.find('input[name=redeem_discount]').val(null);
            parentContainer.find('input[name=redeem_history_id]').val(null);
            parentContainer.find('.wrapRedeemDiscount .redeemDiscount').text(0);
            parentContainer.find('.wrapRedeemDiscount').attr('data-show', 0).hide();

            if (parentContainer.find('#wrapRedeem a').attr('data-id')) {
                parentContainer.find('#wrapRedeem').show();
            }

            MainWeb.fn.calculationFinalPriceCart.call(this);

            parentContainer.find('input[name=is_trigger_del]').val(1);
            parentContainer.find('#btn-andere').removeAttr('disabled').trigger('click');
        });
    },

    uploadAvatar: function () {
        $(document).on('change', ".upload-avatar", function () {
            if (this.files && this.files[0]) {

                var fileType = this.files[0]['type'];
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                if (validImageTypes.includes(fileType)) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.show-img').html('<img width="100%" height="100%" src="' + e.target.result + '" alt="avatar" />');
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    $(this).val(null);
                    Swal.fire({
                        title: Lang.get('common.validation.format_img'),
                        type: "error",
                    });
                }
            }
        });
    },

    updateProfile: function () {
        $('#update_profile').validate({
            onkeyup: false,
            onfocusout: false,
            submitHandler: function (form) {
                MainShared.fn.processFormByAjax(form, 'update');
            }
        });

        $('.mobile-profile-user').on('submit', '#update_profile', function (e) {
            e.preventDefault()
            MainShared.fn.processFormByAjax(this, 'update');
        });
        
        $('#update_gsm').validate({
            onkeyup: false,
            onfocusout: false,
            submitHandler: function (form) {
                MainShared.fn.processFormByAjax(form, 'update');
            }
        });
    },

    getTimeslot: function () {
        window.globalTriggerTimeSlot = 0;

        $(document).on('keypress', 'input[name=settingDateslot]', function () {
            window.globalTriggerTimeSlot = 1;
        });

        $(document).on('change', 'input[name=settingDateslot]', function () {
            var _this = $(this);
            var parent = _this.parents('#parentCart');
            var url = _this.data('route');
            var date = _this.val();
            var groupId = parent.find('input[name=groupId]').val();
            var offsetTimeOrder = parent.find('input[name=offsetTimeOrder]').val();
            var type = parent.find('input[name=type]').val();
            var timezone = moment.tz.guess();

            if (window.globalTriggerTimeSlot == '1') {
                window.globalTriggerTimeSlot = 0;

                var params = [
                    "date=" + btoa(date),
                    "groupId=" + groupId,
                    "offsetTimeOrder=" + offsetTimeOrder,
                    "timezone=" + btoa(timezone),
                    "type=" + type,
                    "cartId=" + $('#parentCart input[name=cart_id]').val(),
                ];

                $.ajax({
                    type: 'GET',
                    url: url + "?" + params.join('&'),
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.code === 200) {
                            _this.parents('.shopping-cart').find('.wrap-sidebar-time').html(response.data);
                            $(".wrap-sidebar-time").owlCarousel('destroy');
                            MainWeb.fn.initTimeslotCarousel.call(this);
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            }
        });
    },

    initTimeslotCarousel: function () {
        $(".wrap-sidebar-time").owlCarousel({
            loop: true,
            nav: true,
            touchDrag: false,
            pullDrag: false,
            mouseDrag: false,
            dots: false,
            navText: [
                '<i class="icon-angle-left"></i>',
                '<i class="icon-angle-right"></i>',
            ],
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1,
                },
                1000: {
                    items: 1,
                },
            },
        });
    },

    web: function () {
        // TaiLA get Product when change order
        $(document).on('click', '.sort-order li a', function () {
            var url = $(this).data('url');
            var order = $(this).data('order');
            var workspace_id = $('#workspace_id').val();
            var category_id = $('#category_id').val();
            var order_type = $('#order_type').val();
            var is_search = $('#is_search').val();
            var locale = $('#locale').val();
            var q = $('.search-box').val();

            $.ajax({
                url: url,
                type: "post", // chọn phương thức gửi là post
                dataType: "text", // dữ liệu trả về dạng text
                data: { // Danh sách các thuộc tính sẽ gửi đi
                    order: order,
                    workspace_id: workspace_id,
                    category_id: category_id,
                    order_type: order_type,
                    locale: locale,
                    is_search: is_search,
                    q: q

                },
                success: function (result) {
                    // Sau khi gửi và kết quả trả về thành công thì gán nội dung trả về
                    // đó vào thẻ div có id = result
                    $('#product').html(result);
                    MainWeb.fn.autoHeight.call();

                    $('.selection').each(function () {
                        var elem = $(this);
                        // elem.click(function (event) {
                        //     elem.toggleClass('active');
                        // });
                        elem.find('span').html(elem.find('.sort-order li a[data-order=' + order + ']').text());
                        // elem.find(".order-sub-menu li a").click(function () {
                        //     $('.actived').removeClass('actived');
                        //     elem.find("span").html($(this).text());
                        //     $(this).addClass('actived');
                        // });
                    });
                }
            });
        });

        $(".header-search .search-action .wrap-action input").focus(function () {
            $('.header-search .search-action .wrap-action .remove-input').addClass('active');
        });/*.focusout(function() {
            window.setTimeout(function() {
                $('.search-category').removeClass('disable');
                $('.header-search .search-action .wrap-action').removeClass('show-input');
                $('.header-search .search-action .wrap-action .remove-input').removeClass('active');
            }, 500);
        });*/

        $(document).on('click', '#remove-input', function () {
            $('.owl-search-category').removeClass('disable');
            $('.header-search .search-action .wrap-action').removeClass('show-input');
            $('.header-search .search-action .wrap-action .remove-input').removeClass('active');

            // $(this).removeClass('active');
            $('#is_search').val(0);
            $('.search-box').val('');
            var url = $(this).data('url');
            var order = $('.sort-order').val();
            var workspace_id = $('#workspace_id').val();
            var category_id = $('#category_id').val();
            var order_type = $('#order_type').val();
            var locale = $('#locale').val();

            $.ajax({
                url: url,
                type: "post", // chọn phương thức gửi là post
                dataType: "text", // dữ liệu trả về dạng text
                data: { // Danh sách các thuộc tính sẽ gửi đi
                    order: order,
                    workspace_id: workspace_id,
                    category_id: category_id,
                    order_type: order_type,
                    is_search: 0,
                    locale: locale

                },
                success: function (result) {
                    // Sau khi gửi và kết quả trả về thành công thì gán nội dung trả về
                    // đó vào thẻ div có id = result
                    $('#product').html(result);
                    MainWeb.fn.autoHeight.call();

                    $('.order-sub-menu li a').removeClass('actived');
                }
            });
        });

        $(document).on('mouseover', '.item-meal .wrap-content .icn-information-o', function (e) {
            var device = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            if (!$(this).parent().parent().find('.wrap-info').hasClass('active')) {
                $(this).parent().parent().find('.wrap-info').addClass('active');
                x = e.clientX;
                y = e.clientY;
                
                if(device < 1024) {
                    $(this).parent().parent().find('.wrap-info').css({'left': x, 'top': y})
                }

            }
        })
        .on('mouseout', '.item-meal .wrap-content .icn-information-o', function (e) {
            $(this).parent().parent().find('.wrap-info').removeClass('active');
        });

        $(document).on('mouseenter', '.item-meal .wrap-content .wrap-info', function () {
            $(this).addClass('active');
        }).on('mouseleave', '.item-meal .wrap-content .wrap-info', function () {
            $(this).removeClass('active');
        });
        
        var device = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        if (device < 767) {
            $(document).on('click', '.icn-information-o', function (e) {
                $(this).parent().parent().find('.wrap-info').toggleClass('active');
                e.preventDefault();
                return false;
            });
        }

        $(document).on('keyup', '.search-box', delay(function () {
            var q = $(this).val();
            var workspace_id = $(this).attr('data-workspace');
            var category_id = $('#category_id').val();
            var url = $(this).attr('data-url');
            var locale = $(this).attr('data-locale');
            var order = $('.sort-order').val();

            $.ajax({
                url: url,
                type: "post", // chọn phương thức gửi là post
                dataType: "text", // dữ liệu trả về dạng text
                data: { // Danh sách các thuộc tính sẽ gửi đi
                    q: q,
                    workspace_id: workspace_id,
                    locale: locale,
                    order: order,
                    category_id: category_id

                },
                success: function (result) {
                    $('#is_search').val(1);
                    // Sau khi gửi và kết quả trả về thành công thì gán nội dung trả về
                    // đó vào thẻ div có id = result
                    $('#product').html(result);
                    MainWeb.fn.autoHeight.call();

                    $('.order-sub-menu li a').removeClass('actived');
                }
            });
        }, 1000));

        $(document).on('click', '.pro-show-detail', function (e) {
            e.preventDefault();
            if ($(e.target).hasClass('icon-heart') || $(e.target).hasClass('icon-heart-o')
            || $(e.target).hasClass('icn-information-o')
            || $(e.target).hasClass('no-clickable')) {
                return false;
            }
            // Tommy: prevent show product detail
            if ($(this).attr('data-allow') !== "") {
                $('.modalUnavaliableTime .contentMess').html($(this).attr('data-allow'));
                $('.modalUnavaliableTime').removeClass('hidden');
                return false;
            }

            // var workspace_id = $(this).attr('data-workspace');
            // var category_id = $('#category_id').val();
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            var locale = $(this).attr('data-locale');
            var checkMobile = $(this).data('mobile');
            // var order = $('.sort-order').val();

            $.ajax({
                url: url,
                type: "post", // chọn phương thức gửi là post
                dataType: "text", // dữ liệu trả về dạng text
                data: { // Danh sách các thuộc tính sẽ gửi đi
                    id: id,
                    // workspace_id: workspace_id,
                    locale: locale,
                    // order: order,
                    // category_id: category_id

                },
                success: function (result) {
                    $('#product-detail').html(result);
                    $('#product-detail .user-modal').removeClass('hidden');

                    let currentWidth = $(window).width()
                    if (currentWidth <= 768) {
                        $('#main-body').hide()
                        $('.mobile-category').hide()
                        $('#product-detail').addClass('mobile-product-detail')
                        $('body.body').addClass('sticky')
                        $('.mobile-header').css('background', $('.primary-color').val())
                        $("body").removeClass('has-popup');
                        window.scrollTo(0, 0)
                        $('.mobile-product-detail').show()

                        localStorage.setItem('positionScroll', e.offsetY - e.clientY);

                        if (checkMobile != 'undefined' && checkMobile == true) {
                            console.log('checkMobile', checkMobile);
                            $('body .product-suggestion').trigger('click');
                        }

                        return false;
                    }
                    // Sau khi gửi và kết quả trả về thành công thì gán nội dung trả về
                    // đó vào thẻ div có id = result
                    $('.user-modal .bg').on('click', function (e) {
                        $(this).parent().addClass('hidden');

                        $("body").removeClass('has-popup');
                        e.preventDefault();
                    });
                }
            });
        });

        $('.payment-choice .item-radio').on('click', function () {
            let radioInput = $(this).find('input')
            radioInput.prop('checked', true)
        })

        let idsBeforeSelectMaster = new Array();
        let lastIds = new Array();
        $(document).on('change', '.option-choice', function (e) {
            let total = $(".total-price").text();
            let total_price = last_price = 0;
            let tmp_price = 0;
            let check = false;
            var qty = $(".minute-content .qty").val();

            var master = $(this).data('master');
            var price = $(this).attr('data-price');
            var item_id = $(this).attr('data-item');
            var option_id = $(this).attr('data-option');
            var product_id = $(this).attr('data-product');
            var price_current = parseFloat($('.price_' + option_id).text());

            //Process for checked
            if ($(this).is(':checked')) {
                last_price = price_current + parseFloat(price);
                total_price = parseFloat(total) + parseFloat((price * qty));

                //active all when select master
                if (master == true) {
                    $('label[data-option=' + option_id + ']').addClass('check-all');

                    $('.option-'+option_id).map(function () {
                        if (item_id != $(this).data('item')) {
                            var _item_id = $(this).data('item');

                            var _price = $(this).data('price');

                            if ($(this).is(":checked")) {
                                check = true;
                                tmp_price = (_price * qty) + tmp_price;
                                $('#check' + _item_id).prop('checked', false);
                            }
                        }
                    });

                    total = parseFloat($('.total_' + product_id).text());
                    if (check) {
                        total_price = total - tmp_price + parseFloat((price * qty));
                    } else {
                        total_price = total + parseFloat((price * qty));
                    }

                    $('.price_' + option_id).text(parseFloat(price).toFixed(2));
                    $('.total-price').text(parseFloat(total_price).toFixed(2));
                    $('.get-total-price').val(parseFloat(total_price).toFixed(2));
                    if (price != 0) {
                        $('.price_' + option_id).closest('.price').show();
                    } else {
                        $('.price_' + option_id).closest('.price').hide();
                    }

                    return false;
                } else {
                    // If you choose master, then choose another option, uncheck all
                    if ($('.option-'+option_id+'[data-master=true]:checked').length && !idsBeforeSelectMaster.length) {
                        $('.option-'+option_id+'[data-master=true]').prop('checked', false).change();
                        return false;
                    }

                    // If you choose 2 items then choose master, then choose the previous one again, uncheck itself
                    if ($('.option-'+option_id+'[data-master=true]:checked').length && idsBeforeSelectMaster.length) {
                        let itemEnd = new Array();
                        if(jQuery.inArray(option_id+"_"+item_id, idsBeforeSelectMaster) !== -1) {
                            $('label[data-option=' + option_id + ']').removeClass('check-all');

                            idsBeforeSelectMaster.forEach(function(item) {
                                if (item != option_id+"_"+item_id) {
                                    //Push id to array
                                    itemEnd.push(item);
                                }
                            });

                            if (itemEnd.length) {
                                //Uncheck all
                                $('.option-'+option_id+'[data-master=true]').prop('checked', false).change();

                                //For and check item
                                itemEnd.forEach(function(item) {
                                    var optionArrray = item.split("_");

                                    //Trigger end item
                                    if (optionArrray[0] == option_id) {
                                        $('.item-'+optionArrray[1]).prop('checked', true).change();
                                    }
                                });
                            } else {
                                //Uncheck all
                                $('.item-'+item_id).prop('checked', false);
                                $('.option-'+option_id+'[data-master=true]').prop('checked', false).change();
                            }
                        } else {
                            //Uncheck all
                            var newIdsBeforeSelectMaster = idsBeforeSelectMaster;
                            $('.option-'+option_id+'[data-master=true]').prop('checked', false).change();

                            newIdsBeforeSelectMaster.forEach(function(item) {
                                var optionArrray = item.split("_");

                                if (optionArrray[0] == option_id) {
                                    $('.item-'+optionArrray[1]).prop('checked', true).change();
                                }
                            });
                        }

                        return false;
                    }

                    //Push id to array
                    idsBeforeSelectMaster.push(option_id+"_"+item_id);
                    //Remove duplicate in array
                    idsBeforeSelectMaster = idsBeforeSelectMaster.filter(function(elem, index, self) {
                        return index === self.indexOf(elem);
                    });

                    // Check all option if select all item
                    var countOption = $('.option-'+option_id+'[data-master=false]').length;
                    var checkedOption = $('.option-'+option_id+':checked').length;

                    // Check case for select max option
                    var max = $('.option-'+option_id).data('max');
                    if (checkedOption > max) {
                        //Set price
                        $('.price_' + option_id).text(parseFloat(last_price).toFixed(2));

                        //Set price
                        $('.total-price').text(parseFloat(total_price).toFixed(2));
                        $('.get-total-price').val(parseFloat(total_price).toFixed(2));

                        lastIds.forEach(function(item) {
                            var optionArrray = item.split("_");

                            if (optionArrray[0] == option_id) {
                                $('.item-'+optionArrray[1]).prop('checked', false).change();

                                //Remove id in array
                                var index = lastIds.indexOf(optionArrray[0]+"_"+optionArrray[1]);
                                lastIds.splice(index, 1);
                            }
                        });

                        lastIds.push(option_id+"_"+item_id);

                        $("body .minute-content .qty").trigger('change');
                        return false;
                    //Check case for max option = total selected option
                    } else if (checkedOption == max && checkedOption != countOption) {
                        // Set last id
                        if (lastIds.length) {
                            lastIds.forEach(function(item) {
                                var optionArrray = item.split("_");

                                if (optionArrray[0] == option_id) {
                                    //Remove id in array
                                    var index = lastIds.indexOf(optionArrray[0]+"_"+optionArrray[1]);
                                    lastIds.splice(index, 1);
                                } else {
                                    lastIds.push(option_id+"_"+item_id);
                                }
                            });
                        } else {
                            lastIds.push(option_id+"_"+item_id);
                        }
                    } else if (checkedOption == countOption) {
                        //Set price
                        $('.price_' + option_id).text(parseFloat(last_price).toFixed(2));
                        if (last_price != 0) {
                            $('.price_' + option_id).closest('.price').show();
                        } else {
                            $('.price_' + option_id).closest('.price').hide();
                        }
                        //Trigger click to master and return false;
                        $('.option-'+option_id+'[data-master=true]').prop('checked', true).change();
                        $("body .minute-content .qty").trigger('change');
                        return false;
                    } else {
                        if (lastIds.length) {
                            lastIds.forEach(function(item) {
                                var optionArrray = item.split("_");

                                if (optionArrray[0] == option_id) {
                                    //Remove id in array
                                    var index = lastIds.indexOf(optionArrray[0]+"_"+optionArrray[1]);
                                    lastIds.splice(index, 1);
                                }
                            });
                        }
                    }
                }

            //Process for uncheck
            } else {
                last_price = price_current + parseFloat(price);

                if(last_price != 0) {
                    last_price = price_current - parseFloat(price);
                }
                total_price = parseFloat(total) - parseFloat((price * qty));

                //deactive all when select master
                if (master == true) {
                    $('label[data-option=' + option_id + ']').removeClass('check-all');

                    //for and uncheck all
                    $('.option-' + option_id).prop('checked', false);

                    //Reset ids
                    idsBeforeSelectMaster = new Array();
                }
            }

            //Set price
            $('.price_' + option_id).text(parseFloat(last_price).toFixed(2));

            //Set price
            $('.total-price').text(parseFloat(total_price).toFixed(2));
            $('.get-total-price').val(parseFloat(total_price).toFixed(2));

            if (last_price != 0) {
                $('.price_' + option_id).closest('.price').show();
            } else {
                $('.price_' + option_id).closest('.price').hide();
            }
        });

        $(document).on('click', '.minute-content .plus', function (e) {
            var num =
                $(this).parent().find("input").val() != ""
                    ? $(this).parent().find("input").val()
                    : 1;
            num++;
            $(this).parent().find("input").val(num).trigger('change');
        });

        $(document).on('click', '.minute-content .minus', function (e) {
            var num =
                $(this).parent().find("input").val() != ""
                    ? $(this).parent().find("input").val()
                    : 1;
            num--;
            if (num < 1) {
                num = 1;
            }
            $(this).parent().find("input").val(num).trigger('change');
        });

        $(document).on('change', '.minute-content .qty', function (e) {
            var num = $(this).val();
            if (num < 1 || isNaN(parseInt(num))) {
                $(this).val(1);
            }

            var qty = $(this).val();
            var total = $(".total-price").data('price') * qty;
            let total_price = 0;
            let price = 0;

            if ($('.option-choice:checked').length) {
                $('.option-choice:checked').map(function () {
                    var _price = $(this).data('price');
                    price += (_price * qty);

                });

                total_price = parseFloat(total) + parseFloat(price);
            } else {
                total_price = parseFloat(total);
            }

            $(".total-price").text(parseFloat(total_price).toFixed(2));
        });

        function delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }
    },

    notifications: function () {
        $('.notifications').mouseover(function () {
            MainWeb.fn.notiCallAjax($(this));
        });
    },

    notiCallAjax: function (_this) {
        var url = _this.data('route');

        if (typeof url != 'undefined') {
            $.ajax({
                url: _this.data('route'),
                type: "get",
                data: {},
                success: function (response) {
                    if (response.success == true) {
                        $("#notification-list").html(response.data);
                        $("#m-notification-list").html(response.data);
                        $("#mobile-messages-user ul").html(response.data);
                    }
                }
            });
        }
    },

    notificationDetail: function () {
        $(document).on('click', '.notification-detail', function () {
            $('.m-side-logo #m-notification-list').addClass("show-m-menu");
            var _this = $(this);
            $.ajax({
                url: $(this).data('route'),
                type: "get",
                data: {},
                success: function (response) {
                    if (response.success == true) {
                        var html = response.data + '<div class="bg"></div>';
                        $("#pop-up-eye").html(html);

                        _this.closest('li').find('.icn-eye-color ').addClass('read');

                        var target = _this.data('target');
                        $('#' + target).addClass('active');

                        MainWeb.fn.convertUtcToLocalTime();
                        $('.pop-up .bg').on('click', function (e) {
                            $(this).parent().removeClass('active');
                            $(this).parent().addClass('hidden');
                            $('body').removeClass('has-popup');
                            e.preventDefault();
                        });
                    } else {
                        console.log("False");
                    }
                }
            });
        });
    },

    submitCreateCart: function () {
        $(document).on('click', '.submit-cart', function () {
            var elem = $('.create-cart');

            $.ajax({
                url: elem.attr('action'),
                type: elem.attr('method'),
                data: elem.serialize() + "&type=" + $('.wrap-sidebar input[name=type]').val(),
                beforeSend: function () {
                    elem.addClass('disabled');
                    elem.find('.submit-cart').attr('disabled', 'disabled');
                },
                success: function (response) {
                    if (response.success == true) {
                        var url = window.location;
                        var params = new URLSearchParams(url.search);

                        if (params.get("step") != 'undefined' && params.get("step") != 1) {
                            var newUrl = url.toString().replace('step='+params.get("step"), 'step=1');
                            window.location.href = newUrl;
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function (response) {
                    $('.check-content-error').removeClass('check-content-error');
                    $('.error-check').hide();

                    if (response.responseJSON.data != 'undefined') {
                        var data = response.responseJSON.data;

                        jQuery.each(data, function (index, item) {
                            $('.check-content-' + index).addClass('check-content-error');
                            $('.error-' + index).show().text(item.msg);
                        });

                        //Scroll to error validation
                        var firstItem = data[Object.keys(data)[0]];

                        if(window.innerWidth > 768) {
                            var scrollTo = $(".error-"+firstItem.id);
                            var container = $('.check-content');

                            container.animate({scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()}, 'slow')
                        }else{
                            var top = $('.mobile-header').height() + 40;
                            $('html,body').animate({scrollTop: $(" .error-"+firstItem.id).offset().top - top},'slow');
                        }

                    }

                    elem.find('.submit-cart').removeAttr('disabled');
                }
            });
        });
    },

    productFavourite: function () {
        $(document).on('click', '.pro-favorite', function () {
            $(this).find('i').toggleClass('icon-heart').toggleClass('icon-heart-o');

            var _this = $(this);

            $.ajax({
                url: _this.data('url'),
                type: 'GET',
                data: {},
                beforeSend: function () {
                    _this.addClass('disabled');
                },
                success: function (response) {
                    if (response.success == true) {
                        console.log(response.message);
                    }
                }
            });
        });
    },

    switchLevering: function () {
        $(document).on('click', '#wrapSwitchAfhaalLevering .btnLevering', function () {
            // Update flow new CR
            // var isRedirectLogin = $(this).data('redirect');
            // if (isRedirectLogin) {
            //     localStorage.setItem('from_select_levering', 1);
            //     window.location.href = $(this).data('redirect');
            // } else {
            //     $(this).parents('.wp-content').hide();
            //     $('#wrapFillAddress').show();
            // }
            $(this).parents('.wp-content').hide();
            $('#wrapFillAddress').show();
        });

        $(document).on('click', '#parentCart .tabLevering', function (event) {
            event.preventDefault();

            var classData = $(this).attr('data-class').trim();
            if (classData === "btn-show-login-modal") {
                $('#modalLogin').removeClass('hidden').show();
                localStorage.setItem('model_fill_address_show', 1);
                return false;
            }

            $('.modelFillAddress').removeClass('hidden');
        });

        $(document).ready(function () {
            if (localStorage.getItem('model_fill_address_show')) {
                $('.modelFillAddress').removeClass('hidden');
                localStorage.removeItem('model_fill_address_show');
            }

            // if (localStorage.getItem('from_select_levering')) {
            //     $('#wrapSwitchAfhaalLevering.home').hide();
            //     $('#wrapFillAddress').show();
            //     if ($('#wrapSwitchAfhaalLevering').hasClass('home')) {
            //         localStorage.removeItem('from_select_levering');
            //     }
            // }
            let fromContact = localStorage.getItem('from-contact');
            if(fromContact == null || fromContact == undefined) {
                setTimeout(function(){
                    $("#wrapSwitchAfhaalLevering.home").show();
                    $("#wrapSwitchAfhaalLevering").css("display", "block");
                },1500);
            }else {
                localStorage.removeItem('from-contact');
            }
            $("#wrapSwitchAfhaalLevering").delay(1500).animate({"margin-left": "0px"});

            let redirect = localStorage.getItem('redirect');
            if(redirect == null || redirect == undefined) {
                setTimeout(function(){
                    $(".animation").css("display", "block");
                },1500);

            }else {
                $('#m-wrapper #wrapSearchGroup').show()
                localStorage.removeItem('redirect');
            }
            $(".animation").delay(1500).animate({"left": "0px"});
        });
    },

    mSwitchLevering: function () {
        $(document).on('click', '#m-wrapper #wrapMSwitchAfhaalLevering .mBtnLevering', function () {
            $(this).parents('#m-wrapper #wrapMSwitchAfhaalLevering').hide();
            $('#m-wrapper #wrapFillAddress').show();
        });

        $(document).on('click', '#parentCart .tabLevering', function (event) {
            event.preventDefault();

            var classData = $(this).attr('data-class').trim();
            if (classData === "btn-show-login-modal") {
                $('#modalLogin').removeClass('hidden').show();
                localStorage.setItem('model_fill_address_show', 1);
                return false;
            }

            $('.modelFillAddress').removeClass('hidden');
        });

        $(document).on('click', '#m-wrapper #wrapFillAddress .backStep,#m-wrapper #wrapSearchGroup .backStep', function () {
            $(this).parents('.wp-content').hide();
            $('#wrapMSwitchAfhaalLevering').show();
        });

        // $(document).on('click', '#m-wrapper .btnSearchGroup', function () {
        //     var isRedirectLogin = $(this).data('redirect');
        //     if (isRedirectLogin) {
        //         localStorage.setItem('from_select_group', 1);
        //         window.location.href = $(this).data('redirect');
        //     } else {
        //         $(this).parents('#m-wrapper #wrapMSwitchAfhaalLevering').hide();
        //         $('#m-wrapper #wrapSearchGroup').hide();
        //     }
        // });

        $(document).ready(function (){
            // if (localStorage.getItem('model_fill_address_show')) {
            //     $('.modelFillAddress').removeClass('hidden');
            //     localStorage.removeItem('model_fill_address_show');
            // }
            var input = $("#m-keyword-search-groups");
            if (input.length > 0) {
                var url = input.data('route');

                input.autocomplete({
                    source: url,
                    minLength: 3,
                    select: function (event, ui) {
                        var form = $(event.target).closest('form');
                        if (ui.item.id !== '' && ui.item.value !== '') {
                            form.find('input[name=type]').val(ui.item.type);
                            form.find('input[name=group_id]').val(ui.item.id);
                            form.find('input[name=name_group]').val(ui.item.value);
                            form.find('button.btn-order').removeClass('disableBtn').prop('disabled', false);
                        } else {
                            form.find('input[name=type]').val(null);
                            form.find('input[name=group_id]').val(null);
                            form.find('input[name=name_group]').val(null);
                            form.find('button.btn-order').addClass('disableBtn').prop('disabled', true);
                        }
                    },
                }).data("ui-autocomplete")._renderItem = function (ul, item) {
                    ul.addClass('getGroup');
                    input.addClass('width300');
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a class='nameGroup'>" + item.value + "</a>")
                        .append("<span class='colseTime'>" + item.close_time + "</span>")
                        .appendTo(ul);
                };

                input.on("input change", function () {
                    var form = $(this).closest('form');
                    if (form.find('input[name=name_group]').val() !== $(this).val()) {
                        form.find('input[name=type]').val(null);
                        form.find('input[name=group_id]').val(null);
                        form.find('button.btn-order').addClass('disableBtn').prop('disabled', true);
                    }
                    if ($(this).val() === "") {
                        $(this).removeClass('width300');
                    }
                });
            }
        });
    },

    switchSearchGroup: function () {
        $(document).on('click', '#wrapSwitchAfhaalLevering .btnSearchGroup, #m-wrapper .btnSearchGroup', function () {
            var isRedirectLogin = $(this).data('redirect');
            if (isRedirectLogin) {
                localStorage.setItem('from_select_group', 1);
                window.location.href = $(this).data('redirect');
            } else {
                $(this).parents('.wp-content').hide();
                $('#wrapSearchGroup').show();
            }
        });

        $(document).ready(function () {
            if (localStorage.getItem('from_select_group')) {
                setTimeout(function(){$("#wrapSwitchAfhaalLevering.home").hide();},1500);
                $('#wrapSearchGroup').show();
                if ($('#wrapSwitchAfhaalLevering').hasClass('home')) {
                    localStorage.removeItem('from_select_group');
                }
            }
        });

        $(document).on('click', '#wrapSwitchAfhaalLevering #form-login .backPrev', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            localStorage.removeItem('from_select_group');
            window.location.href = url;
        });
    },

    autocompleteGroup: function () {
        var input = $("#keyword-search-groups");
        if (input.length > 0) {
            var url = input.data('route');

            input.autocomplete({
                source: url,
                minLength: 3,
                select: function (event, ui) {
                    var form = $(event.target).closest('form');
                    if (ui.item.id !== '' && ui.item.value !== '') {
                        form.find('input[name=type]').val(ui.item.type);
                        form.find('input[name=group_id]').val(ui.item.id);
                        form.find('input[name=name_group]').val(ui.item.value);
                        form.find('button.btn-order').removeClass('disableBtn').prop('disabled', false);
                    } else {
                        form.find('input[name=type]').val(null);
                        form.find('input[name=group_id]').val(null);
                        form.find('input[name=name_group]').val(null);
                        form.find('button.btn-order').addClass('disableBtn').prop('disabled', true);
                    }
                },
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                ul.addClass('getGroup');
                input.addClass('width300');
                return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a class='nameGroup'>" + item.value + "</a>")
                    .append("<span class='colseTime'>" + item.close_time + "</span>")
                    .appendTo(ul);
            };

            input.on("input change", function () {
                var form = $(this).closest('form');
                if (form.find('input[name=name_group]').val() !== $(this).val()) {
                    form.find('input[name=type]').val(null);
                    form.find('input[name=group_id]').val(null);
                    form.find('button.btn-order').addClass('disableBtn').prop('disabled', true);
                }
                if ($(this).val() === "") {
                    $(this).removeClass('width300');
                }
            });
        }
    },

    switchAfhaalLevering: function () {
        $(document).on('click', '#wrapFillAddress .backStep, #wrapSearchGroup .backStep', function () {
            $(this).parents('.wp-content').hide();
            $('#wrapSwitchAfhaalLevering').show();
        });
    },

    autoCheckedRadioAddress: function() {
        $(document).on('focus', '#wrapFillAddress .location, .modelFillAddress .location, #pop-search-address .location', function () {
            $(this).closest('.item-radio').find('input[name=address_type]').prop('checked', true);
            $('.use-maps input[name=address_type]').trigger('change');
        });
    },

    orderDetail: function() {
        $(document).on('click', '.order-detail', function () {
            var _this = $(this);
            $.ajax({
                url: $(this).data('route'),
                type: "get",
                data: {},
                success: function (response) {
                    if (response.success == true) {
                        var orderDetailHtml = response.data.orderDetailHtml
                        var groupInactiveHtml = response.data.groupInactiveHtml
                        orderDetailHtml = orderDetailHtml + '<div class="bg"></div>';
                        $("#pop-order-detail").html(orderDetailHtml);
                        $("#pop-avoid-reorder").html(groupInactiveHtml)

                        var target = _this.data('target');
                        $('#' + target).addClass('active');
                        $('#' + target).removeClass('hidden')

                        MainWeb.fn.convertUtcToLocalTime();
                        MainWeb.fn.showHideFeeShip.call(this);
                        $('.pop-up .bg').on('click', function (e) {
                            $(this).parent().removeClass('active');
                            $(this).parent().addClass('hidden');
                            $('body').removeClass('has-popup');
                            e.preventDefault();
                        });
                    } else {
                        console.log("False");
                    }
                }
            });
        });
    },

    showPopup: function () {
        $(document).on('click', '.show-popup', function () {
            //check group inactive shown in html
            let groupInactiveModal = $('#pop-avoid-reorder').find('.modelGroupInactive')
            if (groupInactiveModal.length > 0) {
                $("#pop-order-detail").addClass('hidden')
                $('#pop-avoid-reorder').removeClass('hidden')
                return false
            }

            var type = $(this).data('order-type');
            var group = $(this).data('group');

            if (type == 0 || group == true) {
                $('.order-again-now').submit();
            } else {
                var target = $(this).data("target");
                $("#" + target).addClass("active");
            }

            return false;
        });
    },

    redirectWhenGroupInactive: function () {
        $(document).on('click', '.modelProductAvaliable .btn-modal', function (){
            MainWeb.fn.handleGroupInactiveEvent($(this))
        })

        $(document).on('click', '.modelGroupInactive .btn-modal', function(){
            MainWeb.fn.handleGroupInactiveReorder($(this))
        })
    },

    handleGroupInactiveEvent: function (_this) {
        let modelProductAvaliable = _this.closest('.modelProductAvaliable')
        if (modelProductAvaliable.length > 0) {
            let route = modelProductAvaliable.data('route')
            if (typeof route != 'undefined') {
                window.location.href = route
            }
        }
    },

    handleGroupInactiveReorder: function (_this) {
        let modelGroupInactive = _this.closest('.modelGroupInactive')
        if (modelGroupInactive.length > 0) {
            $('#pop-avoid-reorder').html('')
            $('#pop-avoid-reorder').addClass('hidden')
        }
    },

    closePopup: function () {
        $(document).on('click', '.pop-up .wrap-popup-card .close', function () {
            $(this).closest('.pop-up').removeClass('active');
            $('.user-modal').addClass('hidden');

            MainWeb.fn.handleGroupInactiveEvent($(this))
            MainWeb.fn.handleGroupInactiveReorder($(this))

            beforeSelectMaster = new Array();
            return false;
        });

        //Close popup after clicking outside the popup
        $(document).on('click', '.modal-authen', function (e){
            let form = $(this).find('form')
            if (!form.is(e.target) && form.has(e.target).length === 0) {
                $(this).addClass('hidden');
                $("body").removeClass('has-popup');
            }
        })
        $(document).on('click', '.wrap-popup-card .btn-modal', function (){
            $("body").removeClass('has-popup');
        });
    },

    checkedTimeslot: function () {
        $(document).on('click', '#parentCart input[name=settingTimeslot]', function () {
            $(this).closest('.wrap-container').find('input').prop('checked', false);
            $(this).prop('checked', true);
            $(this).parent().find('input.display-none').prop('checked', true);
        });
    },

    keyupGSM: function () {
        $(document).on('keypress keyup', '.keyup-gsm', function (e) {
            this.value = this.value.replace(/[^0-9+/\.]/g, '');
            var keyCode = e.keyCode || e.charCode;
            var curchr = this.value.length;
            var curval = $(this).val();

            if ((curchr > 0 && curchr < 6) && curval.indexOf("+") <= -1) {
                $(this).val("+" + curval);
            } else if (curchr == 6 && curval.indexOf("+") > -1 && keyCode != 8) {
                $(this).val(curval + "/");
            } else if (curval.indexOf("+") <= -1 && curval.indexOf("/") != 6) {
                console.log('2');

                $(this).val($(this).val().replace(/^(\d{5})(\d{6,13})+$/, "+$1/$2"));
            } else {
                $(this).val(curval);
                $(this).attr('maxlength', '20');
            }
        });
    },

    autoHeight: function () {
        if ($(".grid-meal").length) {
            var h = $(".grid-meal .item-meal .wrap-item-content").innerHeight();
            var highestBox = 0;
            $(".grid-meal .item-meal .wrap-item-content").each(function () {
                if ($(this).height() > highestBox) {
                    highestBox = $(this).height();
                }
            });
            //var padd = h - highestBox;
            $(".grid-meal .item-meal .wrap-item-content").height(highestBox);
        }
    },

    redirectOntdekOns: function () {
        $(document).on('click', '.ontdek-ons', function(){
            $(this).closest('form').submit();
        })
    },

    redirectFromContact: function () {
        let currentUrl = window.location.href
        if (currentUrl.includes('?from=contact') > 0) {
            localStorage.setItem('from-contact', 1);
            $('.btnSearchGroup').trigger('click')
            currentUrl = currentUrl.replace('?from=contact', '')
            window.history.replaceState({}, document.title, currentUrl)
        }
    },

    validateMandatory: function () {
        let formLogin = $('.modal-authen').find('form')
        $('.modal-authen form').on('keyup', 'input', function(){
            let userModal = $(this).closest('.modal-authen')
            if (userModal.attr('id') == 'modalRegister') {
                return false
            }

            let formInputs = $(this).closest('form').find('input')
            let isAllInput = false
            let i = 0
            $.each(formInputs, function (index, formInput){
                if ($(formInput).val() != '') {
                    i++
                }
            })

            if (i == formInputs.length) {
                formLogin.find('button[type="submit"]').prop('disabled', false)
            } else {
                formLogin.find('button[type="submit"]').prop('disabled', true)
            }
        })
    },

    autoLoginAfterRegister: function (){
        $('#auto-login').on('click', function(){
            let verifyToken = $(this).data('token')

            $.ajax({
                type: "POST",
                url: $(this).data('url'),
                dataType: 'json',
                data: {'verifyToken': verifyToken},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    if (response.success) {
                        location.reload()
                    }
                },
                error: function (error) {
                    console.log(error)
                }
            })
        })
    },
    handleRestaurantBox: function () {
        $(window).resize(function (){
            MainWeb.fn.correctDeliveryInfo()
        })
    },
    correctDeliveryInfo: function () {
        let currentWidth = $(window).width()
        if (currentWidth > 640) {
            $('.search-results .restaurant-box').each(function (index, restaurant){
                let imageHeight = $(restaurant).find('img').height()
                $(restaurant).find('.restaurant-info').height(imageHeight)
            })
        }
    },
    resetFilterPane: function (filterPane) {
        let textInputs = filterPane.find('input[type="text"]')
        let checkboxes = filterPane.find('input[type="checkbox"]')
        let orderByFirst = filterPane.find('.dropdown-menu ul li').first()
        textInputs.val('')
        checkboxes.val('')
        checkboxes.prop('checked', false)
        //Reset hidden fields
        $('#order-by').val('')
        $('#is_loyalty').val('')
        $('#restaurant_name').val('')
        $('#category_id').val('')
        if (orderByFirst.hasClass('active')) {
            return false
        }

        filterPane.find('.dropdown-menu ul li').removeClass('active')
        orderByFirst.addClass('active')
        let defaultOrder = orderByFirst.text()
        filterPane.find('.choose-order-by').text(defaultOrder)
    },
    handleSelection: function () {
        $('.choose-type .dropdown-menu ul li').on('click', 'a', function () {
            let dataType = $(this).data('type')
            let dataText = $(this).text()
            if (dataType) {
                $('.filter-pane').hide()
                MainWeb.fn.resetFilterPane($('.filter-pane.' + dataType))
                $('.filter-pane.' + dataType).show()
                $('.choose-order span').text(dataText)
                $('.choose-type .dropdown-menu ul li').removeClass('active')
                $(this).parent().addClass('active')
                $('#current-type').val(dataType)
                let postData = {
                    "lat": $('.latitude').val(),
                    "long": $('.longitude').val(),
                    "choose_type": dataType
                }

                MainWeb.fn.filterCallAjax(postData)
            }
        })

        //order by
        $('.filter-pane .dropdown-menu ul li').on('click', 'a', function(){
            let orderType = $(this).data('type')
            let dataText = $(this).text()
            let currentType = $('#current-type').val()
            let isLoyalty = $('#is_loyalty').val()
            let categoryId = $('#category_id').val()
            let minimumOrderAmount = '';
            let deliveryCharge = '';

            if(currentType == 'levering') {
                minimumOrderAmount = $(".active input[name='minimum_order_amount']:checked").val();
                deliveryCharge = $(".active input[name='delivery_charge']:checked").val();
            }

            if (orderType) {
                $('.filter-pane.' + currentType + ' a span').text(dataText)
                $('.filter-pane.' + currentType + ' .dropdown-menu ul li').removeClass('active')
                $(this).parent().addClass('active')
                $('#order-by').val(orderType)
                let postData = {
                    "lat": $('.latitude').val(),
                    "long": $('.longitude').val(),
                    "choose_type": currentType,
                    "orderType": orderType,
                    "restaurantName": $('#restaurant_name').val(),
                    "categoryId": categoryId,
                    'minimumOrderAmount': minimumOrderAmount,
                    'deliveryCharge': deliveryCharge
                }

                if (isLoyalty) {
                    postData.isLoyalty = 1;

                } else {
                    postData.isLoyalty = 0
                    postData.hasOwnProperty('isLoyalty') ? delete postData.isLoyalty  : '' ;
                }

                MainWeb.fn.filterCallAjax(postData)
            }
        })

        //Search by dealer
        let timeout = null;
        $('.restaurant-search').on('keyup', function (){
            let restaurantName = $(this).val()
            let currentType = $('#current-type').val()
            let orderType = $('#order-by').val()
            let isLoyalty = $('#is_loyalty').val()
            let categoryId = $('#category_id').val()
            let minimumOrderAmount = '';
            let deliveryCharge = '';

            if(currentType == 'levering') {
                minimumOrderAmount = $(".active input[name='minimum_order_amount']:checked").val();
                deliveryCharge = $(".active input[name='delivery_charge']:checked").val();
            }

            let postData = {
                "lat": $('.latitude').val(),
                "long": $('.longitude').val(),
                "choose_type": currentType,
                "orderType": orderType,
                "restaurantName": restaurantName,
                "categoryId": categoryId,
                'minimumOrderAmount': minimumOrderAmount,
                'deliveryCharge': deliveryCharge
            }
            if (isLoyalty) {
                postData.isLoyalty = 1;

            } else {
                postData.isLoyalty = 0
                postData.hasOwnProperty('isLoyalty') ? delete postData.isLoyalty  : '' ;
            }

            $('#restaurant_name').val(restaurantName)
            clearTimeout(timeout);

            timeout = setTimeout(function () {
                $.ajax({
                    url: $('#search-restaurant-url').val(),
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    beforeSend: function () {
                        $('.restaurant-box').each(function(index, restaurantBox){
                            $(restaurantBox).find('.show-pending').addClass('pending-el')
                        })
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            let resultHtml = response.resultHtml
                            $('.search-results').html(resultHtml)
                        }
                    }
                })
            }, 500)
        })

        //Filter by loyalty
        $('.checkbox-loyalty').on('change', function () {
            let isLoyalty = $(this).is(":checked")
            let restaurantName = $('.restaurant-search').val()
            let currentType = $('#current-type').val()
            let orderType = $('#order-by').val()
            let categoryId = $('#category_id').val()
            let minimumOrderAmount = '';
            let deliveryCharge = '';

            if(currentType == 'levering') {
                minimumOrderAmount = $(".active input[name='minimum_order_amount']:checked").val();
                deliveryCharge = $(".active input[name='delivery_charge']:checked").val();
            }

            let postData = {
                "lat": $('.latitude').val(),
                "long": $('.longitude').val(),
                "choose_type": currentType,
                "orderType": orderType,
                "restaurantName": restaurantName,
                "categoryId": categoryId,
                'minimumOrderAmount': minimumOrderAmount != 'no_preference' ? minimumOrderAmount : '' ,
                'deliveryCharge': deliveryCharge
            }
            if (isLoyalty) {
                postData.isLoyalty = 1;
                isLoyalty = 1;

            } else {
                postData.isLoyalty = 0
                isLoyalty = '';
                postData.hasOwnProperty('isLoyalty') ? delete postData.isLoyalty  : '' ;
            }

            $('#is_loyalty').val(isLoyalty)
            MainWeb.fn.filterCallAjax(postData)
        })

        //Filter by Restaurant category
        $('.owl-search-type .active-type').on('click', 'a', function () {
            let categoryId = $(this).data('id')
            let isLoyalty = $('#is_loyalty').val()
            let restaurantName = $('.restaurant-search').val()
            let currentType = $('#current-type').val()
            let orderType = $('#order-by').val()
            let postData = {
                "lat": $('.latitude').val(),
                "long": $('.longitude').val(),
                "choose_type": currentType,
                "orderType": orderType,
                "restaurantName": restaurantName,
                "categoryId": categoryId
            }

            $('#category_id').val(categoryId)
            $('.owl-search-type .active-type').removeClass('active')
            $(this).parent().addClass('active')

            if (isLoyalty !== '') {
                postData.isLoyalty = isLoyalty
            }

            MainWeb.fn.filterCallAjax(postData)
        })

        //Filter by Minimum order amount
        $('input[type=radio][name=minimum_order_amount]').on('change', function(){
            let categoryId = $(this).data('id')
            let isLoyalty = $('#is_loyalty').val()
            let restaurantName = $('.restaurant-search').val()
            let currentType = $('#current-type').val()
            let orderType = $('#order-by').val()
            let minimumOrderAmount = '';
            let deliveryCharge = '';

            if(currentType == 'levering') {
                minimumOrderAmount = this.value != 'no_preference' ? this.value : '';
                deliveryCharge = $(".active input[name='delivery_charge']:checked").val();
            }

            let postData = {
                "lat": $('.latitude').val(),
                "long": $('.longitude').val(),
                "choose_type": currentType,
                "orderType": orderType,
                "restaurantName": restaurantName,
                "categoryId": categoryId,
                'minimumOrderAmount': minimumOrderAmount,
                'deliveryCharge': deliveryCharge
            }

            $('#category_id').val(categoryId)
            $('.owl-search-type .active-type').removeClass('active')
            $(this).parent().addClass('active')

            if (isLoyalty) {
                postData.isLoyalty = 1;
            } else {
                postData.isLoyalty = 0
                postData.hasOwnProperty('isLoyalty') ? delete postData.isLoyalty  : '' ;
            }

            MainWeb.fn.filterCallAjax(postData)
        })
        //Filter by delivery charge
        $('input[type=radio][name=delivery_charge]').on('change', function(){
            let categoryId = $(this).data('id')
            let isLoyalty = $('#is_loyalty').val()
            let restaurantName = $('.restaurant-search').val()
            let currentType = $('#current-type').val()
            let orderType = $('#order-by').val()
            let minimumOrderAmount = '';
            let deliveryCharge = '';

            if(currentType == 'levering') {
                minimumOrderAmount = $(".active input[name='minimum_order_amount']:checked").val();
                deliveryCharge = this.value;
            }

            let postData = {
                "lat": $('.latitude').val(),
                "long": $('.longitude').val(),
                "choose_type": currentType,
                "orderType": orderType,
                "restaurantName": restaurantName,
                "categoryId": categoryId,
                'minimumOrderAmount': minimumOrderAmount,
                'deliveryCharge': deliveryCharge
            }

            $('#category_id').val(categoryId)
            $('.owl-search-type .active-type').removeClass('active')
            $(this).parent().addClass('active')

            if (isLoyalty) {
                postData.isLoyalty = 1;
            } else {
                postData.isLoyalty = 0
                postData.hasOwnProperty('isLoyalty') ? delete postData.isLoyalty  : '' ;
            }

            MainWeb.fn.filterCallAjax(postData)
        })
    },

    filterCallAjax: function (postData) {
        $.ajax({
            url: $('#search-restaurant-url').val(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function(){
                $('.restaurant-box').each(function(index, restaurantBox){
                    $(restaurantBox).find('.show-pending').addClass('pending-el')
                })
            },
            data: postData,
            success: function (response) {
                if (response.status == 200) {
                    let resultHtml = response.resultHtml
                    $('.search-results').html(resultHtml)
                }
            }
        })
    },
    stickyHeader: function () {
        if ($('.mobile-header').length > 0 && !$('body').hasClass('web-user-index')) {
            $(window).scroll(function (){
                if ($('.isShoppingCartBottomClick').val() == 1) {
                    return false
                }

                if ($('#product-detail .user-modal').length > 0) {
                    return false
                }
                if ($(window).scrollTop() >= 99 && ($('#main-body #product').css('display') == 'none' || $('#main-body').css('display') == 'none')) {
                    $('body.body').addClass('sticky')
                    $('.mobile-header').css('background', $('.primary-color').val())
                } else {
                    $('body.body').removeClass('sticky')
                    $('.mobile-header').css('background', $('.gallery-url').val())
                    $('.mobile-header').css('background-size', 'cover')
                    $('.mobile-header').css('background-position', '50% 50%')
                }
            })
        }
    },

    openModal: function (modalClass) {
        let element = document.getElementsByClassName(modalClass)[0]
        if (typeof element != 'undefined') {
            element.classList.remove('hidden')
            return true
        }

        return false
    },

    handleMyProfileUser: function () {
        let currentWidth = $(window).width()
        $('.profile-user-button').on('click', function () {
            if (currentWidth <= 768) {
                let profileInformation = $('.modelProfileUser, .modelProfileUser1').html()
                MainWeb.fn.resetForBottomButton.call()
                $('.show-menu-user').addClass('active');
                $('.mobile-profile-user').html(profileInformation)
                $('.mobile-profile-user').show()
                $('#main-body').hide()
                $('.mobile-category').hide()
                $('.mobile-order-history').hide()
                $('.menu-user').addClass('hidden')
                $('#mobile-messages-user').addClass('hidden');
                // https://vitex1.atlassian.net/browse/ITR-1038 
                $('.modelFillAddress').addClass('hidden');
                //end
                $('.loyalties-button').removeClass('active')
                $('.mobile-profile-user').find('.show-date').map(function () {
                    MainWeb.fn.initDateTimeForFutureElem.call(this, $(this));
                });
            } else {
                MainWeb.fn.openModal.call(this, 'modelProfileUser');
                MainWeb.fn.openModal.call(this, 'modelProfileUser1');
            }
        });

        $('.profile-mobile').on('click', function () {
            if (currentWidth <= 768) {
                MainWeb.fn.openModal.call(this, 'modelProfileUser1');
                $('#mobile-messages-user').removeClass('hidden');
            }
        });
    },

    handleAccountBottomMenu: function () {
        $('.show-menu-user').on('click', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                // MainWeb.fn.resetForBottomButton.call()
                $(this).addClass('active')
                let isLogin = $(this).data('islogin')
                if (isLogin == 1) {
                    $('.menu-user').removeClass('hidden')
                } else {
                    MainWeb.fn.handleLoginPopup.call(this)
                }
            }
        })
    },

    handleLoginMobileHomepage: function () {
        $('.login-homepage').on('click', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                $('#wrap-mobile #wrapSearchGroup').hide()
                $('#m-wrapper .hp-slider').hide()
                $('#m-wrapper #m-content').hide()
                $('#m-wrapper .mobile-header').show()
                let modalLoginHtml = $('#modalLogin').html()
                $('.mobile-login').html(modalLoginHtml)
                $('.mobile-login').find('.pop-up').removeClass('pop-up')
                $('.mobile-login').show()
                MainWeb.fn.handleLoginMobile.call(this)
                
                if ($("#holiday").length) {
                    $("body #holiday").hide();
                }
            }
        });
        $('#m-wrapper .btnSearchGroup').on('click', function(){
            let currentWidth = $(window).width();
            var logged = $(this).data('logged');
            if (currentWidth <= 768 && logged == 0) {
                $('#m-wrapper .hp-slider').hide()
                $('#m-wrapper #m-content').hide()
                $('#m-wrapper .mobile-header').show()
                let modalLoginHtml = $('#modalLogin').html()
                $('.mobile-login').html(modalLoginHtml)
                $('.mobile-login').find('.pop-up').removeClass('pop-up')
                $('.mobile-login').show()
                $('#wrap-mobile #wrapSearchGroup').hide()
                localStorage.setItem('redirect', "groupSearch");

                MainWeb.fn.handleLoginMobile.call(this)
            } else if (currentWidth <= 768 && logged != 0) {
                $(this).parents('#m-wrapper #wrapMSwitchAfhaalLevering').hide();
                $('#m-wrapper #wrapSearchGroup').show()
            }
        })
        $('.mobile-login').on('click', '.btn-show-forgot-password-modal', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                $(this).removeAttr('data-toggle')
                $('#modalForgotPassword').addClass('hidden');
                $('.responsive-modal').hide()
                let modalForgotPassword = $('#modalForgotPassword').html()
                $('.mobile-forgot-password').html(modalForgotPassword)
                $('.mobile-forgot-password').find('.pop-up').removeClass('pop-up')
                $('.mobile-forgot-password').show()

                MainWeb.fn.handleForgotPasswordMobile.call(this)
            }
        })
        $('.mobile-login').on('click', '.btn-show-register-modal', function () {
            $('#select-date').attr('readonly', true);
            $('#modalRegister').addClass('hidden');
            $('.responsive-modal').hide()
            let modalRegister = $('#modalRegister').html()
            $('.mobile-register').html(modalRegister)
            $('.mobile-register').find('.pop-up').removeClass('pop-up')
            $('.mobile-register').show()
            $('#main-body').hide()

            $('.mobile-register').find('[name="birthday_display"]').map(function () {
                MainWeb.fn.initDateTimeForFutureElem.call(this, $(this));
            });

            MainWeb.fn.handleRegisterMobile.call(this)
        });
        if($('body').hasClass('loyalties-index')) {
            $('.bottom-menu ul li a').removeClass('active')
            $('.loyalties-button').addClass('active')
        }
    },

    handleOrderHistory: function () {
        let currentWidth = $(window).width()
        $('.order-history-button').on('click', function () {
            if (currentWidth <= 768) {
                MainWeb.fn.resetForBottomButton.call()
                $('.mobile-order-history').show()
                $('.mobile-messages-user').addClass('hidden')
                $('#main-body').hide()
                $('.mobile-category').hide()
                $('.menu-user').addClass('hidden')
                $('.loyalties-button').removeClass('active')
            } else {
                MainWeb.fn.handleLoginPopup.call(this)
            }
        })
        $('.order-history-mobile').on('click', function () {
            if (currentWidth <= 768) {
                MainWeb.fn.openModal.call(this, 'modelOrderHistory');
                $('.modelOrderHistory .mobile-order-history').show()
            }
        });
    },


    initDateTimeForFutureElem: function (dateInput) {
        // Show date picker
        var $now = new Date();
        var $year = $now.getFullYear();
        var _this = dateInput;
        var config = {
            timepicker: false,
            formatDate: "d/m/Y",
            format: "d/m/Y",
            next: 'icon-chevron-right',
            prev: 'icon-chevron-left',
            yearStart: 1920,
            yearEnd: $year,
            minDate: false,
            maxDate: true,
            dayOfWeekStart: 1,
            onSelectDate: function () {
                window.globalTriggerTimeSlot = 1;
            }
        };

        // Custom attribute from HTML
        var strMaxDate = _this.data("max-date");

        if (typeof strMaxDate !== 'undefined') {
            var maxDate = new Date(strMaxDate);
            var dateFormat = _this.data('date-format');
            var momentDate = moment(maxDate);
            strMaxDate = momentDate.format(dateFormat);

            // Max date
            config.maxDate = strMaxDate;

            if ($now > maxDate) {
                // Default date if now > max date
                config.defaultDate = maxDate;
            }
        }

        // Init date picker
        /** @link https://xdsoft.net/jqplugins/datetimepicker/ */
        _this.datetimepicker(config);

        $.datetimepicker.setLocale($('html').attr('lang'));
    },

    handleSearchMenuButton: function () {
        $('.search-menu-button').on('click', function () {
            MainWeb.fn.resetForBottomButton.call()
            // $('.wrap-search .search-action .wrap-action .search-text').trigger('click')
            // $('.wrap-search .search-action .wrap-action .search-box').focus()
            $(this).addClass('active')
            $('body.body').removeClass('sticky')
            $('#mobile-messages-user').addClass('hidden');
            $('.mobile-header').css('background', $('.gallery-url').val())
            $('.mobile-header').css('background-size', 'cover')
            $('.mobile-header').css('background-position', '50% 50%')
            window.scrollBy(0, 0)
        })
    },

    handleResizeProfileUser: function () {
        $(window).resize(function () {
            let currentWidth = $(window).width()

            if (currentWidth > 768) {
                // MainWeb.fn.resetForBottomButton.call(this)
                $('.bottom-menu ul li a').removeClass('active')
            } else {
                $('.modelProfileUser').addClass('hidden')
            }
        })
    },

    handleShoppingBottomButton: function () {
        let currentWidth = $(window).width()

        if (currentWidth <= 768 && $('.isShoppingCartBottomClick').val() == 1) {
            $('#parentCart').show()
            $('#product').hide()
            $('.mobile-category').hide()
            $('body.body').addClass('sticky')
            $('.mobile-header').css('background', $('.primary-color').val())
            $('input[name="settingDateslot"]').prop('readonly', true)
            $('.shopping-cart-button').addClass('active');
            $('.search-menu-button').removeClass('active');
        }
        $('.show-date').on('keypress', function(event) {
            event.preventDefault();
            return false;
        });
        $('.shopping-cart-button').on('click', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                MainWeb.fn.resetForBottomButton.call()
                $(this).addClass('active')
                $('#parentCart').show()
                $('#product').hide()
                $('.mobile-category').hide()
                $('#mobile-messages-user').addClass('hidden');
                $('body.body').addClass('sticky')
                $('.mobile-header').css('background', $('.primary-color').val())
                $('.isShoppingCartBottomClick').val(1)
                window.scrollTo(0, 0)
            }
        })
    },
    handleLoyaltiesButton: function () {
        $('.loyalties-button').on('click', function () {
            let url = $(this).data('route')
            MainWeb.fn.resetForBottomButton.call(this)
            $(this).addClass('active')
            $('#mobile-messages-user').addClass('hidden');
            let loyaltyActive = $(this).data('loyalty-active')
            if (loyaltyActive) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        let wrapLoyaltyHtml = response.data.wrapLoyaltyHtml
                        if (wrapLoyaltyHtml) {
                            $('.mobile-category').hide()
                            $('#product').hide()
                            $('#loyalties-container').html(wrapLoyaltyHtml)
                            $('#loyalties-container').show()
                        }
                    },
                    error: function (error) {

                    }
                })
            } else {
                let notUseLoyaltyCardText = $('.not_use_loyalty_card').val()
                MainWeb.fn.resetForBottomButton.call(this)
                $('.mobile-category').hide()
                $('#product').hide()
                let wrapSidebar = '<div class="wrap-sidebar"><span class="text-center">' + notUseLoyaltyCardText +
                    '</span></div>'
                $('#loyalties-container').html(wrapSidebar)
                $('#loyalties-container').show()
            }
        })
        let currentWidth = $(window).width()
        $('.loyalty-mobile').on('click', function () {
            if (currentWidth <= 768) {
                localStorage.setItem('loyalty-mobile', 1);
            }
        })
        let loyaltyMobile = localStorage.getItem('loyalty-mobile');
        if(currentWidth <= 768 && loyaltyMobile && loyaltyMobile == 1) {
            $('.loyalties-button').trigger('click');
        }
    },
    resetForBottomButton: function () {
        let currentWidth = $(window).width()
        if (currentWidth <= 768) {
            $('#parentCart').hide()
            if (!$('.menu-user').hasClass('hidden')) {
                $('.menu-user').addClass('hidden')
            }

            if (!$('#modalLogin').hasClass('hidden')) {
                $('#modalLogin').addClass('hidden')
            }
        }

        $('input[name="settingDateslot"]').prop('readonly', true)
        $('#product').show()
        $('.mobile-category').show()
        $('#main-body').show()

        $('.isShoppingCartBottomClick').val(0)
        $('.mobile-profile-user').hide()
        $('.mobile-order-history').hide()
        $('.responsive-modal').hide()
        $('.bottom-menu ul li a').removeClass('active')
        $('#loyalties-container').hide()
        $('.mobile-product-detail').hide()
        let loyaltyMobile = localStorage.getItem('loyalty-mobile');
        if(currentWidth <= 768 && loyaltyMobile && loyaltyMobile == 1) {
            $('.loyalties-button').addClass('active')
            localStorage.removeItem('loyalty-mobile')
        }
    },
    checkIfTemplateAppInstalled: function (iosTemplateApp, androidTemplateApp) {
        window.addEventListener('DOMContentLoaded', (event) => {
            var device = getMobileOperatingSystem();
            if (device === 'Android') {
                if (androidTemplateApp) {
                    //Deep link URL for existing users with app already installed on their device
                    window.location = androidTemplateApp;
                }
            } else if (device === 'iOS') {
                if (iosTemplateApp) {
                    // Deep link URL for existing users with app already installed on their device
                    window.location = iosTemplateApp;
                }
            }
        });
    },
    handleMobileHeaderBackButton: function () {
        $('.mobile-header').on('click', '.mobile-header-back-button', function (e) {
            if ($("#holiday").length) {
                $("body #holiday").show();
            }
            
            if ($('#modalRedeemSuccess').length > 0 && $('#modalRedeemSuccess').is(':visible')) {
                let modalRedeemSuccess = $('#modalRedeemSuccess')
                modalRedeemSuccess.hide();
                // Prevent reload page
                if (modalRedeemSuccess.data('prevent-reload')) {
                    return;
                }
                return
            }

            if ($('#modalRedeemFailed').length > 0 && $('#modalRedeemFailed').is(':visible')) {
                let modalRedeemFailed = $('#modalRedeemFailed')
                modalRedeemFailed.hide();
                return
            }

            if ($('.responsive-modal').is(':visible')) {
                if (!$('.mobile-login').is(':visible')) {
                    $('.responsive-modal').hide()
                    $('.mobile-login').show()
                    return
                }
            }

            let productDetailModal = $('.mobile-product-detail .user-modal')
            if (productDetailModal.length > 0 && !productDetailModal.hasClass('hidden') || $('#parentCart').is(':visible')
            || $('.responsive-modal').is(':visible')) {
                productDetailModal.addClass('hidden')
                $("body").removeClass('has-popup')
                $('body.body').removeClass('sticky')
                $('.mobile-header').css('background', $('.gallery-url').val())
                $('.mobile-header').css('background-size', 'cover')
                $('.mobile-header').css('background-position', '50% 50%')
                $('#main-body').show()
                $('.mobile-category').show()
                $('#product-detail').removeClass('mobile-product-detail')
                $('#parentCart').hide()
                $('.responsive-modal').hide()
                $('.bottom-menu ul li a').removeClass('active')
                $('.search-menu-button').addClass('active')

                if($('body.homepage #m-wrapper #m-content').is(':hidden')) {
                    $('#m-wrapper .mobile-header').hide();
                    $('#m-wrapper .hp-slider').show();
                    $('#m-wrapper #m-content').show()
                    $('#m-wrapper .mobile-header').hide()
                }
                if ($('#product').is(':hidden')) {
                    $('#product').show()
                }
                if(localStorage.getItem('positionScroll')) {
                    $('html,body').animate({scrollTop: localStorage.getItem('positionScroll')}, 'fast');
                    localStorage.removeItem('positionScroll')
                }

                return true
            }

            let dataUrl = $(this).data('url')
            window.location.href = dataUrl
        })
    },
    handleLoginPopup: function () {
        $('.responsive-modal').hide()
        let modalLoginHtml = $('#modalLogin').html()
        $('.mobile-login').html(modalLoginHtml)
        $('.mobile-login').find('.pop-up').removeClass('pop-up')
        $('.mobile-login').show()
        $('#main-body').hide()
        $('.mobile-category').hide()
        $('.menu-user').addClass('hidden')
        $('body.body').addClass('sticky')
        $('.mobile-product-detail').hide()
        $('.mobile-header').css('background', $('.primary-color').val())
        $('.isShoppingCartBottomClick').val(1)
        MainWeb.fn.handleLoginMobile.call(this)
    },
    handleShowForgotPasswordEvent: function (){
        $('.mobile-login').on('click', '.btn-show-forgot-password-modal', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                $(this).removeAttr('data-toggle')
                MainWeb.fn.handleOpenShowForgotPassword.call(this)
            }

        })
    },
    handleOpenShowForgotPassword: function () {
        $('.responsive-modal').hide()
        let modalForgotPassword = $('#modalForgotPassword').html()
        $('.mobile-forgot-password').html(modalForgotPassword)
        $('.mobile-forgot-password').find('.pop-up').removeClass('pop-up')
        $('.mobile-forgot-password').show()
        $('#main-body').hide()
        $('.mobile-category').hide()
        $('.menu-user').addClass('hidden')
        // $('body.body').addClass('sticky')
        $('.mobile-product-detail').hide()
        $('.mobile-header').css('background', $('.primary-color').val())
        $('.isShoppingCartBottomClick').val(1)
        MainWeb.fn.handleForgotPasswordMobile.call(this)
    },
    handleForgotPasswordMobile: function () {
        var modalForgotPassword = $('.mobile-forgot-password');
        var formForgotPassword = modalForgotPassword.find('form[name="form_forgot_password"]');
        var errorContainer = modalForgotPassword.find('.error');

        formForgotPassword.on('submit', function (e) {
            e.preventDefault();

            // Hide error
            formForgotPassword.removeClass('invalid');
            errorContainer.text('');

            // Get roles by workspace
            $.ajax({
                type: formForgotPassword.attr('method'),
                url: formForgotPassword.attr('action'),
                data: formForgotPassword.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Hide current modal
                        $('.responsive-modal').hide();
                        // Show confirmation modal
                        MainWeb.fn.showForgotPasswordConfirmation.call()
                    } else {
                        alert(response.message);
                    }
                },
                error: function (error) {
                    var response = error.responseJSON;

                    // Show error
                    formForgotPassword.addClass('invalid');
                    errorContainer.text(response.message);
                },
            });

            return false;
        });
    },
    showForgotPasswordConfirmation: function () {
        $('.responsive-modal').hide()
        let modalForgotPasswordConfirmation = $('#modalForgotPasswordConfirmation').html()
        $('.mobile-forgot-password-confirmation').html(modalForgotPasswordConfirmation)
        $('.mobile-forgot-password-confirmation').find('.pop-up').removeClass('pop-up')
        $('.mobile-forgot-password-confirmation').show()
        $('#main-body').hide()
        $('.mobile-category').hide()
        $('.menu-user').addClass('hidden')
        $('.mobile-product-detail').hide()
        $('body.body').addClass('sticky')
        $('.mobile-header').css('background', $('.primary-color').val())
        $('.isShoppingCartBottomClick').val(1)
    },
    handleShowRegisterMobileEvent: function () {
        $('.mobile-login').on('click', '.btn-show-register-modal', function () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                $('#select-date').attr('readonly', true);
                $(this).removeAttr('data-toggle')
                MainWeb.fn.showRegisterMobile.call(this)
            }
        })
    },
    showRegisterMobile: function () {
        $('.responsive-modal').hide()
        let modalRegister = $('#modalRegister').html()
        $('.mobile-register').html(modalRegister)
        $('.mobile-register').find('.pop-up').removeClass('pop-up')
        $('.mobile-register').show()
        $('#main-body').hide()
        $('.mobile-category').hide()
        $('.menu-user').addClass('hidden')
        $('.mobile-product-detail').hide()
        // $('body.body').addClass('sticky')
        $('.mobile-header').css('background', $('.primary-color').val())
        $('.isShoppingCartBottomClick').val(1)
        $('.mobile-register').find('[name="birthday_display"]').map(function () {
            MainWeb.fn.initDateTimeForFutureElem.call(this, $(this));
        });

        MainWeb.fn.handleRegisterMobile.call(this)
    },
    handleLoginMobile: function () {
        var modalLogin = $('.mobile-login');
        var formLogin = modalLogin.find('form[name="form_login"]');
        var errorContainer = modalLogin.find('.error');

        formLogin.on('submit', function (e) {
            e.preventDefault();

            // Hide error
            formLogin.removeClass('invalid');
            errorContainer.text('');
            let withoutCartUrl = $('.mobile-login').data('withoutcart')
            let workspaceId = $('.mobile-login').data('workspace-id')

            // Get roles by workspace
            $.ajax({
                type: formLogin.attr('method'),
                url: formLogin.attr('action'),
                data: formLogin.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var data = response.data;

                        // Update cart when has login
                        $.ajax({
                            type: "POST",
                            url: withoutCartUrl,
                            data: {workspaceId: workspaceId, userId: data.id},
                            dataType: 'json',
                            success: function (response) {
                                if (response.code === 200) {
                                    window.location.href = window.DOMAIN + '/auth/token/' + data.token + '?redirect=' + window.location.href;
                                }
                            },
                            error: function (error) {
                                console.log(error);
                            },
                        });

                    } else {
                        alert(response.message);
                    }
                },
                error: function (error) {
                    var response = error.responseJSON;

                    // Show error
                    formLogin.addClass('invalid');
                    errorContainer.text(response.message);
                },
            });

            return false;
        });
    },
    handleRegisterMobile: function () {
        var modalRegister = $('.mobile-register');
        var formRegister = modalRegister.find('form[name="form_register"]');
        var txtBirthdayDisplay = formRegister.find(':input[name="birthday_display"]');
        var txtBirthday = formRegister.find(':input[name="birthday"]');
        var chkAgree = formRegister.find('[name="checkbox-register"]');
        // var errorContainer = modalRegister.find('.error');
        var requiredFields = ['first_name', 'last_name', 'gsm', 'email', 'address', 'birthday', 'password', 'password_confirmation'];

        // Apply change from birthday display field to birthday field
        txtBirthdayDisplay.on('change', function () {
            var value = $(this).val();
            var date = moment(value, "DD/MM/YYYY");
            var valFormat = date.format('YYYY-MM-DD');

            if (!date || !valFormat) {
                // Process when invalid value
                valFormat = '';
            }

            // Set value for submit data
            txtBirthday.val(valFormat);
        });

        /**
         * Validate fields
         *
         * @returns {boolean}
         */
        var isValidForm = function () {
            for (var k in requiredFields) {
                var fieldName = requiredFields[k];
                var field = formRegister.find('[name="' + fieldName + '"]');

                // Check is required
                if (field.length > 0 && field.val() === '') {
                    return false;
                }
            }

            return true;
        };

        // Highlight Terms & Conditions when not check
        chkAgree.change(function () {
            if (this.checked) {
                //Do stuff
                modalRegister.find('.wrap-checkbox-register').removeClass('invalid-input');
            }
        });

        // Submit form
        formRegister.on('submit', function (e) {
            e.preventDefault();

            // Hide error
            formRegister.removeClass('invalid');
            // errorContainer.text('');
            formRegister.find('.error')
                .text('')
                .hide();
            formRegister.find('.invalid-input')
                .removeClass('invalid-input');

            // Get roles by workspace
            $.ajax({
                type: formRegister.attr('method'),
                url: formRegister.attr('action'),
                data: formRegister.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Hide current modal
                        $('.mobile-register').hide()
                        // Show confirmation modal
                        MainWeb.fn.showRegisterConfirmationMobile.call(this)
                    } else {
                        alert(response.message);
                    }
                },
                error: function (error) {
                    var response = error.responseJSON;

                    if (response.data) {
                        var data = response.data;

                        for (var fieldName in data) {
                            var field = formRegister.find('[name="' + fieldName + '"]').first();

                            if (field.length > 0) {
                                var fieldContainer = field.closest('.form-line-modal');

                                if (fieldContainer.length > 0) {
                                    var fieldError = data[fieldName];
                                    var fieldErrorMessage = (fieldError && fieldError.length > 0) ? fieldError[0] : '';
                                    fieldContainer.find('.error')
                                        .text(fieldErrorMessage)
                                        .show();

                                    var fieldBox = fieldContainer.find('.input-container');

                                    if (fieldName == 'checkbox-register') {
                                        fieldBox = fieldContainer.find('.wrap-checkbox-register')
                                    }

                                    if (fieldBox.length > 0) {
                                        fieldBox.addClass('invalid-input');
                                    }
                                }
                            }
                        }
                    }

                    // Show error
                    formRegister.addClass('invalid');
                    // errorContainer.text(response.message);
                },
            });

            return false;
        });
    },
    showRegisterConfirmationMobile: function () {
        $('.responsive-modal').hide()
        let modalRegisterConfirmation = $('#modalRegisterConfirmation').html()
        $('.mobile-register-confirmation').html(modalRegisterConfirmation)
        $('.mobile-register-confirmation').find('.pop-up').removeClass('pop-up')
        $('.mobile-register-confirmation').show()
        $('#main-body').hide()
        $('.mobile-category').hide()
        $('.menu-user').addClass('hidden')
        $('.mobile-product-detail').hide()
        $('body.body').addClass('sticky')
        $('.mobile-header').css('background', $('.primary-color').val())
        $('.isShoppingCartBottomClick').val(1)
    },
    _openPopup: function () {
        $(document).on('click', '[data-toggle="popup"]', function (e) {
            var target = $(this).data('target');
            if ($(e.target).hasClass('icon-heart') || $(e.target).hasClass('icon-heart-o')
                || $(e.target).hasClass('icn-information-o')
                || $(e.target).hasClass('no-clickable')) {
                return false;
            }

            $("body").addClass('has-popup');
        });
    },

    _closePopup: function () {
        $(document).on('click', '[data-dismiss="popup"]', function () {
            var target = $(this).data('target');
            $(target).addClass('hidden');

            $("body").removeClass('has-popup');

            if ($(this).hasClass('close-product-detail')) {
                $('body.body').removeClass('sticky')
                $('.mobile-header').css('background', $('.gallery-url').val())
                $('.mobile-header').css('background-size', 'cover')
                $('.mobile-header').css('background-position', '50% 50%')
                $('#main-body').show()
                $('.mobile-category').show()
                $('#product-detail').removeClass('mobile-product-detail')
            }
        });
    },

    getIdCategoryAndScroll: function() {
        var device = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        if (device < 768 && $(".mobile-category-title").length) {
            var getCategoryIdByUrlParameter = function getCategoryIdByUrlParameter() {
                var sPageURL = window.location.href,
                    sURLVariables = sPageURL.split('?'),
                    sEndParameter = sURLVariables[0].split('/category/')[1];

                return parseInt(sEndParameter);
            };

            if(getCategoryIdByUrlParameter()) {
                ctId = document.getElementById("ct-"+getCategoryIdByUrlParameter());
                $('html,body').animate({scrollTop: ctId.offsetTop + 20}, 'slow');
            }

        }
    },

    handleMessagesInMobile: function () {
        let currentWidth = $(window).width();
        $('.messages-button').on('click', function () {
            if (currentWidth <= 768) {
                MainWeb.fn.notiCallAjax($(this));
                $('#mobile-messages-user').removeClass('hidden');

                MainWeb.fn.resetForBottomButton.call();
                $(this).addClass('active');
                $('#parentCart').hide();
                $('#product').hide();
                $('.mobile-category').hide();
                $('body.body').addClass('sticky');
                $('.mobile-header').css('background', $('.primary-color').val());
                $('.isShoppingCartBottomClick').val(0);
                $('.show-menu-user').addClass('active');
                window.scrollTo(0, 0);
            }
        })
        $('.messages-button-mobile').on('click', function () {
            if (currentWidth <= 768) {
                MainWeb.fn.notiCallAjax($(this));
                MainWeb.fn.openModal.call(this, 'modelMessage');
            }
        });
    },

    stickyHeaderProductCategory: function() {
        var device = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

        if (($('body').hasClass('web-user-index') || $('body').hasClass('web-favourite-index')) && device < 768) {
            if ($('.mobile-header').length > 0) {
                var titleCodinateYs = [];

                $('.mobile-category-title').map(function(){
                    var id = $(this).data('id');
                    var ctId = document.getElementById("ct-"+id);

                    titleCodinateYs.push({
                        index: $(this).data('index'),
                        id: id,
                        top: ctId.offsetTop,
                        bottom: ctId.offsetTop + ctId.offsetHeight,
                        height: ctId.offsetHeight
                    });
                });
                var scanActiveElement = function(currentTop) {
                    if(titleCodinateYs.length > 0) {
                        var activeId = numberIndex = '';
                        $.each(titleCodinateYs, function(index, elementInfo) {
                            if(currentTop >= elementInfo.top && currentTop <= elementInfo.bottom) {
                                activeId = elementInfo.id;
                                numberIndex = elementInfo.index;
                            }
                        });

                        if(activeId != '') {
                            $('.active-category').removeClass('current');
                            $('#menu-category-'+activeId).addClass('current');
                        }

                        if (numberIndex != '') {
                            var owlSearchCategoryMain = $('.owl-search-category');
                            owlSearchCategoryMain.owlCarousel({
                                dots: false,
                                autoWidth:true
                            });
                            owlSearchCategoryMain.trigger('refresh.owl.carousel');
                            owlSearchCategoryMain.trigger("to.owl.carousel", [numberIndex, 500, true]);
                        }
                    }
                };

                scanActiveElement($(window).scrollTop());

                $(window).scroll(function (){
                    if ($(window).scrollTop() >= 56) {
                        $('body.body').addClass('sticky-category');
                    } else {
                        $('body.body').removeClass('sticky-category');
                    }

                    if ($(window).scrollTop() == 0) {
                        $('.active-category').removeClass('current');
                        $('.owl-search-category .owl-item').eq(0).find('.active-category').addClass('current');

                        var owlSearchCategoryMainScroll = $('.owl-search-category');
                        owlSearchCategoryMainScroll.owlCarousel({
                            dots: false,
                            autoWidth:true
                        });
                        owlSearchCategoryMainScroll.trigger('refresh.owl.carousel');
                        owlSearchCategoryMainScroll.trigger("to.owl.carousel", [0, 500, true]);
                    }
                    var currentTop = $(this).scrollTop();
                    var lastItem = titleCodinateYs[titleCodinateYs.length-1];

                    var checkLastItem = window.innerHeight - ($(document).height() - lastItem.bottom) + currentTop

                    if(Math.round(checkLastItem) < lastItem.bottom) {
                        scanActiveElement(currentTop);
                    }else {
                        scanActiveElement(lastItem.top);
                    }
                })
            }
        }
    },

    toggleMapInformation: function() {
        $(document).on('click', '#m-wrapper .hp-slider .information', function () {
            $(this).closest('#m-wrapper').find('.wrap-info-map-header').toggleClass('active');
            $(this).closest('#m-wrapper').find('.overlay-mobile').toggleClass('active');
        });
    },

    rule: function () {
        $(document).ready(function () {
            MainWeb.fn.init.call(this);
        });
    },
    closeAddressBox: function () {
        window.addEventListener('mouseup',function(event){
            var element = document.getElementById('address-box');
            var placeResult = document.getElementById('place-results');
            if (element && !element.contains(event.target)) {
                element.style.display = 'none';
            }
            if(placeResult && !placeResult.contains(event.target)) {
                placeResult.style.display = 'none';
            }
        });
    },
    loadingPage: function () {
        $(document).ready(function () {
            setTimeout(function(){
                $("#loader-page").fadeOut(500, function () {
                    $("#loader-page").css('visibility', 'hidden');
                })
            }, 500)
        });

    },
    keyPressMobile: function() {
        let currentWidth = $(window).width()
        if (currentWidth <= 768) {
            return true;
        }else
            return false;
    },
    strip: function (number) {
        return (parseFloat(number).toPrecision(12));
    },
    
    triggerShowProfile: function () {
        // Check and show popup address
        if (MainWeb.fn.getUrlParameter('show_address_box')) {
            $("#parentCart .tabLevering").trigger('click'); 
        }
        
        $(document).on('click', '.show-profile', function () {
           // $(".profile-user-button").trigger('click');
           MainWeb.fn.openModal.call(this, 'modelProfileUser1');
           $("#update_profile").append('<input name="show_address_box" type="hidden" value="1">');
        });
    },
    
    getUrlParameter: function (sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    }
};

MainWeb.fn.rule();
