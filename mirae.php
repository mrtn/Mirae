<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://maartenkumpen.com
 * @since             1.0.1
 * @package           Mirae
 *
 * @wordpress-plugin
 * Plugin Name:       Mirae
 * Plugin URI:        https://maartenkumpen.com
 * Description:       Mirae lets you build a customizable Linktree-style profile with platform buttons, icons, and custom text. All managed from your WordPress admin.
 * Version:           0.0.1
 * Author:            Maarten Kumpen
 * Author URI:        https://maartenkumpen.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mirae
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MIRAE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mirae-activator.php
 */
function activate_mirae() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mirae-activator.php';
	Mirae_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mirae-deactivator.php
 */
function deactivate_mirae() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mirae-deactivator.php';
	Mirae_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mirae' );
register_deactivation_hook( __FILE__, 'deactivate_mirae' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mirae.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mirae() {

	$plugin = new Mirae();
	$plugin->run();

}
run_mirae();
