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

		$widget_options = Plebeian_Market_Admin_Utils::plebeian_market_load_options(PLEBEIAN_MARKET_FORM_FIELDS_PREFIX, true);

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
			$title_fontsize_text = 'style="font-size: ' . esc_attr($title_fontsize) . 'px"';
		} else {
			$title_fontsize_text = '';
		}
		if ($description_fontsize) {
			$description_fontsize_text = 'style="font-size: ' . esc_attr($description_fontsize) . 'px"';
		} else {
			$description_fontsize_text = '';
		}

		$content = '
		<div
				class="pleb_item_superdiv"
				data-type="' . esc_attr($type) . '"
				data-key="' . esc_attr($key) . '"
				style="
					max-width: ' . esc_attr($size ? $size : '') . '%;
					display: ' . esc_attr($atts['called_from_listing'] === "true" ? 'inline-flex' : 'flex') . '"
			>

			<h3 class="pleb_buynow_item_title" ' . $title_fontsize_text . '>' . esc_html($title) . '</h3>';

		// Slideshow / Pictures
		if (count($pictures) > 0) {
			$content .= '<div
				class="pleb_buynow_item_slideshow"
				data-slideshow-transitions="' . esc_attr($slideshow_delay) . '"
				data-disabled-slideshow="' . esc_attr($args['slideshow_enabled'] === 'false' || count($pictures) == 1 ? '1' : '0') . '">';

			$firstImageInLoop = true;
			foreach ($pictures as $picture) {
				$picture = (object)$picture;	// In case it's not already an object
				$content .= '<img data-src="' . esc_url($picture->url) . '" class="' . esc_attr($firstImageInLoop ? 'active' : '') . '">';

				if ($firstImageInLoop && $args['slideshow_enabled'] === 'false') {
					break;
				}

				$firstImageInLoop = false;
			}

			$content .= '</div>';
		}

		$content .= '<div class="pleb_buynow_item_description" ' . $description_fontsize_text . '>' . esc_html($description) . '</div>';

		// Price
        if ($type === 'buynow') {
            $price_usd = esc_html($item->price_usd);
            $price_sats = '~' . Plebeian_Market_Communications::fiatToSats($price_usd);

            $content .= '<div class="pleb_buynow_item_price">';
            if ($args['show_price_fiat'] !== 'false') {
                $price_fiat_text = '$' . $price_usd . ' ';
            }
            if ($args['show_price_sats'] !== 'false') {
                $price_sats_text = '(' . $price_sats . ' sats) ';
            }
            $content .= $price_fiat_text . $price_sats_text . '<button type="button" class="btn btn-success btn-buynow" data-key="' . esc_attr($key) . '">Buy Now</button> </div>';
        }

        // Bids
        if ($type === 'auction') {
            $content .= '<div class="pleb_buynow_item_price">
                            <div class="pleb_bids_info">
                                <div class="d-flex justify-content-center">
							        <div class="spinner-border" role="status"></div>
						        </div>
						    </div>

						    <button type="button" class="btn btn-success btn-bidnow" data-key="' . esc_attr($key) . '">' . ($item->ended ? 'See bids' : 'Bid now') . '</button>
                         </div>';
        }

		// Shipping
		if ($args['show_shipping_info'] !== 'false' && $shipping_from != '') {
			$content .= '<div class="pleb_buynow_item_shipping">Shipping from ' . esc_html($shipping_from) . '</div>';
		}

		// Quantity
		if ($type === 'buynow' && $args['show_quantity_info'] === 'true') {
            $available_quantity = $item->available_quantity;
			$content .= '<div class="pleb_buynow_item_quantity">' . esc_html($available_quantity) . ' available</div>';
		}

		$content .= '</div>';

		return $content;
	}
}
