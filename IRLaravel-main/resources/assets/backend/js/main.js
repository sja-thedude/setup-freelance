function MainBackend() {
}

MainBackend.fn = {
    init: function () {
    },

    rule: function () {
        $(document).ready(function () {
            MainBackend.fn.init.call(this);
        });
    },
};

MainBackend.fn.rule();