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
    return new Promise(function (resolve, reject) {
        $.ajax({
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
        })
        .done(function (response) {
            // console.log('response', response);
            resolve(response.data);
        })
        .fail(function (e) {
            console.log('Error: ', e);
            reject(e);
        });
    });
}

async function getAuctionInfoPeriodically(auctionObject) {
    let key = auctionObject.data('key');

    await getAuctionInfo(key)
    .then(async function (auctionInfo) {
        // console.log('Information retrieved successfully!', auctionInfo);

        let bids = auctionInfo.bids;
        let numBids = bids.length;

        if (numBidsLastTimeWeLookAllAuctions[key] !== numBids) {
            numBidsLastTimeWeLookAllAuctions[key] = numBids;

            let ended = auctionInfo.ended;
            let starting_bid = auctionInfo.starting_bid;
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

            if (ended || auctionInfo.end_date === null) {
                htmlBids += '<p class="auctionEnded">Auction ended</p>'
            } else {
                let endDate = moment(auctionInfo.end_date).format('YYYY/MM/DD HH:mm:ss');
                htmlBids += getCountDownHTML(endDate);
            }

            let bids_info = auctionObject.find('.pleb_bids_info');
            $(bids_info).html(htmlBids);
        }
    })
    .catch(function (e) {
        console.log('Error while trying to load auction:', e);
        //reject();
    });

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

    await getAuctionInfo(key)
    .then(async function (auctionInfo) {
        console.log('Information retrieved successfully!', auctionInfo);

        let sales = auctionInfo.sales;

        if (auctionInfo.ended && sales.length > 0) {
            let sale = sales[0];
            // console.log('sale', sale);
// TODO: - check if buyer_nym is me

            if (['EXPIRED'].includes(sale.state)) {
                showPurchaseExpired(sale);
            } else if (['REQUESTED'].includes(sale.state)) {
                // Show QR for contribution
                purchaseStep1(sale);
            } else if (['CONTRIBUTION_SETTLED'].includes(sale.state)) {
                // Contribution paid. Show BTC QR.
                purchaseStep2(sale);
            } else if (['TX_DETECTED'].includes(sale.state)) {
                // Contribution paid
                purchaseStep3(sale);
            } else if (['TX_CONFIRMED'].includes(sale.state)) {
                // Contribution paid
                purchaseStep4(sale);
            } else {
                console.log('--------------------------------------------------------------------');
                console.log('Status not known yet: ' + sale.state, sale);
                console.log('--------------------------------------------------------------------');
            }

            if ( !['EXPIRED', 'TX_CONFIRMED'].includes(sale.state)) {
                console.log('Sleeping 2 secs (showBidsExtendedInfo)...');
                payingBidSetTimeout = setTimeout(function () {
                    showBidsExtendedInfo(key, false, false);
                }, 2000)
            }

            return;
        }

        // Auction not ended
        let bids = auctionInfo.bids;
        let numBids = bids.length;

        if (loadEvenWithoutChanges || numBids !== numBidsLastTimeWeLook) {
            numBidsLastTimeWeLook = numBids;

            let auction_ended = auctionInfo.ended;
            let reserveReached = auctionInfo.reserve_bid_reached;
            // let lastBid = numBids > 0 ? auctionInfo.bids[0] : null;

            let starting_bid = auctionInfo.starting_bid;
            let suggested_bid = numBids === 0 ? starting_bid : (auctionInfo.bids[0]?.amount * 1.1);
            suggested_bid = suggested_bid.toFixed(0);

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

                let endDate = moment(auctionInfo.end_date).format('YYYY/MM/DD HH:mm:ss');
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

        console.log('Sleeping 2 secs (showBidsExtendedInfo)...');
        bidsExtendedInfoSetTimeout = setTimeout(function () {
            showBidsExtendedInfo(key, false, false);
        }, 2000)
    })
    .catch(function (e) {
        console.log('Error while trying to load auction:', e);
        //reject();
    });
}

function showPurchaseExpired(sale) {
    if (typeof sale === 'object') {
        console.log("Showing EXPIRED dialog", sale);

        let date_expired = moment(sale.expired_at).format('YYYY/MM/DD HH:mm:ss');

        let textToShowInWidget =
            `<p class="text-center fs-2">Payment expired!</p>
             <p class="text-center fs-2">We didn't receive your payment by `+date_expired+`, so the auction has been assigned to the next bidder.</p>`;

        putIntoHtmlElementTextQrLnAddress(
            '#gpModal',
            'buynowStep4',
            textToShowInWidget,
            null,
            null,
            null,
            'Payment expired'
        );

        hideLoadingModal();
        showGPModal();
    } else {
        console.error('showPurchaseExpired - sale is not an object:', typeof sale);
    }
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
        let paid_at = moment(bid.settled_at).format('D/M/YYYY, HH:mm:ss');

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

            putIntoHtmlElementTextQrLnAddress(
                '#gpModal',
                'makeNewBid',
                textToShowInWidget,
                response.payment_request,
                [response.qr],
                'lightning',
                'Make bid',
                true
            );

            hideLoadingModal();
            showGPModal();

            // Search for bid in bid list
            continueListeningForBidPayment = true;

            do {
                await getAuctionInfo(key)
                .then(async function (auctionInfo) {
                    // console.log('Information retrieved successfully!', auctionInfo);

                    auctionInfo.bids.forEach(function(bid) {
                        if (bid.amount == amount && bid.payment_request === response.payment_request) {
                            continueListeningForBidPayment = false;
                            stopBidsExtendedInfoSetTimeout();
                            showBidsExtendedInfo(key, true);
                        }
                    });
                })
                .catch(function (e) {
                    console.log('Error while trying to load auction info:', e);
                    hideLoadingModal();
                    showAlertModal('Error while trying to load auction info. Contact Plebeian Market support.');
                });

                await new Promise(r => setTimeout(r, 2000));
            }
            while (continueListeningForBidPayment);
        })
        .fail(function (e) {
            console.log('Error: ', e);
            hideLoadingModal();
            showAlertModal('Error: ' + e.responseJSON.message);
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
        if (['bidsExtendedInfo', 'makeNewBid'].includes(modalBeingClosed)) {
            stopBidsExtendedInfoSetTimeout();

            if (modalBeingClosed === 'makeNewBid') {
                showBidsExtendedInfo(current_auction_key, true);
            }
        }

        if (modalBeingClosed.startsWith('buynowStep')) {
            stopPayingBidSetTimeout();
        }
    });
});
