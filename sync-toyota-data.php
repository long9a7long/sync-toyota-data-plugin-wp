<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fanmedia.com.vn/
 * @since             1.0.0
 * @package           Sync_Toyota_Data
 *
 * @wordpress-plugin
 * Plugin Name:       Sync Toyota Data
 * Plugin URI:        https://fanmedia.com.vn/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Celestial
 * Author URI:        https://fanmedia.com.vn/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sync-toyota-data
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
define( 'SYNC_TOYOTA_DATA_VERSION', '1.0.0' );
define( 'SYNC_TOYOTA_DATA_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'SYNC_TOYOTA_DATA_PLUGIN_PATH', plugin_dir_path(__FILE__) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sync-toyota-data-activator.php
 */
function activate_sync_toyota_data() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sync-toyota-data-activator.php';
	$activator = new Sync_Toyota_Data_Activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sync-toyota-data-deactivator.php
 */
function deactivate_sync_toyota_data() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sync-toyota-data-activator.php';
	$activator = new Sync_Toyota_Data_Activator();

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sync-toyota-data-deactivator.php';

	$deactivator = new Sync_Toyota_Data_Deactivator($activator);
	$deactivator->deactivate();

}

register_activation_hook( __FILE__, 'activate_sync_toyota_data' );
register_deactivation_hook( __FILE__, 'deactivate_sync_toyota_data' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sync-toyota-data.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sync_toyota_data() {

	$plugin = new Sync_Toyota_Data();
	$plugin->run();

}
run_sync_toyota_data();
