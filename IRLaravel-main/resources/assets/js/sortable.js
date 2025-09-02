function Sortable() {
}

Sortable.fn = {
    init: function() {
        Sortable.fn.main.call(this);
    },
    main: function() {
        $('.sortable-root').map(function(){
            var _this = $(this);
            
            _this.sortable({
                group: 'nested',
                handle: 'i.fa-arrows',
                onDrop: function($item, container, _super) {                
                    var itemId = $item.attr('data-id');
                    var parent = container.el.closest('li');
                    var parentId = parent.attr('data-id');
                    var token = $('#data-token').val();
                    var url = _this.attr('data-url');
                    var keys = {};
                    
                    if($('#lang-keys').length) {
                        var keys = JSON.parse($('#lang-keys').val());
                    }                    
                    
                    if(typeof parentId == 'undefined') {
                        parentId = '';
                    }

                    $('body').loading('toggle');                    
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            "_token": token,
                            "itemId": itemId,
                            "parentId": parentId
                        }
                    }).success(function (response) {
                        if (!response.success) {
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
                        if(error.statusCode == 500) {
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

                    _super($item, container);                
                }
            });
        });
    },
    rule: function() {
        $(document).ready(function() {
            Sortable.fn.init.call(this);
        });
    }
};

Sortable.fn.rule();