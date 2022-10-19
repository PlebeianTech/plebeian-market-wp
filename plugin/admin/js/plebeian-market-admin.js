let $ = jQuery;

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

/**
 * 
 * @param {string} text     - Mandatory - the text to show in the notification
 * @param {string} whenText - Optional - The time when the action related with the notification occurred. If
 *                            not provided, will print "Just now"
 * @param {string} title    - Optional - The title of the notification. If not provided, will print "Plebeian Market"
 */
 function showNotification(text, whenText, title) {
    let notificationElement = document.getElementById('liveToast');
    let notification = new bootstrap.Toast(notificationElement);

    $('#liveToastBody').html(text);

    if (typeof whenText !== 'undefined') {
        $('#liveToastSmallWhen').html(whenText);
    }

    if (typeof title !== 'undefined') {
        $('#liveToastTitle').html(title);
    }

    notification.show();
}

function showAlertModal(message) {
    $('#alertModalText').text(message);

    const myModal = new bootstrap.Modal('#alertModal', {keyboard: true});
    myModal.show();
}

function BtnLoading(elem, textLoading) {
    $(elem).data("original-text", $(elem).html());
    $(elem).prop("disabled", true);
    $(elem).html('<i class="spinner-border spinner-border-sm"></i> ' + textLoading);
}
function BtnReset(elem) {
    $(elem).prop("disabled", false);
    $(elem).html($(elem).data("original-text"));
}

$(document).ready( function () {
    $('.fiat-to-btc-source').keyup(function() {
        let elementId = this.id;
        let fiatPrice = this.value;

        if ( ! $.isNumeric(fiatPrice)) {
            $('#' + elementId + '_sats').text('-');
            return;
        }

        $.ajax({
            url: requests.wordpress_pm_api.ajax_url,
            cache: false,
            type: 'POST',
            data: {
                _ajax_nonce: requests.wordpress_pm_api.nonce,
                action: "plebeian-get-price-btc",
                plebeian_fiat_price: fiatPrice
            },
            success: function (response) {
                if (response.success === true && $.isNumeric(response.data.plebeian_sats_price)) {
                    let sats = response.data.plebeian_sats_price;
                    $('#' + elementId + '_sats').text('~' + sats + ' sats');
                } else {
                    $('#' + elementId + '_sats').text('-');
                }
            },
            error: function (error) {
                console.log("ERROR loading values from WordPress : ", error);
    
                $('#' + elementId + '_sats').text('-');
            }
        });
    });
})