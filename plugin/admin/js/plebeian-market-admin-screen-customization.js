$(document).ready(function () {

    // Get options from WP
    $.ajax({
        url: wp_api_ajax_params.ajax_url,
        cache: false,
        type: 'POST',
        data: {
            _ajax_nonce: wp_api_ajax_params.nonce,
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
                _ajax_nonce: wp_api_ajax_params.nonce,
                action: 'plebeian-save-options'
            };

            $(form).find(':input').each(function () {
                let id = this.id;
                let value = $(this).val();

                if (id.startsWith('plebeian_market')) {
                    data[id] = value;
                }
            })

            $.ajax({
                url: wp_api_ajax_params.ajax_url,
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

});
