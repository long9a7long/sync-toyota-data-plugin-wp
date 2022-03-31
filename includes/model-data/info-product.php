<?php
class InfoProdData
{
    private $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getList(RequestListBaseModel $baseReq, $type)
    {
        $curl = new CustomCurl();
        $url = '';
        switch ($type) {
            case InfoType::$feature:
                $url = Constants::instance()->getFeature();
                break;
            case InfoType::$furniture:
                $url = Constants::instance()->getFurniture();
                break;
            case InfoType::$exterior:
                $url = Constants::instance()->getExterior();
                break;
        }
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        return $res->result->items;
    }

    public function getTotalCount($type)
    {

        $curl = new CustomCurl();
        $url = '';
        switch ($type) {
            case InfoType::$feature:
                $url = Constants::instance()->getFeature();
                break;
            case InfoType::$furniture:
                $url = Constants::instance()->getFurniture();
                break;
            case InfoType::$exterior:
                $url = Constants::instance()->getExterior();
                break;
        }
        $baseReq = new RequestListBaseModel(null, null, 0, 1);
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        if ($res) {
            return $res->result->totalCount;
        }
        return 0;
    }

    public function updateTotalCount($total, $type)
    {
        global $wpdb;

        $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_{$type}_sync'");
        if (count($total_record) > 0) {
            $wpdb->update(
                "{$wpdb->prefix}sync_toyota_info",
                array(
                    'meta_value' => $total,
                ),
                array(
                    'meta_key' => "total_records_{$type}_sync"
                )
            );
        } else {
            $wpdb->insert(
                "{$wpdb->prefix}sync_toyota_info",
                array(
                    'meta_key' => "total_records_{$type}_sync",
                    'meta_value' => $total,
                )
            );
        }
    }

    public function get_total_records($type)
    {
        global $wpdb;
        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_{$type}_sync'");
        if ($total_record) {
            return $total_record->meta_value;
        }
        return 0;
    }

    public function update($data, $type)
    {
        if ($data != null) {
            $postId = $this->getProductIdByGradeId($data->gradeId);
            if ($postId != null) {
                switch ($type) {
                    case InfoType::$feature:
                        $infoId = $data->featureId;
                        break;
                    case InfoType::$furniture:
                        $infoId = $data->furnitureId;
                        break;
                    case InfoType::$exterior:
                        $infoId = $data->exteriorId;
                        break;
                }
                $idRecord = $this->getInfo($postId, $infoId, $type);
                global $wpdb;
                $tableName = $wpdb->prefix . TableName::$informationCar;
                $urlImage = '';
                if($data->url!= null || $data->imageUrl!= null) {
                    $imgUrlData = $data->url;
                    if($type == InfoType::$feature) {
                        $imgUrlData =  $data->imageUrl;
                    }
                    $urlImage = Constants::instance()->getBaseUrl() . $imgUrlData;
                } else {
                    if(count($data->items) >0 ) {
                        $urlImage = Constants::instance()->getBaseUrl() . $data->items[0]->urlWebsite;
                    }
                }

                if ($idRecord != null) {

                    $wpdb->update(
                        $tableName,
                        array(
                            'postId' => $postId,
                            'infoType' => $type,
                            'infoId' => $infoId,
                            'infoType' => $type,
                            'name' => $data->name,
                            'detail' => $data->detail,
                            'url' => $urlImage,
                            'ordering' => $data->ordering,
                            'category' => $data->category,
                        ),
                        array(
                            'id' => $idRecord
                        )
                    );
                } else {
                    $wpdb->insert(
                        $tableName,
                        array(
                            'postId' => $postId,
                            'infoType' => $type,
                            'infoId' => $infoId,
                            'infoType' => $type,
                            'name' => $data->name,
                            'detail' => $data->detail,
                            'url' => $urlImage,
                            'ordering' => $data->ordering,
                            'category' => $data->category,
                        )
                    );
                }
            }
        }
    }

    public function get_size_per_step($type)
    {
        global $wpdb;
        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='size_per_step_product_sync'");
        if ($total_record) {
            return $total_record->meta_value;
        }
        return 0;
    }

    public function getInfo($postId, $infoId, $type)
    {
        global $wpdb;
        $tableName = TableName::$informationCar;
        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}{$tableName}` WHERE `postId`={$postId} AND `infoId`={$infoId} AND `infoType`='{$type}'");
        if (count($total_record) > 0) {
            return $total_record->id;
        }
        return null;
    }

    public function getProductIdByGradeId($gradeId)
    {
        global $wpdb;
        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `meta_key`='gradeId' AND `meta_value`={$gradeId}");
        if (count($total_record) > 0) {
            return $total_record->post_id;
        }
        return null;
    }
}
