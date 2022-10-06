let loadingModal;

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

function showLoading() {
    loadingModal.show();
}
function hideLoading() {
    loadingModal.hide();
}

function putIntoHtmlElementTextQrLnAddress(elementSelector, text, lnurl, qr, title) {
    let loginWidget =
        text +
        '<div class="qrcodeImageDiv">' +
        '   <a href="lightning:' + lnurl + '"><svg id="qrcodeImage"></svg></a>' +
        '</div>' +
        '<div class="lnurlValue"><input type="text" class="form-control" value="' + lnurl + '"></div>';

    $(elementSelector + ' .modal-body').html(loginWidget);

    if (typeof title !== 'undefined' && title !== '') {
        $(elementSelector + ' .modal-title').html(title);
    } else {
        $(elementSelector + ' .modal-title').html('');
    }

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
});