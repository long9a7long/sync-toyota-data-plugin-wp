<?php 
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'entities/base-model.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'entities/product/model-product.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'constants/api-urls.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'custom-curl.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'constants/table-name.php';

class ProductModelData extends BaseModel {
    private $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getDetail($idProd) {
		global $wpdb;
		$tableName = TableName::$modelCar;
		$kq = $wpdb->get_row( 
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$tableName." WHERE modelId={$idProd}") 
		);

		if ($kq)
			return new ModelProduct($kq->modelId, $kq->modelName);
		return null;

    }

    public function create($productModel ) {
        global $wpdb;
		$tableName = TableName::$modelCar;
		$wpdb->insert(

			$wpdb->prefix.$tableName,

			array(

				'modelId' => $productModel->getModelId(),

				'modelName' => $productModel->getModelName(),

			)

		);
    }

    public function update($productModel ) {
        global $wpdb;
		$tableName = TableName::$modelCar;
		$wpdb->update(

			$wpdb->prefix.$tableName,

			array(
				'modelName' => $productModel->getModelName(),
			),
			array(
				'modelId' => $productModel->getModelId(),
			)

		);
    }

    public function getList(RequestListBaseModel $baseReq) {
        $curl = new CustomCurl();
        $url = Constants::instance()->getModelProd();
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        return $res->result->items;
    }

    public function getTotalCount() {
        
        $curl = new CustomCurl();
        $url = Constants::instance()->getModelProd();
        $baseReq = new RequestListBaseModel(null, null, 0, 1);
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        if($res) {
            return $res->result->totalCount;
        }
        return 0;
    }

    public function updateTotalCountProductModel($total) {
        global $wpdb;

		$total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_model_prod_sync'");

		if(count($total_record)>0) {

			$wpdb->update(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_value' => $total,

				),

				array(

					'meta_key' => 'total_records_model_prod_sync'

				)

			);

		} else {

			$wpdb->insert(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_key' => 'total_records_model_prod_sync',

					'meta_value' => $total,

				)

			);

		}
    }

    public function update_size_per_step($size) {

		global $wpdb;

		$total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='size_per_step_model_prod_sync'");

		if(count($total_record)>0) {

			$wpdb->update(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_value' => $size,

				),

				array(

					'meta_key' => 'size_per_step_model_prod_sync'

				)

			);

		} else {

			$wpdb->insert(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_key' => 'size_per_step_model_prod_sync',

					'meta_value' => $size,

				)

			);

		}

		

	}

    public function get_size_per_step() {

		global $wpdb;

		$total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='size_per_step_model_prod_sync'");

		if($total_record) {

			return $total_record->meta_value;

		}

		return 0;	

	}

    public function update_start_at($time) {

		global $wpdb;

		$total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='start_at_model_prod_sync'");

		if(count($total_record)>0) {

			$wpdb->update(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_value' => $time,

				),

				array(

					'meta_key' => 'start_at_model_prod_sync'

				)

			);

		} else {

			$wpdb->insert(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_key' => 'start_at_model_prod_sync',

					'meta_value' => $time,

				)

			);

		}

		

	}

	

	public function update_end_at($time) {

		global $wpdb;

		$total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='end_at_model_prod_sync'");

		if(count($total_record)>0) {

			$wpdb->update(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_value' => $time,

				),

				array(

					'meta_key' => 'end_at_model_prod_sync'

				)

			);

		} else {

			$wpdb->insert(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_key' => 'end_at_model_prod_sync',

					'meta_value' => $time,

				)

			);

		}

		

    }

    public function get_total_records() {

		global $wpdb;

		$total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_model_prod_sync'");

		if($total_record) {

			return $total_record->meta_value;

		}

		return 0;	

	}


    public function delete($idProd) {
        
    }
}
