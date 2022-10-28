function satsToBTC(sats) {
    return sats / 100000000;
}
function satsToFiat(sats) {
    // USD for now
    return (satsToBTC(sats) * btcPriceInUSD).toFixed(2);
}

function showLoadingModal() {
    loadingModal.show();
}
function hideLoadingModal() {
    loadingModal.hide();
}
function showGPModal() {
    gpModal.show();
}
function hideGPModal() {
    gpModal.hide();
}

function putIntoHtmlElementTextQrLnAddress(elementSelector, text, lnurl, qr, protocol, title, waitingPaymentSpinnerEnabled) {
    let loginWidget = text;

    if (lnurl !== null && qr !== null && protocol !== null) {
        loginWidget +=
            '<div class="qrcodeImageDiv">' +
            '   <a href="' + protocol + ':' + lnurl + '"><svg id="qrcodeImage"></svg></a>' +
            '</div>' +
            '<div class="input-group lnurlValue">' +
            '   <input type="text" class="form-control text-truncate" value="' + lnurl + '" id="url"> <button type="button" class="input-group-btn btn btn-outline-primary" id="btc-copy-url">Copy</button>' +
            '</div>';

        loginWidget +=
            '<script>' +
            '   $("#btc-copy-url").click(function () {' +
            '       navigator.clipboard.writeText($("#url").val())' +
            '   });' +
            '</script>';
    }

    if (waitingPaymentSpinnerEnabled) {
        loginWidget +=
            '<div class="d-flex justify-content-center spinnerWaitingPayment"> ' +
            '   <div class="spinner-border" role="status"></div> ' +
            '   <div class="justify-content-center d-flex spinnerWaitingPaymentText"><p>Waiting for payment...</p></div> ' +
            '</div>';
    }

    $(elementSelector + ' .modal-body').html(loginWidget);

    if (typeof title !== 'undefined' && title !== '') {
        $(elementSelector + ' .modal-title').html(title);
    } else {
        $(elementSelector + ' .modal-title').html('');
    }

    let aaa = '<div class="position-relative m-4">' +
        '<div class="progress" style="height: 1px;">' +
        '  <div class="progress-bar" role="progressbar" aria-label="Progress" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>' +
        '</div>' +
        '<button type="button" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">1</button>' +
        '<button type="button" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">2</button>' +
        '<button type="button" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-secondary rounded-pill" style="width: 2rem; height:2rem;">3</button>' +
        '</div>';
    //$(elementSelector + ' .modal-header').html(aaa);

    $('#qrcodeImage').replaceWith($('<div/>').append(qr).find('svg:first').attr('id', 'qrcodeImage'));
}