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
                className: 'createButton',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#add-buynow-modal'
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
                data: 'start_date',
                className: "dt-center"
            },
            {
                render: function (data, type, row) {
                    let key = row.key;
                    let title = row.title;
                    let isMine = row.is_mine;

                    let iconsToBeDisplayed = '';

                    if (true) {
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/pencil-square.svg" class="dataTablesActionIcon editButton" data-pmtype="buynow" data-key="' + key + '" data-title="' + title + '" alt="Edit item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">' +
                            '<img src="' + pluginBasePath + 'img/trash.svg" class="dataTablesActionIcon deleteButton" data-pmtype="buynow" data-key="' + key + '" data-title="' + title + '" alt="Delete item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete">';
                    }

                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/code-square.svg" class="dataTablesActionIcon copyShortCodeButton" data-pmtype="buynow" data-key="' + key + '" alt="Copy Shortcode" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy shortcode">';

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

        $('.createButton').click(function () {
            $('#titleModalItemInfo').text('New BuyNow product');
            clearForm();
        })
    });

    /* Save Form */
    $('#saveBuyNowItem').click(function () {
        let form = $('#buyNowForm')[0];
        let validity = form.checkValidity();
        form.classList.add('was-validated');

        if (validity) {
            var $saveButton = $(this);
            BtnLoading($saveButton, 'Saving...');

            let buyNowForm = $("#buyNowForm");
            let buyNowFormData = getFormData(buyNowForm);

            let key = buyNowFormData['key'];

            let url;
            let modifying;

            if (typeof key !== 'undefined' && key !== '') {
                // Modifying
                modifying = true;
                url = requests.pm_api.buynow.edit.url.replace('{KEY}', key);
            } else {
                // New item
                modifying = false
                url = requests.pm_api.buynow.new.url;

                buyNowFormData['start_date'] = (new Date()).toISOString();
            }

            console.log('modifying', modifying);

            $.ajax({
                url: url,
                data: JSON.stringify(buyNowFormData),
                cache: false,
                dataType: 'JSON',
                contentType: 'application/json;charset=UTF-8',
                type: modifying ? requests.pm_api.buynow.edit.method : requests.pm_api.buynow.new.method,
                headers: { "X-Access-Token": requests.pm_api.XAccessToken },
            })
                .done(function (response) {
                    console.log('response', response);

                    console.log('Product saved correctly. Saving images now...');

                    if (modifying) {
                        saveImagesToProduct(key);
                        $('#add-buynow-modal').modal('hide');
                        showNotification('<p><b>Item modified successfully!!</b></p>');
                    } else {
                        let newItemKey = response.listing.key;
                        saveImagesToProduct(newItemKey);
                        showNotification('<p><b>Item created successfully!!</b></p>');

                        // START
                        $.ajax({
                            url: requests.pm_api.buynow.start.url.replace('{KEY}', newItemKey),
                            cache: false,
                            dataType: 'JSON',
                            data: '{}',
                            contentType: 'application/json;charset=UTF-8',
                            type: requests.pm_api.buynow.start.method,
                            headers: { "X-Access-Token": requests.pm_api.XAccessToken },
                        })
                            .done(function (response) {
                                console.log('Start OK. Response: ', response);
                            })
                            .fail(function (e) {
                                console.log('Error: ', e);
                            })
                            .always(function () {
                            });
                    }

                    itemsDatatable.ajax.reload();
                    $('#add-buynow-modal').modal('hide');
                })
                .fail(function (e) {
                    console.log('Error: ', e);
                    showAlertModal('ERROR while trying to save the item: ' + e.message + '. Contact Plebeian Market support.');
                })
                .always(function () {
                    BtnReset($saveButton);
                });
        }
    });
});