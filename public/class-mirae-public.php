<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 * @subpackage Mirae/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Mirae
 * @subpackage Mirae/public
 * @author     Maarten Kumpen <maarten@mrtn.be>
 */
class Mirae_Public {

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
	 * Whether the public stylesheets have already been enqueued.
	 *
	 * @var bool
	 */
	private $styles_enqueued = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register stylesheets only when the page actually uses Mirae output.
	 *
	 * Hooked on `wp_enqueue_scripts`. The Miro theme renders Mirae on the
	 * front page without the shortcode, so we always enqueue when `is_front_page()`
	 * is true; otherwise we only enqueue when the current post contains `[mirae]`.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		if ( $this->should_enqueue() ) {
			$this->register_styles();
		}
	}

	/**
	 * Decide whether the current request needs Mirae's CSS.
	 */
	private function should_enqueue() {
		if ( is_front_page() || is_home() ) {
			return true;
		}

		if ( is_singular() ) {
			$post = get_post();
			if ( $post && has_shortcode( $post->post_content, 'mirae' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enqueue the public stylesheets exactly once.
	 */
	private function register_styles() {
		if ( $this->styles_enqueued ) {
			return;
		}
		$this->styles_enqueued = true;

		wp_enqueue_style( 'littlelink-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'littlelink-brands', plugin_dir_url( __FILE__ ) . 'css/brands.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mirae-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Render the [mirae] shortcode output.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function miraedisplaydata() {
		// Late-enqueue in case the shortcode runs on a screen that should_enqueue() didn't catch
		// (e.g. a custom template that doesn't satisfy is_singular()).
		$this->register_styles();

		$display_data = json_decode( get_option( 'link_data', '[]' ), true );

		if ( ! is_array( $display_data ) || empty( $display_data ) ) {
			return '<p>' . esc_html__( 'No links available.', 'mirae' ) . '</p>';
		}

		$html  = "<div class='container'>\n";
		$html .= "<div class='column'>\n";

		foreach ( $display_data as $link ) {
			if ( ! is_array( $link ) || empty( $link['platform'] ) || empty( $link['link'] ) ) {
				continue;
			}

			$entry = Mirae_Data::get( $link['platform'] );
			if ( null === $entry ) {
				continue;
			}

			$user_link = esc_url( $link['link'] );
			if ( '' === $user_link ) {
				continue;
			}

			$icon_path    = esc_url( Mirae_Data::icon_url( $entry['icon'] ) );
			$button_class = esc_attr( $entry['class'] );
			$alt_text     = esc_attr( $entry['platform'] . ' Logo' );

			$button_text = ( isset( $link['buttonText'] ) && '' !== $link['buttonText'] && 'Default' !== $link['buttonText'] )
				? esc_html( $link['buttonText'] )
				: esc_html( $entry['button'] );

			$html .= "<a class='{$button_class}' href='{$user_link}' target='_blank' rel='noopener noreferrer' role='button'>\n";
			$html .= "<img class='icon' aria-hidden='true' src='{$icon_path}' alt='{$alt_text}'>\n";
			$html .= "{$button_text}</a>\n";
		}

		$html .= "</div>\n";
		$html .= "</div>\n";

		return $html;
	}
}
