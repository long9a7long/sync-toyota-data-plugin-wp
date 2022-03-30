<?php
class CatalogueData
{
    private $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getList(RequestListBaseModel $baseReq)
    {
        $curl = new CustomCurl();
        $url = Constants::instance()->getCatalogue();
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        return $res->result->items;
    }

    public function getTotalCount()
    {

        $curl = new CustomCurl();
        $url = Constants::instance()->getCatalogue();
        $baseReq = new RequestListBaseModel(null, null, 0, 1);
        $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
        if ($res) {
            return $res->result->totalCount;
        }
        return 0;
    }

    public function updateTotalCount($total)
    {
        global $wpdb;
        $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_catalogue_sync'");
        if (count($total_record) > 0) {
            $wpdb->update(
                "{$wpdb->prefix}sync_toyota_info",
                array(
                    'meta_value' => $total,
                ),
                array(
                    'meta_key' => 'total_records_catalogue_sync'
                )
            );
        } else {
            $wpdb->insert(
                "{$wpdb->prefix}sync_toyota_info",
                array(
                    'meta_key' => 'total_records_catalogue_sync',
                    'meta_value' => $total,
                )
            );
        }
    }

    public function get_total_records()
    {
        global $wpdb;
        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_catalogue_sync'");
        if ($total_record) {
            return $total_record->meta_value;
        }
        return 0;
    }

    public function create($catalogue)
    {
        if ($catalogue != null) {
            $postIds = $this->getProductIdByModelId($catalogue->modelId);
            if ($postIds != null && $catalogue->url != null) {
                foreach ($postIds as $key => $record) {
                    $postId = $record->post_id;

                    $url = Constants::instance()->getBaseUrl() . $catalogue->url;
                    update_field('catalogue_url', $url, $postId);
                }
            }
        }
    }

    public function get_size_per_step()
    {

        global $wpdb;

        $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='size_per_step_product_sync'");

        if ($total_record) {

            return $total_record->meta_value;
        }

        return 0;
    }

    public function getProductIdByModelId($id)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `meta_key`='modelId' AND `meta_value`={$id}");

        if (count($results) > 0) {
            return $results;
        }
        return null;
    }
}
