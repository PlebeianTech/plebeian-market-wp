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

function setupSlideshow() {
    $('.pleb_buynow_item_slideshow').each(function () {
        resizeSlideshowToImgSize(this);

        if ($(this).data('disabled-slideshow') === 0) {
            setInterval(startSlideShows, $(this).data('slideshow-transitions'), this);
        }
    });
}

$(document).ready(function () {
    setupSlideshow();
});