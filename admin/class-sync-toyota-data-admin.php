<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/connection-toyota-data.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model-data/product-model.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model-data/product.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/entities/product/product.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/entities/product/product-info.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model-data/request-list-base-model.php';



/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://fanmedia.com.vn/
 * @since      1.0.0
 *
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sync_Toyota_Data
 * @subpackage Sync_Toyota_Data/admin
 * @author     Celestial <truonglong@fanmedia.com.vn>
 */
class Sync_Toyota_Data_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $username;
	private $password;
	private $tenantId;

	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->username = "API_DATAHUB_TVT";
		$this->tenantId = 1005;
		$this->password = "[(V*vu7U5%^$";
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sync_Toyota_Data_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sync_Toyota_Data_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$valid_pages = array("sync-toyota-products", "sync-toyota-models");
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		if (in_array($page, $valid_pages)) {
			wp_enqueue_style("bootstrap", SYNC_TOYOTA_DATA_PLUGIN_URL . 'assets/css/boostraps.min.css', array(), $this->version, 'all');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sync-toyota-data-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sync_Toyota_Data_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sync_Toyota_Data_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$valid_pages = array("sync-toyota-products", "sync-toyota-models");
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		if (in_array($page, $valid_pages)) {
			wp_enqueue_script("bootstrap", SYNC_TOYOTA_DATA_PLUGIN_URL . 'assets/js/boostraps.min.js', array('jquery'), $this->version, false);

			wp_localize_script($this->plugin_name, "sync_toyota_data", array(
				"name" => "Sync Data Toyota",
				"author" => "Celestial",
				"ajaxurl" => admin_url("admin-ajax.php")
			));
		}


		if ($page == "sync-toyota-products") {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sync-toyota-data-admin-product.js', array('jquery'), $this->version, false);
		}
		if ($page == "sync-toyota-models") {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sync-toyota-data-admin-model-product.js', array('jquery'), $this->version, false);
		}
	}

	/**
	 * Create menu
	 *
	 * @since    1.0.0
	 */
	public function create_sync_menu()
	{
		add_menu_page(
			"Sync Toyota Data",
			"Sync Toyota Data",
			"manage_options",
			"sync-toyota-tool",
			array($this, "sync_management_plugin")
		);

		// create plugin submenus dashboard
		add_submenu_page(
			"sync-toyota-tool",
			"Dashboard",
			"Dashboard",
			"manage_options",
			"sync-toyota-tool",
			array($this, "sync_management_plugin")
		);

		// create plugin submenus Product
		add_submenu_page(
			"sync-toyota-tool",
			"Sync Model",
			"Sync Model",
			"manage_options",
			"sync-toyota-models",
			array($this, "sync_management_models")
		);

		add_submenu_page(
			"sync-toyota-tool",
			"Sync Product",
			"Sync Product",
			"manage_options",
			"sync-toyota-products",
			array($this, "sync_management_products")
		);
	}

	/**
	 * Menu callback function sync management plugin
	 *
	 * @since    1.0.0
	 */
	public function sync_management_plugin()
	{
		echo "<h3>Welcome</h3>";
	}

	/**
	 * Menu callback function sync management products
	 *
	 * @since    1.0.0
	 */
	public function sync_management_models()
	{
		$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
		$productMD = new ProductModelData($connectionData);
		$total_records = $productMD->getTotalCount();
		$productMD->updateTotalCountProductModel($total_records);
		$size_per_step = $this->get_meta_sync_value('size_per_step_model_prod_sync');
		ob_start();
		include_once(SYNC_TOYOTA_DATA_PLUGIN_PATH . "admin/partials/sync-toyota-data-admin-model-prod.php");
		$template = ob_get_contents();
		ob_end_clean();
		echo $template;
	}

	/**
	 * Menu callback function sync management products
	 *
	 * @since    1.0.0
	 */
	public function sync_management_products()
	{
		$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
		$productD = new ProductData($connectionData);

		$total_records = $productD->getTotalCount();
		// $total_records = $productD->getListImage(new RequestListBaseModel(null, null, 0, 2));
		// echo "<pre>";
		// print_r($total_records);
		// echo "</pre>";

		$productD->updateTotalCountProductModel($total_records);
		$size_per_step = $this->get_meta_sync_value('size_per_step_product_sync');
		ob_start();
		include_once(SYNC_TOYOTA_DATA_PLUGIN_PATH . "admin/partials/sync-toyota-data-admin-product.php");
		$template = ob_get_contents();
		ob_end_clean();
		echo $template;
	}

	/**
	 * Handle ajax admin request
	 *
	 * @since    1.0.0
	 */
	public function handle_ajax_requests_admin()
	{
		$param = isset($_REQUEST['param']) ? $_REQUEST['param'] : "";
		if (!empty($param)) {
			switch ($param) {
				case "get_total_records_model_prod":
					$this->process_ajax_get_total_records_model_prod();
					break;
				case "change_size_per_step_model_prod":
					$this->process_ajax_change_size_per_step_model_prod();
					break;
				case "sync_model_prod":
					$this->process_ajax_sync_model_prod();
					break;
				case "get_total_records_product":
					$this->process_ajax_get_total_records_product();
					break;
				case "change_size_per_step_product":
					$this->process_ajax_change_size_per_step_product();
					break;
				case "sync_product":
					$this->process_ajax_sync_product();
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Sync Model Prod data got from Toyota.
	 *
	 * @since    1.0.0
	 */
	public function process_ajax_get_total_records_model_prod()
	{
		try {
			$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
			$productMD = new ProductModelData($connectionData);
			$baseReq = new RequestListBaseModel(null, null, 0, 5);
			$total_records = $productMD->getTotalCount($baseReq);
			$productMD->updateTotalCountProductModel($total_records);
			echo json_encode(array(
				"status" => 1,
				"message" => "Success",
				"data" => $total_records,
			));
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	public function process_ajax_change_size_per_step_model_prod()
	{
		try {
			$result = array();
			$size = isset($_POST['size_per_step']) ? $_POST['size_per_step'] : 0;
			if ($size == 0) {
				$result = array(
					"status" => 0,
					"message" => "Invalid data",
				);
			} else {

				$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
				$productMD = new ProductModelData($connectionData);
				$productMD->update_size_per_step($size);
				$result = array(
					"status" => 1,
					"message" => "Success",
					"data" => $size,
				);
			}

			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	public function process_ajax_sync_model_prod()
	{
		try {
			$result = array();
			$step = isset($_POST['step']) ? $_POST['step'] : 0;
			$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
			$productMD = new ProductModelData($connectionData);

			if ($step == 0) {
				$result = array(
					"status" => 0,
					"message" => "Invalid data",
				);
			} else {
				if ($step == 1) {
					$now = new DateTime();
					$productMD->update_start_at($now->format('Y-m-d H:i:s'));
				}
				$size_per_step = $productMD->get_size_per_step();
				$total_records = $productMD->get_total_records();

				$data_synced = $this->sync_model_prod($productMD, $size_per_step, $step, $total_records);

				$total_step = ceil($total_records / $size_per_step);
				if ($step == $total_step) {
					$now = new DateTime();
					$productMD->update_end_at($now->format('Y-m-d H:i:s'));
				}
				$result = array(
					"status" => 1,
					"message" => "Success",
					"data" => [
						"step" => $step + 1,
						"total_step" => $total_step,
						"model_prods" => $data_synced,
					],
				);
			}

			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	public function sync_model_prod($productMD, $size_per_step, $step, $total_records)
	{
		$syncedCus = array();
		$baseReq = new RequestListBaseModel(null, null, $size_per_step * ($step - 1), $size_per_step);

		$results = $productMD->getList($baseReq);
		if ($results) {
			foreach ($results as $key => $value) {
				$modelProd = new ModelProduct($value->modelId, $value->modelName);
				$modelProdDb = $productMD->getDetail($value->modelId);
				if ($modelProdDb) {
					$productMD->update($modelProd);
				} else {
					$productMD->create($modelProd);
				}
				array_push($syncedCus, $modelProd->getModelName());
			}
		}
		unset($results);
		return $syncedCus;
	}

	/**
	 * Sync Customer data got from Toyota.
	 *
	 * @since    1.0.0
	 */
	public function sync_product($productMD, $size_per_step, $step, $total_records)
	{
		$syncedCus = array();
		$baseReq = new RequestListBaseModel(null, null, $size_per_step * ($step - 1), $size_per_step);
		$mapVehicleImage = function ($vehicleImages): VehicleImage {
			return new VehicleImage(
				$vehicleImages->imageUrl,
				$vehicleImages->colorId,
				$vehicleImages->colorName,
				$vehicleImages->hexcode,
				$vehicleImages->price
			);
		};

		$mapInternalColorImage = function ($internalColorImages): InternalColorImage {
			return new InternalColorImage(
				$internalColorImages->imageUrl,
				$internalColorImages->iColorId,
				$internalColorImages->iColorName,
				$internalColorImages->iHexcode
			);
		};

		$mapOverview = function ($overview): OverviewProduct {
			return new OverviewProduct(
				$overview->bigGroupId,
				$overview->bigGroupName,
				array_map(
					function ($group): GroupOverviewProduct {
						return new GroupOverviewProduct(
							$group->groupId,
							$group->groupName,
							$group->groupValue,
							array_map(
								function ($detail): DetailOverviewProduct {
									return new DetailOverviewProduct(
										$detail->detailId,
										$detail->detailName,
										$detail->detailValue
									);
								},
								$group->detail
							)
						);
					},
					$overview->group
				),
			);
		};

		$results = $productMD->getList($baseReq);
		if ($results) {
			foreach ($results as $key => $value) {
				$product = new Product(
					$value->id,
					$value->banner,
					null,
					$value->gradeId,
					$value->commercialName,
					$value->slogan,
					$value->img45,
					$value->img90,
					$value->imgDetail,
					$value->description,
					array_map(
						$mapVehicleImage,
						$value->vehicleImages
					),
					array_map(
						$mapInternalColorImage,
						$value->internalColorImages
					),
					array_map(
						$mapOverview,
						$value->overview
					),
				);
				$product->setModelCarByID($value->modelId);
				$productDb = $productMD->getDetail($value->id);
				if ($productDb) {
					$productMD->update($product);
				} else {
					$productMD->create($product);
				}
				array_push($syncedCus, $product);
			}
		}
		unset($results);
		return $syncedCus;
	}



	public function process_ajax_get_total_records_product()
	{
		try {
			$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
			$productMD = new ProductData($connectionData);
			$baseReq = new RequestListBaseModel(null, null, 0, 5);
			$total_records = $productMD->getTotalCount($baseReq);
			$productMD->updateTotalCountProductModel($total_records);
			echo json_encode(array(
				"status" => 1,
				"message" => "Success",
				"data" => $total_records,
			));
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	public function process_ajax_change_size_per_step_product()
	{
		try {
			$result = array();
			$size = isset($_POST['size_per_step']) ? $_POST['size_per_step'] : 0;
			if ($size == 0) {
				$result = array(
					"status" => 0,
					"message" => "Invalid data",
				);
			} else {

				$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
				$productMD = new ProductData($connectionData);
				$productMD->update_size_per_step($size);
				$result = array(
					"status" => 1,
					"message" => "Success",
					"data" => $size,
				);
			}

			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	/**
	 * return response for ajax sync product
	 *
	 * @since    1.0.0
	 */
	public function process_ajax_sync_product()
	{
		try {
			$result = array();
			$step = isset($_POST['step']) ? $_POST['step'] : 0;
			$connectionData = new ConnectionData($this->username, $this->password, $this->tenantId);
			$productMD = new ProductData($connectionData);

			if ($step == 0) {
				$result = array(
					"status" => 0,
					"message" => "Invalid data",
				);
			} else {
				if ($step == 1) {
					$now = new DateTime();
					$productMD->update_start_at($now->format('Y-m-d H:i:s'));
				}
				$size_per_step = $productMD->get_size_per_step();
				$total_records = $productMD->get_total_records();

				$data_synced = $this->sync_product($productMD, $size_per_step, $step, $total_records);

				$total_step = ceil($total_records / $size_per_step);
				if ($step == $total_step) {
					$now = new DateTime();
					$productMD->update_end_at($now->format('Y-m-d H:i:s'));
				}
				$result = array(
					"status" => 1,
					"message" => "Success",
					"data" => [
						"step" => $step + 1,
						"total_step" => $total_step,
						"prods" => $data_synced,
					],
				);
			}

			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode(array(
				"status" => 0,
				"message" => "Error!",
			));
		}

		wp_die();
	}

	public function get_meta_sync_value($meta_key)
	{
		global $wpdb;
		$kq = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sync_toyota_info WHERE `meta_key`='{$meta_key}'");
		if ($kq) return (int)$kq->meta_value;
		return 0;
	}
}
