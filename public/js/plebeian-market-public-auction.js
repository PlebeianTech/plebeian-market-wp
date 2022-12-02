let current_auction_key;
let bidsExtendedInfoSetTimeout;
let payingBidSetTimeout;
let numBidsLastTimeWeLookAllAuctions = {};
let numBidsLastTimeWeLook = 99;
let continueListeningForBidPayment;

function stopBidsExtendedInfoSetTimeout() {
    clearTimeout(bidsExtendedInfoSetTimeout);
}
function stopPayingBidSetTimeout() {
    clearTimeout(payingBidSetTimeout);
}

function updateAuctionsPeriodically() {
    $('.pleb_item_superdiv').each(function () {
        let auctionObject = $(this);

        if (auctionObject.data('type') === 'auction'){
            getAuctionInfoPeriodically(auctionObject);
        }
    });
}

function getAuctionInfo(key) {
    return $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        dataType: "JSON",
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: 'plebeian-ajax_get_item_info',
            plebeian_item_key: key,
            plebeian_item_type: 'auction'
        }
    });
}

async function getAuctionInfoPeriodically(auctionObject) {
    let key = auctionObject.data('key');

    try {
        const response = await getAuctionInfo(key);
        //console.log(response);

        if (response.success === true) {
            let item_info_from_api = response.data;
            // console.log('Information retrieved successfully!', item_info_from_api);

            let bids = item_info_from_api.bids;
            let numBids = bids.length;

            if (numBidsLastTimeWeLookAllAuctions[key] !== numBids) {
                numBidsLastTimeWeLookAllAuctions[key] = numBids;

                let ended = item_info_from_api.ended;
                let starting_bid = item_info_from_api.starting_bid;
                let htmlBids = '<p>Bids: ' + numBids + '</p>';

                if (numBids === 0) {
                    htmlBids += '<p>Starting bid: ' + starting_bid + ' sats</p>'

                } else {
                    let current_bid = bids[0]?.amount;
                    let bidder_name = bids[0]?.buyer_nym;

                    if (ended) {
                        htmlBids += '<p>Winning bid: ' + current_bid + ' sats</p>'
                        htmlBids += '<p>Winner: ' + bidder_name + '</p>'
                    } else {
                        htmlBids += '<p>Top bid: ' + current_bid + ' sats</p>'
                        htmlBids += '<p>Bidder: ' + bidder_name + '</p>'
                    }
                }

                if (ended) {
                    htmlBids += '<h4>Auction ended</h4>'
                } else {
                    let endDate = moment(item_info_from_api.end_date).format('YYYY/MM/DD hh:mm:ss');
                    htmlBids += getCountDownHTML(endDate);
                }

                let bids_info = auctionObject.find('.pleb_bids_info');
                $(bids_info).html(htmlBids);
            }

        } else {
            console.log("ERROR getting information: ", response);
        }

    } catch(error) {
        console.log("ERROR getting information: ", error);
    }

    runTheCountDowns();

    console.log('Sleeping 2 secs (getAuctionInfoPeriodically)...');
    setTimeout(function () {
        getAuctionInfoPeriodically(auctionObject);
    }, 2000)
}

