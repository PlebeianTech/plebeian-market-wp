let loadingModal, gpModal;

function contributionUpdated() {
    let contributionPercent = $('#contribution_percent').val();
    $('#contribution_percent_text').html('Contributing ' + contributionPercent + '%');

    let contributionEmoji = $('#contributionEmoji')[0];

    switch (contributionPercent) {
        case '0':
            $(contributionEmoji).text('💩');
            break;
        case '0.5':
        case '1':
        case '1.5':
            $(contributionEmoji).text('😥');
            break;
        case '2':
        case '2.5':
            $(contributionEmoji).text('😕');
            break;
        case '3':
        case '3.5':
            $(contributionEmoji).text('😐');
            break;
        case '4':
        case '4.5':
            $(contributionEmoji).text('🙂');
            break;
        case '5':
            $(contributionEmoji).text('😍');
            break;
    }
}

function showSavedForAMoment(badgeId, timeMillis) {
    if (typeof badgeId !== 'undefined') {
        $('#' + badgeId).fadeIn(200);

        setTimeout(function () {
            $('#' + badgeId).fadeOut();

            if (adminKey === '') {
                location.reload();
            }
        }, timeMillis);
    }
}

function updateUserInfo(newUserData, placeToFlashIfSuccessful) {

    $("#saveUserOptions").prop("disabled", true);

    let pmAuthKey = $('#pmAuthKey').val();
    let pmURL = $('#pmURL').val();

    let saveURL;
    if (!pmURL || pmURL === '') {
        saveURL = requestHostname + requestURL;
    } else {
        saveURL = pmURL + requestURL
    }

    $.ajax({
        url: saveURL,
        data: JSON.stringify(newUserData),
        cache: false,
        dataType: "JSON",
        contentType: 'application/json;charset=UTF-8',
        type: setRequestMethod,
        headers: { "X-Access-Token": pmAuthKey },
        success: function (response) {
            console.log('User updated successfully!');

            $("#saveUserOptions").prop("disabled", false);

            showSavedForAMoment(placeToFlashIfSuccessful, 2500);
        },
        error: function (error) {
            console.log("ERROR : ", error);

            showAlertModal(error.responseJSON.message);
            $("#saveUserOptions").prop("disabled", false);
        }
    });
}

function loginValid() {
    console.log('loginValid - Nothing needs to be done');
}
function loginInvalid() {
    console.log('loginInvalid - Logout and reload');
    adminLogout(setupURLWithoutLogin);
}

