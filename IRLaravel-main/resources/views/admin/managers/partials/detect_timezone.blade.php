<script>
    $(document).ready(function () {
        var formName = '{{ isset($formName) ? $formName : 'register' }}';
        var fieldName = '{{ isset($fieldName) ? $timezoneName : 'timezone' }}';
        /**
         *  Get timezone from your device by moment.js
         * @link https://laracasts.com/discuss/channels/general-discussion/l5-best-way-to-get-user-timezone?page=1
         * @link https://momentjs.com/docs/
         * @link https://momentjs.com/timezone/docs/
         */
        var form = $('form[name="' + formName + '"]');
        var txtTimezone = form.find('[name="' + fieldName + '"]');
        // Get timezone by moment
        txtTimezone.val(moment.tz.guess());
    });
</script>