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
class Plebeian_Market_Render
{
	static function plebeian_item_render_html($atts = [], $type, $item = null): string
    {
		$atts = array_change_key_case((array) $atts, CASE_LOWER);		// normalize attribute keys, lowercase

		$default_values = [
			'size'				=> 30,
			'title_fontsize'	=> '',
			'description_fontsize' => '20',
			'slideshow_enabled'	=> 'true',
			'slideshow_delay'   => 4000,
			'show_price_fiat'	=> 'true',
			'show_price_sats'	=> 'true',
			'show_shipping_info' => 'true',
			'show_quantity_info' => 'false'
		];

		$widget_options = Plebeian_Market_Admin_Utils::load_options(PLEBEIAN_MARKET_FORM_FIELDS_PREFIX, true);

		$args = shortcode_atts($default_values, $widget_options);	// Options for the Customization screen + default values
		$args = shortcode_atts($args, $atts);	// What's passet to shortcode as parameters + result from previous line

		if (is_object($item)) {
			$key = $item->key;
		} else {
			if (!array_key_exists('key', $atts)) {
				return "<div><b>Plebeian Market plugin</b>: product key not specified</div>";
			}

			$key = $atts['key'];
            $item = Plebeian_Market_Communications::getItem($type, $key);

			if (!is_object($item)) {
				return "<div class='pleb_item_superdiv'><b>Plebeian Market</b>: This product no longer exists</div>";
			}
		}

		$size = $args['size'];
		$slideshow_delay = $args['slideshow_delay'];

		if (!is_numeric($size)) {
			switch ($size) {
				case 'small':
					$size = 25;
					break;
				case 'medium':
					$size = 50;
					break;
				case 'big':
					$size = 75;
					break;
				case 'huge':
					$size = 100;
					break;
			}
		}

		if (!is_numeric($slideshow_delay)) {
			$slideshow_delay = 4000;
		}

		$title = $item->title;
		$description = $item->description;
		$shipping_from = $item->shipping_from;
		$pictures = $item->media;

		$title_fontsize = $args['title_fontsize'];
		$description_fontsize = $args['description_fontsize'];

		if ($title_fontsize) {
			$title_fontsize_text = 'style="font-size: ' . $title_fontsize . 'px"';
		} else {
			$title_fontsize_text = '';
		}
		if ($description_fontsize) {
			$description_fontsize_text = 'style="font-size: ' . $description_fontsize . 'px"';
		} else {
			$description_fontsize_text = '';
		}

		$content = '
		<div
				class="pleb_item_superdiv"
				data-type="' . $type . '"
				data-key="' . $key . '"
				style="
					max-width: ' . ($size ? $size : '') . '%;
					display: ' . ($atts['called_from_listing'] === "true" ? 'inline-flex' : 'flex') . '"
			>

			<h3 class="pleb_buynow_item_title" ' . $title_fontsize_text . '>' . $title . '</h3>';

		// Slideshow / Pictures
		if (count($pictures) > 0) {
			$content .= '<div
				class="pleb_buynow_item_slideshow"
				data-slideshow-transitions="' . $slideshow_delay . '"
				data-disabled-slideshow="' . ($args['slideshow_enabled'] === 'false' || count($pictures) == 1 ? '1' : '0') . '">';

			$firstImageInLoop = true;
			foreach ($pictures as $picture) {
				$picture = (object)$picture;	// In case it's not already an object
				$content .= '<img data-src="' . $picture->url . '" class="' . ($firstImageInLoop ? 'active' : '') . '">';

				if ($firstImageInLoop && $args['slideshow_enabled'] === 'false') {
					break;
				}

				$firstImageInLoop = false;
			}

			$content .= '</div>';
		}

		$content .= '<div class="pleb_buynow_item_description" ' . $description_fontsize_text . '>' . $description . '</div>';

		// Price
        if ($type === 'buynow') {
            $price_usd = $item->price_usd;
            $price_sats = '~' . Plebeian_Market_Communications::fiatToSats($price_usd);

            $content .= '<div class="pleb_buynow_item_price">';
            if ($args['show_price_fiat'] !== 'false') {
                $price_fiat_text = '$' . $price_usd . ' ';
            }
            if ($args['show_price_sats'] !== 'false') {
                $price_sats_text = '(' . $price_sats . ' sats) ';
            }
            $content .= $price_fiat_text . $price_sats_text . '<button type="button" class="btn btn-success btn-buynow" data-key="' . $key . '">Buy Now</button> </div>';
        }

        // Bids
        if ($type === 'auction') {
            $bids = $item->bids;
            $numBids = count($bids);

            $content .= '<div class="pleb_buynow_item_price">';
            $content .= '   <div class="pleb_bids_info">';
            $content .= '       <p>Bids: ' . $numBids . '</p>';
            if ($numBids) {
                $content .= '   <p>Top bid: ' . $numBids . '</p>';
                $content .= '   <p>Bidder: ' . $numBids . '</p>';
            }
            $content .= '   </div>';
            $content .= '   <button type="button" class="btn btn-success btn-bidnow" data-key="' . $key . '">Bid now</button>';
            $content .= '</div>';
        }


		// Shipping
		if ($args['show_shipping_info'] !== 'false' && $shipping_from != '') {
			$content .= '<div class="pleb_buynow_item_shipping">Shipping from ' . $shipping_from . '</div>';
		}

		// Quantity
		if ($type === 'buynow' && $args['show_quantity_info'] === 'true') {
            $available_quantity = $item->available_quantity;
			$content .= '<div class="pleb_buynow_item_quantity">' . $available_quantity . ' available</div>';
		}

		$content .= '</div>';

		return $content;
	}
}
