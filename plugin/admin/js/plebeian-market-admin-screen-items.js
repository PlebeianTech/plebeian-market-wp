// This file gets included in both Auctions and BuyNow screens
// and contains common functions used in both screens

let itemsDatatable;

function addImageToProduct(url, hash, index, saved) {
    let imageHTML =
        '<li class="ui-state-default">' +
        '   <img src="' + url + '" class="img-thumbnail" data-hash="' + hash + '" data-index="' + index + '" data-saved="' + saved + '" />' +
        '   <span class="product-images-delete-badge" data-hash="' + hash + '">-</span>' +
        '</li>';
    $("#product-images-container").append(imageHTML);

    bindDeleteImagesAgainAfterAddingNew();
}

function bindDeleteImagesAgainAfterAddingNew() {
    $('.product-images-delete-badge').off().click(function () {
        let deleteButtonHash = $(this).data('hash');
        console.log('deleting images with hash:', deleteButtonHash);

        $('#product-images-container img').each(function () {
            let imageObject = $(this);

            if (imageObject.data('hash') === deleteButtonHash) {
                imageObject.data('delete', 'true');
                imageObject.parent().hide();
            }
        });
    });
}

function makeImagesOrderable() {
    $("#product-images-container").sortable({
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    });
    $("#product-images-container").disableSelection();
}

function saveImagesToProduct(pmtype, key) {
    let imagesSave = [];
    let imagesDelete = [];

    $('#product-images-container img').each(function () {
        let image = this;
        console.log('* Processing image for item ' + key + ':', image.src);

        let imageAlreadySaved = $(image).data('saved');
        let imageHash = $(image).data('hash');
        let imageIndex = $(image).data('index');
        let imageDeleteNow = $(image).data('delete');

        if (imageAlreadySaved) {
            // Image already saved
            if (imageDeleteNow) {
                console.log("   - delete image:", image.src);
                imagesDelete.push({
                    hash: imageHash,
                });
            } else {
                console.log("   - already saved and not deleted. Ignoring:", image.src);
            }

        } else {
            // Image not saved (added now)
            if (imageDeleteNow) {
                console.log("   - not saved and then deleted. Ignoring:", image.src);
            } else {
                console.log("   - saving image:", image.src);
                imagesSave.push({
                    url: image.src
                });
            }
        }
    });

    console.log('* images save', imagesSave);
    console.log('* images delete', imagesDelete);

    if (imagesSave.length !== 0 || imagesDelete.length !== 0) {
        $.ajax({
            url: requests.wordpress_pm_api.ajax_url,
            cache: false,
            dataType: "JSON",
            type: 'POST',
            data: {
                _ajax_nonce: requests.wordpress_pm_api.nonce,
                action: "plebeian-ajax_save_image_into_item",
                plebeian_item_key: key,
                plebeian_item_type: pmtype,
                images: {
                    save: imagesSave,
                    delete: imagesDelete
                }
            },
            success: function (response) {
                console.log('Information retrieved successfully!', response);

                if (response.success === true) {


                } else {
                    console.log("ERROR getting information: ", response);
                }
            },
            error: function (error) {
                console.log("ERROR getting information: ", error);
            }
        });
    }
}

function clearForm() {
    $('#itemForm')[0].classList.remove('was-validated');

    $('#key').val('');
    $('#title').val('');
    $('#description').val('');
    $('#price_usd').val('');
    $('#available_quantity').val('');
    $('#shipping_from').val('');
    $('#shipping_domestic_usd').val('');
    $('#shipping_worldwide_usd').val('');

    $('#product-images-container').empty();

    $('.sats_container').text('');
}

