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

class Plebeian_Market_Admin_Screen_Customization {

	static function plebeian_admin_customization_page_html() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>You can customize the appearance and functionality of the widgets provided by this plugin by adding your custom CSS or JS snippets:</p>
		</div>

		<h2>CSS</h2>
		<p>Enter your custom CSS here:</p>
		<textarea id="css" name="css" rows="15" cols="70"></textarea>

		<h2>Javascript</h2>
		<p>Enter your custom JS here:</p>
		<textarea id="js" name="js" rows="15" cols="70"></textarea>

		<?php
		submit_button( 'Save Settings' );
	}
}