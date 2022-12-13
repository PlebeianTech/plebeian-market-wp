function setFormDefaultValues() {
    let shipping_domestic_usd = document.getElementById('shipping_domestic_usd');
    if (!shipping_domestic_usd.value) {
        shipping_domestic_usd.value = 0;
    }

    let shipping_worldwide_usd = document.getElementById('shipping_worldwide_usd');
    if (!shipping_worldwide_usd.value) {
        shipping_worldwide_usd.value = 0;
    }
}

$(document).ready(function () {
    itemsDatatable = $('#table_items').DataTable({
        ajax: {
            url: requests.pm_api.buynow.list.url,
            dataSrc: 'listings',
            headers: { "X-Access-Token": requests.pm_api.XAccessToken }
        },
        order: [[5, 'desc']],
        dom: '<"toolbar">Bfrtip',
        //select: true,
        buttons: [
            'colvis',
            'csv',
            {
                text: 'New BuyNow item',
                className: 'newItemButton',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#add-item-modal'
                }
            }
        ],
        language: {
            "emptyTable": "Create your first product using the <b>New BuyNow item</b>",
            "info": "Showing _START_ to _END_ of _TOTAL_ products",
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
                data: 'available_quantity',
                className: "dt-center"
            },
            {
                data: 'price_usd',
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    return row.media.length;
                },
                className: "dt-center"
            },
            {
                data: 'created_at',
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    if (!row.started) {
                        return '<button type="button" class="btn btn-primary btn-sm confirmActionButton" data-action="publish" data-pmtype="buynow" data-key="' + row.key + '" data-title="' + row.title + '">Publish</button>';
                    }
                    return moment(row.start_date).format('D/M/YYYY, HH:mm:ss');
                },
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    let key = row.key;
                    let title = row.title;

                    let iconsToBeDisplayed = '';
                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'admin/img/pencil-square.svg" class="dataTablesActionIcon editButton" data-pmtype="buynow" data-key="' + key + '" data-title="' + title + '" alt="Edit item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">';
                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'admin/img/trash.svg" class="dataTablesActionIcon confirmActionButton" data-action="delete" data-pmtype="buynow" data-key="' + key + '" data-title="' + title + '" alt="Delete item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete">';
                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'admin/img/code-square.svg" class="dataTablesActionIcon copyShortCodeButton" data-pmtype="buynow" data-key="' + key + '" alt="Copy Shortcode" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy shortcode">';
                    return iconsToBeDisplayed;
                },
                className: "dt-center"
            }
        ],
        columnDefs: [
            {
                targets: [0, 1],
                render: DataTable.render.text(),
            },
            {
                targets: 3,
                render: DataTable.render.number(',', '.', 1, '$'),
            },
            {
                targets: [5, 6],
                render: DataTable.render.datetime(),
            },
            {
                targets: 7,
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
            $('#titleModalItemInfo').text('New BuyNow product');
            clearForm();
            setFormDefaultValues();
        })

        $('#tableUpdatedAt').text(getNowPrintable());
    });
});
