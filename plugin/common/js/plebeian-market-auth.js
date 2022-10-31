$ = jQuery;

let loginSetTimeout;

function buyerLoginThenCallFunction(callback) {
    if (buyerAmILoggedIn()) {
        callback(true);
    } else {
        getLoginWidget(callback);
    }
}

function adminLoginThenCallFunction(callback) {
    let additionalText =
        "<p>You need to <b>login with your Lightning wallet</b> to be able to use the <b>Plebeian Market API</b>.</p>" +
        "<p>If you've already used Plebeian Market before, use the same wallet to login now so you can use the same account.</p>";
    getLoginWidget(callback, additionalText, true);
}

function buyerGetPlebeianMarketAuthToken() {
    return Cookies.get('plebeianMarketAuthToken');
}

/**
 * Checks if the user is logged-in into the PM
 * backend and returns a boolean.
 * 
 * @returns boolean
 */
function buyerAmILoggedIn() {
    let plebeianMarketAuthToken = buyerGetPlebeianMarketAuthToken();
    console.log('imLoggedIn - plebeianMarketAuthToken: ', plebeianMarketAuthToken);

    if (!plebeianMarketAuthToken || typeof plebeianMarketAuthToken === 'undefined' || plebeianMarketAuthToken === '') {
        return false;
    } else {
        // TODO: we must check here with the server if the token is still valid (they expire)
        return true;
    }
}

function adminLogout(redirectToURL = null) {
    // Logout from Plebeian Market API
    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: 'plebeian-admin-logout'
        },
        success: function (response) {
            console.log('Logged out successfully!', response);

            // If no URL provided, just reload
            if (redirectToURL === null) {
                location.reload();
            } else {
                window.location.href = redirectToURL;
            }
        },
        error: function (error) {
            console.error("ERROR logging out from PM API: ", error);
        }
    });
}

function adminSaveAPIKey(pmAuthKey, goToUrl = null) {
    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        dataType: "JSON",
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: "plebeian-save-options",
            plebeian_market_auth_key: pmAuthKey,
        },
        success: function (response) {
            console.log('Options saved successfully!');

            if (goToUrl !== null) {
                window.location.href = goToUrl;
            }
        },
        error: function (error) {
            console.log("ERROR saving values into WordPress : ", error);

            showAlertModal('Error: ' + error.responseJSON.data.errorMessage);
        }
    });
}

function adminCheckAPIKeyIsValid(functionValid, functionInvalid) {

    if (plebeian_market_auth_key === '') {
        console.log('No auth key present');
        return false;
    }

    let pmURL = $('#pmURL').val();

    let testUrl;
    if (!pmURL || pmURL === '') {
        testUrl = requests.pm_api.user_info.url;
    } else {
        testUrl = pmURL + requests.pm_api.user_info.path;
    }

    $.ajax({
        url: testUrl,
        cache: false,
        dataType: "JSON",
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.user_info.getMethod,
        headers: { "X-Access-Token": plebeian_market_auth_key },
        success: function (response) {
            console.log('Connection successful: connection valid');
            functionValid();
        },
        error: function (e) {
            console.log("ERROR : ", e);
            functionInvalid();
        }
    });
}

/**
 * Get login widget and run callback function
 * when/if login is ok.
 * 
 * @param {function} callback 
 */
function getLoginWidget(callback, additionalText = '', adminLogin = false) {

    $.ajax({
        url: requests.pm_api.get_login_info.url,
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.get_login_info.method
    })
        .done(function (response) {
            console.log('response', response);

            let textToShowInWidget = '';

            if (additionalText !== '') {
                textToShowInWidget += additionalText;
            }

            textToShowInWidget += '<p>Scan with <a class="link text-reset" target="_blank" href="https://breez.technology/">Breez</a>, ' +
                '<a class="link text-reset" target="_blank" href="https://phoenix.acinq.co/">Phoenix</a>, ' +
                '<a class="link text-reset" target="_blank" href="https://zeusln.app/">Zeus</a>, ' +
                'or use <a class="link text-reset" target="_blank" href="https://getalby.com/">Alby</a>, ' +
                '<a class="link text-reset" target="_blank" href="https://thunderhub.io/">Thunderhub</a> ' +
                'or any <a class="link text-reset" target="_blank" href="https://github.com/fiatjaf/lnurl-rfc#lnurl-documents">' +
                'LNurl compatible wallet</a> to login into the marketplace.</p>';

            putIntoHtmlElementTextQrLnAddress('#gpModal', textToShowInWidget, response.lnurl, response.qr, 'lightning');

            showGPModal();

            checkIfLoginDone(response.k1, callback, adminLogin);
        })
        .fail(function (e) {
            console.log('Error: ', e);
        });
}

function checkIfLoginDone(k1, callback, adminLogin = false) {
    console.log('checkIfLoginDone k1:', k1);

    $.ajax({
        url: requests.pm_api.check_login.url + k1,
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.check_login.method
    })
        .done(function (response) {
            console.log('checkIfLoginDone response:', response);

            if (response.success !== true) {
                loginSetTimeout = setTimeout(function () {
                    checkIfLoginDone(k1, callback, adminLogin);
                }, 2000);

            } else {
                let authToken = response.token;
                if (authToken !== '') {
                    hideGPModal();

                    if (adminLogin) {
                        // Login in the admin area to use PM API
                        adminSaveAPIKey(authToken, pluginSetupURL);
                    } else {
                        // Login a customer while trying to buy a product
                        Cookies.set('plebeianMarketAuthToken', authToken);
                        callback(true);
                    }

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
