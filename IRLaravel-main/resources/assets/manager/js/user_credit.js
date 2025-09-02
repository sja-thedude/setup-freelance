function Credit() {
}

Credit.fn = {
    init: function () {
        Credit.fn.submitUpdateForm.call(this);
        Credit.fn.resetCredit.call(this);
    },

    
    submitUpdateForm: function () {
        $('.update-credit').map(function () {
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                rules: {
                    'point': {
                        onlyFullNumber: true
                    },
                },
                submitHandler: function (form) {
                    MainShared.fn.processFormByAjax(form, 'update');
                },
            });
        });
    },
    
    resetCredit: function () {
        $(document).on('click', '.reset-0', function(){
            Swal.fire({
                width: 512,
                padding: '43px 25px 30px 25px',
                title: '<span class="ir-h3">' + Lang.get('user.reset_credit') + '</span>',
                html: '<span class="ir-popup-content">' + Lang.get('user.confirm_reset_credit') + '</span>',
                showDenyButton: true,
                showCancelButton: false,
                focusConfirm: false,
                focusDeny: false,
                denyButtonText: Lang.get('user.yes_reset'),
                confirmButtonText: Lang.get('common.no_cancel'),
                showCloseButton: true,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                } else if (result.isDenied) {

                   var form = $(this).closest('form'); 
                   $('input[name=point]').val(0);
                   
                   form.submit();
                }
            });

            return false;
        });
    },

    rule: function () {
        $(document).ready(function () {
            Credit.fn.init.call(this);
        });
    },
};

Credit.fn.rule();