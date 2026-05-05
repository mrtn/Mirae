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
	 * @param    string $plugin_name    The name of this plugin.
	 * @param    string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Render the "Mirae requires the Miro theme" admin notice when a different
	 * theme is active.
	 *
	 * Hooked from class-mirae.php via the loader, so the activator path can
	 * still instantiate this class for sanitizer access without registering
	 * a second copy of the notice.
	 */
	public function maybe_render_theme_warning() {
		$theme = wp_get_theme();
		if ( 'Miro' === $theme->get( 'Name' ) ) {
			return;
		}

		echo '<div class="notice notice-error"><p>'
			. wp_kses(
				__( '<strong>Mirae</strong> requires the <em>Miro</em> theme to be installed and active.', 'mirae' ),
				array(
					'strong' => array(),
					'em'     => array(),
				)
			)
			. '</p></div>';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {
		if ( ! $this->is_mirae_screen( $hook ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mirae-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! $this->is_mirae_screen( $hook ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'mirae-tablednd', plugin_dir_url( __FILE__ ) . 'js/jquery.tablednd.1.0.5.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mirae-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );

		wp_localize_script(
			$this->plugin_name,
			'miraeAdmin',
			array(
				'platforms' => $this->get_platforms_for_js(),
				'i18n'      => array(
					'selectPlatform'  => __( '-- Select a platform --', 'mirae' ),
					'edit'            => __( 'edit', 'mirae' ),
					'delete'          => __( 'delete', 'mirae' ),
					'save'            => __( 'save', 'mirae' ),
					'cancel'          => __( 'cancel', 'mirae' ),
					'noRecords'       => __( 'No data found', 'mirae' ),
					'confirmDelete'   => __( 'Are you sure you want to delete this link?', 'mirae' ),
					'mediaTitle'      => __( 'Select or upload an image', 'mirae' ),
					'mediaButtonText' => __( 'Use this image', 'mirae' ),
					'previewAlt'      => __( 'Preview', 'mirae' ),
				),
			)
		);
	}

	/**
	 * Limit assets to the Mirae admin pages.
	 */
	private function is_mirae_screen( $hook ) {
		if ( ! is_string( $hook ) ) {
			return false;
		}
		return false !== strpos( $hook, 'mirae' );
	}

	/**
	 * Build a small platform list for the JS dropdown.
	 */
	private function get_platforms_for_js() {
		$platforms = Mirae_Data::platforms();
		$out       = array();
		foreach ( $platforms as $key => $entry ) {
			$out[ $key ] = isset( $entry['button'] ) ? $entry['button'] : $key;
		}
		return $out;
	}

	/**
	 * Add custom menu
	 *
	 * @since    1.0.0
	 */
	public function mirae_admin_menu() {
		add_menu_page(
			__( 'Mirae Settings', 'mirae' ),
			__( 'Mirae', 'mirae' ),
			'manage_options',
			'mirae',
			array( $this, 'mirae_admin_page' ),
			'dashicons-admin-links',
			250
		);
		add_submenu_page(
			'mirae',
			__( 'Front page Settings', 'mirae' ),
			__( 'Settings', 'mirae' ),
			'manage_options',
			'mirae/settings',
			array( $this, 'mirae_admin_subpage' ),
			100
		);
	}

	public function mirae_admin_page() {
		require_once 'partials/mirae-admin-display.php';
	}

	public function mirae_admin_subpage() {
		require_once 'partials/mirae-admin-settings-display.php';
	}

	/**
	 * Register custom field for plugin settings
	 *
	 * @since    1.0.0
	 */
	public function register_mirae_general_settings() {
		register_setting( 'mirae_settings', 'display_name', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'mirae_settings', 'intro_text', array( 'sanitize_callback' => array( $this, 'sanitize_intro_text' ) ) );
		register_setting( 'mirae_settings', 'profile_picture', array( 'sanitize_callback' => 'esc_url_raw' ) );
		register_setting( 'mirae_settings', 'background_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
		register_setting( 'mirae_settings', 'overlay_pattern', array( 'sanitize_callback' => 'esc_url_raw' ) );
		register_setting( 'mirae_settings', 'container_bg_color', array( 'sanitize_callback' => array( $this, 'sanitize_color' ) ) );
		register_setting( 'mirae_settings', 'text_color', array( 'sanitize_callback' => array( $this, 'sanitize_color' ) ) );
		register_setting( 'mirae_settings', 'container_bg_alpha', array( 'sanitize_callback' => array( $this, 'sanitize_alpha' ) ) );

		register_setting( 'mirae_links', 'link_data', array( 'sanitize_callback' => array( $this, 'sanitize_link_data' ) ) );
	}

	/**
	 * Allow only basic inline formatting in the intro text.
	 */
	public function sanitize_intro_text( $value ) {
		return wp_kses(
			(string) $value,
			array(
				'a'      => array(
					'href'   => array(),
					'title'  => array(),
					'rel'    => array(),
					'target' => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
			)
		);
	}

	/**
	 * Accept hex colors only (#fff or #ffffff).
	 */
	public function sanitize_color( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		return preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value ) ? $value : '';
	}

	/**
	 * Clamp alpha to [0, 1] with at most 2 decimals.
	 */
	public function sanitize_alpha( $value ) {
		$value = (float) $value;
		if ( $value < 0 ) {
			$value = 0;
		}
		if ( $value > 1 ) {
			$value = 1;
		}
		return (string) round( $value, 2 );
	}

	/**
	 * Validate the JSON link list submitted from the admin.
	 * Returns a JSON-encoded, normalized list. Drops anything unsafe.
	 */
	public function sanitize_link_data( $value ) {
		$decoded = json_decode( (string) $value, true );
		if ( ! is_array( $decoded ) ) {
			return wp_json_encode( array() );
		}

		$platforms = Mirae_Data::platforms();
		$clean     = array();
		$seq       = 0;

		foreach ( $decoded as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$platform = isset( $row['platform'] ) ? sanitize_key( $row['platform'] ) : '';
			$url      = isset( $row['link'] ) ? esc_url_raw( $row['link'] ) : '';

			if ( '' === $platform || ! isset( $platforms[ $platform ] ) ) {
				continue;
			}
			if ( '' === $url || ! preg_match( '#^https?://#i', $url ) ) {
				continue;
			}

			$button_text = isset( $row['buttonText'] ) ? sanitize_text_field( $row['buttonText'] ) : 'Default';
			if ( '' === $button_text ) {
				$button_text = 'Default';
			}

			++$seq;
			$clean[] = array(
				'sequence'   => $seq,
				'platform'   => $platform,
				'link'       => $url,
				'buttonText' => $button_text,
			);
		}

		return wp_json_encode( $clean );
	}
}
