let buynow_product_buying_key;
let buynow_product_buying_currentStep;

let buyNowSetTimeout;

function waitAndAskAgain() {
    loginSetTimeout = setTimeout(function () {
        buyNow(false);
    }, 4000);
}

function buyNow(shouldShowLoadingModal) {
    if (shouldShowLoadingModal) {
        showLoadingModal();
    }

    getBuyNowItemInfo(
        buynow_product_buying_key,
        function (buynowInfo) {
            console.log('buynowInfo', buynowInfo);
            let sales = buynowInfo.sales;

            if (!sales || typeof sales === 'undefined' || sales === null) {
                step0();    // No sales or no active sales, so let's create one
                waitAndAskAgain();

            } else {
                // We have sales. In what state?

                let oneSaleIsValid = false;

                sales.every(function (sale) {
                    if (sale.state !== 'EXPIRED') {
                        console.log('   - Sale not expired state=' + sale.state, sale);

                        oneSaleIsValid = true;

                        if (['REQUESTED'].includes(sale.state)) {
                            // Show QR for contribution
                            step1(sale);
                            waitAndAskAgain();
                            return false;   // break for "every"

                        } else if (['CONTRIBUTION_SETTLED'].includes(sale.state)) {
                            // Contribution paid. Show BTC QR.
                            step2(sale);
                            waitAndAskAgain();
                            return false;   // break for "every"

                        } else if (['TX_DETECTED'].includes(sale.state)) {
                            // Contribution paid
                            step3(sale);
                            waitAndAskAgain();
                            return false;   // break for "every"

                        } else if (['TX_CONFIRMED'].includes(sale.state)) {
                            // Contribution paid
                            step4(sale);
                            return false;   // break for "every"

                        } else {
                            console.log('--------------------------------------------------------------------');
                            console.log('Status not known yet: ' + sale.state, sale);
                            console.log('--------------------------------------------------------------------');
                        }
                    }

                    return true;    // continue for "every"
                });

                if (!oneSaleIsValid) {
                    // We had sales, but none is valid, so let's create one
                    // and keep checking
                    console.log("   - We don't have any valid sale, so requesting a new one...", sales);

                    step0();
                    waitAndAskAgain();
                }
            }
        }
    );
}

function step0() {
    if (buynow_product_buying_currentStep === 0) {
        console.log("We're already at step 0");
        return;
    }

    console.log("Running step 0 !", buynow_product_buying_key);
    buynow_product_buying_currentStep = 0;

    // We don't yet have a sale, so lets start one
    $.ajax({
        url: requests.pm_api.buynow_buy.url.replace('{KEY}', buynow_product_buying_key),
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.buynow_buy.method,
        headers: { "X-Access-Token": buyerGetPlebeianMarketAuthToken() },
    })
        .done(function (response) {
            console.log('step0 response:', response);
        })
        .fail(function (e) {
            console.log('step0 error: ', e);
            let errorMessage = e.responseJSON.message;

            if (errorMessage === 'Invalid token.') {
                Cookies.remove('plebeianMarketAuthToken');
            }
        });
}

function step1(sale) {
    if (buynow_product_buying_currentStep === 1) {
        console.log("We're already at step 1");
        return;
    }

    console.log("Running step 1 !", buynow_product_buying_key);
    buynow_product_buying_currentStep = 1;

    let content = $('#gpModal .modal-body');
    content.html('<p>Loading...</p>');

    if (typeof sale === 'object') {
        // We're showing the same information of the last non-expired sale
        console.log('   ---- We have a sale', sale);

        let textToShowInWidget =
            '<p class="fs-4 text-center">The seller wishes to donate ' + sale.contribution_amount +
            ' sats (' + satsToBTC(sale.contribution_amount) + ' BTC / ~$' + satsToFiat(sale.contribution_amount) +
            ') out of the total price to Plebeian Market. ' +
            'Please send the amount using the QR code below!</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            textToShowInWidget,
            sale.contribution_payment_request,
            sale.contribution_payment_qr,
            'lightning',
            'Step 1/3 - Contribution',
            true
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step1 - sale is not an object:', typeof sale);
    }
}

