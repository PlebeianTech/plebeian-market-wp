let loadingModal, gpModal;

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

    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });
});