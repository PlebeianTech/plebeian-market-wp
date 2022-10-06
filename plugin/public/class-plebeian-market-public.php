<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/public
 */

class Plebeian_Market_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/plebeian-market-public.css',
			[],
			$this->version, 'all'
		);

		wp_enqueue_style( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css'); // all the bootstrap javascript goodness
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script('js.cookie',
			plugin_dir_url( __FILE__ ) . 'js/js.cookie.min.js',
			['jquery'],
			$this->version,
			false
		);

		wp_enqueue_script(
			'bootstrap',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js',
			['jquery']
		);

		wp_enqueue_script('plebeian-market-public-auth',
			plugin_dir_url( __FILE__ ) . 'js/plebeian-market-public-auth.js',
			['jquery', 'js.cookie', 'bootstrap'],
			$this->version,
			false
		);

		wp_enqueue_script('plebeian-market-public',
			plugin_dir_url( __FILE__ ) . 'js/plebeian-market-public.js',
			['jquery', 'plebeian-market-public-auth'],
			$this->version,
			false
		);

		wp_enqueue_script('plebeian-market-public-buynow',
			plugin_dir_url( __FILE__ ) . 'js/plebeian-market-public-buynow.js',
			['jquery', 'js.cookie', 'bootstrap', 'plebeian-market-public-auth', 'plebeian-market-public'],
			$this->version,
			false
		);
	}

	public function plebeian_output_custom_css() {
		// if ($output){
		//	echo '<style type="text/css">' . $output . '</style>';
		//}
		echo '<style type="text/css"></style>';
	}

	/**
	 * Central location to create all shortcodes.
	 */
	function plebeian_shortcodes_init() {

		function plebeian_show_buynow_listing($atts = []) {
			$args = shortcode_atts([		// default values
				'slideshow_delay'   	=> 4000,
				'slideshow'				=> 'false',
				'size'					=> 15,
				'show_price_fiat'		=> 'true',
				'show_price_sats'		=> 'true',
				'show_shipping_info'	=> 'true',
				'show_quantity_info'	=> 'false',
				'called_from_listing'	=> 'true'
			], $atts);

			$buyNowAllItems = Plebeian_Market_Communications::getBuyNowListing();

			if (count($buyNowAllItems) > 0) {
				$content = '';

				foreach($buyNowAllItems as $buyNowItem) {
					$args['key'] = $buyNowItem->key;
					$content .= plebeian_show_buynow($args);
				}

				return $content;

			} else {
				return "<div>Currently there are no products to show.</div>";
			}
		}

		function plebeian_show_buynow($atts = []) {
			$atts = array_change_key_case((array) $atts, CASE_LOWER);		// normalize attribute keys, lowercase

			$args = shortcode_atts([		// default values
				'slideshow_delay'   => 4000,
				'slideshow'			=> 'true',
				'size'				=> 30,
				'show_price_fiat'	=> 'true',
				'show_price_sats'	=> 'true',
				'show_shipping_info'=> 'true',
				'show_quantity_info'=> 'false'
			], $atts);

			if ( ! array_key_exists('key', $atts)) {
				return "<div><b>Plebeian Market plugin</b>: product key not specified</div>";
			}

			$key = $atts['key'];
			$size = $args['size'];
			$slideshow_delay = $args['slideshow_delay'];

			if ( ! is_numeric($size)) {
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

			if ( ! is_numeric($slideshow_delay)) {
				$slideshow_delay = 4000;
			}

			$buyNowItem = Plebeian_Market_Communications::getBuyNow($key);

			if ( ! is_object($buyNowItem)) {
				return "<div class='pleb_buynow_item_superdiv'><b>Plebeian Market</b>: This product no longer exists</div>";
			}

			$title = $buyNowItem->title;
			$description = $buyNowItem->description;
			$price_usd = $buyNowItem->price_usd;
			$price_sats = '~' . Plebeian_Market_Communications::getBTCPrice($price_usd);
			$available_quantity = $buyNowItem->available_quantity;
			$shipping_from = $buyNowItem->shipping_from;
			$pictures = $buyNowItem->media;

			$content = '
			<div
					class="pleb_buynow_item_superdiv"
					style="
						max-width: ' . ($size ? $size : '') . '%;
						display: ' . ($atts['called_from_listing'] === "true" ? 'inline-flex' : 'flex') . '"
				>

				<div class="pleb_buynow_item_title">' . $title . '</div>';

			// Slideshow / Pictures
			if (count($pictures) > 0) {
				$content .= '<div
					class="pleb_buynow_item_slideshow"
					data-slideshow-transitions="' . $slideshow_delay . '"
					data-disabled-slideshow="' . ($args['slideshow'] === 'false' ? '1' : '0') . '">';

				$firstImageInLoop = true;
				foreach($pictures as $picture) {
					$content .= '<img data-src="' . $picture->url . '" class="' . ($firstImageInLoop ? 'active' : '') . '">';

					if ($firstImageInLoop && $args['slideshow'] === 'false') {
						break;
					}

					$firstImageInLoop = false;
				}

				$content .= '</div>';
			}

			$content .= '<div class="pleb_buynow_item_description">' . $description . '</div>';

			// Price
			$content .= '<div class="pleb_buynow_item_price">';
			if ($args['show_price_sats'] !== 'false') {
				$price_sats_text = $price_sats . ' sats';
			}
			if ($args['show_price_fiat'] !== 'false') {
				$price_fiat_text = '($' . $price_usd . ')';
			}
			$content .= $price_sats_text . ' - ' . $price_fiat_text . '<button type="button" class="btn btn-success btn-buynow" data-key="'.$key.'">Buy Now</button> </div>';

			// Shipping
			if ($args['show_shipping_info'] !== 'false' && $shipping_from != '') {
				$content .= '<div class="pleb_buynow_item_shipping">Shipping from ' . $shipping_from . '</div>';
			}

			// Quantity
			if ($args['show_quantity_info'] === 'true') {
				$content .= '<div class="pleb_buynow_item_quantity">' . $available_quantity . ' available</div>';
			}

			$content .= '</div>';

			return $content;
		}

		function plebeian_common_public_code() { ?>
			<script>
				let requests = {
					pm_api: {
						get_login_info: {
							url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_GET_LOGIN_INFO_URL ?>',
							method: '<?= PM_API_GET_LOGIN_INFO_METHOD ?>'
						},
						check_login: {
							url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_CHECK_LOGIN_URL ?>',
							method: '<?= PM_API_CHECK_LOGIN_METHOD ?>'
						},
						buynow_get: {
							url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_GET_BUYNOW_URL ?>',
							method: '<?= PM_API_GET_BUYNOW_METHOD ?>'
						},
						buynow_buy: {
							url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_BUY_BUYNOW_URL ?>',
							method: '<?= PM_API_BUY_BUYNOW_METHOD ?>'
						},
					},
					wordpress_pm_api: {
						ajax_url: '<?= admin_url('admin-ajax.php') ?>',
						nonce: '<?= wp_create_nonce('save_options_nonce') ?>'
					}
				}
			</script>;

			<!-- General-Purpose Modal -->
			<div id="gpModal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-xl modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeGPModal"></button>
						</div>
						<div class="modal-body" id="gp-modal-body"></div>
					</div>
				</div>
			</div>

			<!-- Loading Modal -->
			<div id="loadingModal" class="modal fade" role="dialog" data-bs-backdrop="static">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-body" id="loadingModal-body">
							<div class="d-flex justify-content-center">
								<div class="spinner-border" role="status"></div>
							</div>
							<div class="justify-content-center d-flex loadingModalLoadingText">
								<p>Loading...</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
		}
		add_action('wp_footer', 'plebeian_common_public_code');

		function plebeian_show_auctions_listing($atts = [], $content = null) {
			$filter_text = "";

			$atts = array_change_key_case((array) $atts, CASE_LOWER);		// normalize attribute keys, lowercase

			if (array_key_exists('bids', $atts)) {
				$filter_text .= "Showing auctions with {$atts['bids']} bids.";
			}

			$auctions_body_array = Plebeian_Market_Communications::getFeatured('auctions');

			$content = 
			'<div class="pleb_listing_superdiv">
				<p>Current auctions:</p>
				<div class="pleb_listing_auctions">';

			if (count($auctions_body_array) > 0) {
				foreach($auctions_body_array as $auction) {
					$auction_title = $auction->title;
					// $auctions_posted_by = $auction->seller_twitter_username;
					// $auction_bids = $auction->bids;
					$auction_media = $auction->media;
					$auction_first_image = $auction_media[0]->url;

					$content .=
					'<div class="pleb_listing_auction">
						<div class="pleb_listing_auction_title">
							' . $auction_title . '
						</div>
						<div class="pleb_listing_auction_image">
							<img src="' . $auction_first_image . '">
						</div>
						<div class="pleb_listing_auction_description">
							Piece of Bitcoin Reformation -- a copy of original work "Money Changer and his Wife" by Quentin Matsys, 1514.
							Shipping from EU
						</div>
					</div>';
				}
			} else {
				$content .=
				'<p>--- There are no auctions right now ---</p>

				<div class="pleb_listing_auction">
					<div class="pleb_listing_auction_title">
						“Pepe Changer and his Wife”
					</div>
					<div class="pleb_listing_auction_image">
						<img src="https://f004.backblazeb2.com/file/plebeian-market/P_auction_RLOA_media_1.jpeg">
					</div>
					<div class="pleb_listing_auction_description">
						Piece of Bitcoin Reformation — a copy of original work “Money Changer and his Wife” by Quentin Matsys, 1514.
						Shipping from EU
					</div>
				</div>';
			}

				$content .= '



				<div class="pleb_listing_auction">
					<div class="pleb_listing_auction_title">
						“Pepe Changer and his Wife”
					</div>
					<div class="pleb_listing_auction_image">
						<img src="https://f004.backblazeb2.com/file/plebeian-market/P_auction_RLOA_media_1.jpeg">
					</div>
					<div class="pleb_listing_auction_description">
						Piece of Bitcoin Reformation — a copy of original work “Money Changer and his Wife” by Quentin Matsys, 1514.
						Shipping from EU
					</div>
				</div>


				</div>

				<div class="pleb_listing_filter_text">';
			$content .= $filter_text;
			$content .=
				'</div>
			</div>';
			return $content;
		}

		add_shortcode('plebeian_show_buynow', 'plebeian_show_buynow');
		add_shortcode('plebeian_show_buynow_listing', 'plebeian_show_buynow_listing');

		add_shortcode('plebeian_show_auctions_listing', 'plebeian_show_auctions_listing');
	}
}