function rebindIconClicks() {
    /* Modify item */
    $('.editButton').click(function () {
        let clickedElementKey = $(this).data('key');

        clearNewBuyNowForm();

        $.ajax({
            url: requests.wordpress_pm_api.ajax_url,
            cache: false,
            dataType: "JSON",
            type: 'POST',
            data: {
                _ajax_nonce: requests.wordpress_pm_api.nonce,
                action: "plebeian-ajax_get_item_info",
                plebeian_buynow_item_key: clickedElementKey
            },
            success: function (response) {
                console.log('Information retrieved successfully!', response);

                if (response.success === true) {
                    let buynow_item_info = response.data;

                    if (buynow_item_info.is_mine === false) {
                        //                        showAlertModal("You don't have permission to modify this item because it's not yours.");
                        //                        return;
                    }

                    $('#titleModalItemInfo').text('Modify BuyNow product');

                    $('#key').val(buynow_item_info.key);
                    $('#title').val(buynow_item_info.title);
                    $('#description').val(buynow_item_info.description);
                    $('#price_usd').val(buynow_item_info.price_usd);
                    $('#available_quantity').val(buynow_item_info.available_quantity);
                    $('#shipping_from').val(buynow_item_info.shipping_from);
                    $('#shipping_domestic_usd').val(buynow_item_info.shipping_domestic_usd);
                    $('#shipping_worldwide_usd').val(buynow_item_info.shipping_worldwide_usd);

                    $('.sats_container').text('');

                    const modifyModal = new bootstrap.Modal('#add-buynow-modal', { keyboard: true });
                    modifyModal.show();

                } else {
                    console.log("ERROR getting information: ", response);
                }
                //$("#saveUserOptions").prop("disabled", false);
                //showSavedForAMoment(placeToFlashIfSuccessful, 2500);
            },
            error: function (error) {
                console.log("ERROR getting information: ", error);

                // showAlertModal('Error: ' + error.responseJSON.data.errorMessage);

                //showAlertModal(error.responseJSON.message)
                //$("#saveUserOptions").prop("disabled", false);
            }
        });
    });

    /* Show Delete item confirmation modal */
    $('.deleteButton').click(function () {
        let clickedElementKey = $(this).data('key');
        let clickedElementTitle = $(this).data('title');

        $('#delete-buynow-modal-body').html(
            '<p>Are you sure you want to <b>delete</b> this product?</p>' +
            '<p><b>' + clickedElementTitle + '</b></p>'
        );

        $('#deleteBuyNowItem').data('key', clickedElementKey);

        const deleteModal = new bootstrap.Modal('#delete-buynow-modal', { keyboard: true });
        deleteModal.show();
    });

    /* Copy short-code */
    $('.copyShortCodeButton').click(function () {
        let clickedElementKey = $(this).data('key');
        let template = '[plebeian_show_buynow key=THE_KEY]';
        let finalShortCode = template.replace('THE_KEY', clickedElementKey);

        navigator.clipboard.writeText(finalShortCode).then(function () {
            showNotification('<p><b>Shortcode copied</b>.</p> <p>Paste it in a post or page with CTRL + V.</p>');
        }, function () {
            showNotification('We cannot copy the shortcode. Your browser could not have permission to copy to the clipboard');
        });
    });
}

function clearNewBuyNowForm() {
    $('#buyNowForm')[0].classList.remove('was-validated');

    $('#key').val('');
    $('#title').val('');
    $('#description').val('');
    $('#price_usd').val('');
    $('#available_quantity').val('');
    $('#shipping_from').val('');
    $('#shipping_domestic_usd').val('');
    $('#shipping_worldwide_usd').val('');

    $('.sats_container').text('');
}