async function showBidsExtendedInfo(key, loadEvenWithoutChanges = false, shouldShowLoadingModal = true) {
    if (shouldShowLoadingModal) {
        showLoadingModal();
    }

    const response = await getAuctionInfo(key);
     console.log(response);

    if (response.success === true) {
        let item_info_from_api = response.data;

        let bids = item_info_from_api.bids;
        let numBids = bids.length;

        if (loadEvenWithoutChanges || numBids !== numBidsLastTimeWeLook) {
            numBidsLastTimeWeLook = numBids;

            let auction_ended = item_info_from_api.ended;
            let reserveReached = item_info_from_api.reserve_bid_reached;
            let lastBid = numBids > 0 ? item_info_from_api.bids[0] : null;

            let starting_bid = item_info_from_api.starting_bid;
            let suggested_bid = numBids === 0 ? starting_bid : (item_info_from_api.bids[0]?.amount * 1.1);

            let title = 'Participate in this auction';

            let htmlToShowInWidget = `
                <div class="row">
                   <div class="col-3"></div>
                   <div class="col-6">`;

            if (auction_ended) {
                htmlToShowInWidget += '<h4>Auction ended.</h4>';

                if (numBids === 0) {
                    title = 'Auction ended without bids';
                    htmlToShowInWidget += '<h4>There was not a winner.</h4>';
                } else {
                    title = 'Auction ended';
                }
            } else {
                if (numBids === 0) {
                    htmlToShowInWidget += '<p>There are no bids yet. Be the first to bid!</p>';
                }

                let endDate = moment(item_info_from_api.end_date).format('YYYY/MM/DD hh:mm:ss');
                htmlToShowInWidget += getCountDownHTML(endDate);

                htmlToShowInWidget += `
                    <div class="bidNowWidget">
                        <form class="row g-3 needs-validation" novalidate>
                            <div class="col-3"></div>
                            <div class="col-6">
                                <label for="make_bid_sats" class="col-form-label">Bid amount:</label>
                                <input type="text" class="form-control btc-to-fiat-source" id="make_bid_sats" name="make_bid_sats" size="10" value="` + suggested_bid + `">
                                <small class="fiatDestination"></small><small> (suggested bid)</small>
                            </div>
                            <button type="button" class="btn btn-success btn-makeNewBid" data-key="` + key + `">Bid now</button>
                        </form>
                    </div>`;
            }

            if (numBids > 0) {
                if (!reserveReached) {
                    htmlToShowInWidget += '<h3>Reserve not met!</h3>';
                }

                htmlToShowInWidget += getBidsTable(bids);
            }

            htmlToShowInWidget += `
                    </div>
                    <div class="col-3"></div>
                </div>`;

            putIntoHtmlElementText(
                '#gpModal',
                'bidsExtendedInfo',
                htmlToShowInWidget,
                title
            );

            bindEverythingForExtendedBidInfo();

            hideLoadingModal();
            showGPModal();
        }
    }

    console.log('Sleeping 2 secs (showBidsExtendedInfo)...');

    bidsExtendedInfoSetTimeout = setTimeout(function () {
        showBidsExtendedInfo(key, false, false);
    }, 2000)
}

function getBidsTable(bids) {
    let htmlToShowInWidget = `
            <table class="table table-responsive auctionBidInfo">
                <tr>
                    <th>Bidder</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>`;

    bids.forEach(function(bid, idx, array) {
        let bidder_nym = bid.buyer_nym;
        let bidder_img = bid.buyer_profile_image_url;
        let amount = bid.amount;
        let paid_at = moment(bid.settled_at).format('D/M/YYYY, H:mm:ss');

        htmlToShowInWidget += `
                <tr>
                    <td><img src="`+bidder_img+`" alt="` + bidder_nym + `'s Plebeian Market nym " width="40" height="40">` + bidder_nym + `</td>
                    <td><span class="btc-to-fiat-source">` + amount + `</span> (<small class="fiatDestination"></small>)</td>
                    <td>` + paid_at + `</td>
                </tr>`;
    });

    htmlToShowInWidget += '</table>';

    return htmlToShowInWidget;
}

