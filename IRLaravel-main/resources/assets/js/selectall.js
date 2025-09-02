function SelectAll() {
}

SelectAll.fn = {
    init: function() {
        SelectAll.fn.handle.call(this);
    },
    handle: function() {
        $(document).on('click', '[data-action="select-action"]', function(){
            var _this = $(this);
            var root = _this.closest('[data-id="root-select"]');
            var contentArea = root.find('[data-id="select-content"]');   
            var type = _this.attr('data-id');
            
            if(type == 'select') {
                contentArea.find('[data-id="item-select"]').map(function(){
                    $(this).prop("checked", true);
                });
            } else {
                contentArea.find('[data-id="item-select"]').map(function(){
                    $(this).prop("checked", false);
                });                
            }
        });
    },
    rule: function() {
        $(document).ready(function() {
            SelectAll.fn.init.call(this);
        });
    }
};

SelectAll.fn.rule();