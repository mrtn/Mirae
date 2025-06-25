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
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		//wp_enqueue_style( 'littlelink-reset', plugin_dir_url( __FILE__ ) . 'css/reset.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'littlelink-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'littlelink-brands', plugin_dir_url( __FILE__ ) . 'css/brands.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mirae-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mirae-public.js', array( 'jquery' ), $this->version, false );

	}

	//shortcode
	public function miraedisplaydata(){
		$platform_data = json_decode($this->getData(), true); // assoc array
		$display_data = json_decode(get_option('link_data'), true); // assoc array
		
		if (!is_array($display_data)) {
			return '<p>Geen links beschikbaar.</p>';
		}

		$html = "<div class='container'>\n";
		$html .= "<div class='column'>\n";


		foreach ($display_data as $link) {

			$key = $link['platform'];

			if (isset($platform_data[$key])) {
				$entry = $platform_data[$key];
				$icon_path = esc_url(plugin_dir_url(__DIR__) . 'assets/icons/' . basename($entry['icon']));
				$button_class = esc_attr($entry['class']);
				$user_link = $link['link'];

				$button_text = (isset($link['buttonText']) && $link['buttonText'] !== 'Default') 
				? esc_html($link['buttonText']) 
				: esc_html($entry['button']);

				$alt_text = esc_attr($entry['platform'] . ' Logo');
	
				$html .= "<a class='{$button_class}' href='{$user_link}' target='_blank' rel='noopener' role='button'>\n";
				$html .= "<img class='icon' aria-hidden='true' src='{$icon_path}' alt='{$alt_text}'>\n";
				$html .= "{$button_text}</a>\n";
			}
		}	
		$html .= "</div>";
		$html .= "</div>";

		return $html;
	}

	public function getData(){
		$file_path = plugin_dir_url( __DIR__ ) . 'admin/data/data.json';
		$json_data = file_get_contents($file_path);

		if ($json_data === null) {
				return 'Error decoding JSON file';
			} else {
				return $json_data;
			}

	}
}
