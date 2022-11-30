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

class Plebeian_Market_Public
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style(
			'plebeian-market-css',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/css/plebeian-market.css',
			[],
			$this->version,
			'all'
		);

        wp_enqueue_style('bootstrap-css', PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/css/bootstrap.min.css', [], '5.2.3');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script(
			'js.cookie',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'public/js/js.cookie.min.js',
			['jquery'],
			$this->version,
			false
		);

        wp_enqueue_script('bootstrap-js', PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/bootstrap.bundle.min.js', ['jquery'], '5.2.3');

		wp_enqueue_script(
			'plebeian-market-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market.js',
			['jquery', 'bootstrap-js'],
			$this->version,
			false
		);

		wp_enqueue_script(
			'plebeian-market-auth-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market-auth.js',
			['jquery', 'js.cookie', 'bootstrap-js', 'plebeian-market-js'],
			$this->version,
			false
		);

		wp_enqueue_script(
			'plebeian-market-slideshow-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market-slideshow.js',
			['jquery'],
			$this->version,
			false
		);

		wp_enqueue_script(
			'plebeian-market-public-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'public/js/plebeian-market-public.js',
			['jquery', 'plebeian-market-auth-js', 'plebeian-market-slideshow-js'],
			$this->version,
			false
		);

        wp_enqueue_script(
            'plebeian-market-public-auction',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'public/js/plebeian-market-public-auction.js',
            ['jquery', 'js.cookie', 'bootstrap-js', 'plebeian-market-auth-js', 'plebeian-market-public-js'],
            $this->version,
            false
        );

		wp_enqueue_script(
			'plebeian-market-public-buynow',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'public/js/plebeian-market-public-buynow.js',
			['jquery', 'js.cookie', 'bootstrap-js', 'plebeian-market-auth-js', 'plebeian-market-public-js'],
			$this->version,
			false
		);

        wp_enqueue_script(
            'jquery-countdown-min-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/jquery.countdown.min.js',
            ['jquery'],
            '2.2.0.1',
            false
        );

        wp_enqueue_script('moment');
	}

	public function plebeian_output_custom_css()
	{
		$css_output = get_option('plebeian_market_cutomization_css');

		if ($css_output) {
			echo '<style>' . wp_kses_data($css_output) . '</style>';
		}
	}

	public function plebeian_output_custom_js()
	{
		$js_output = stripslashes(get_option('plebeian_market_cutomization_js'));

		if ($js_output) {
			echo '<script>' . esc_js($js_output) . '</script>';
		}
	}

	/**
	 * Central location to create all Public shortcodes.
	 */
	function plebeian_shortcodes_init()
	{
		function plebeian_show_buynow($atts = [], $buyNowItem = null): string
        {
			$atts = array_change_key_case((array) $atts, CASE_LOWER);		// normalize attribute keys, lowercase
			return Plebeian_Market_Render::plebeian_item_render_html($atts, 'buynow', $buyNowItem);
		}

        function plebeian_show_auction($atts = [], $auctionItem = null): string
        {
            $atts = array_change_key_case((array) $atts, CASE_LOWER);		// normalize attribute keys, lowercase
            return Plebeian_Market_Render::plebeian_item_render_html($atts, 'auction', $auctionItem);
        }

        function plebeian_show_buynow_listing($atts = []): string
        {
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

                foreach ($buyNowAllItems as $buyNowItem) {
                    $args['key'] = $buyNowItem->key;
                    $content .= plebeian_show_buynow($args, $buyNowItem);
                }

                return $content;
            } else {
                return "<div>Currently there are no products to show.</div>";
            }
        }

		function plebeian_common_public_code()
		{ ?>
			<script>
				let pluginBasePath = '<?php echo PLEBEIAN_MARKET_PLUGIN_BASEPATH ?>';

				let btcPriceInUSD = <?php echo Plebeian_Market_Communications::getBTCPriceInUSD() ?>;

				let requests = {
					pm_api: {
						default_timeout: 10000,
						get_login_info: {
							url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_GET_LOGIN_INFO_URL ?>',
							method: '<?php echo PLEBEIAN_MARKET_API_GET_LOGIN_INFO_METHOD ?>'
						},
						check_login: {
							url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_CHECK_LOGIN_URL ?>',
							method: '<?php echo PLEBEIAN_MARKET_API_CHECK_LOGIN_METHOD ?>'
						},
                        buynow: {
                            get: {
                                url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_GET_BUYNOW_URL ?>',
                                method: '<?php echo PLEBEIAN_MARKET_API_GET_BUYNOW_METHOD ?>'
                            },
                            buy: {
                                url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_BUY_BUYNOW_URL ?>',
                                method: '<?php echo PLEBEIAN_MARKET_API_BUY_BUYNOW_METHOD ?>'
                            },
                        },
                        auctions: {
                            bid: {
                                url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_BID_AUCTIONS_URL ?>',
                                method: '<?php echo PLEBEIAN_MARKET_API_BID_AUCTIONS_METHOD ?>'
                            }
                        }
					},
					wordpress_pm_api: {
						ajax_url: '<?php echo admin_url('admin-ajax.php') ?>',
						nonce: '<?php echo wp_create_nonce('save_options_nonce') ?>'
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
						<div class="modal-body text-center" id="gp-modal-body"></div>
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

			<!-- Alert Modal -->
			<div id="alertModal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-body">
							<p id="alertModalText"></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

<?php
		}

		add_action('wp_footer', 'plebeian_common_public_code');

		add_shortcode('plebeian_show_buynow', 'plebeian_show_buynow');
		add_shortcode('plebeian_show_buynow_listing', 'plebeian_show_buynow_listing');

        add_shortcode('plebeian_show_auction', 'plebeian_show_auction');
		//add_shortcode('plebeian_show_auctions_listing', 'plebeian_show_auctions_listing');
	}
}
