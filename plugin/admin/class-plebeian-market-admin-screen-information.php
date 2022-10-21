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

class Plebeian_Market_Admin_Screen_Information
{

	static function plebeian_admin_information_page_html()
	{
?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<p>This is the WordPress plugin for integration with <a href="https://plebeian.market/" target="_blank">Plebeian Market</a>, powered by <a href="https://plebeian.technology/" target="_blank">Plebeian Technology</a>.
			</p>
		</div>

		<img src="<?= plugin_dir_url(__FILE__) ?>img/plebeian_market_logo.png" alt="Plebeian Market, powered by Plebeian Technology">

		<p>
			Plebeian Market is a marketplace where anyone can sell anything. All you need is a Bitcoin wallet and you'll start selling or
			auctioning your stuff for Bitcoins in no time and in a sovereign way.
		</p>
		<p>
			With this plugin, you'll be able to manage your products in Plebeian Market and add widgets in your posts or pages using shortcodes. You can pick
			the one that suits you better and paste it there.
		</p>
		<p>
			If you need support, you can find us on <a class="link" href="https://t.me/PlebeianMarket" target="_blank">Telegram</a> or email us at <a class="link" href="email:support@plebeian.market">
				support@plebeian.market</a>. If you have a more technical feedback or would like to contribute to
			the development, you can find us on <a class="link" href="https://github.com/PlebeianTech" target="_blank">GitHub</a>.
		</p>
		<p>
			You will own your market stall and you'll be happy!
		</p>

		<p></p><br>

		<h2>Buy Now products</h2>
		<hr>
		<p>
			You can go to the <a href="<?= admin_url('admin.php?page=plebeian_market_buynow') ?>">
				Buy Now items</a> menu to manage (create, modify, delete) your <i>Buy Now</i> products, then use
			shortcodes this way to show the products in your WordPress posts or pages:
		</p>

		<h3>Show a specific product</h3>
		<p>Use this shortcode to show the specific product with key <b>EZ</b> in a post or page:</p>
		<code>[plebeian_show_buynow key="EZ"]</code>
		<p></p>
		<p>
			You can use the
			<img src="<?= plugin_dir_url(__FILE__) ?>img/code-square.svg" class="shortCodeIcon" alt="Copy Shortcode" style="font-size: 2em;">
			(copy shortcode) icon in <a href="<?= admin_url('admin.php?page=plebeian_market_buynow') ?>">Buy Now items</a> to
			directly copy the shortcode for a specific item.
		</p>
		<p></p>
		> <a class="" data-bs-toggle="collapse" href="#buyNowSpecificWidgetAdvanced" role="button" aria-expanded="false" aria-controls="buyNowSpecificWidgetAdvanced">
			See advanced options
		</a>
		<div class="collapse" id="buyNowSpecificWidgetAdvanced">
			<div class="card card-body">
				<p>
					You can also pass parameters to configure how your widget will look like:
				<ul>
					<b>size</b> - You can pass <code>small</code>, <code>medium</code>, <code>big</code>, <code>huge</code> or directly putting the <code>%</code> of the page
					that you want the widget to occupy. If no size specified, 30% will be used by default.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="medium"]</code> <br>
					or <br>
					<code>[plebeian_show_buynow key="EZ" size="70"]</code>
				</ul>
				<ul>
					<b>slideshow</b> - If you pass this parameter with the <code>false</code> value, there will not be a picture
					slideshow and just the first image of the product will be shown.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" slideshow="false"]</code>
				</ul>
				<ul>
					<b>slideshow_delay</b> - You can pass this parameter to set the time to wait between slideshow transitions
					in milliseconds. Default value is 4000 (4 seconds).
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" slideshow_delay="6000"]</code>
				</ul>
				<ul>
					<b>show_price_fiat</b> - If you pass this parameter with the <code>false</code> value, the price of the product
					in fiat will not be shown.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" show_price_fiat="false"]</code>
				</ul>
				<ul>
					<b>show_price_sats</b> - If you pass this parameter with the <code>false</code> value, the price of the product
					in satoshis will not be shown.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" show_price_sats="false"]</code>
				</ul>
				<ul>
					<b>show_shipping_info</b> - If you pass this parameter with the <code>false</code> value, the shipping information of
					the product will not be shown in the widget.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" show_shipping_info="false"]</code>
				</ul>
				<ul>
					<b>show_quantity_info</b> - If you pass this parameter with the <code>true</code> value, the information about
					the quantity of products that you're selling will be shown in the widget. It's hidden by default.
					<br>
					<code>[plebeian_show_buynow key="EZ" size="small" show_quantity_info="true"]</code>
				</ul>
				</p>
			</div>
		</div>

