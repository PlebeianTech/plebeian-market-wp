function getBuyNowInfo(key) {
    return new Promise(function (resolve, reject) {
        if (typeof key === 'undefined' || key === '') {
            console.log('getBuyNowItemInfo - I cannot get the info of the buynow item: ', key);
            reject();
        }

        $.ajax({
            url: requests.pm_api.buynow.get.url.replace('{KEY}', key),
            timeout: requests.pm_api.default_timeout,
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: requests.pm_api.buynow.get.method,
            headers: { "X-Access-Token": customerGetPlebeianMarketAuthToken() },
        })
        .done(function (response) {
            console.log('response', response);
            resolve(response.listing);
        })
        .fail(function (e) {
            console.log('Error: ', e);
            reject(e);
        });
    });
}
