<?php 

// In DB
abstract class BaseModel {
    abstract public function getDetail($idProd);
    abstract public function create(Object $product);
    abstract public function update(Object $product);
    abstract public function getList(RequestListBaseModel $baseReq);
    abstract public function delete($idProd);
}