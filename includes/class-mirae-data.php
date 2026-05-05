<?php
/**
 * Centralized access to platform data.
 *
 * @package    Mirae
 * @subpackage Mirae/includes
 */

class Mirae_Data {

	private static $platforms = null;

	public static function platforms() {
		if ( null === self::$platforms ) {
			$path            = plugin_dir_path( __DIR__ ) . 'admin/data/platforms.php';
			$data            = file_exists( $path ) ? include $path : array();
			self::$platforms = is_array( $data ) ? $data : array();
		}
		return self::$platforms;
	}

	public static function get( $key ) {
		$platforms = self::platforms();
		return isset( $platforms[ $key ] ) ? $platforms[ $key ] : null;
	}

	public static function icon_url( $icon_filename ) {
		return plugin_dir_url( __DIR__ ) . 'assets/icons/' . ltrim( $icon_filename, '/' );
	}
}
