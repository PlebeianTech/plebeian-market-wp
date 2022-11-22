function waitAndAskAgainAuctions() {
    plebeianSetTimeout = setTimeout(function () {
        updateAuctionsPeriodically(false);
    }, 5000);
}

function getAuctionInfo(pmtype, key) {
    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        dataType: "JSON",
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: 'plebeian-ajax_get_item_info',
            plebeian_item_key: key,
            plebeian_item_type: pmtype
        }
    })
        .done(function (response) {
            console.log('Information retrieved successfully!', response);

            if (response.success === true) {
                let item_info_from_api = response.data;

                // pmtype === 'buynow'

                console.log('item_info_from_api', item_info_from_api);

                if (pmtype === 'auction') {
                    $('#starting_bid').val(item_info_from_api.starting_bid);
                    $('#reserve_bid').val(item_info_from_api.reserve_bid);

                    let duration_hours = item_info_from_api.duration_hours;
                    if (duration_hours % 24 === 0) {
                        $('#duration').val(duration_hours / 24);
                        $('#duration_unit').val('d');
                    } else {
                        $('#duration').val(duration_hours);
                        $('#duration_unit').val('h');
                    }
//                } else if (pmtype === 'buynow') {
//                    $('#price_usd').val(item_info_from_api.price_usd);
//                    $('#available_quantity').val(item_info_from_api.available_quantity);
                }

            } else {
                console.log("ERROR getting information: ", response);
            }
        })
        .fail(function (error) {
            console.log("ERROR getting information: ", error);
        })
        .always(function () {
            waitAndAskAgainAuctions();
        });
}

function updateAuctionsPeriodically() {
    $('.pleb_item_superdiv').each(function () {
        let auctionObject = $(this);

        if (auctionObject.data('type') !== 'auction'){
            return;
        }

        let key = auctionObject.data('key');
        let bids_info = auctionObject.find('.pleb_bids_info');

        console.log('key', key);

        getAuctionInfo(key);
        console.log('bids_info', bids_info);
        $(bids_info).html('rucu');

        getAuctionInfo('auction', key);
    });
}
