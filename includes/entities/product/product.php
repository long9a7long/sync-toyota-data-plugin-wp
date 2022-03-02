<?php
// require_once('../../constants/table-name.php');
// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'product/model-product.php';

class Product {
    public $id;
    public $banner;
    public $modelCar;
    public $gradeId;
    public $commercialName;
    public $slogan;
    public $img45;
    public $img90;
    public $imgDetail;
    public $description;
    public $vehicleImages;
    public $internalColorImages;
    public $overview;

    public function __construct($id, $banner, $modelCar, $gradeId, $commercialName, $slogan, $img45, $img90, $imgDetail, $description, $vehicleImages, $internalColorImages, $overview )
    {
        $this->id = $id;
        $this->banner = $banner;
        $this->modelCar = $modelCar;
        $this->gradeId = $gradeId;
        $this->commercialName = $commercialName;
        $this->slogan = $slogan;
        $this->img45 = $img45;
        $this->img90 = $img90;
        $this->imgDetail = $imgDetail;
        $this->description = $description;
        $this->vehicleImages = $vehicleImages;
        $this->internalColorImages = $internalColorImages;
        $this->overview = $overview;
    }

    public function setModelCarByID($modelCarId) {
        global $wpdb;
        $tableName = TableName::$modelCar;
		$rec = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}{$tableName}` WHERE `modelId`='{$modelCarId}'");

		if($rec) {
            $modelCar = new ModelProduct($rec->modelId,$rec->modelName);
			$this->modelCar = $modelCar;
		}
    }

}


