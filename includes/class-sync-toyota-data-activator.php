<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/constants/table-name.php';

/**
 * Fired during plugin activation
 *
 * @link       https://fanmedia.com.vn/
 * @since      1.0.0
 *
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/includes
 * @author     Celestial <truonglong@fanmedia.com.vn>
 */
class Sync_Toyota_Data_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function activate() {
		$this->init_table_sync_info();
		$this->init_table_model_car();
		$this->init_table_grade_car();
		$this->init_table_information_car();

	}

	public function init_table_sync_info() {
		global $wpdb;
		if($wpdb->get_var("SHOW tables like '".$this->get_config_table_name()."'") != $this->get_config_table_name()) {
			$table_query = "CREATE TABLE `".$this->get_config_table_name()."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`meta_key` varchar(100) NOT NULL,
				`meta_value` longtext DEFAULT NULL,
				PRIMARY KEY (`id`)
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
	
			require_once (ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta( $table_query );
		}
		// Insert data product_sync
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_records_prod_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'size_per_step_product_sync',
				'meta_value' => 5,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'current_step_product_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_times_product_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_records_gallery_prod_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_records_catalogue_sync',
				'meta_value' => 0,
			)
		);
		
	}

	public function init_table_model_car() {
		global $wpdb;
		$tableName = TableName::$modelCar;
		if($wpdb->get_var("SHOW tables like '".$wpdb->prefix.$tableName."'") != $wpdb->prefix.$tableName) {
			$table_query = "CREATE TABLE `".$wpdb->prefix.$tableName."` (
				`modelId` int(11) NOT NULL AUTO_INCREMENT,
				`modelName` varchar(100) NOT NULL,
				PRIMARY KEY (`modelId`)
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
	
			require_once (ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta( $table_query );
		}

		// Insert data product_sync
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_records_model_prod_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'size_per_step_model_prod_sync',
				'meta_value' => 10,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'current_step_model_prod_sync',
				'meta_value' => 0,
			)
		);
		$wpdb->insert(
			$this->get_config_table_name(),
			array(
				'meta_key' => 'total_times_model_prod_sync',
				'meta_value' => 0,
			)
		);
	}

	public function init_table_grade_car() {
		global $wpdb;
		$gradeCarTableName = TableName::$gradeCar;
		$gradeModelCarTableName = TableName::$gradeModelCar;
		if($wpdb->get_var("SHOW tables like '".$wpdb->prefix.$gradeCarTableName."'") != $wpdb->prefix.$gradeCarTableName) {
			$table_query = "CREATE TABLE `".$wpdb->prefix.$gradeCarTableName."` (
				`gradeId` int(11) NOT NULL AUTO_INCREMENT,
				`gradeName` varchar(100) NOT NULL,
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
	
			require_once (ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta( $table_query );
		}

		if($wpdb->get_var("SHOW tables like '".$wpdb->prefix.$gradeModelCarTableName."'") != $wpdb->prefix.$gradeModelCarTableName) {
			$table_query = "CREATE TABLE `".$wpdb->prefix.$$gradeModelCarTableName."` (
				`gradeId` int(11) NOT NULL AUTO_INCREMENT,
				`modelId` int(11) NOT NULL,
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
	
			require_once (ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta( $table_query );
		}
	}

	public function init_table_information_car() {
		global $wpdb;
		$tableName = TableName::$informationCar;
		if($wpdb->get_var("SHOW tables like '".$wpdb->prefix.$tableName."'") != $wpdb->prefix.$tableName) {
			$table_query = "CREATE TABLE `".$wpdb->prefix.$tableName."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`postId` int(11) NOT NULL,
				`infoType` varchar(100) NOT NULL,
				`infoId` int(11) NOT NULL,
				`name` varchar(100) NOT NULL,
				`detail` longtext,
				`url` varchar(100),
				`ordering` int(11) NULL,
				`category` varchar(100) NULL,
				PRIMARY KEY (`id`)
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
	
			require_once (ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta( $table_query );
		}
	}

	public function get_config_table_name() {
		global $wpdb;
		$tableName = TableName::$syncInfoTable;
		return $wpdb->prefix.$tableName;
	}

}
