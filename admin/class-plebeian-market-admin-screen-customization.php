<?php

/**
 * The admin-specific screens of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/admin
 */

class Plebeian_Market_Admin_Screen_Customization
{

	static function plebeian_admin_customization_page_html()
	{
		if (!current_user_can('manage_options')) {
			return;
		}

		wp_enqueue_script(
			'plebeian-market-admin-screen-customization',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'admin/js/plebeian-market-admin-screen-customization.js',
			['jquery'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		wp_enqueue_script(
			'plebeian-market-slideshow-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market-slideshow.js',
			['jquery'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		wp_enqueue_style(
			'plebeian-market-css',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/css/plebeian-market.css',
			[],
			PLEBEIAN_MARKET_VERSION,
			'all'
		);
?>
		<div class="wrap">
			<div id="alertsDiv"></div>

			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<p>
				You can customize the default appearance and functionality of the widgets provided
				by this plugin by setting up this options or adding your custom CSS or JS snippets.

			</p>
			<p>
				If required, you'll be able to override this settings for a specific widget passing
				parameters to the shortcode:
			</p>
		</div>

		<h2>Widget customization</h2>
		<?php Plebeian_Market_Admin_Common::plebeian_market_common_admin_code() ?>


		<div class="row">
			<div class="col-7">
				<form class="row g-3 needs-validation" id="customizationForm" novalidate>

					<div class="mb-3">
						<label for="plebeian_market_widget_size" class="form-label">Widget size</label>
						<div class="col-md-2 mb-0">
							<input type="text" id="plebeian_market_widget_size" class="form-control" aria-describedby="plebeian_market_widget_sizeHelpBlock">
							<div class="invalid-feedback">
								Please enter a valid size for the widgets.
							</div>
						</div>
						<div id="plebeian_market_widget_sizeHelpBlock" class="form-text col-md-9">
							This is the size of the widget for each item. You can put <code>small</code>,
							<code>medium</code>, <code>big</code>, <code>huge</code> or directly putting
							the <code>%</code> of the page that you want the widget to occupy. If no size
							specified, <code>30</code>% will be used by default.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_title_fontsize" class="form-label">Title font size</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_title_fontsize" name="plebeian_market_widget_title_fontsize" class="form-control" aria-describedby="plebeian_market_widget_title_fontsizeHelpBlock">
								<?php echo Plebeian_Market_Admin_Common::getHTMLOptions() ?>
							</select>
						</div>
						<div id="plebeian_market_widget_title_fontsizeHelpBlock" class="form-text col-md-9">
							Choose the size of the font used for the title. If left empty, it will have the same
							size that <code>&lt;h4&gt;</code> elements in your theme.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_description_fontsize" class="form-label">Description font size</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_description_fontsize" name="plebeian_market_widget_title_fontsize" class="form-control" aria-describedby="plebeian_market_widget_description_fontsizeHelpBlock">
								<?php echo Plebeian_Market_Admin_Common::getHTMLOptions() ?>
							</select>
						</div>
						<div id="plebeian_market_widget_description_fontsizeHelpBlock" class="form-text col-md-9">
							Choose the size of the font used for the description. If left empty, it will have
							size = 20px.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_slideshow_enabled" class="form-label">Slideshow</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_slideshow_enabled" name="plebeian_market_widget_slideshow_enabled" class="form-control" aria-describedby="plebeian_market_widget_slideshow_enabledHelpBlock">
								<option value="true">Enabled</option>
								<option value="false">Disabled</option>
							</select>
						</div>
						<div id="plebeian_market_widget_slideshow_enabledHelpBlock" class="form-text col-md-9">
							Choose if you want to have a slideshow with all the pictures of each product,
							or just show the first image.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_slideshow_delay" class="form-label">Slideshow delay</label>
						<div class="col-md-2 mb-0">
							<input type="text" id="plebeian_market_widget_slideshow_delay" class="form-control" aria-describedby="plebeian_market_widget_slideshow_delayHelpBlock">
						</div>
						<div id="plebeian_market_widget_slideshow_delayHelpBlock" class="form-text col-md-9">
							You can set the time to wait between slideshow transitions (from one image to the next one)
							in milliseconds. Default value is 4000 (4 seconds).
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_show_price_fiat" class="form-label">Show price of the products in fiat</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_show_price_fiat" class="form-control" aria-describedby="plebeian_market_widget_show_price_fiatHelpBlock">
								<option value="true">Enabled</option>
								<option value="false">Disabled</option>
							</select>
						</div>
						<div id="plebeian_market_widget_show_price_fiatHelpBlock" class="form-text col-md-9">
							Choose if you want to show the price of the products in fiat money.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_show_price_sats" class="form-label">Show price of the products in Bitcoin (satoshis)</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_show_price_sats" class="form-control" aria-describedby="plebeian_market_widget_show_price_satsHelpBlock">
								<option value="true">Enabled</option>
								<option value="false">Disabled</option>
							</select>
						</div>
						<div id="plebeian_market_widget_show_price_satsHelpBlock" class="form-text col-md-9">
							Choose if you want to show the price of the products in Bitcoin.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_show_shipping_info" class="form-label">Show shipping information</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_show_shipping_info" class="form-control" aria-describedby="plebeian_market_widget_show_shipping_infoHelpBlock">
								<option value="true">Enabled</option>
								<option value="false">Disabled</option>
							</select>
						</div>
						<div id="plebeian_market_widget_show_shipping_infoHelpBlock" class="form-text col-md-9">
							Choose if you want to the show the shipping information in the widget.
						</div>
					</div>

					<div class="mb-3">
						<label for="plebeian_market_widget_show_quantity_info" class="form-label">Show quantity info</label>
						<div class="col-md-2 mb-0">
							<select id="plebeian_market_widget_show_quantity_info" class="form-control" aria-describedby="plebeian_market_widget_show_quantity_infoHelpBlock">
								<option value="true">Enabled</option>
								<option value="false">Disabled</option>
							</select>
						</div>
						<div id="plebeian_market_widget_show_quantity_infoHelpBlock" class="form-text col-md-9">
							Choose if you want to the show the information about the quantity of products that you're selling in the widget.
						</div>
					</div>

					<a data-bs-toggle="collapse" href="#customizationAdvanced" role="button" aria-expanded="false" aria-controls="customizationAdvanced">
						See advanced options
					</a>
					<div class="collapse" id="customizationAdvanced">
						<h2>Advanced</h2>
						<h4>CSS</h4>
						<p>Enter your custom CSS here:</p>
						<textarea id="plebeian_market_cutomization_css" name="plebeian_market_cutomization_css" rows="15" cols="60"></textarea>

						<h4>Javascript</h4>
						<p>Enter your custom JS here:</p>
						<textarea id="plebeian_market_cutomization_js" name="plebeian_market_cutomization_js" rows="15" cols="60"></textarea>
					</div>

					<div class="col-12">
						<button class="btn btn-success" type="submit" id="saveUserOptions">Save changes</button>
					</div>

				</form>
			</div>

			<div id="buyNowPreview" class="col-5"></div>
		</div>
<?php
	}
}
