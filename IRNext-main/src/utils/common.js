/**
 * Remove a parameter to the URL
 *
 * @link https://stackoverflow.com/a/16941754 *
 * @param {*} key
 * @param {*} sourceURL
 * @returns
 */
export function removeParam(key, sourceURL) {
    let rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (let i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

export function checkLinkBackToPortal(link) {
    const query = new URLSearchParams(window.location.search);
    const portal = query.get('portal')

    if (portal) {
        link = link + (link.includes('?') ? '&' : '?') + 'portal=' + portal
    }

    return link
}