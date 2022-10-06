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

class Plebeian_Market_Admin_Ajax_Api {

	function ajax_load_options() {
		wp_send_json_success([
			'plebeian_auth_key' => get_option('plebeian_market_auth_key'),
			'plebeian_url_connect' => get_option('plebeian_market_url_connect')
		]);
	}

	/**
	 * AJAX handler using JSON
	 */
	function ajax_save_options() {
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

	function ajax_get_price_in_btc() {
		$item_price_usd = $_POST['plebeian_fiat_price'];

		if ( ! is_numeric($item_price_usd)) {
			wp_send_json_error([
				'errorMessage' => 'There was a problem reading the fiat price in the request.'
			], 400);
		}

		wp_send_json_success([
			'plebeian_sats_price' => Plebeian_Market_Communications::getBTCPrice($item_price_usd)
		]);
	}

	function ajax_get_item_info() {
		$key = $_POST['plebeian_buynow_item_key'];

		$buyNowItem = Plebeian_Market_Communications::getBuyNow($key);

		if ($buyNowItem) {
			wp_send_json_success($buyNowItem);
		}

		wp_send_json_error([
			'errorMessage' => 'There was a problem getting the info for the BuyNow item with key=' . $key . '.'
		], 400);
	}
}