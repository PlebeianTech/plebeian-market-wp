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
class Plebeian_Market_Communications
{

	/**
	 * Get the URL to the Plebeian Market.
	 *
	 * This will return the saved URL or the default one if none was saved.
	 *
	 * @since    1.0.0
	 */
	public static function getAPIUrl()
	{
		$customUrl = get_option('plebeian_market_url_connect');
		return ($customUrl === false || $customUrl === '') ? PLEBEIAN_MARKET_API_URL_DEFAULT : $customUrl;
	}

	public static function getBackendAPIUrl()
	{
		$customUrl = get_option('plebeian_market_url_connect');
		return ($customUrl === false || $customUrl === '') ? PLEBEIAN_MARKET_API_URL_BACKEND_DEFAULT : $customUrl;
	}

	public static function getXAccessToken()
	{
		return get_option('plebeian_market_auth_key');
	}

    public static function getItem($type, $key)
    {
        switch($type) {
            case 'buynow':
                $query_path = 'listings';
                $json_path = 'listing';
                break;
            case 'auction':
                $query_path = 'auctions';
                $json_path = 'auction';
                break;
        }

        $item_response = wp_remote_get(
            self::getBackendAPIUrl() . '/' . $query_path . '/' . $key,
            [
                'headers'     => [
                    'X-Access-Token' => Plebeian_Market_Communications::getXAccessToken()
                ]
            ]
        );
        $item_body_json = wp_remote_retrieve_body($item_response);
        $item_http_code = wp_remote_retrieve_response_code($item_response);

        if ($item_http_code === 200) {
            $item_body = json_decode($item_body_json);
            return $item_body->{$json_path};
        }

        return null;
    }

	public static function getBuyNowListing()
	{
		$buyNowListing_respose = wp_remote_get(
			self::getBackendAPIUrl() . PLEBEIAN_MARKET_API_LIST_BUYNOW_URL,
			[
				'headers'     => [
					'X-Access-Token' => Plebeian_Market_Communications::getXAccessToken()
				]
			]
		);

		$buyNowListing_body_json = wp_remote_retrieve_body($buyNowListing_respose);
		$buyNowListing_http_code = wp_remote_retrieve_response_code($buyNowListing_respose);

		if ($buyNowListing_http_code === 200) {
			$buyNowListing_body = json_decode($buyNowListing_body_json);
			return $buyNowListing_body->listings;
		}

		return null;
	}

	public static function getBTCPriceInUSD()
	{
		$cache_key = 'bitcoin_price';
		$bitcoin_price_quote = get_transient($cache_key);

		if (false === $bitcoin_price_quote) {

			$btcprice_response = wp_remote_get(PLEBEIAN_MARKET_KRAKEN_BTCUSD_API_URL);
			$btcprice_body_json = wp_remote_retrieve_body($btcprice_response);
			$btcprice_http_code = wp_remote_retrieve_response_code($btcprice_response);

			if ($btcprice_http_code === 200) {
				$btcprice_body = json_decode($btcprice_body_json);
				$bitcoin_price_quote = $btcprice_body->result->XXBTZUSD->c[0];
			} else {
				return 0;
			}

			set_transient($cache_key, $bitcoin_price_quote, PLEBEIAN_MARKET_KRAKEN_BTCUSD_API_CACHETIME);
		}

		return $bitcoin_price_quote;
	}

	/**
	 * Converts a price given in fiat (USD for now) to satoshis.
	 *
	 * 1- Obtains the Bitcoin price in USD from the Kraken API.
	 *
	 * 2- Returns the price of the item in satoshis.
	 *
	 * Uses the WordPress cache, so we don't query Kraken too often.
	 */
	public static function fiatToSats($item_price_usd)
	{
		$bitcoin_price = self::getBTCPriceInUSD();

		$item_price_bitcoin = $item_price_usd / $bitcoin_price;
		$item_price_satoshi = $item_price_bitcoin * 100000000;

		return round($item_price_satoshi, 0);
	}
}
