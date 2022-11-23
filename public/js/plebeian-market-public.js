let loadingModal, gpModal;

$(document).ready(function () {
    updateAuctionsPeriodically();

    $('.btn-buynow').click(function () {
        if (buynow_product_buying_key !== $(this).data('key')) {
            buynow_product_buying_key = $(this).data('key');
            buynow_product_buying_currentStep = null;
        }

        console.log('Buy now: ', buynow_product_buying_key);

        buyerLoginThenCallFunction(buyNow);
    })

    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });
});