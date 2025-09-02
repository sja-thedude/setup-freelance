@php
    $primaryColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->primary_color : null;
    $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
@endphp
<style type="text/css">
    .btn.btn-andere,
    .btn-modal,
    .user-modal .modal-content .form-line .checkbox-sex .wrap-content input:checked,
    .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td:hover div,
    .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td.xdsoft_current div, .xdsoft_option:hover,
    #m-wrapper #m-content .btn-mobile.btn-color-primary,
    .checkbox-sex.checkbox-color .wrap-content input:checked,
    .password-reset #container #main-body .step-register .custom-form-input .btn {
        background: {{$primaryColor}};
    }
    
    a:hover, a:focus,
    .btn-modal.btn-register{
        color: {{$primaryColor}}
    }
    
    .btn-modal,
    .btn-modal.btn-register,
    .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td:hover div,
    .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td.xdsoft_current div,
    .checkbox-sex.checkbox-color {
        border-color: {{$primaryColor}};
    }
    .lds-dual-ring:after {
        border-color: {{$primaryColor}} transparent {{$primaryColor}} transparent;
    }
    @media screen and (max-width: 768px) {
        #m-wrapper .wrap-mobile #wrapFillAddress .btn.btn-disable.btn-order,
        #mobile-messages-user ul li .notification-detail .icn-eye-color,
        #wrapSearchGroup .btn.btn-order.disableBtn {
            background: {{$primaryColor}};
        }
        .checkbox-sex .wrap-content input + .slider,
        .btn-pr-custom:hover{
            color: {{$primaryColor}};
        }

        #wrapSearchGroup .btn.btn-order,
        .btn-pr-custom {
            background: {{$primaryColor}}!important;
        }

        .wrap-info-map-header .wrap-table-co-2 span,
        .wrap-info-map-header .wrap-table-header h6.color {
            color: {{$secondColor}}
        }
    }
</style>