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
		wp_enqueue_style('plebeian-market-admin-css', pluginBasePath . 'admin/css/plebeian-market-admin.css', [], $this->version, 'all');

		// Datatables
		wp_enqueue_style('jquery.dataTables', pluginBasePath . 'admin/css/datatables/jquery.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('buttons.dataTables', pluginBasePath . 'admin/css/datatables/buttons.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('dataTables.dateTime', pluginBasePath . 'admin/css/datatables/dataTables.dateTime.min.css', [], $this->version, 'all');
		wp_enqueue_style('fixedHeader.dataTables', pluginBasePath . 'admin/css/datatables/fixedHeader.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('responsive.dataTables', pluginBasePath . 'admin/css/datatables/responsive.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('scroller.dataTables', pluginBasePath . 'admin/css/datatables/scroller.dataTables.min.css', [], $this->version, 'all');
		wp_enqueue_style('select.dataTables', pluginBasePath . 'admin/css/datatables/select.dataTables.min.css', [], $this->version, 'all');
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
		wp_enqueue_script('plebeian-market-admin', pluginBasePath . 'admin/js/plebeian-market-admin.js', ['jquery', 'plebeian-market-js'], $this->version, false);
		wp_enqueue_script('plebeian-market-js',	pluginBasePath . 'common/js/plebeian-market.js', ['jquery', 'bootstrap-js'], $this->version, false);

		// Datatables
		wp_enqueue_script('jquery.dataTables', pluginBasePath . 'admin/js/datatables/jquery.dataTables.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.buttons', pluginBasePath . 'admin/js/datatables/dataTables.buttons.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('buttons.colVis', pluginBasePath . 'admin/js/datatables/buttons.colVis.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('buttons.html5', pluginBasePath . 'admin/js/datatables/buttons.html5.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.dateTime', pluginBasePath . 'admin/js/datatables/dataTables.dateTime.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.fixedHeader', pluginBasePath . 'admin/js/datatables/dataTables.fixedHeader.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.responsive', pluginBasePath . 'admin/js/datatables/dataTables.responsive.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.scroller', pluginBasePath . 'admin/js/datatables/dataTables.scroller.min.js', ['jquery'], $this->version, false);
		wp_enqueue_script('dataTables.select', pluginBasePath . 'admin/js/datatables/dataTables.select.min.js', ['jquery'], $this->version, false);
        wp_enqueue_script('moment.js', pluginBasePath . 'admin/js/datatables/moment.min.js', ['jquery'], $this->version, false);

    // DataTables-1.12.1
		// Buttons-2.2.3
		// DateTime-1.1.2
		// FixedHeader-3.2.4
		// Responsive-2.3.0
		// Scroller-2.0.7
		// Select-1.4.0
        // moment-2.11.2

		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', ['jquery']);
		wp_enqueue_script('jquery-ui-js', 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['jquery', 'bootstrap-js']);

		wp_enqueue_media();
	}

	/**
	 * Menus / submenus
	 */

	function plebeian_main_menu_standard()
	{
		self::plebeian_show_main_menu_entry('plebeian_market');
	}
	function plebeian_main_menu_for_setup()
	{
		self::plebeian_show_main_menu_entry('plebeian_market_setup');
	}

	function plebeian_show_main_menu_entry($path)
	{
		add_menu_page(
			'Plebeian Market WordPress plugin',
			'Plebeian Market',
			'manage_options',
			$path,
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

	function plebeian_setup_submenu()
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

	/**
	 * Do we have a Plebeian Market API auth key for admin?
	 */
	static function plebeian_have_admin_auth_key()
	{
		$authToken = Plebeian_Market_Communications::getXAccessToken();
		if ($authToken === false || $authToken === '') {
			return false;
		}

		return true;
	}

	function add_plugin_link($plugin_actions, $plugin_file)
	{
		$new_actions = [];
		if (strpos($plugin_file, 'plebeian-market.php') !== false) {
			$new_actions['cl_settings'] = sprintf(__('<a href="%s">Settings</a>'), esc_url(admin_url('admin.php?page=plebeian_market_setup')));
		}
		return array_merge($new_actions, $plugin_actions);
	}
}
