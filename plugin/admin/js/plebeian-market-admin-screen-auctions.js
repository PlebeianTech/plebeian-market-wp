function setFormDefaultValues() {
    let starting_bid = document.getElementById('starting_bid');
    if (!starting_bid.value) {
        starting_bid.value = 0;
    }

    let reserve_bid = document.getElementById('reserve_bid');
    if (!reserve_bid.value) {
        reserve_bid.value = 0;
    }

    let shipping_domestic_usd = document.getElementById('shipping_domestic_usd');
    if (!shipping_domestic_usd.value) {
        shipping_domestic_usd.value = 0;
    }

    let shipping_worldwide_usd = document.getElementById('shipping_worldwide_usd');
    if (!shipping_worldwide_usd.value) {
        shipping_worldwide_usd.value = 0;
    }

    let duration = document.getElementById('duration');
    if (!duration.value) {
        duration.value = 3;
        document.getElementById('duration_unit').value = 'd';
    }
}

$(document).ready( function () {
    itemsDatatable = $('#table_items').DataTable({
        ajax: {
            url: requests.pm_api.auction.list.url,
            dataSrc: 'auctions',
            headers: { "X-Access-Token": requests.pm_api.XAccessToken }
        },
        order: [[4, 'desc']],
        dom: '<"toolbar">Bfrtip',
        //select: true,
        buttons: [
            'colvis',
            'csv',
            {
                text: 'New Auction',
                className: 'newItemButton',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#add-item-modal'
                }
            }
        ],
        language: {
            "emptyTable": "Create your first auction using the <b>New Auction button</b>",
            "info": "Showing _START_ to _END_ of _TOTAL_ auctions",
            "sInfoEmpty": "You have no auctions yet"
        },
        columns: [
            {
                data: 'key'
            },
            {
                data: 'title'
            },
            {
                render: function (data, type, row) {
                    return row.media.length;
                },
                className: "dt-center"
            },
            {
                data: 'duration_hours',
                className: "dt-center"
            },
            {
                data: 'created_at',
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    if (!row.started) {
                        return '<button type="button" class="btn btn-primary btn-sm publishButton confirmActionButton" data-action="publish" data-pmtype="auction" data-key="' + row.key + '" data-title="' + row.title + '">Publish</button>';
                    }
                    return row.start_date;
                },
                className: "dt-center"
            },
            {
                data: 'end_date',
                className: "dt-center"
            },
            {
                data: 'starting_bid',
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    return row.bids.length;
                },
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    return row.current_bid ?? '';
                },
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    let key = row.key;
                    let title = row.title;

                    let iconsToBeDisplayed = '';

                    if (row.started) {
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/pencil-square.svg" class="dataTablesActionIconDisabled" data-pmtype="auction" data-key="' + key + '" data-title="' + title + '" alt="Edit item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit is disabled because the auction already started">';
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/trash.svg" class="dataTablesActionIconDisabled" data-pmtype="auction" data-key="' + key + '" data-title="' + title + '" alt="Delete item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete is disabled because the auction already started">';
                    } else {
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/pencil-square.svg" class="dataTablesActionIcon editButton" data-pmtype="auction" data-key="' + key + '" data-title="' + title + '" alt="Edit item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">';
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/trash.svg" class="dataTablesActionIcon deleteButton" data-action="delete" data-pmtype="auction" data-key="' + key + '" data-title="' + title + '" alt="Delete item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete">';
                    }

                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/code-square.svg" class="dataTablesActionIcon copyShortCodeButton" data-pmtype="auction" data-key="' + key + '" alt="Copy Shortcode" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy shortcode">';
                    return iconsToBeDisplayed;
                },
                className: "dt-center"
            }
        ],
        columnDefs: [
            {
                targets: 1,
                render: DataTable.render.text(),
            },
            {
                targets: [4, 5, 6],
                render: DataTable.render.datetime(),
            },
            {
                targets: 10,
                width: '10%'
            },
        ],
        fixedColumns: true

    }).on('draw', function () {
        $("#table_items").show();
        rebindIconClicks();

        // Enable button tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        $('.newItemButton').click(function () {
            $('#titleModalItemInfo').text('New Auction');
            clearForm();
            setFormDefaultValues();
        })
    });
});
