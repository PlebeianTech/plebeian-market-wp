<?php

/**
 * The admin-specific ajax (internal api) functionality of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/admin
 */

class Plebeian_Market_Admin_Ajax_Api
{
	function ajax_admin_logout()
	{
		check_ajax_referer('save_options_nonce');

		update_option('plebeian_market_auth_key', false);

		wp_send_json_success();
	}

	function ajax_load_options()
	{
        $filter = sanitize_text_field($_POST['filter']) ?? null;

		wp_send_json_success(Plebeian_Market_Admin_Utils::plebeian_market_load_options($filter));
	}

	function ajax_save_options()
	{
		check_ajax_referer('save_options_nonce');

		foreach (PLEBEIAN_MARKET_OPTIONS as $option) {
			if (isset($_POST[$option])) {
				update_option($option, sanitize_text_field($_POST[$option]));
			}
		}

		wp_send_json_success();
	}

	function ajax_get_price_in_btc()
	{
		$item_price_usd = sanitize_text_field($_POST['plebeian_fiat_price']);

		if (!is_numeric($item_price_usd)) {
			wp_send_json_error([
				'errorMessage' => 'There was a problem reading the fiat price in the request.'
			], 400);
		}

		wp_send_json_success([
			'plebeian_sats_price' => Plebeian_Market_Communications::fiatToSats($item_price_usd)
		]);
	}

	function ajax_get_buynow_preview_html()
	{
		$parameters = $_POST['parameters'];

		foreach ($parameters as $key => $value) {
			if (substr($key, 0, strlen(PLEBEIAN_MARKET_FORM_FIELDS_PREFIX)) === PLEBEIAN_MARKET_FORM_FIELDS_PREFIX) {
				$key_without_prefix = substr($key, strlen(PLEBEIAN_MARKET_FORM_FIELDS_PREFIX));
				$parameters[$key_without_prefix] = sanitize_text_field($value);
			} else {
                $parameters[$key] = sanitize_text_field($value);
            }
		}

		$html = Plebeian_Market_Render::plebeian_item_render_html($parameters, 'buynow', (object)PLEBEIAN_MARKET_DEMO_BUYNOW_PRODUCT);

		wp_send_json_success([
			'html' => $html
		]);
	}

	function ajax_get_item_info()
	{
        $type = sanitize_text_field($_POST['plebeian_item_type']);
        $key = sanitize_text_field($_POST['plebeian_item_key']);

        $item = Plebeian_Market_Communications::getItem($type, $key);

		if ($item) {
			wp_send_json_success($item);
		}

		wp_send_json_error([
			'errorMessage' => 'There was a problem getting the info for the item with key=' . $key . '.'
		], 400);
	}

	function ajax_save_image_into_item()
	{
        $pmtype = sanitize_text_field($_POST['plebeian_item_type']);
		$key = sanitize_text_field($_POST['plebeian_item_key']);
		$images = $_POST['images'];

		$saveImages =	$images['save'];
		$deleteImages =	$images['delete'];

        $backendAPIUrl = Plebeian_Market_Communications::getBackendAPIUrl();

        if ($pmtype == 'auction') {
            $addUrl = $backendAPIUrl . PLEBEIAN_MARKET_API_ADD_MEDIA_AUCTION_URL;
            $deleteUrl = $backendAPIUrl . PLEBEIAN_MARKET_API_DELETE_MEDIA_AUCTION_URL;
        } else {
            $addUrl = $backendAPIUrl . PLEBEIAN_MARKET_API_ADD_MEDIA_BUYNOW_URL;
            $deleteUrl = $backendAPIUrl . PLEBEIAN_MARKET_API_DELETE_MEDIA_BUYNOW_URL;
        }

		$addUrl = str_replace('{KEY}', $key, $addUrl);
		$deleteUrl = str_replace('{KEY}', $key, $deleteUrl);

		foreach ($saveImages as $saveImage) {
			$imageUrl = $saveImage['url'];
			$filename = pathinfo($imageUrl, PATHINFO_BASENAME);
			$imagePathLocal = "/tmp/" . $filename;

			file_put_contents($imagePathLocal, file_get_contents($imageUrl));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $addUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'X-Access-Token: ' . Plebeian_Market_Communications::getXAccessToken(),
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'media' => new CURLFile($imagePathLocal)
			]);
			$result = curl_exec($ch);

			$curl_errNo = curl_errno($ch);
			if ($curl_errNo) {
				wp_send_json_error([
					'errorMessage' => 'There was a problem uploading pictures using PM API: ' . $curl_errNo,
					'host' => $addUrl
				], 400);
			}
		}

		foreach ($deleteImages as $deleteImage) {
			$imageHash = $deleteImage['hash'];
			$deleteUrlThisImage = str_replace('{HASH}', $imageHash, $deleteUrl);

			$deleteImageResponse = wp_remote_request(
				$deleteUrlThisImage,
				[
					'headers'     => [
						'X-Access-Token' => Plebeian_Market_Communications::getXAccessToken()
					],
					'method'     => PLEBEIAN_MARKET_API_DELETE_MEDIA_BUYNOW_METHOD
				]
			);
			$deleteImage_http_code = wp_remote_retrieve_response_code($deleteImageResponse);
			echo (" - HTTP code: " . $deleteImage_http_code);
			if (!$deleteImage_http_code == 200) {
				wp_send_json_error([
					'errorMessage' => 'There was a problem deleting pictures using PM API'
				], 400);
			}
		}

		wp_send_json_success();
	}
}
