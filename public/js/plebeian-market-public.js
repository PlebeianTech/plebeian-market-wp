let loadingModal, gpModal;

let buynow_product_buying_currentStep;
let buynow_product_buying_key;

let buynowRefreshSetTimeout;

function convertAllBtcToFiat() {
    $('.btc-to-fiat-source').each(function () {
        let satsPrice = this.value;

        if (!$.isNumeric(satsPrice)) {
            satsPrice = $(this).html();
        }

        let destination = $(this).parent().find('.fiatDestination')[0];

        if (!$.isNumeric(satsPrice)) {
            $(destination).text('-');
            return;
        }

        let fiat = satsPrice * btcPriceInUSD / 100000000;

        if (parseInt(fiat) < 1) {
            fiat = parseFloat(fiat).toFixed(4);
        } else if (parseInt(fiat) >= 1 && parseInt(fiat) < 1000) {
            fiat = parseFloat(fiat).toFixed(2);
        } else {
            fiat = parseFloat(fiat).toFixed(0);
        }

        $(destination).text('~$' + fiat);
    });
}

$(document).ready(function () {
    updateAuctionsPeriodically();

    $('.btn-buynow').click(async function () {
        if (buynow_product_buying_key !== $(this).data('key')) {
            buynow_product_buying_key = $(this).data('key');
            buynow_product_buying_currentStep = null;
        }

        console.log('Buy now: ', buynow_product_buying_key);

        await buyerIsLoggedInOrDoLogin()
            .then(function () {
                buyNow();
            })
            .catch(function (e) {
                console.log('Not showing buynow dialog because there was a problem:', e);
            });
    });

    $('.btn-bidnow').click(async function () {
        let key = $(this).data('key');

        await buyerIsLoggedInOrDoLogin()
            .then(function () {
                numBidsLastTimeWeLook = 9999;    // Reset
                showBidsExtendedInfo(key);
            })
            .catch(function (e) {
                console.log('Not showing bid dialog because there was a problem:', e);
            });
    });

    $('#closeGPModal').click(function () {
        stopSetTimeout();
        buynow_product_buying_currentStep = null;
    });

    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });
});

function stopSetTimeout() {
    clearTimeout(buynowRefreshSetTimeout);
}

function waitAndAskAgain() {
    buynowRefreshSetTimeout = setTimeout(function () {
        buyNow(false);
    }, 3000);
}

async function buyNow(shouldShowLoadingModal) {
    if (shouldShowLoadingModal) {
        showLoadingModal();
    }

    await getBuyNowInfo(buynow_product_buying_key)
    .then(async function (buynowInfo) {
        console.log('buynowInfo', buynowInfo);
        let sales = buynowInfo.sales;

        if (!sales || typeof sales === 'undefined') {
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
                        purchaseStep1(sale);
                    } else if (['CONTRIBUTION_SETTLED'].includes(sale.state)) {
                        // Contribution paid. Show BTC QR.
                        purchaseStep2(sale);
                    } else if (['TX_DETECTED'].includes(sale.state)) {
                        // Contribution paid
                        purchaseStep3(sale);
                    } else if (['TX_CONFIRMED'].includes(sale.state)) {
                        // Contribution paid
                        purchaseStep4(sale);
                    } else {
                        console.log('--------------------------------------------------------------------');
                        console.log('Status not known yet: ' + sale.state, sale);
                        console.log('--------------------------------------------------------------------');
                    }

                    if (!['TX_CONFIRMED'].includes(sale.state)) {
                        waitAndAskAgain();
                    }

                    return false;   // break for "every"
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
    })
    .catch(function (e) {
        console.log('Error while trying to login:', e);
        //reject();
    });
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
        url: requests.pm_api.buynow.buy.url.replace('{KEY}', buynow_product_buying_key),
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.buynow.buy.method,
        headers: { "X-Access-Token": customerGetPlebeianMarketAuthToken() },
    })
    .done(function (response) {
        console.log('step0 response:', response);
    })
    .fail(function (e) {
        console.log('step0 error: ', e);

        stopSetTimeout();

        let errorMessage = e.responseJSON?.message ?? 'Unknown error';

        if (errorMessage === 'Listing not active.') {
            hideLoadingModal();
            showAlertModal('The item is not yet active, so you cannot buy it now.');
        }

        if (errorMessage === 'Invalid token.') {
            Cookies.remove('plebeianMarketAuthToken');
        }
    });
}

function purchaseStep1(sale) {
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
            'buynowStep1',
            textToShowInWidget,
            sale.contribution_payment_request,
            [sale.contribution_payment_qr],
            'lightning',
            'Step 1/3 - Contribution',
            true
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step1 - sale is not an object:', typeof sale);
        stopSetTimeout();
    }
}

