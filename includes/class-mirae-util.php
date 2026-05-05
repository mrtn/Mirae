<?php
/**
 * Small shared utilities.
 *
 * @package    Mirae
 * @subpackage Mirae/includes
 */

class Mirae_Util {

	/**
	 * Convert a #RGB or #RRGGBB color to an rgba() string.
	 * Falls back to transparent black on invalid input.
	 */
	public static function hex_to_rgba( $hex, $alpha = 0.8 ) {
		$hex   = ltrim( (string) $hex, '#' );
		$alpha = max( 0, min( 1, (float) $alpha ) );

		if ( 3 === strlen( $hex ) ) {
			$r = hexdec( str_repeat( $hex[0], 2 ) );
			$g = hexdec( str_repeat( $hex[1], 2 ) );
			$b = hexdec( str_repeat( $hex[2], 2 ) );
		} elseif ( 6 === strlen( $hex ) && ctype_xdigit( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		} else {
			return 'rgba(0,0,0,' . $alpha . ')';
		}

		return "rgba({$r}, {$g}, {$b}, {$alpha})";
	}
}
