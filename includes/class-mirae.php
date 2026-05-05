<?php

require_once plugin_dir_path( __FILE__ ) . 'class-mirae-github-updater.php';

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 * @subpackage Mirae/includes
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
 * @package    Mirae
 * @subpackage Mirae/includes
 * @author     Maarten Kumpen <maarten@mrtn.be>
 */
class Mirae {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mirae_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {
		if ( defined( 'MIRAE_VERSION' ) ) {
			$this->version = MIRAE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mirae';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_action( 'plugins_loaded', array( $this, 'maybe_run_migrations' ) );

		// Skip the GitHub-hosted self-updater on builds that ship through the
		// WordPress.org plugin directory, where WP handles updates itself and
		// external service calls are not allowed.
		if ( ! defined( 'MIRAE_DISABLE_GITHUB_UPDATER' ) || ! MIRAE_DISABLE_GITHUB_UPDATER ) {
			new Mirae_GitHub_Updater( plugin_dir_path( __DIR__ ) . 'mirae.php' );
		}
	}

	/**
	 * Run activation-style migrations when the stored version is older than the current one.
	 * Covers WP auto-updates where register_activation_hook does not fire.
	 */
	public function maybe_run_migrations() {
		$stored = get_option( 'mirae_db_version' );
		if ( $stored === $this->version ) {
			return;
		}

		require_once plugin_dir_path( __DIR__ ) . 'includes/class-mirae-activator.php';
		Mirae_Activator::activate();

		update_option( 'mirae_db_version', $this->version );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$base = plugin_dir_path( __DIR__ );

		require_once $base . 'includes/class-mirae-loader.php';
		require_once $base . 'includes/class-mirae-i18n.php';
		require_once $base . 'includes/class-mirae-data.php';
		require_once $base . 'includes/class-mirae-util.php';
		require_once $base . 'admin/class-mirae-admin.php';
		require_once $base . 'public/class-mirae-public.php';

		$this->loader = new Mirae_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mirae_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mirae_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mirae_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mirae_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_mirae_general_settings' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'maybe_render_theme_warning' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mirae_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		$this->loader->add_shortcode( 'mirae', $plugin_public, 'miraedisplaydata' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mirae_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
