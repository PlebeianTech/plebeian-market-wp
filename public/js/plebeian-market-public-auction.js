function updateAuctionsPeriodically() {
    $('.pleb_item_superdiv').each(function () {
        let auctionObject = $(this);

        if (auctionObject.data('type') === 'auction'){
            getAuctionInfoPeriodically(auctionObject);
        }
    });
}

function getAuctionInfoPeriodically(auctionObject) {
    let pmtype = auctionObject.data('type');
    let key = auctionObject.data('key');
    let bids_info = auctionObject.find('.pleb_bids_info');

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
            // console.log('Information retrieved successfully!', response);

            if (response.success === true) {
                let item_info_from_api = response.data;
                // console.log('Information retrieved successfully!', item_info_from_api);

                let ended = item_info_from_api.ended;
                let bids = item_info_from_api.bids.length;
                let starting_bid = item_info_from_api.starting_bid;
                let htmlBids = '<p>Bids: ' + bids + '</p>';

                if (bids === 0) {
                    htmlBids += '<p>Starting bid: ' + starting_bid + '</p>'

                } else {
                    let current_bid = item_info_from_api.bids[0]?.amount;
                    let bidder_name = item_info_from_api.bids[0]?.buyer_display_name;

                    if (ended) {
                        htmlBids += '<p>Winning bid: ' + current_bid + ' sats</p>'
                        htmlBids += '<p>Winner: ' + bidder_name + '</p>'
                    } else {
                        htmlBids += '<p>Top bid: ' + current_bid + ' sats</p>'
                        htmlBids += '<p>Bidder: ' + bidder_name + '</p>'
                    }
                }

                if (ended) {
                    htmlBids += '<p>Ended</p>'
                } else {
                    let end_time = item_info_from_api.end_date;
                    htmlBids += '<p>End time: ' + end_time + '</p>'
                }

                $(bids_info).html(htmlBids);

            } else {
                console.log("ERROR getting information: ", response);
            }
        })
        .fail(function (error) {
            console.log("ERROR getting information: ", error);
        })
        .always(function () {
            console.log('Sleeping 5 secs...');

            setTimeout(function () {
                getAuctionInfoPeriodically(auctionObject);
            }, 5000)
        });
}