function step2(sale) {
    if (buynow_product_buying_currentStep === 2) {
        console.log("We're already at step 2");
        return;
    }

    if (typeof sale === 'object') {
        console.log("Running step 2 (" + buynow_product_buying_key + ")! - We have a sale:", sale);
        buynow_product_buying_currentStep = 2;

        let textToShowInWidget =
            '<p class="fs-3 text-center">Please send the remaining amount of ' + sale.amount + ' sats ' +
            '(' + satsToBTC(sale.amount) + ' BTC / ~$' + satsToFiat(sale.amount) + ') plus shipping ' +
            'directly to the seller!</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            textToShowInWidget,
            sale.address,
            sale.address_qr,
            'bitcoin',
            'Step 2/3 - Payment',
            true
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step2 - sale is not an object:', typeof sale);
    }
}

function step3(sale) {
    if (buynow_product_buying_currentStep === 3) {
        console.log("We're already at step 3");
        return;
    }

    if (typeof sale === 'object') {
        console.log("Running step 3 (" + buynow_product_buying_key + ")! - We have a sale:", sale);
        buynow_product_buying_currentStep = 3;

        let textToShowInWidget =
            '<p class="fs-2">Thank you for your payment!</p>' +
            '<div class="mb-4">' +
            '   <div class="w-100">' +
            '       <img src="' + pluginBasePath + 'common/plebeian_market_logo.png">' +
            '   </div>' +
            '   <p class="fs-4">TxID: <a class="link" target="_blank" href="https://mempool.space/tx/' + sale.txid + '">' + sale.txid + '</a>. Save it as a purchase receipt.</p>' +
            '</div>' +
            '<p class="fs-2">Your purchase will be completed when the payment is confirmed by the network.</p>' +
            '<p class="fs-2">In the mean time, you can follow the transaction on <a class="link" target="_blank" href="https://mempool.space/tx/' + sale.txid + '">mempool.space</a>!</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            textToShowInWidget,
            null,
            null,
            null,
            'Step 3/3 - Confirmation',
            true
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step3 - sale is not an object:', typeof sale);
    }
}

function step4(sale) {
    if (buynow_product_buying_currentStep === 4) {
        console.log("We're already at step 4");
        return;
    }

    if (typeof sale === 'object') {
        console.log("Running step 4 (" + buynow_product_buying_key + ")! - We have a sale:", sale);
        buynow_product_buying_currentStep = 4;

        let sellerEmail = sale.seller.seller_email;
        console.log('---------- EMAIL: ', sellerEmail);
        sellerEmail = 'aaa@bbb.com';


        let textToShowInWidget =
            '<p class="text-center fs-2">Payment confirmed!</p>' +
            '<p class="text-center fs-2">Please <a target="_blank" href="mailto:' + sellerEmail + '" class="link">contact the seller</a> directly to discuss shipping.</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            textToShowInWidget,
            null,
            null,
            null,
            'Step 3/3 - Confirmation'
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step4 - sale is not an object:', typeof sale);
    }
}

function getBuyNowItemInfo(key, callback) {
    if (typeof key === 'undefined' || key == '') {
        console.log('getBuyNowItemInfo - I cannot get the info of the buynow item: ', key);
        return null;
    }

    $.ajax({
        url: requests.pm_api.buynow_get.url.replace('{KEY}', key),
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.buynow_get.method,
        headers: { "X-Access-Token": buyerGetPlebeianMarketAuthToken() },
    })
        .done(function (response) {
            console.log('response', response);
            callback(response.listing);
        })
        .fail(function (e) {
            console.log('Error: ', e);
            console.log('Error message: ', e.message);
        });
}
