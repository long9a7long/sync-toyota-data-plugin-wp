<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://fanmedia.com.vn/
 * @since      1.0.0
 *
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 * @author     Celestial <truonglong@fanmedia.com.vn>
 */
class Sync_Toyota_Data_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sync-toyota-data',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
