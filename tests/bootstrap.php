<?php
/**
 * Bootstrap for unit tests that exercise pure-PHP logic without spinning up
 * a full WordPress install. We stub the small surface of WP core functions
 * that the unit under test touches.
 *
 * @package Mirae
 */

if ( ! defined( 'WPINC' ) ) {
	define( 'WPINC', 'wp-includes' );
}

if ( ! defined( 'MIRAE_VERSION' ) ) {
	define( 'MIRAE_VERSION', '0-test' );
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );
		return preg_replace( '/[^a-z0-9_\-]/', '', $key );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		$str = (string) $str;
		$str = strip_tags( $str );
		$str = preg_replace( '/[\r\n\t]+/', ' ', $str );
		return trim( preg_replace( '/\s+/', ' ', $str ) );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		$url = trim( (string) $url );
		// Stripped-down approximation: only allow http(s) URLs.
		if ( '' === $url ) {
			return '';
		}
		return preg_match( '#^https?://#i', $url ) ? $url : '';
	}
}

if ( ! function_exists( 'wp_kses' ) ) {
	function wp_kses( $string, $allowed_html ) {
		return strip_tags( (string) $string, '<' . implode( '><', array_keys( (array) $allowed_html ) ) . '>' );
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = null ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = null ) {
		return (string) $text;
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action() { /* noop in unit tests */ }
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return rtrim( dirname( $file ), '/\\' ) . DIRECTORY_SEPARATOR;
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file ) {
		return 'https://example.test/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
	}
}

// Load the units under test.
require_once dirname( __DIR__ ) . '/includes/class-mirae-data.php';
require_once dirname( __DIR__ ) . '/includes/class-mirae-util.php';
require_once dirname( __DIR__ ) . '/admin/class-mirae-admin.php';
