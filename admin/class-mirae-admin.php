<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 * @subpackage Mirae/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mirae
 * @subpackage Mirae/admin
 * @author     Maarten Kumpen <maarten@mrtn.be>
 */
class Mirae_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_notices', function () {
			$theme = wp_get_theme();
			if ($theme->get('Name') !== 'Miro') {
				echo '<div class="notice notice-error"><p><strong>Mirae</strong> requires the <em>Arke</em> theme to be installed and active.</p></div>';
			}
		});

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mirae_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mirae_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mirae-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap-icons', plugin_dir_url( __FILE__ ) . 'css/bootstrap-icons.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mirae_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mirae_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_media();
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'bootstrap-dragndrop', plugin_dir_url( __FILE__ ) . 'js/jquery.tablednd.1.0.5.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mirae-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );
	}

	/**
	 * Add custom menu
	 *
	 * @since    1.0.0
	 */
	public function mirae_admin_menu(){
		add_menu_page('Mirae Settings', 'Mirae', 'manage_options', 'mirae', array($this, 'mirae_admin_page'), 'dashicons-admin-links', 250 );
		add_submenu_page('mirae', 'Front page Settings', 'Settings', 'manage_options', 'mirae/settings', array($this, 'mirae_admin_subpage'), 100);
	}

	public function mirae_admin_page(){
		//return views
		require_once('partials/mirae-admin-display.php');
	}

	public function mirae_admin_subpage(){
		//return views
		require_once('partials/mirae-admin-settings-display.php');
	}

	/**
	 * Register custom field for plugin settings
	 *
	 * @since    1.0.0
	 */
	public function register_mirae_general_settings(){
		//registers all settings
		register_setting('mirae_settings', 'display_name' );
		register_setting('mirae_settings', 'intro_text' );
		register_setting('mirae_settings', 'profile_picture' );
		register_setting('mirae_settings', 'background_image' );
		register_setting('mirae_settings', 'overlay_pattern');
		register_setting('mirae_settings', 'container_bg_color');
		register_setting('mirae_settings', 'text_color');
		register_setting('mirae_settings', 'container_bg_alpha');

		register_setting( 'mirae_links', 'link_data' );
	}

}
