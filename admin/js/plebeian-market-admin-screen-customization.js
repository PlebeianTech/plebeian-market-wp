function getBuyNowPreview() {
    let parameters = {};

    $('#customizationForm').find(':input').each(function () {
        let id = this.id;

        if (id.startsWith('plebeian_market')) {
            parameters[id] = $(this).val();
        }
    });

    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: 'plebeian-get_buynow_preview_html',
            parameters: parameters
        },
        success: function (response) {
            console.log('BuyNow preview loaded successfully!', response);
            let data = response.data;

            $('#buyNowPreview').html(data.html);

            setupSlideshow();
        },
        error: function (error) {
            console.error("getBuyNowPreview - ERROR loading values from WordPress : ", error);
            $('#buyNowPreview').html('<p>Preview not available.</p>');
        }
    });
}

$(document).ready(function () {

    // Get options from WP
    $.ajax({
        url: requests.wordpress_pm_api.ajax_url,
        cache: false,
        type: 'POST',
        data: {
            _ajax_nonce: requests.wordpress_pm_api.nonce,
            action: 'plebeian-load-options',
            filter: 'plebeian_market_widget_'
        },
        success: function (response) {
            console.log('Options loaded successfully!', response);
            let data = response.data;

            $.each(data, function (index, value) {
                if (index.startsWith('plebeian_market')) {
                    console.log('each_data: ' + index, value);

                    let formElement = $('#' + index);

                    if ($(formElement).length > 0) {
                        formElement.val(value);
                    } else {
                        console.log("Field doesn't exist in the form: " + index);
                    }
                }
            });

            getBuyNowPreview();
        },
        error: function (error) {
            console.error("ERROR loading values from WordPress : ", error);
        }
    });

    // Save values into WordPress Options
    $('#saveUserOptions').click(function (e) {
        e.preventDefault();

        let form = $('#customizationForm')[0];
        let validity = form.checkValidity();
        form.classList.add('was-validated');

        if (validity) {
            let data = {
                _ajax_nonce: requests.wordpress_pm_api.nonce,
                action: 'plebeian-save-options'
            };

            $(form).find(':input').each(function () {
                let id = this.id;
                let value = $(this).val();

                if (id.startsWith('plebeian_market')) {
                    data[id] = value;
                }
            });

            $.ajax({
                url: requests.wordpress_pm_api.ajax_url,
                cache: false,
                dataType: "JSON",
                type: 'POST',
                data: data,
                success: function (response) {
                    console.log('Options saved successfully!');
                    showNotification('<p><b>Options saved successfully!!</b></p>');
                },
                error: function (error) {
                    console.log("ERROR saving values into WordPress : ", error);
                    showAlertModal('Error: ' + error.responseJSON.data.errorMessage);
                }
            });
        }
    });

    // This would be called if any of the input element has got a change inside the form
    $('#customizationForm').find(':input').on('input', function () {
        getBuyNowPreview();
    });
});
