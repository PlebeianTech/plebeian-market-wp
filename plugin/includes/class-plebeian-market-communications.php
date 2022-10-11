<?php

/**
 * Communication related functions
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/includes
 */

/**
 * @since      1.0.0
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/includes
 */
class Plebeian_Market_Communications {

	/**
	 * Get the URL to the Plebeian Market.
	 *
	 * This will return the default URL if the user didn't save a new one, or the new saved one.
	 *
	 * @since    1.0.0
	 */
	public static function getAPIUrl() {
		/*
		If getOptions .....
		*/
		//$customUrl = get_option('plebeian_market_url_connect');
		//return ($customUrl !== false) ? "https" : PM_API_URL_DEFAULT;

		return PM_API_URL_DEFAULT;
	}

	public static function getBackendAPIUrl() {
		/*
		If getOptions .....
		*/
		//$customUrl = get_option('plebeian_market_url_connect');
		//return ($customUrl !== false) ? "https" : PM_API_URL_DEFAULT;

		return PM_API_URL_BACKEND_DEFAULT;
	}

	public static function getXAccessToken() {
		return get_option('plebeian_market_auth_key');
	}

	public static function getUser() {
		return 'btc_remnant';
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function getFeatured($type) {
		if ( ! in_array($type, ['auctions', 'listings'])) {
			return [];
		}

		if ($type === 'auctions') {
			$auctions_response = wp_remote_get("https://plebeian.market/api/$type/featured");
			//$auctions_response = wp_remote_get(self::getAPIUrl() . "/$type/featured");
		}
        
        $auctions_body_json = wp_remote_retrieve_body($auctions_response);
        $auctions_http_code = wp_remote_retrieve_response_code( $auctions_response );

		if ($auctions_http_code === 200) {
			$auctions_body_array = json_decode($auctions_body_json);
			return $auctions_body_array->{$type};
		} else {
			return null;
		}
	}



	/**
	 * Buy now
	 */
	public static function getBuyNow($key) {
		$buyNowItem_response = wp_remote_get(self::getBackendAPIUrl() . '/listings/' . $key);

		$buyNowItem_body_json = wp_remote_retrieve_body($buyNowItem_response);
        $buyNowItem_http_code = wp_remote_retrieve_response_code($buyNowItem_response);

		if ($buyNowItem_http_code === 200) {
			$buyNowItem_body = json_decode($buyNowItem_body_json);
			return $buyNowItem_body->listing;
		}

		return null;
	}
	public static function getBuyNowListing() {
		$buyNowListing_respose = wp_remote_get(self::getBackendAPIUrl() . '/listings/featured');

		$buyNowListing_body_json = wp_remote_retrieve_body($buyNowListing_respose);
        $buyNowListing_http_code = wp_remote_retrieve_response_code($buyNowListing_respose);

		if ($buyNowListing_http_code === 200) {
			$buyNowListing_body = json_decode($buyNowListing_body_json);
			return $buyNowListing_body->listings;
		}

		return null;
	}

	/**
	 * Auctions
	 */
	public static function getAuctionInfo($auction_id) {
		$auction_response = wp_remote_get('https://plebeian.market/auctions/' . $auction_id);
        $auction_body_json = wp_remote_retrieve_body($auction_response);
        $auction_http_code = wp_remote_retrieve_response_code( $auction_response );

		if ($auction_http_code === 200) {
			$auction_body = json_decode($auction_body_json);
			return $auction_body;
		} else {
			return null;
		}
	}

	/**
	 * Converts a price given in fiat (USD for now) to satoshis.
	 * 
	 * 1- Obtains the Bitcoin price in USD from the Kraken API.
	 * 
	 * 2- Returns the price of the item in satoshis.
	 * 
	 * Uses the WordPress cache so we don't query Kraken too often.
	 */
	public static function getBTCPrice($item_price_usd) {
		$cache_key = 'bitcoin_price';
		$bitcoin_price_quote = get_transient($cache_key);

		if (false === $bitcoin_price_quote) {

			$btcprice_response = wp_remote_get(KRAKEN_BTCUSD_API_URL);
			$btcprice_body_json = wp_remote_retrieve_body($btcprice_response);
			$btcprice_http_code = wp_remote_retrieve_response_code($btcprice_response);
	
			if ($btcprice_http_code === 200) {
				$btcprice_body = json_decode($btcprice_body_json);
				$bitcoin_price_quote = $btcprice_body->result->XXBTZUSD->c[0];
			} else {
				return null;
			}

			set_transient($cache_key, $bitcoin_price_quote, KRAKEN_BTCUSD_API_CACHETIME);
		}

		$item_price_bitcoin = $item_price_usd / $bitcoin_price_quote;
		$item_price_satoshi = $item_price_bitcoin * 100000000;

		return round($item_price_satoshi, 0);
	}

	public static function fiatToBitcoin($fiat) {

	}

	public static function bitcoinToFiat($sats) {

	}
}