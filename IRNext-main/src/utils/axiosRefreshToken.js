'use client'

import Cookies from 'js-cookie';

/**
 * Set cookie 'loggedToken' with value 'token'
 * @param token
 */
const handleLoginToken = function (token) {
    const expires = new Date();
    expires.setMonth(expires.getMonth() + 1); // survive for 1 month
    let cookiesOptions = {
        expires: expires,
        domain: window.location.hostname.split('.').slice(-2).join('.')
    };

    Cookies.set('loggedToken', token, cookiesOptions);
}

/**
 * Remove cookie 'loggedToken'
 */
const handleLogoutToken = function () {
    Cookies.remove('loggedToken');
    let cookiesOptions = {
        domain: window.location.hostname.split('.').slice(-2).join('.')
    };
    Cookies.remove('loggedToken', cookiesOptions);
}

export {handleLoginToken, handleLogoutToken}
