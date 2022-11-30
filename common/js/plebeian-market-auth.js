let continueListeningForLoginSignal = false;

async function buyerIsLoggedInOrDoLogin(adminLogin = false, additionalText = '') {
    return new Promise(async function (resolve, reject) {
        if (buyerAmILoggedIn()) {
            resolve();
        } else {
            await tryLogin()
                .then(function () {
                    resolve();
                })
                .catch(function () {
                    reject();
                });
        }
    });
}

async function tryLogin(additionalText = '', adminLogin = false) {
    return new Promise(async function (resolve, reject) {
        await getLoginWidget(additionalText)
            .then(async function (k1) {
                continueListeningForLoginSignal = true;

                do {
                    await checkIfLoginDone(k1, adminLogin)
                        .then(function () {
                            continueListeningForLoginSignal = false;
                            resolve();
                        })
                        .catch(function () {
                            // Not logged in yet, be we must catch the reject here
                        });

                    await new Promise(r => setTimeout(r, 2000));
                }
                while (continueListeningForLoginSignal);

                reject();
            })
            .catch(function (e) {
                console.log('Error while trying to login:', e);
                reject();
            });
    });
}

/**
 * Get login widget
 */
function getLoginWidget(additionalText = '') {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: requests.pm_api.get_login_info.url,
            timeout: requests.pm_api.default_timeout,
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: requests.pm_api.get_login_info.method,
            success: function (loginResponse) {
                let textToShowInWidget = '';
                textToShowInWidget += additionalText;

                textToShowInWidget += '<p>Scan with <a class="link text-reset" target="_blank" href="https://breez.technology/">Breez</a>, ' +
                    '<a class="link text-reset" target="_blank" href="https://phoenix.acinq.co/">Phoenix</a>, ' +
                    '<a class="link text-reset" target="_blank" href="https://zeusln.app/">Zeus</a>, ' +
                    'or use <a class="link text-reset" target="_blank" href="https://getalby.com/">Alby</a>, ' +
                    '<a class="link text-reset" target="_blank" href="https://thunderhub.io/">Thunderhub</a> ' +
                    'or any <a class="link text-reset" target="_blank" href="https://github.com/fiatjaf/lnurl-rfc#lnurl-documents">' +
                    'LNurl compatible wallet</a> to login into the marketplace.</p>';

                putIntoHtmlElementTextQrLnAddress('#gpModal', textToShowInWidget, loginResponse.lnurl, loginResponse.qr, 'lightning');

                showGPModal();

                resolve(loginResponse.k1);
            },
            error: function (error) {
                console.log("ERROR getting information: ", error);
                reject();
            }
        });
    });
}

function checkIfLoginDone(k1, adminLogin) {
    console.log('checkIfLoginDone k1:', k1);

    return new Promise(function (resolve, reject) {
        $.ajax({
            url: requests.pm_api.check_login.url + k1,
            timeout: requests.pm_api.default_timeout,
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: requests.pm_api.check_login.method,
            success: function (data) {
                if (data.success === true) {
                    let authToken = data.token;
                    if (authToken !== '') {
                        hideGPModal();

                        if (adminLogin) {
                            // Login in the admin area to use PM API
                            adminSaveAPIKey(authToken, pluginSetupURL);
                        } else {
                            // Login a customer while trying to buy a product
                            Cookies.set('plebeianMarketAuthToken', authToken);
                        }

                        resolve();
                    } else {
                        console.log("plebeianMarketAuthToken not set - response: ", response);
                        reject();
                    }
                } else {
                    reject();
                }
            },
            error: function (error) {
                console.log("ERROR getting information: ", error);
                reject();
            }
        });
    });
}

function customerGetPlebeianMarketAuthToken() {
    return Cookies.get('plebeianMarketAuthToken');
}

/**
 * Checks if the user is logged-in into the PM
 * backend and returns a boolean.
 * 
 * @returns boolean
 */
function buyerAmILoggedIn() {
    let plebeianMarketAuthToken = customerGetPlebeianMarketAuthToken();

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

$(document).ready(function () {
    $('#closeGPModal').click(function () {
        continueListeningForLoginSignal = false;
    });
});