$(document).ready(function () {
    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });

    if (adminKey === '') {
        // We don't have the token, so lets login/register
        adminLoginThenCallFunction();
        return;

    } else {
        // We have the token, but we need to test if it's still valid
        adminCheckAPIKeyIsValid(loginValid, loginInvalid);
    }

    $("#logoutButton").click(function () {
        console.log("Logout button clicked");
        adminLogout(adminURLWithLogin);
    });

    //This would be called if any of the input element has got a change inside the form
    $('#setupForm input').on('input', function () {
        $('#saveUserOptions').prop('disabled', true);
    });

    // Bootstrap 5 form validation
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()

    $('#testConnectionAndParams').click(function (e) {
        e.preventDefault();

        let form = $('#setupForm')[0];
        let validity = form.checkValidity();
        form.classList.add('was-validated');

        let xpubNewValue = $('#xpubKey').val();
        let pmAuthKey = $('#pmAuthKey').val();
        let pmURL = $('#pmURL').val();
        let contribution_percent_text = $('#contribution_percent_text').val();

        if (xpubNewValue === '') {
            showAlertModal('You need to provide an XPUB to be able to sell items in Plebeian Market.');
            return;
        }
        if (!(xpubNewValue.startsWith("xpub") || xpubNewValue.startsWith("ypub") || xpubNewValue.startsWith("zpub"))) {
            showAlertModal('This is not a valid XPUB. A valid XPUB must start with "xpub", "ypub" or "zpub".');
            return;
        }
        if (pmAuthKey === '') {
            showAlertModal('You need to provide the authentication key to connect with Plebeian Market. You can find the key in your admin panel.');
            return;
        }

        let testUrl;
        if (!pmURL || pmURL === '') {
            testUrl = requestHostname + requestURL;
        } else {
            testUrl = pmURL + requestURL
        }

        // Test connection by getting user options from PM
        $.ajax({
            url: testUrl,
            cache: false,
            dataType: "JSON",
            contentType: 'application/json;charset=UTF-8',
            type: getRequestMethod,
            headers: { "X-Access-Token": pmAuthKey },
            success: function (response) {
                $('#saveUserOptions').prop('disabled', false);
                showAlertModal('Connection successful! You can now click the Save button.');
            },
            error: function (e) {
                console.log("ERROR : ", e);
                showAlertModal('Connection error. Review connection params and try again.');
            }
        });

    });

    $('#saveUserOptions').click(function () {
        let form = $('#setupForm')[0];
        let validity = form.checkValidity();
        form.classList.add('was-validated');

        if (validity) {
            // 1/2 - Send new values to PM through the API
            let contributionNewValue = $('#contribution_percent').val();
            let xpubNewValue = $('#xpubKey').val();
            let sellerEmail = $('#sellerEmail').val();

            updateUserInfo(
                {
                    contribution_percent: contributionNewValue,
                    xpub: xpubNewValue,
                    sellerEmail: sellerEmail
                },
                'savedContribution'
            );

            // 2/2 - Save values into WordPress Options locally
            let pmAuthKey = $('#pmAuthKey').val();
            let pmUrlConnect = $('#pmURL').val();

            $.ajax({
                url: requests.wordpress_pm_api.ajax_url,
                cache: false,
                dataType: "JSON",
                type: 'POST',
                data: {
                    _ajax_nonce: requests.wordpress_pm_api.nonce,
                    action: "plebeian-save-options",
                    plebeian_market_auth_key: pmAuthKey,
                    plebeian_market_url_connect: pmUrlConnect
                },
                success: function (response) {
                    console.log('Options saved successfully!');

                    //$("#saveUserOptions").prop("disabled", false);
                    //showSavedForAMoment(placeToFlashIfSuccessful, 2500);
                },
                error: function (error) {
                    console.log("ERROR saving values into WordPress : ", error);

                    showAlertModal('Error: ' + error.responseJSON.data.errorMessage);

                    //showAlertModal(error.responseJSON.message)
                    //$("#saveUserOptions").prop("disabled", false);
                }
            });
        }
    });


    // Get connection options from WP. If successful, get options from PM.
    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: "plebeian-load-options",
        },
        success: function (response) {
            console.log('Options loaded successfully!', response);

            let plebeian_market_auth_key = response.data.plebeian_market_auth_key;
            let plebeian_market_url_connect = response.data.plebeian_market_url_connect;

            if (response.success === true && plebeian_market_auth_key !== false) {

                if (plebeian_market_auth_key !== false) {
                    $('#pmAuthKey').val(plebeian_market_auth_key);
                }
                if (plebeian_market_url_connect !== false) {
                    $('#pmURL').val(plebeian_market_url_connect);
                }

                // Get user options from PM if I have the auth key only
                if (plebeian_market_auth_key !== '') {
                    $.ajax({
                        url: requestHostname + requestURL,
                        cache: false,
                        dataType: "JSON",
                        contentType: 'application/json;charset=UTF-8',
                        type: getRequestMethod,
                        headers: { "X-Access-Token": plebeian_market_auth_key },
                        success: function (response) {
                            let user = response.user;
                            // console.log('user', user);

                            let xpub = user.xpub;
                            let contribution_percent = user.contribution_percent;
                            let sellerEmail = user.sellerEmail;

                            if (xpub) {
                                $('#xpubKey').val(xpub);
                            }

                            if (contribution_percent) {
                                $('#contribution_percent').val(contribution_percent);
                                contributionUpdated();
                            }

                            $('#sellerEmail').val(sellerEmail);
                        },
                        error: function (e) {
                            console.log("ERROR: " + e.statusText, e);
                            // Don't use an AlertModal here because it will fire first time we use the plugin
                        }
                    });
                }

            } else {
                if (plebeian_market_auth_key !== false) {
                    showAlertModal('Error: ' + 'There was an error while loading options. Please contact Plebeian Market support.');
                }
            }

            //$("#saveUserOptions").prop("disabled", false);
            //showSavedForAMoment(placeToFlashIfSuccessful, 2500);
        },
        error: function (error) {
            console.log("ERROR loading values from WordPress : ", error);

            // showAlertModal('Error: ' + error.responseJSON.data.errorMessage);

            //showAlertModal(error.responseJSON.message)
            //$("#saveUserOptions").prop("disabled", false);
        }
    });
});