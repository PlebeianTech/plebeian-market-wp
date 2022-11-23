<?php

/**
 * @link              https://github.com/PlebeianTech/plebeian-market-wp
 * @since             1.0.0
 * @package           Plebeian_Market
 *
 * @wordpress-plugin
 * Plugin Name:       Plebeian Market
 * Plugin URI:        https://github.com/PlebeianTech/plebeian-market-wp
 * Description:       This is the WordPress plugin for Plebeian Market sites. You can make your actions appear in your WordPress site so your users don't need to abandon the site to bid on your items.
 * Version:           1.1.0
 * Author:            Plebeian Technology
 * Author URI:        https://plebeian.technology/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       plebeian-market
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLEBEIAN_MARKET_VERSION', '1.1.0' );

function activate_plebeian_market() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plebeian-market-activator.php';
	Plebeian_Market_Activator::activate();
}
function deactivate_plebeian_market() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plebeian-market-deactivator.php';
	Plebeian_Market_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_plebeian_market' );
register_deactivation_hook( __FILE__, 'deactivate_plebeian_market' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plebeian-market.php';

require_once plugin_dir_path( __FILE__ ) . 'config.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plebeian_market() {

	$plugin = new Plebeian_Market();
	$plugin->run();

}
run_plebeian_market();