function showMakeNewBidPaymentScreen(key, amount, shouldShowLoadingModal = true) {
    stopBidsExtendedInfoSetTimeout();

    if (shouldShowLoadingModal) {
        showLoadingModal();
    }

    current_auction_key = key;

    $.ajax({
        url: requests.pm_api.auctions.bid.url.replace('{KEY}', key),
        data: JSON.stringify({
            amount: amount,
        }),
        timeout: requests.pm_api.default_timeout,
        cache: false,
        dataType: 'JSON',
        contentType: 'application/json;charset=UTF-8',
        type: requests.pm_api.auctions.bid.method,
        headers: { "X-Access-Token": customerGetPlebeianMarketAuthToken() },
    })
        .done(async function (response) {
            // console.log('response', response);

            let message = response.messages.join('. ');

            let textToShowInWidget = '<p class="fs-4 text-center">' + message + '</p>';
            /*'<p class="fs-4 text-center">The seller wishes to donate ' + sale.contribution_amount +
            ' sats (' + satsToBTC(sale.contribution_amount) + ' BTC / ~$' + satsToFiat(sale.contribution_amount) +
            ') out of the total price to Plebeian Market. ' +
            'Please send the amount using the QR code below!</p>';*/

            let payment_request = response.payment_request;
            let qr = response.qr;

            putIntoHtmlElementTextQrLnAddress(
                '#gpModal',
                'makeNewBid',
                textToShowInWidget,
                payment_request,
                qr,
                'lightning',
                'Make bid',
                true
            );

            hideLoadingModal();
            showGPModal();

            // Search for bid in bid list
            try {
                continueListeningForBidPayment = true;

                do {
                    const response = await getAuctionInfo(key);

                    if (response.success === true) {
                        response.data.bids.forEach(function(bid) {
                            if (bid.amount == amount && bid.payment_request === payment_request) {
                                continueListeningForBidPayment = false;
                                stopBidsExtendedInfoSetTimeout();
                                showBidsExtendedInfo(key, true);
                            }
                        });
                    } else {
                        console.log("ERROR getting information: ", response);
                    }

                    await new Promise(r => setTimeout(r, 2000));
                }
                while (continueListeningForBidPayment);

            } catch (error) {
                console.log("ERROR getting information: ", error);
            }
        })
        .fail(function (e) {
            console.log('Error: ', e);
            console.log('Error message: ', e.message);
        });
}

function bindEverythingForExtendedBidInfo() {
    convertAllBtcToFiat();

    $('.btc-to-fiat-source').keyup(function () {
        convertAllBtcToFiat();
    });

    $('.btn-makeNewBid').click(function () {
        let key = $(this).data('key');
        let amount = $('#make_bid_sats').val();

        showMakeNewBidPaymentScreen(key, amount);
    });

    runTheCountDowns();
}

function getCountDownHTML(endDate) {
    return `
        <span class="plebeian_auctions_countdown" data-enddate="` + endDate + `">
          <div class="countdown-wrapper justify-content-center">
            <div class="item">
              <div class="number">
                <span class="days"></span>
              </div>
              <span>Days</span>
            </div>
            <div class="item">
              <div class="number">
                <span class="hours"></span>
              </div>
              <span>Hours</span>
            </div>
            <div class="item">
              <div class="number">
                <span class="minutes"></span>
              </div>
              <span>Minutes</span>
            </div>
            <div class="item">
              <div class="number">
                <span class="seconds"></span>
              </div>
              <span>Seconds</span>
            </div>
          </div>
        </span>`;
}

function runTheCountDowns() {
    $('.plebeian_auctions_countdown').each(function() {
        let $this = $(this), finalDate = $(this).data('enddate');

        $this.countdown(finalDate)
            .on('update.countdown', function(event) {
                $this.find('.days').first().text(event.offset.totalDays);
                $this.find('.hours').first().text(event.offset.hours);
                $this.find('.minutes').first().text(event.offset.minutes);
                $this.find('.seconds').first().text(event.offset.seconds);
            })
            .on('finish.countdown', function() {
                $('.countdown-wrapper').html('<span class="finished">Auction Finished</span>');
            });
    });
}

$(document).ready(function () {
    $('#closeGPModal').click(function () {
        let modalBeingClosed = $(this).data('modalName');

        // Stop existing timer in all auctions modal
        // closings to start with a new one. This
        // prevents race conditions
        if (modalBeingClosed === 'bidsExtendedInfo' || modalBeingClosed === 'makeNewBid') {
            stopBidsExtendedInfoSetTimeout();

            if (modalBeingClosed === 'makeNewBid') {
                showBidsExtendedInfo(current_auction_key, true);
            }
        }
    });
});