		<p></p>

		<h3>Show product listing</h3>
		<p>Use this shortcode to show the full listing of your <i>Buy Now</i> products:</p>
		<code>[plebeian_show_buynow_listing]</code>
		<p></p>
		> <a class="" data-bs-toggle="collapse" href="#buyNowListingWidgetAdvanced" role="button" aria-expanded="false" aria-controls="buyNowListingWidgetAdvanced">
			See advanced options
		</a>
		<div class="collapse" id="buyNowListingWidgetAdvanced">
			<div class="card card-body">
				<p>
					You can use that shortcode directly to use the default parameters, which are:
				<ul><b>size</b> - 15</ul>
				<ul><b>slideshow</b> - false</ul>
				<ul><b>slideshow_delay</b> - 4000</ul>
				<ul><b>show_price_fiat</b> - true</ul>
				<ul><b>show_price_sats</b> - true</ul>
				<ul><b>show_shipping_info</b> - true</ul>
				<ul><b>show_quantity_info</b> - false</ul>

				But you can also pass all the parameters available to be used for single products in the shortcode to change the default behavior.
				The specified parameters will be used for all the products in the listing. So for example:
				<br>
				<code>[plebeian_show_buynow_listing slideshow="true" slideshow_delay="7000" size="10" show_price_fiat="false" show_shipping_info="false"]</code>
				<br>
				<p>This will show all the <i>Buy Now</i> products in the store, enable the picture slideshow, set the picture change delay to 7 seconds,
					show the pictures at 10% the size of the screen, and hide the fiat price and the shipping info.</p>
			</div>
		</div>

		<p></p><br>

		<h2>Auctions</h2>
		<hr>
		<h5><span class="badge text-bg-danger">Comming soon!</span></h5>
		<!--
		<p>
			You can go to the <a href="<?= admin_url('admin.php?page=plebeian_market_auctions') ?>">
				Auctions</a> menu to manage (create, modify, delete) your Auctions, then use shortcodes
			this way to show the products in your WordPress posts or pages:
		</p>
		<h3>Show auctions listing</h3>
		<p>Use this shortcode to show the full listing of your current auctions:</p>
		<code>[plebeian_show_auctions_listing]</code>
		<p>You can also pass parameters to filter what auctions are shown. For instance this will show auctions without bids yet:</p>
		<code>[plebeian_show_auctions_listing bids=0]</code>
		<p>Find a list of parameters here:</p>
		<ul><code>bids</code> - sets the number of bids you want your products to have</ul>
		<ul><code>listing-title</code> - sets the title of the widget to show</ul>

		<p></p>
		<h3>Show a specific auction</h3>
		<p>Use this shortcode to show the specific auction with key <b>RLOA</b> in a post or page:</p>
		<code>[plebeian_show_auction key="RLOA"]</code>
		<p></p>
		<p>You can use the
			<img src="<?= plugin_dir_url(__FILE__) ?>img/code-square.svg" class="shortCodeIcon" alt="Copy Shortcode" style="font-size: 2em;">
			(copy shortcode) icon in <a href="<?= admin_url('admin.php?page=plebeian_market_auctions') ?>">Auctions</a> to directly copy the shortcode for a specific auction.
		</p>
		-->
<?php
	}
}
