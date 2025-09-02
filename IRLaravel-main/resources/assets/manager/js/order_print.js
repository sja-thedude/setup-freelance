function OrderPrint() {
}

OrderPrint.fn = {
    init: function () {
        OrderPrint.fn.printItem.call(this);
        OrderPrint.fn.printMultiple.call(this);
    },

    printItem: function() {
        $(document).on('click', '.print-item', function(){
            $('body').loading('toggle');

            var _this = $(this);
            var type = _this.data('type');
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var timezone = moment.tz.guess();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: _token,
                    timezone: timezone
                },
                dataType: 'json',
            }).success(function (response) {
                if (response.success == true) {
                    if(type == 'a4') {
                        OrderPrint.fn.printElem.call(this, '#print-preview-area', response.data.view);
                    }
                }
            }).always(function () {
                $('body').loading('toggle');
            });
        });
    },

    printMultiple: function() {
        $(document).on('click', '.print-multi', function(){
            $('body').loading('toggle');

            var _this = $(this);
            var type = _this.data('type');
            var form = _this.closest('form');
            var url = _this.data('url');
            var formData = new FormData(form[0]);

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false
            }).done(function(response) {
                if (response.success == true) {
                    if(type == 'a4') {
                        OrderPrint.fn.printElem.call(this, '#print-preview-area', response.data.view);
                    }
                }
            }).fail(function(xhr) {
                console.log('error', xhr);
            }).always(function () {
                $('body').loading('toggle');
            });
        });
    },

    printElem: function(element, html) {
        var view = $(element).find('#print-preview-main');
        view.empty().append(html);
        view.print({mediaPrint: true});
    },

    rule: function () {
        $(document).ready(function () {
            OrderPrint.fn.init.call(this);
        });
    },
};

OrderPrint.fn.rule();