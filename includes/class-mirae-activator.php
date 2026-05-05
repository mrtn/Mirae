<?php

/**
 * Fired during plugin activation
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 * @subpackage Mirae/includes
 */

class Mirae_Activator {

	/**
	 * Seed default options and re-sanitize legacy data on activation/upgrade.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( false === get_option( 'link_data' ) ) {
			update_option( 'link_data', wp_json_encode( array() ) );
		}

		self::migrate_link_data();
		self::migrate_orphan_options();
	}

	/**
	 * Re-run the link_data through the current sanitizer so legacy rows that
	 * predate the platform-key whitelist or url validation are normalized.
	 */
	private static function migrate_link_data() {
		$raw = get_option( 'link_data' );
		if ( ! is_string( $raw ) || '' === $raw ) {
			return;
		}

		require_once plugin_dir_path( __DIR__ ) . 'includes/class-mirae-data.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-mirae-admin.php';

		$admin   = new Mirae_Admin( 'mirae', defined( 'MIRAE_VERSION' ) ? MIRAE_VERSION : '0' );
		$cleaned = $admin->sanitize_link_data( $raw );

		if ( $cleaned !== $raw ) {
			update_option( 'link_data', $cleaned );
		}
	}

	/**
	 * Drop the dead `userdata` option seeded by versions < 1.1.0.
	 */
	private static function migrate_orphan_options() {
		if ( false !== get_option( 'userdata' ) ) {
			delete_option( 'userdata' );
		}
	}
}
