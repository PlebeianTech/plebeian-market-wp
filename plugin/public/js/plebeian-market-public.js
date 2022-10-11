let loadingModal, gpModal;

function startSlideShows(slideshow) {
    var $active = $(slideshow).find('IMG.active');

    if ($active.length == 0)
        $active = $(slideshow).find('IMG:last');

    var $next = $active.next('IMG').length ? $active.next('IMG')
        : $(slideshow).find('IMG:first');

    $active.addClass('last-active');

    $next.css({ opacity: 0.0 })
        .addClass('active')
        .animate(
            { opacity: 1.0 },
            1200,
            function () {
                $active.removeClass('active last-active');
            }
        );
}

function resizeSlideshowToImgSize(slideshow) {

    let maxHeight = 0;

    // We must first attach the onload event
    // and then set the mig src so when the
    // image is loaded, we're 100% sure the
    // event will fire.
    $(slideshow).find('img').each(function () {
        this.onload = function () {

            let heightThisImage = this.height;

            if (heightThisImage > maxHeight) {
                maxHeight = heightThisImage;

                $(slideshow).height(maxHeight);
            }
        }
        this.src = $(this).data('src');
    });
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
            '   <input type="text" class="form-control" value="' + lnurl + '" id="url"> <button type="button" class="input-group-btn btn btn-outline-primary" id="btc-copy-url">Copy</button>' +
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

$(document).ready(function () {
    // Setup slideshows
    $('.pleb_buynow_item_slideshow').each(function () {
        resizeSlideshowToImgSize(this);

        if ($(this).data('disabled-slideshow') === 0) {
            setInterval(startSlideShows, $(this).data('slideshow-transitions'), this);
        }
    });

    $('.btn-buynow').click(function () {
        if (buynow_product_buying_key !== $(this).data('key')) {
            buynow_product_buying_key = $(this).data('key');
            buynow_product_buying_currentStep = null;
        }

        console.log('Buy now: ', buynow_product_buying_key);

        loginThenCallFunction(buyNow);
    })

    loadingModal = new bootstrap.Modal('#loadingModal', { keyboard: true });
    gpModal = new bootstrap.Modal('#gpModal', { keyboard: true });
});