function rebindIconClicks() {
    /* Modify item */
    $('.editButton').click(function () {
        let pmtype = $(this).data('pmtype');
        let clickedElementKey = $(this).data('key');

        clearForm();

        $.ajax({
            url: requests.wordpress_pm_api.ajax_url,
            cache: false,
            dataType: "JSON",
            type: 'POST',
            data: {
                _ajax_nonce: requests.wordpress_pm_api.nonce,
                action: 'plebeian-ajax_get_item_info',
                plebeian_item_key: clickedElementKey,
                plebeian_item_type: pmtype
            },
            success: function (response) {
                console.log('Information retrieved successfully!', response);

                if (response.success === true) {
                    let item_info_from_api = response.data;

                    if (item_info_from_api.is_mine === false) {
                        //                        showAlertModal("You don't have permission to modify this item because it's not yours.");
                        //                        return;
                    }

                    $('#titleModalItemInfo').text(pmtype === 'buynow' ? 'Modify BuyNow product' : 'Modify Auction');

                    $('#key').val(item_info_from_api.key);
                    $('#title').val(item_info_from_api.title);
                    $('#description').val(item_info_from_api.description);

                    $('#shipping_from').val(item_info_from_api.shipping_from);
                    $('#shipping_domestic_usd').val(item_info_from_api.shipping_domestic_usd);
                    $('#shipping_worldwide_usd').val(item_info_from_api.shipping_worldwide_usd);

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

                    } else if (pmtype === 'buynow') {
                        $('#price_usd').val(item_info_from_api.price_usd);
                        $('#available_quantity').val(item_info_from_api.available_quantity);
                    }

                    $(item_info_from_api.media).each(function () {
                        addImageToProduct(this.url, this.hash, this.index, 'true');
                    });

                    $('.sats_container').text('');

                    const modifyModal = new bootstrap.Modal('#add-item-modal', { keyboard: true });
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
        let pmtype = $(this).data('pmtype');
        let clickedElementKey = $(this).data('key');
        let clickedElementTitle = $(this).data('title');

        $('#delete-item-modal-body').html(
            '<p>Are you sure you want to <b>delete</b> this item?</p>' +
            '<p><b>' + clickedElementTitle + '</b></p>'
        );

        $('#deleteItem')
            .data('pmtype', pmtype)
            .data('key', clickedElementKey);

        const deleteModal = new bootstrap.Modal('#delete-item-modal', { keyboard: true });
        deleteModal.show();
    });

    /* Copy short-code */
    $('.copyShortCodeButton').click(function () {
        let pmtype = $(this).data('pmtype');
        let clickedElementKey = $(this).data('key');
        let finalShortCode = '[plebeian_show_'+pmtype+' key='+clickedElementKey+']';

        navigator.clipboard.writeText(finalShortCode).then(function () {
            showNotification('<p><b>Shortcode copied</b>.</p> <p>Paste it in a post or page with CTRL + V.</p>');
        }, function () {
            showNotification('We cannot copy the shortcode. Your browser could not have permission to copy to the clipboard');
        });
    });
}

$(document).ready( function () {
    makeImagesOrderable();

    /* Delete Item after user confirmation */
    $('#deleteItem').click(function () {
        $("#deleteItem").prop("disabled", true);

        let pmtype = $(this).data('pmtype');
        let clickedElementKey = $(this).data('key');

        $.ajax({
            url: requests.pm_api[pmtype].delete.url.replace('{KEY}', clickedElementKey),
            cache: false,
            dataType: 'JSON',
            contentType: 'application/json;charset=UTF-8',
            type: requests.pm_api[pmtype].delete.method,
            headers: { "X-Access-Token": requests.pm_api.XAccessToken }
        })
            .done(function (response) {
                itemsDatatable.ajax.reload();
                showNotification('<p><b>Item deleted successfully</b></p>');
            })
            .fail(function (e) {
                let errorMessage = e.responseJSON.message;
                console.log("ERROR : ", errorMessage);
                showNotification('<p><b>ERROR while trying to delete the item: ' + errorMessage + '</b>.</p> <p>Contact Plebeian Market support</p>');
            })
            .always(function () {
                $(".btn-delete").prop("disabled", false);
                $('#delete-item-modal').modal('hide');
            });
    });

    /* Save Form */
    $('#saveItem').click(function () {
        let form = $('#itemForm')[0];
        let pmtype = $(form).data('pmtype');
        let validity = form.checkValidity();
        form.classList.add('was-validated');

        if (validity) {
            let saveButton = $(this);
            BtnLoading(saveButton, 'Saving...');

            let itemForm = $("#itemForm");
            let itemFormData = getFormData(itemForm);

            let url;
            let modifying;

            let key = itemFormData['key'];

            if (pmtype === 'auction') {
                let duration = document.getElementById('duration').value;
                let duration_unit = document.getElementById('duration_unit').value;

                if (duration_unit === 'd') {
                    duration *= 24;
                }

                itemFormData['duration_hours'] = duration;
            }

            if (typeof key !== 'undefined' && key !== '') {
                // Modifying
                modifying = true;
                url = requests.pm_api[pmtype].edit.url.replace('{KEY}', key);
            } else {
                // New item
                modifying = false
                url = requests.pm_api[pmtype].new.url;

                itemFormData['start_date'] = (new Date()).toISOString();
            }

            console.log('modifying', modifying);

            $.ajax({
                url: url,
                data: JSON.stringify(itemFormData),
                cache: false,
                dataType: 'JSON',
                contentType: 'application/json;charset=UTF-8',
                type: modifying ? requests.pm_api[pmtype].edit.method : requests.pm_api[pmtype].new.method,
                headers: { "X-Access-Token": requests.pm_api.XAccessToken },
            })
                .done(function (response) {
                    console.log('response', response);

                    console.log('Product saved correctly. Saving images now...');

                    if (modifying) {
                        saveImagesToProduct(pmtype, key);
                        $('#add-item-modal').modal('hide');
                        showNotification('<p><b>Item modified successfully!!</b></p>');
                    } else {
                        let newItemKey = response[pmtype].key;
                        saveImagesToProduct(pmtype, newItemKey);
                        showNotification('<p><b>Item created successfully!!</b></p>');

                        // START
                        $.ajax({
                            url: requests.pm_api[pmtype].start.url.replace('{KEY}', newItemKey),
                            cache: false,
                            dataType: 'JSON',
                            data: '{}',
                            contentType: 'application/json;charset=UTF-8',
                            type: requests.pm_api[pmtype].start.method,
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
                    $('#add-item-modal').modal('hide');
                })
                .fail(function (e) {
                    console.log('Error: ', e);
                    showAlertModal('ERROR while trying to save the item: ' + e.responseJSON.message + '. Contact Plebeian Market support.');
                })
                .always(function () {
                    BtnReset(saveButton);
                });
        }
    });

    // WordPress image gallery setup
    var ds = ds || {};

    var media;

    ds.media = media = {
        buttonId: '#open-media-modal',
        detailsTemplate: '#attachment-details-tmpl',

        frame: function () {
            if (this._frame)
                return this._frame;

            this._frame = wp.media({
                title: 'Select Your Images',
                button: {
                    text: 'Choose'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            this._frame.on('ready', this.ready);

            this._frame.state('library').on('select', this.select);

            return this._frame;
        },

        ready: function () {
            $('.media-modal').addClass('no-sidebar smaller');
        },

        select: function () {
            var settings = wp.media.view.settings,
                selection = this.get('selection');

            $('.added').remove();
            selection.map(media.showAttachmentDetails);
        },

        showAttachmentDetails: function (attachment) {
            let url = attachment.attributes.url;
            addImageToProduct(url, '', null, 'false');
        },

        init: function () {
            $(media.buttonId).on('click', function (e) {
                e.preventDefault();

                media.frame().open();
            });
        }
    };

    $(media.init);
});
