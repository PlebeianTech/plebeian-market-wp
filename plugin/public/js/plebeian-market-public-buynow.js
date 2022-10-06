let buynow_product_buying_key;
let buynow_product_buying_currentStep;

function buyNow(showLoadingModal) {
    if (showLoadingModal) {
        showLoading();
    }

    getBuyNowItemInfo(
        buynow_product_buying_key,
        function (buynowInfo) {
            console.log('buynowInfo', buynowInfo);
            let sales = buynowInfo.sales;

            if (!sales || typeof sales === 'undefined' || sales === null) {
                step1();    // No sales or no active sales, so let's create one
            } else {
                // We have sales. In what state?
                let oneSaleIsValid = false;

                sales.every(function (sale) {
                    if (sale.state !== 'EXPIRED') {
                        console.log('   - Sale not expired (state):' + sale.state, sale);

                        oneSaleIsValid = true;
    
                        if (['CONTRIBUTION_SETTLED', 'REQUESTED'].includes(sale.state)) {   // !== 'CONTRIBUTION_SETTLED'
                            // Contribution paid
                            step2();
                            buyNow(false);
                            return false;   // break for "every"
                        } else {
                            // Show QR for contribution
                            step1(sale);
                            buyNow(false);
                            return false;   // break for "every"
                        }
                    }
                });

                if ( ! oneSaleIsValid) {
                    // We had sales, but none is valid, so let's create one
                    // and keep checking
                    console.log("   - We don't have any valid sale, so requesting a new one...", sales);

                    step1();
                    buyNow(false);
                }
            }
        }
    );
}

/**
 * 
 * @param {object} sale 
 * @returns 
 */
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

        hideLoading();

        let textToShowInWidget =
            '<p>The seller wishes to donate ' + sale.contribution_amount +
            ' sats ($xxx.xx) sats out of the total price to Plebeian Technology. ' +
            'Please send the amount using the QR code below!</p>';

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            textToShowInWidget,
            sale.contribution_payment_request,
            sale.contribution_payment_qr,
            'Step 1/3 - Contribution'
        );

        const myModal = new bootstrap.Modal('#gpModal', { keyboard: true });
        myModal.show();

    } else {
        // We don't yet have a sale, so lets start one
        console.log('   ---- We don\'t yet have a sale. Creating one...');
        $.ajax({
            url: requests.pm_api.buynow_buy.url.replace('{KEY}', buynow_product_buying_key),
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: requests.pm_api.buynow_buy.method,
            headers: { "X-Access-Token": getPlebeianMarketAuthToken() },
        })
            .done(function (response) {
                console.log('response', response);

                let textToShowInWidget =
                    '<p>The seller wishes to donate ' + response.sale.contribution_amount +
                    ' sats ($xxx.xx) sats out of the total price to Plebeian Technology. ' +
                    'Please send the amount using the QR code below!</p>';

                putIntoHtmlElementTextQrLnAddress(
                    '#gpModal',
                    textToShowInWidget,
                    response.sale.contribution_payment_request,
                    response.sale.contribution_payment_qr,
                    'Step 1/3 - Contribution'
                );
            })
            .fail(function (e) {
                console.log('Error: ', e);

                let buyNowWidget = 'Error: ' + e.message;

                content.html(buyNowWidget);
            })
            .always(function () {
                hideLoading();

                const myModal = new bootstrap.Modal('#gpModal', { keyboard: true });
                myModal.show();
            });
    }


}

function step2() {
    if (buynow_product_buying_currentStep === 2) {
        console.log("We're already at step 2");
        return;
    }

    console.log("Running step 2 !", buynow_product_buying_key);
    buynow_product_buying_currentStep = 2;

    let content = $('#gpModal .modal-body');
    content.html('<p>Loading...</p>');

    hideLoading();

    const myModal = new bootstrap.Modal('#gpModal', { keyboard: true });
    myModal.show();
}

function getBuyNowItemInfo(key, callback) {
    if (typeof key === 'undefined' || key == '') {
        console.log('getBuyNowItemInfo - I cannot get the info of the buynow item: ', key);
        return null;
    }

    $.ajax({
        url: requests.pm_api.buynow_get.url.replace('{KEY}', key),
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.buynow_get.method,
        headers: { "X-Access-Token": getPlebeianMarketAuthToken() },
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
