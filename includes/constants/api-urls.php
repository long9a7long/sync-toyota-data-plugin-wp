<?php
class Constants
{
    private static $constants = NULL;
    private $baseUrl = "https://ssa-api.toyotavn.com.vn/";
    private $tokenAuth = "api/TokenAuth/Authenticate";
    private $modelProd = "api/services/app/DataHubApi/GetListModelProduct";
    private $products = "api/services/app/DataHubApi/GetVehicleProductOverview";
    private $vehicleProductLibraryImage = "api/services/app/DataHubApi/GetListVehicleProductLibraryImage";
    


    static function instance()
    {
        if (self::$constants == NULL) {
            self::$constants = new Constants();
        }
        return self::$constants;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getTokenAuth()
    {
        return $this->baseUrl . $this->tokenAuth;
    }

    public function getModelProd()
    {
        return $this->baseUrl . $this->modelProd;
    }

    public function getProducts()
    {
        return $this->baseUrl . $this->products;
    }

    public function getVehicleProductLibraryImage()
    {
        return $this->baseUrl . $this->vehicleProductLibraryImage;
    }

    public function downloadAImage($url)
    {
        include_once(ABSPATH . 'wp-admin/includes/image.php');
        $imageurl = $this->baseUrl.$url;
        $imagetype = end(explode('/', getimagesize($imageurl)['mime']));
        $uniq_name = date('dmY') . '' . (int) microtime(true);
        $filename = $uniq_name . '.' . $imagetype;

        $uploaddir = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $filename;
        $contents = file_get_contents($imageurl);
        $savefile = fopen($uploadfile, 'w');
        fwrite($savefile, $contents);
        fclose($savefile);

        $wp_filetype = wp_check_filetype(basename($filename), null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $filename,
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $uploadfile);
        $imagenew = get_post($attach_id);
        $fullsizepath = get_attached_file($imagenew->ID);
        $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }
}
