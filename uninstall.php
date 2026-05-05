<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes every option Mirae has written so the site is left clean.
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$mirae_options = array(
	// Link list.
	'link_data',

	// Front-page settings (registered in mirae_settings group).
	'display_name',
	'intro_text',
	'profile_picture',
	'background_image',
	'overlay_pattern',
	'container_bg_color',
	'text_color',
	'container_bg_alpha',

	// Internal bookkeeping.
	'mirae_db_version',

	// Legacy / orphan.
	'userdata',
);

if ( is_multisite() ) {
	$site_ids = get_sites( array( 'fields' => 'ids' ) );
	foreach ( $site_ids as $site_id ) {
		switch_to_blog( $site_id );
		foreach ( $mirae_options as $option ) {
			delete_option( $option );
		}
		restore_current_blog();
	}
} else {
	foreach ( $mirae_options as $option ) {
		delete_option( $option );
	}
}

// Clear cached GitHub update lookups so a re-install starts fresh.
delete_site_transient( 'mirae_github_remote_version' );
delete_site_transient( 'mirae_github_release_body' );
