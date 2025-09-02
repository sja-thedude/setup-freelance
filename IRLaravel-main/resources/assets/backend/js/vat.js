function VAT() {
}

VAT.fn = {
    init: function () {
        VAT.fn.submitCreateFormVAT.call(this);
        VAT.fn.changeCountry.call(this);
        VAT.fn.addMore.call(this);
        VAT.fn.deleteVat.call(this);
        VAT.fn.checkEnableSubmit.call(this);
        VAT.fn.handleEditVat.call(this);
    },

    handleEditVat: function(){
        $(document).on('click', '.edit-vat', function(){
            var form = $(this).closest('form');
            VAT.fn.clearReadOnly(form);
        });
    },

    setReadOnly: function(form){
        form.find('input[type="number"]').attr('readonly', true);
    },

    clearReadOnly: function(form){
        form.find('input[type="number"]').removeAttr('readonly');
    },

    checkEnableSubmit: function() {
        $(document).on('click keyup', '.enable-submit', function(){
            $('.vat-submit').find('button[type="submit"]').removeAttr('disabled');
        });
    },
    
    deleteVat: function(){
        $(document).on('click', '.remove-vat', function() {
            $(this).closest('.list-item-none').remove();
        });
    },
    
    addMore: function(){
        $(document).on('click', '.ir-add-more', function(){
            var defaultVatRow = $('.vat-default-row');
            var body = $('.vat-form .list-body');
            var lastRow = body.find('.list-item-none').last();
            var lastNumber = lastRow.data('number');
            var newNumber = lastNumber + 1;
            var newRow = $('<div class="list-item-none row" data-number="'+ newNumber +'"></div>');
            var submit = $(this).closest('form').find('[type="submit"]');

            newRow.append(defaultVatRow.html());
            newRow.find('[name="vat_id"]').attr('name', 'vat['+ newNumber +'][id]');
            newRow.find('[name="vat_name"]').attr('name', 'vat['+ newNumber +'][name]');
            newRow.find('[name="vat_take_out"]').attr('name', 'vat['+ newNumber +'][take_out]');
            newRow.find('[name="vat_delivery"]').attr('name', 'vat['+ newNumber +'][delivery]');
            newRow.find('[name="vat_in_house"]').attr('name', 'vat['+ newNumber +'][in_house]');
            body.append(newRow);

            submit.removeAttr('disabled');
        });
    },
    
    autoSubmitVat: function() {
        var _this = $(this);
        var form = _this.closest('form');
        
        VAT.fn.formVat.call(this, form);
    },
    
    formVat: function(selfForm){
        if (selfForm.valid()) {
            var url = selfForm.attr('action');
            var method = selfForm.attr('method');
            var submit = selfForm.find('[type="submit"]');
            var data = selfForm.serializeArray();
            
            $('body').loading('toggle');
            
            if(submit.length) {
                submit.removeAttr('disabled');
            }

            $.ajax({
                url: url,
                type: method,
                data: data
            }).success(function (response) {
                if(response.success == true) {
                    $('.edit-vat').show();
                    $('.vat-submit').hide();
                    $('.ir-add-more').hide();
                    $('.vat-form .list-body').empty().append(response.data.view);

                    MainShared.fn.formatNumberDecimal();
                    VAT.fn.setReadOnly(selfForm);
                    
                    // Swal.fire({
                    //     title: '<span class="ir-popup-title">' + Lang.get('vat.title') + '</span>',
                    //     html: '<span class="ir-popup-content">' + response.message + '</span>',
                    //     width: 512,
                    //     padding: '43px 60px 30px 60px',
                    //     showConfirmButton: false,
                    //     showCloseButton: true,
                    //     showCancelButton: true,
                    //     cancelButtonText: Lang.get('common.close')
                    // });
                } else {
                    if(response.data) {
                        MainShared.fn.showErrorMessages(response, selfForm, true);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showErrorMessages(response, selfForm, true);
                }
            }).always(function() {
                submit.attr('disabled', 'disabled');
                $('body').loading('toggle');
            });
        }
    },
    
    submitCreateFormVAT: function() {
        $('.vat-form').map(function(){
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                rules: {
                    'name[]': {
                        customRequired: true,
                    },
                    'take_out[]': {
                        customRequired: true,
                    },
                    'delivery[]': {
                        customRequired: true,
                    },
                    'in_house[]': {
                        customRequired: true,
                    }
                },
                submitHandler: function(form) {
                    VAT.fn.formVat.call(this, $(form));
                }
            });
        });
    },
    
    changeCountry: function(){
        $(document).on('change', '.vat-country', function(){
            var _this = $(this);
            var url = _this.data('route');
            var countryId = _this.val();
            var form = _this.closest('form');
            var submit = form.find('[type="submit"]');

            $('body').loading('toggle');
            submit.removeAttr('disabled');

            $.ajax({
                url: url,
                type: 'GET',
                data: {country_id: countryId}
            }).success(function (response) {
                if(response.success == true) {
                    $('.edit-vat').show();
                    $('.vat-submit').hide();
                    $('.ir-add-more').hide();
                    $('.vat-form .list-body').empty().append(response.data.view);

                    VAT.fn.setReadOnly(form);
                } else {
                    if(response.data) {
                        MainShared.fn.showErrorMessages(response, selfForm, true);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showErrorMessages(response, selfForm, true);
                }
            }).always(function() {
                submit.attr('disabled', 'disabled');
                $('body').loading('toggle');
            }); 
        });
    },

    rule: function () {
        $(document).ready(function () {
            VAT.fn.init.call(this);
        });
    },
};

VAT.fn.rule();