$(document).ready(function () {

    let buyNowDatatable = $('#table_buynow').DataTable({
        ajax: {
            'url': requests.pm_api.list.url,
            'dataSrc': 'listings'
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
                render: function (data, row, meta) {
                    return '<p></p>';       // images
                }
            },
            {
                data: 'created_at'
            },
            {
                data: 'start_date'
            },
            {
                render: function (data, type, row) {
                    let key = row.key;
                    let title = row.title;
                    let isMine = row.is_mine;

                    let iconsToBeDisplayed = '';

                    if (true) {
                        iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/pencil-square.svg" class="dataTablesActionIcon editButton" data-key="' + key + '" data-title="' + title + '" alt="Edit item">' +
                            '<img src="' + pluginBasePath + 'img/trash.svg" class="dataTablesActionIcon deleteButton" data-key="' + key + '" data-title="' + title + '" alt="Delete item">';
                    }

                    iconsToBeDisplayed += '<img src="' + pluginBasePath + 'img/code-square.svg" class="dataTablesActionIcon copyShortCodeButton" data-key="' + key + '" alt="Copy Shortcode">';

                    return iconsToBeDisplayed;
                }
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
                width: '6%'
            },
        ],
        fixedColumns: true

    }).on('draw', function () {
        $("#table_buynow").show();
        rebindIconClicks();

        $('.createButton').click(function () {
            $('#titleModalItemInfo').text('New BuyNow product');
            clearNewBuyNowForm();
        })
    });

    /* Delete Item after user confirmation */
    $('#deleteBuyNowItem').click(function () {
        $("#deleteBuyNowItem").prop("disabled", true);

        let clickedElementKey = $(this).data('key')
        console.log('clickedElementKey', clickedElementKey)
        requestURLDelete = request.delete.url.replace('{KEY}', clickedElementKey);

        $.ajax({
            url: requestURLDelete,
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: request.delete.method,
            headers: { "X-Access-Token": requests.pm_api.XAccessToken }
        })
            .done(function (response) {
                console.log('response', response);
                buyNowDatatable.ajax.reload();
                showNotification('<p><b>Item deleted successfully</b></p>');
            })
            .fail(function (e) {
                let errorMessage = e.responseJSON.message;
                console.log("ERROR : ", errorMessage);
                showNotification('<p><b>ERROR while trying to delete the item: ' + errorMessage + '</b>.</p> <p>Contact Plebeian Market support</p>');
            })
            .always(function () {
                $(".btn-delete").prop("disabled", false);

                $('#delete-buynow-modal').modal('hide');
            });
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
                url = requests.pm_api.edit.url.replace('{KEY}', key);
            } else {
                // New item
                modifying = false
                url = requests.pm_api.new.url;
            }

            console.log('modifying', modifying);

            $.ajax({
                url: url,
                data: JSON.stringify(buyNowFormData),
                cache: false,
                dataType: 'JSON',
                contentType: 'application/json;charset=UTF-8',
                type: modifying ? requests.pm_api.edit.method : requests.pm_api.new.method,
                headers: { "X-Access-Token": requests.pm_api.XAccessToken },
                success: function (response) {
                    // console.log('response', response);
                    console.log('modifying_2', modifying);
                    if (modifying) {
                        BtnReset($saveButton);
                        $('#add-buynow-modal').modal('hide');
                        buyNowDatatable.ajax.reload();
                        showNotification('<p><b>Item modified successfully!!</b></p>');
                    } else {
                        let newItemKey = response.listing.key;

                        $.ajax({
                            url: requests.pm_api.twitter.url.replace('{KEY}', newItemKey),
                            cache: false,
                            dataType: 'JSON',
                            contentType: 'application/json;charset=UTF-8',
                            type: requests.pm_api.twitter.method,
                            headers: { "X-Access-Token": requests.pm_api.XAccessToken }
                        })
                            .done(function (response_twitter) {
                                console.log('response_twitter', response_twitter);
                                buyNowDatatable.ajax.reload();
                                showNotification('<p><b>Item created successfully!!</b></p>');
                                $('#add-buynow-modal').modal('hide');
                            })
                            .fail(function (e) {
                                console.log('Error: ', e);
                                showAlertModal('ERROR while trying to save the item: '+e.message+'. Contact Plebeian Market support.');
                            })
                            .always(function () {
                                BtnReset($saveButton);
                            });
                    }
                },
                error: function (e) {
                    console.log("ERROR : ", e);

                    BtnReset($saveButton);

                    showAlertModal('ERROR while trying to save the item. Contact Plebeian Market support.');
                }
            });
        }
    });


    // Image uploader
    var ds = ds || {};

    var media;

    ds.media = media = {
        buttonId: '#open-media-modal',
        detailsTemplate: '#attachment-details-tmpl',

        frame: function() {
            if ( this._frame )
                return this._frame;

            this._frame = wp.media( {
                title: 'Select Your Images',
                button: {
                    text: 'Choose'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            } );

            this._frame.on( 'ready', this.ready );

            this._frame.state( 'library' ).on( 'select', this.select );

            return this._frame;
        },

        ready: function() {
            $( '.media-modal' ).addClass( 'no-sidebar smaller' );
        },

        select: function() {
            var settings = wp.media.view.settings,
                selection = this.get( 'selection' );

            $( '.added' ).remove();
            selection.map( media.showAttachmentDetails );
        },

        showAttachmentDetails: function( attachment ) {
            let url = attachment.attributes.url;
            console.log('url', url);

            $('.image-preview-wrapper').append('<img src="'+url+'" data-addednow="true" />')
        },

        init: function() {
            $( media.buttonId ).on( 'click', function( e ) {
                e.preventDefault();

                media.frame().open();
            });
        }
    };

    $( media.init );
});