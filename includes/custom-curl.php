<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Curl/Curl.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/connection-toyota-data.php';

use Curl\Curl;

class CustomCurl {

    public function __construct() {
    }

    public function get($connectionData, $url, $params) {
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorization', 'Bearer '.$connectionData->getToken());
        if($params && count($params)> 0)
            $url = $url.'?'.$this->paramsText($params);
        $curl->get($url);
        
        if ($curl->error) {
            $connectionData->tokenAuth();
            $curl->setHeader('Authorization', 'Bearer '.$connectionData->getToken());
            $curl->get($url);
            if ($curl->error) {
                $curl->close();
                return null;
            } else {
                $curl->close();
                return $curl->response;
            }
        } else {
            $curl->close();
            return $curl->response;
        }
    }

    private function paramsText($params) {
        if(!$params && count($params)== 0) return;
        $reqList = array();
        foreach ($params as $key => $value) {
            array_push($reqList, $key.'='.$value);
        }

        return implode('&', $reqList);
    }
}