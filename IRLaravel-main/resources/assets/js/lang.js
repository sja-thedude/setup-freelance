function LangConfiguration() {
}

LangConfiguration.fn = {
    init: function () {
        Lang.setLocale(defaultLang);
    },

    rule: function () {
        $(document).ready(function () {
            LangConfiguration.fn.init.call(this);
        });
    },
};

LangConfiguration.fn.rule();