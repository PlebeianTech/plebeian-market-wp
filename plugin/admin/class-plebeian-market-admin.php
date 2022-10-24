<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/admin
 */

class Plebeian_Market_Admin
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
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('plebeian-market-admin-css', plugin_dir_url(__FILE__) . 'css/plebeian-market-admin.css', [], $this->version, 'all');

		// Datatables
		wp_enqueue_style('jquery.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/jquery.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('buttons.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/buttons.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('dataTables.dateTime', plugin_dir_url(__FILE__) . 'css/datatables/dataTables.dateTime.min.css', [], $this->version, 'all');
		wp_enqueue_style('fixedHeader.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/fixedHeader.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('responsive.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/responsive.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('scroller.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/scroller.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('select.dataTables', plugin_dir_url(__FILE__) . 'css/datatables/select.dataTables.min.css', [], $this->version, 'all');
		// DataTables-1.12.1
		// Buttons-2.2.3
		// DateTime-1.1.2
		// FixedHeader-3.2.4
		// Responsive-2.3.0
		// Scroller-2.0.7
		// Select-1.4.0

		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script('plebeian-market-admin', plugin_dir_url(__FILE__) . 'js/plebeian-market-admin.js', ['jquery'], $this->version, false);

		// Datatables
		wp_enqueue_script('jquery.dataTables', plugin_dir_url(__FILE__) . 'js/datatables/jquery.dataTables.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.buttons', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.buttons.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('buttons.colVis', plugin_dir_url(__FILE__) . 'js/datatables/buttons.colVis.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('buttons.html5', plugin_dir_url(__FILE__) . 'js/datatables/buttons.html5.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.dateTime', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.dateTime.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.fixedHeader', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.fixedHeader.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.responsive', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.responsive.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.scroller', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.scroller.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.select', plugin_dir_url(__FILE__) . 'js/datatables/dataTables.select.min.js', ['jquery'], $this->version, false);
		// DataTables-1.12.1
		// Buttons-2.2.3
		// DateTime-1.1.2
		// FixedHeader-3.2.4
		// Responsive-2.3.0
		// Scroller-2.0.7
		// Select-1.4.0

		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', ['jquery']);
		wp_enqueue_script('jquery-ui-js', 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['jquery', 'bootstrap-js']);

		wp_enqueue_media();
	}

	/**
	 * Menus / submenus
	 */
	function plebeian_main_menu()
	{
		add_menu_page(
			'Plebeian Market WordPress plugin',
			'Plebeian Market',
			'manage_options',
			'plebeian_market',
			null,
			'dashicons-store',
			200
		);
	}

	function plebeian_information_submenu()
	{
		add_submenu_page(
			'plebeian_market',
			'Information',
			'Information',
			'manage_options',
			'plebeian_market',
			'Plebeian_Market_Admin_Screen_Information::plebeian_admin_information_page_html',
			1
		);
	}

	function plebeian_fixedprice_submenu()
	{
		add_submenu_page(
			'plebeian_market',
			'Buy Now items',
			'Buy Now items',
			'manage_options',
			'plebeian_market_buynow',
			'Plebeian_Market_Admin_Screen_Buynow::plebeian_admin_buynow_page_html',
			2
		);
	}

	function plebeian_auctions_submenu()
	{
		add_submenu_page(
			'plebeian_market',
			'Auctions',
			'Auctions',
			'manage_options',
			'plebeian_market_auctions',
			'Plebeian_Market_Admin_Screen_Auctions::plebeian_admin_auctions_page_html',
			3
		);
	}

	function plebeian_customization_submenu()
	{
		add_submenu_page(
			'plebeian_market',
			'Customization',
			'Customization',
			'manage_options',
			'plebeian_market_customization',
			'Plebeian_Market_Admin_Screen_Customization::plebeian_admin_customization_page_html',
			4
		);
	}

	function plebeian_setup_submenu_with_others()
	{
		add_submenu_page(
			'plebeian_market',
			'Setup',
			'Setup',
			'manage_options',
			'plebeian_market_setup',
			'Plebeian_Market_Admin_Screen_Setup::plebeian_admin_setup_page_html',
			5
		);
	}

	function plebeian_setup_submenu_standalone()
	{
		add_submenu_page(
			'plebeian_market',
			'Setup',
			'Setup',
			'manage_options',
			'plebeian_market',
			'Plebeian_Market_Admin_Screen_Setup::plebeian_admin_setup_page_html',
			1
		);
	}

	/**
	 * If we don't have an auth key, we need to setup
	 */
	static function plebeian_setup_is_needed()
	{
		if (get_option('plebeian_market_auth_key') === false || get_option('plebeian_market_auth_key') === '') {
			return true;
		}

		return false;
	}
}
