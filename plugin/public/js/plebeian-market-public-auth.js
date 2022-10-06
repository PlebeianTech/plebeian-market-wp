let $ = jQuery;

let loginSetTimeout;

function loginThenCallFunction(callback) {
    if (amILoggedIn()) {
        callback(true);
    } else {
        getLoginWidget(callback);
    }
}

function getPlebeianMarketAuthToken() {
    return Cookies.get('plebeianMarketAuthToken');
}

/**
 * Checks if the user is logged-in into the PM
 * backend and returns a boolean.
 * 
 * @returns boolean
 */
function amILoggedIn() {
    let plebeianMarketAuthToken = getPlebeianMarketAuthToken();
    console.log('imLoggedIn - plebeianMarketAuthToken: ', plebeianMarketAuthToken);

    if (!plebeianMarketAuthToken || typeof plebeianMarketAuthToken === 'undefined' || plebeianMarketAuthToken === '') {
        return false;
    } else {
        // TODO: we must check here with the server if the user is authenticated with that token
        return true;
    }
}

/**
 * Get login widget and run callback function
 * when login is ok.
 * 
 * @param {function} callback 
 */
function getLoginWidget(callback) {

    $.ajax({
        url: requests.pm_api.get_login_info.url,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.get_login_info.method
    })
        .done(function (response) {
            console.log('response', response);

            let textToShowInWidget = '<p>Scan with <a class="link" target="_blank" href="https://breez.technology/">Breez</a>, ' +
            '<a class="link" target="_blank" href="https://phoenix.acinq.co/">Phoenix</a>, ' +
            '<a class="link" target="_blank" href="https://zeusln.app/">Zeus</a>, ' +
            'or use <a class="link" target="_blank" href="https://getalby.com/">Alby</a>, ' +
            '<a class="link" target="_blank" href="https://thunderhub.io/">Thunderhub</a> ' +
            'or any <a class="link" target="_blank" href="https://github.com/fiatjaf/lnurl-rfc#lnurl-documents">' +
            'LNurl compatible wallet</a> to login into the marketplace.</p>';

            putIntoHtmlElementTextQrLnAddress('#gpModal', textToShowInWidget, response.lnurl, response.qr);

            const myModal = new bootstrap.Modal('#gpModal', { keyboard: true });
            myModal.show();

            checkIfLoginDone(response.k1, callback);
        })
        .fail(function (e) {
            console.log('Error: ', e);
        });
}

function checkIfLoginDone(k1, callback) {
    console.log('checkIfLoginDone k1:', k1);

    $.ajax({
        url: requests.pm_api.check_login.url + k1,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.check_login.method
    })
        .done(function (response) {
            console.log('checkIfLoginDone response:', response);

            if (response.success !== true) {
                loginSetTimeout = setTimeout(function(){
                    checkIfLoginDone(k1, callback);
                }, 2000);
            } else {
                let authToken = response.token;
                if (authToken !== '') {
                    Cookies.set('plebeianMarketAuthToken', authToken);
                    callback(true);
                } else {
                    console.log("plebeianMarketAuthToken not set - response: ", response);
                }
            }
        })
        .fail(function (e) {
            console.log('Error: ', e);
            console.log('Error message: ', e.message);
        })
        .always(function () {

        });
}
function stopChecking() {
    clearTimeout(loginSetTimeout);
}

$(document).ready(function () {
    $('#closeGPModal').click(function () {
        stopChecking();
    });
});
