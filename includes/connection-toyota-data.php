<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Curl/Curl.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/constants/api-urls.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-curl.php';

use Curl\Curl;
class ConnectionData {

    private $userName;
    
    private $password;

    private $tenantId;

    public function __construct( $userName, $password, $tenantId ) {
        $this->userName = $userName;
        $this->password = $password;
        $this->tenantId = $tenantId;
    }

    public function tokenAuth() {
        $curl = new Curl();
        $curl->setHeader('Abp.TenantId', $this->tenantId);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post(Constants::instance()->getTokenAuth(), [
            'userNameOrEmailAddress' => $this->userName,
            'password' => $this->password,
        ]);

        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            
            $token = $curl->response->result->accessToken;

            $this->saveToken($token);
        }
        $curl->close();
    }

    public function saveToken($token)
    {
        global $wpdb;
        $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='token'");

		if(count($total_record)>0) {

			$wpdb->update(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_value' => $token,

				),

				array(

					'meta_key' => 'token'

				)

			);

		} else {

			$wpdb->insert(

				"{$wpdb->prefix}sync_toyota_info",

				array(

					'meta_key' => 'token',

					'meta_value' => $token,

				)

			);

		}
    }

    public function getToken() {

		global $wpdb;

		$total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='token'");

		if($total_record) {

			return $total_record->meta_value;

		}

		return "";

	}

}
