<?php
class Constants {
    private static $constants = NULL;
    private $baseUrl = "https://ssa-api.toyotavn.com.vn/";
    private $tokenAuth = "api/TokenAuth/Authenticate";
    private $modelProd ="api/services/app/DataHubApi/GetListModelProduct";
    private $products = "api/services/app/DataHubApi/GetVehicleProductOverview";

    static function instance() {
        if (self::$constants == NULL) {
            self::$constants = new Constants();
        }
        return self::$constants;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getTokenAuth() {
        return $this->baseUrl.$this->tokenAuth;
    }

    public function getModelProd() {
        return $this->baseUrl.$this->modelProd;
    }

    public function getProducts() {
        return $this->baseUrl.$this->products;
    }
}
