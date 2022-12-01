$ = jQuery;

function satsToBTC(sats) {
    let amountInBTC = sats / 100000000;
    return amountInBTC.toLocaleString('fullwide', { useGrouping: true, maximumSignificantDigits: 6 });
}
function satsToFiat(sats) {
    // USD for now
    return (sats / 100000000 * btcPriceInUSD).toFixed(2);
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

function addAlertToDivElement(element, message, type) {

    let svgImageToShow;

    if (type === 'success') {
        svgImageToShow = 'check-circle-fill';
    } else {
        svgImageToShow = 'exclamation-triangle-fill';
    }

    $(element).append([
        '<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">',
        '    <symbol id="check-circle-fill" viewBox="0 0 16 16">',
        '        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />',
        '    </symbol>',
        '    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">',
        '                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />',
        '    </symbol>',
        '</svg>',
        '<div class="alert alert-' + type + ' d-flex align-items-center alert-dismissible" role="alert">',
        '   <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#' + svgImageToShow + '"/></svg>',
        '   <div>' + message + '</div>',
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join(''));
}

function putIntoHtmlElementText(elementSelector, modalName, html, title) {
    $('#closeGPModal').data('modalName', modalName);

    $(elementSelector + ' .modal-body').html(html);

    if (typeof title !== 'undefined' && title !== '') {
        $(elementSelector + ' .modal-title').html(title);
    } else {
        $(elementSelector + ' .modal-title').html('');
    }
}

function putIntoHtmlElementTextQrLnAddress(elementSelector, modalName, text, lnurl, qr, protocol, title, waitingPaymentSpinnerEnabled) {
    let loginWidget = text;

    $('#closeGPModal').data('modalName', modalName);

    if (lnurl !== null && qr !== null && protocol !== null) {
        loginWidget +=
            '<div class="qrcodeImageDiv">' +
            '   <a href="' + protocol + ':' + lnurl + '"><svg id="qrcodeImage"></svg></a>' +
            '</div>' +
            '<div class="input-group lnurlValue">' +
            '   <input type="text" class="form-control text-truncate text-center" value="' + lnurl + '" id="url"> <button type="button" class="input-group-btn btn btn-outline-primary" id="btc-copy-url">Copy</button>' +
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

    $('#qrcodeImage').replaceWith($('<div/>').append(qr).find('svg:first').attr('id', 'qrcodeImage'));
}

function showAlertModal(message) {
    $('#alertModalText').text(message);

    const myModal = new bootstrap.Modal('#alertModal', { keyboard: true });
    myModal.show();
}
