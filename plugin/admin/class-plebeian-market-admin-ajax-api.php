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

	function ajax_load_options()
	{
		wp_send_json_success([
			'plebeian_auth_key' => get_option('plebeian_market_auth_key'),
			'plebeian_url_connect' => get_option('plebeian_market_url_connect')
		]);
	}

	function ajax_save_options()
	{
		check_ajax_referer('save_options_nonce');

		$plebeian_auth_key = $_POST['plebeian_auth_key'];
		$plebeian_url_connect = $_POST['plebeian_url_connect'];

		if (false) { // TO-DO checks
			wp_send_json_error([
				'errorMessage' => 'Options could not be saved. Contact Plebeian Market support.'
			], 400);
		}

		update_option('plebeian_market_auth_key', $plebeian_auth_key);
		update_option('plebeian_market_url_connect', $plebeian_url_connect);

		wp_send_json_success();
	}

	function ajax_get_price_in_btc()
	{
		$item_price_usd = $_POST['plebeian_fiat_price'];

		if (!is_numeric($item_price_usd)) {
			wp_send_json_error([
				'errorMessage' => 'There was a problem reading the fiat price in the request.'
			], 400);
		}

		wp_send_json_success([
			'plebeian_sats_price' => Plebeian_Market_Communications::fiatToSats($item_price_usd)
		]);
	}

	function ajax_get_item_info()
	{
		$key = $_POST['plebeian_buynow_item_key'];

		$buyNowItem = Plebeian_Market_Communications::getBuyNow($key);

		if ($buyNowItem) {
			wp_send_json_success($buyNowItem);
		}

		wp_send_json_error([
			'errorMessage' => 'There was a problem getting the info for the BuyNow item with key=' . $key . '.'
		], 400);
	}

	function ajax_save_image_into_item()
	{
		$key = $_POST['item_key'];
		$images = $_POST['images'];

		$saveImages =	$images['save'];
		$deleteImages =	$images['delete'];

		$addUrl = Plebeian_Market_Communications::getBackendAPIUrl() . PM_API_ADD_MEDIA_BUYNOW_URL;
		$addUrl = str_replace('{KEY}', $key, $addUrl);

		$deleteUrl = Plebeian_Market_Communications::getBackendAPIUrl() . PM_API_DELETE_MEDIA_BUYNOW_URL;

		foreach ($saveImages as $saveImage) {
			$imageUrl = $saveImage['url'];
			$filename = pathinfo($imageUrl, PATHINFO_FILENAME);
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

			$deleteUrl = str_replace(['{KEY}', '{HASH}'], [$key, $imageHash], $deleteUrl);

			$deleteImageResponse = wp_remote_request(
				$deleteUrl,
				[
					'headers'     => [
						'X-Access-Token' => Plebeian_Market_Communications::getXAccessToken()
					],
					'method'     => PM_API_DELETE_MEDIA_BUYNOW_METHOD
				]
			);
			$deleteImage_http_code = wp_remote_retrieve_response_code($deleteImageResponse);
			if (!$deleteImage_http_code === 200) {
				wp_send_json_error([
					'errorMessage' => 'There was a problem deleting pictures using PM API'
				], 400);
			}
		}

		wp_send_json_success();
	}
}