function purchaseStep2(sale) {
    if (buynow_product_buying_currentStep === 2) {
        console.log("We're already at step 2");
        return;
    }

    if (typeof sale === 'object') {
        console.log("Running step 2 (" + buynow_product_buying_key + ")! - We have a sale:", sale);
        buynow_product_buying_currentStep = 2;

        let remaining_price = sale.amount;
        let shipping_domestic = sale.shipping_domestic;
        let shipping_worldwide = sale.shipping_worldwide;

        let textToShowInWidget = '<p class="fs-3 text-center">Please send the remaining amount plus shipping directly to the seller!</p>';

        textToShowInWidget +=
            '<div id="shipping_widget" class="fs-5 justify-content-md-center">' +

            '   <div id="shipping_widget_result" class="row fs-5">' +
            '       <span id="shipping_widget_result_product" class="row justify-content-md-center"></span>' +
            '       <span class="row justify-content-md-center shipping_widget_operands"><b>+</b></span>' +

            '       <div id="shipping_widget_chooser" class="row">' +
            '           <div class="col-3"></div>' +
            '           <div class="form-check col-6" id="shipping_widget_chooser_center">' +
            '               <label class="form-check-label" for="flexRadioDefault1">Domestic Shipping ' + shipping_domestic + ' sats (~$' + satsToFiat(shipping_domestic) + ')</label>' +
            '               <input class="form-check-66input" type="radio" name="shippingChooser" id="shipping_domestic">' +

            '               <label class="form-check-label" for="flexRadioDefault2">Worldwide Shipping ' + shipping_worldwide + ' sats (~$' + satsToFiat(shipping_worldwide) + ')</label>' +
            '               <input class="form-check-66input" type="radio" name="shippingChooser" id="shipping_worldwide" checked>' +
            '           </div>' +
            '       </div>' +

            '       <span class="row justify-content-md-center shipping_widget_operands"><b>=</b></span>' +
            '       <span id="shipping_widget_result_total" class="row justify-content-md-center fs-3"></span>' +
            '   </div>' +

            '   <script>' +
            '       let remaining_price = ' + remaining_price + ';' +
            '       let shipping_domestic = ' + shipping_domestic + ';' +
            '       let shipping_worldwide = ' + shipping_worldwide + ';' +
            '       shippingCalculatorQRChooser(remaining_price, shipping_domestic, shipping_worldwide);' +
            '       $("input[type=radio][name=shippingChooser]").change(function() { shippingCalculatorQRChooser(remaining_price, shipping_domestic, shipping_worldwide); });' +
            '   </script>' +

            '</div>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            'buynowStep2',
            textToShowInWidget,
            sale.address,
            [
                sale.qr,
                sale.qr_domestic,
                sale.qr_worldwide,
            ],
            'bitcoin',
            'Step 2/3 - Payment',
            true
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('step2 - sale is not an object:', typeof sale);
        stopSetTimeout();
    }
}

function purchaseStep3(sale) {
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
            '       <img src="' + pluginBasePath + 'common/img/plebeian_market_logo.png">' +
            '   </div>' +
            '   <p class="fs-4">TxID: <a class="link" target="_blank" href="https://mempool.space/tx/' + sale.txid + '">' + sale.txid + '</a>. Save it as a purchase receipt.</p>' +
            '</div>' +
            '<p class="fs-2">Your purchase will be completed when the payment is confirmed by the network.</p>' +
            '<p class="fs-2">In the mean time, you can follow the transaction on <a class="link" target="_blank" href="https://mempool.space/tx/' + sale.txid + '">mempool.space</a>!</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            'buynowStep3',
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
        stopSetTimeout();
    }
}

function purchaseStep4(sale) {
    if (buynow_product_buying_currentStep === 4) {
        console.log("We're already at step 4");
        return;
    }

    if (typeof sale === 'object') {
        console.log("Running step 4 (" + buynow_product_buying_key + ")! - We have a sale:", sale);
        buynow_product_buying_currentStep = 4;

        let sellerEmail = sale.seller_email;

        let textToShowInWidget =
            '<p class="text-center fs-2">Payment confirmed!</p>' +
            '<p class="text-center fs-2">Please <a target="_blank" href="mailto:' + sellerEmail + '" class="link">contact the seller</a> directly to discuss shipping.</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            'buynowStep4',
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

    stopSetTimeout();
}

function shippingCalculatorQRChooser(remaining_price, shipping_domestic, shipping_worldwide) {
    // Remaining price
    $('#shipping_widget_result_product').html('Remaining amount: ' + remaining_price + ' sats (~$' + satsToFiat(remaining_price) + ')');

    $('.qrImageDiv').hide();

    // Shipping price
    let shippingPrice;
    if ($('#shipping_domestic').prop('checked')) {
        shippingPrice = shipping_domestic;
        $('#qrImageDiv_1').show();
    } else {
        shippingPrice = shipping_worldwide;
        $('#qrImageDiv_2').show();
    }
    $('#shipping_widget_result_shipping').html('Shipping: ' + shippingPrice + ' sats (~$' + satsToFiat(shippingPrice) + ')');

    // Total
    let total = remaining_price + shippingPrice;
    $('#shipping_widget_result_total').html('Total: ' + total + ' sats (' + satsToBTC(total) + ' BTC / ~$' + satsToFiat(total) + ')');
}
