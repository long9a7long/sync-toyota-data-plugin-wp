<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://fanmedia.com.vn/
 * @since      1.0.0
 *
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 * @author     Celestial <truonglong@fanmedia.com.vn>
 */
class Sync_Toyota_Data_Deactivator {

	private $table_activator;

	public function __construct($activator)
	{
		$this->table_activator = $activator->get_config_table_name();
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function deactivate() {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS ".$this->table_activator );
	}

}
