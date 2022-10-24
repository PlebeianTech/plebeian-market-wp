<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/includes
 */
class Plebeian_Market
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plebeian_Market_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('PLEBEIAN_MARKET_VERSION')) {
			$this->version = PLEBEIAN_MARKET_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'plebeian-market';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plebeian_Market_Loader. Orchestrates the hooks of the plugin.
	 * - Plebeian_Market_i18n. Defines internationalization functionality.
	 * - Plebeian_Market_Admin. Defines all hooks for the admin area.
	 * - Plebeian_Market_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plebeian-market-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plebeian-market-i18n.php';

		/**
		 * The class responsible for defining communication internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plebeian-market-communications.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plebeian-market-render.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-ajax-api.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-utils.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-screen-information.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-screen-buynow.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-screen-auctions.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-screen-customization.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-screen-setup.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plebeian-market-admin-common.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-plebeian-market-public.php';

		$this->loader = new Plebeian_Market_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plebeian_Market_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Plebeian_Market_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Plebeian_Market_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');


		$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_main_menu');

		if (Plebeian_Market_Admin::plebeian_setup_is_needed()) {
			$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_setup_submenu_standalone');
		} else {
			$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_information_submenu');
			$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_fixedprice_submenu');
			// $this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_auctions_submenu');
			$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_customization_submenu');
			$this->loader->add_action('admin_menu', $plugin_admin, 'plebeian_setup_submenu_with_others');

			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'add_media_script');
		}

		// Plebeian Market internal Ajax calls
		$plugin_ajax = new Plebeian_Market_Admin_Ajax_Api();
		$this->loader->add_action('wp_ajax_plebeian-load-options', $plugin_ajax, 'ajax_load_options');
		$this->loader->add_action('wp_ajax_plebeian-save-options', $plugin_ajax, 'ajax_save_options');
		$this->loader->add_action('wp_ajax_plebeian-get-price-btc', $plugin_ajax, 'ajax_get_price_in_btc');
		$this->loader->add_action('wp_ajax_plebeian-get_buynow_preview_html', $plugin_ajax, 'ajax_get_buynow_preview_html');
		$this->loader->add_action('wp_ajax_plebeian-ajax_get_buynow_info', $plugin_ajax, 'ajax_get_buynow_info');
		$this->loader->add_action('wp_ajax_plebeian-ajax_save_image_into_item', $plugin_ajax, 'ajax_save_image_into_item');
	}

	/**
	 * Register all of the hooks related to the public-facing are of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Plebeian_Market_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_action('wp_head', $plugin_public, 'plebeian_output_custom_css');

		$this->loader->add_action('init', $plugin_public, 'plebeian_shortcodes_init');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plebeian_Market_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
