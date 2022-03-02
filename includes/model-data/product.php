<?php 
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'entities/base-model.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'constants/api-urls.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'custom-curl.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'constants/table-name.php';

class ProductData extends BaseModel {
    private $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getDetail($idProd) {
		global $wpdb;
    }
}