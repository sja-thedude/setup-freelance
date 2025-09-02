$(function () {
    $('body').on('click', '.btn-redeem', function (e) {
        e.preventDefault();
        var modalRedeemSuccess = $('#modalRedeemSuccess');
        var rewardPhysical = modalRedeemSuccess.find('#reward-physical');
        var rewardGift = modalRedeemSuccess.find('#reward-gift');
        var rewardContent = modalRedeemSuccess.find('.reward-content');
        var rewardDiscount = rewardContent.find('.reward-discount');
        var rewardTitle = rewardContent.find('.reward-title');
        var rewardEmail = rewardContent.find('.reward-email');
        var rewardImageBox = modalRedeemSuccess.find('.reward-image');
        var rewardImage = rewardImageBox.find('img');

        /**
         * Get timezone from your device by moment.js
         * @link https://laracasts.com/discuss/channels/general-discussion/l5-best-way-to-get-user-timezone?page=1
         * @link https://momentjs.com/docs/
         * @link https://momentjs.com/timezone/docs/
         */
        var timezone = moment.tz.guess();

        // Modal failed
        var modalRedeemFailed = $('#modalRedeemFailed');
        var modalRedeemFailedMessageBox = modalRedeemFailed.find('.message-box');
        var url = $(this).data('url');

        // Invalid URL
        if (!url) {
            return;
        }

        // Create new redeem
        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'Authorization': 'Bearer ' + $('meta[name="bearer-token"]').attr('content'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Timezone': timezone
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    var reward = data.reward;
                    var user = data.user;

                    if(reward == null) {
                        window.location.reload();
                    }
                    // Fill data to popup

                    // Type = physical
                    if (reward.type == 1) {
                        rewardContent.find('.title-discount').html(reward.title);
                        rewardDiscount.html(reward.reward);
                        rewardPhysical.show();
                        rewardGift.hide();
                    }
                    // Type = gift
                    else if (reward.type == 2) {
                        // rewardTitle.html(reward.title);
                        rewardEmail.html(user.email);
                        rewardTitle.html(reward.title);
                        rewardPhysical.hide();
                        rewardGift.show();
                    }

                    // Show / Hide photo box
                    if (!reward.photo) {
                        // Full width content box
                        rewardContent.css({
                            'float': 'none',
                            'margin': 'auto',
                            'width': '100%'
                        });

                        // Hide image box
                        rewardImageBox.hide();
                    } else {
                        rewardImage.attr('src', reward.photo);
                    }

                    modalRedeemSuccess.show();
                } else {
                    modalRedeemFailedMessageBox.html(response.message);
                    modalRedeemFailed.removeClass('hidden');
                    modalRedeemFailed.show();
                    // console.log(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // Show error
                modalRedeemFailedMessageBox.html(response.message);
                modalRedeemFailed.removeClass('hidden');
                modalRedeemFailed.show();
                // console.log(response.message);
            },
        });


        return false;
    });

    $('body').on('click', '.btn-redeem-history', function (e) {
        e.preventDefault();
        var modalRedeemSuccess = $('#modalRedeemSuccess');
        var rewardPhysical = modalRedeemSuccess.find('#reward-physical');
        var rewardGift = modalRedeemSuccess.find('#reward-gift');
        var rewardContent = modalRedeemSuccess.find('.reward-content');
        var rewardDiscount = rewardContent.find('.reward-discount');
        var rewardTitle = rewardContent.find('.reward-title');
        var rewardEmail = rewardContent.find('.reward-email');
        var rewardImageBox = modalRedeemSuccess.find('.reward-image');
        var rewardImage = rewardImageBox.find('img');

        /**
         * Get timezone from your device by moment.js
         * @link https://laracasts.com/discuss/channels/general-discussion/l5-best-way-to-get-user-timezone?page=1
         * @link https://momentjs.com/docs/
         * @link https://momentjs.com/timezone/docs/
         */
        var timezone = moment.tz.guess();

        // Modal failed
        var modalRedeemFailed = $('#modalRedeemFailed');
        var modalRedeemFailedMessageBox = modalRedeemFailed.find('.message-box');
        // Get reward history
        $.ajax({
            url: $(this).data('url'),
            headers: {
                'Authorization': 'Bearer ' + $('meta[name="bearer-token"]').attr('content'),
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    var reward = data.reward_data;
                    var user = data.user;

                    // Fill data to popup

                    // Type = physical
                    if (reward.type == 1) {
                        rewardContent.find('.title-discount').html(reward.title);
                        rewardDiscount.html(reward.reward);
                        rewardPhysical.show();
                        rewardGift.hide();
                        $('.show-korting').hide();
                    }
                    // Type = gift
                    else if (reward.type == 2) {
                        // rewardTitle.html(reward.title);
                        rewardEmail.html(user.email);
                        rewardTitle.html(reward.title);
                        rewardPhysical.hide();
                        rewardGift.show();
                    }

                    // Show / Hide photo box
                    if (!reward.photo) {
                        // Full width content box
                        rewardContent.css({
                            'float': 'none',
                            'margin': 'auto',
                            'width': '100%'
                        });

                        // Hide image box
                        rewardImageBox.hide();
                    } else {
                        rewardImage.attr('src', reward.photo);
                    }

                    // Prevent reload page
                    modalRedeemSuccess.data('prevent-reload', 'true');

                    // Show modal
                    modalRedeemSuccess.show();
                } else {
                    modalRedeemFailedMessageBox.html(response.message);
                    modalRedeemFailed.removeClass('hidden');
                    modalRedeemFailed.show();
                    // console.log(response.message);
                }
            },
            error: function (error) {
                var response = error.responseJSON;

                // Show error
                modalRedeemFailedMessageBox.html(response.message);
                modalRedeemFailed.removeClass('hidden');
                modalRedeemFailed.show();
                // console.log(response.message);
            },
        });


        return false;
    });

    // When close popup
    $('body').on('click', '#modalRedeemSuccess .close', function () {
        let modalRedeemSuccess = $(this).closest('#modalRedeemSuccess')
        modalRedeemSuccess.hide();

        // Prevent reload page
        if (modalRedeemSuccess.data('prevent-reload')) {
            return;
        }

        // Reload page (default)
        window.location.reload();
    });
});