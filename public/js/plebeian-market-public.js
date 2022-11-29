let loadingModal, gpModal;

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
                showBidsExtendedInfo(key);
            })
            .catch(function (e) {
                console.log('Not showing bid dialog because there was a problem:', e);
            });
    });

    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });
});