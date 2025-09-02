function Cookies() {
}

Cookies.fn = {
    init: function () {
        Cookies.fn.handleCookie.call(this)
    },
    handleCookie: function () {
        let isAcceptCookie = localStorage.getItem('isAcceptCookie')
        if (!isAcceptCookie) {
            $('.cookie-bar').show()
        }

        $('.cookie-bar').on('click', 'a.btn', function () {
            $('.cookie-bar').fadeOut()
            localStorage.setItem('isAcceptCookie', 1)
        })
    },
    rules: function () {
        $(document).ready(function () {
            Cookies.fn.init.call(this);
        });

    }
};

Cookies.fn.rules();





