<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'entities/base-model.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'constants/api-urls.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'custom-curl.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'constants/table-name.php';

class ProductData
{
  private $connection;
  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function getProdIdFromData($idData) {
    global $wpdb;
    $tableName = "postmeta";
    $kq = $wpdb->get_row(
      $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $tableName . " WHERE meta_key='id_toyota' AND meta_value={$idData}")
    );

    if ($kq) {
      return $kq->post_id;
    }
    return null;
  }

  public function getDetail($idProd)
  {
    global $wpdb;
    $tableName = "postmeta";
    $kq = $wpdb->get_row(
      $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $tableName . " WHERE meta_key='id_toyota' AND meta_value={$idProd}")
    );

    if ($kq) {
      $id = $kq->meta_value;
      $banner = null;
      $modelCar = null;
      $gradeId = null;
      $commercialName = null;
      $slogan = null;
      $img45 = null;
      $img90 = null;
      $imgDetail = null;
      $description = null;
      $vehicleImages = null;
      $internalColorImages = null;
      $overview = null;
      return new Product($id, $banner, $modelCar, $gradeId, $commercialName, $slogan, $img45, $img90, $imgDetail, $description, $vehicleImages, $internalColorImages, $overview);
    }

    return null;
  }

  public function getList(RequestListBaseModel $baseReq)
  {
    $curl = new CustomCurl();
    $url = Constants::instance()->getProducts();
    $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
    return $res->result->items;
  }

  public function getListImage(RequestListBaseModel $baseReq)
  {
    $curl = new CustomCurl();
    $url = Constants::instance()->getVehicleProductLibraryImage();
    $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
    return $res->result->items;
  }

  public function getProductIdByGradeId($gradeId)
  {
    global $wpdb;

    $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `meta_key`='gradeId' AND `meta_value`={$gradeId}");

    if ($total_record) {
      return $total_record->post_id;
    }
    return null;
  }

  public function createGalleryProduct($image)
  {
    $postId = $this->getProductIdByGradeId($image->gradeId);
    if ($postId != null) {
      $checkIsExisted = false;
      $current_thu_vien_text = get_post_meta($postId, 'thu_vien_anh_ngoai_that_toyota');
      if ($current_thu_vien_text) {
        $current_thu_vien = json_decode($current_thu_vien_text[0]);
        if(count($current_thu_vien)>0) {
          for ($i = 0; $i < count($current_thu_vien); $i++) {

            if ($current_thu_vien[$i] != NULL && (($current_thu_vien[$i]->url) == ($image->url))) {
              $checkIsExisted = true;
              break;
            }
          }
        }
        
      }
      if ($checkIsExisted == false) { // Chua ton tai
        $value = get_field("thu_vien_anh_ngoai_that", $postId);
        
        $imageId = Constants::instance()->downloadAImage($image->url);
        $oldVal = array();
        if($value && is_array($value) && count($value)> 0)
          foreach ($value as $key => $val) {
            array_push($oldVal, $val['id']);
          }
        if (count($oldVal) > 0) {
          array_push($oldVal, $imageId);
          $newVal = $oldVal;
        }
        else $newVal = [$imageId];
        update_field("thu_vien_anh_ngoai_that", $newVal, $postId);
        $imgArr = array(
          array(
            'libraryId' => $image->libraryId,
            'url' => $image->url,
          )
        );

        if ($current_thu_vien_text) {
          $current_thu_vien = json_decode($current_thu_vien_text[0]);
          foreach ($current_thu_vien as $key => $value) {
            array_push($imgArr, $value);
          }
          
        }
        update_post_meta($postId, 'thu_vien_anh_ngoai_that_toyota', json_encode($imgArr));
      }

    }
  }

  public function updateGalleryProduct($image)
  {
  }

  public function getTotalCount()
  {

    $curl = new CustomCurl();
    $url = Constants::instance()->getProducts();
    $baseReq = new RequestListBaseModel(null, null, 0, 1);
    $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
    if ($res) {
      return $res->result->totalCount;
    }
    return 0;
  }

  public function getTotalCountGalleryProduct()
  {

    $curl = new CustomCurl();
    $url = Constants::instance()->getVehicleProductLibraryImage();
    $baseReq = new RequestListBaseModel(null, null, 0, 1);
    $res = $curl->get($this->connection, $url, $baseReq->getParamsModel());
    if ($res) {
      return $res->result->totalCount;
    }
    return 0;
  }

  public function updateTotalCountProductModel($total)
  {
    global $wpdb;
    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_prod_sync'");
    if (count($total_record) > 0) {
      $wpdb->update(
        "{$wpdb->prefix}sync_toyota_info",
        array(
          'meta_value' => $total,
        ),
        array(
          'meta_key' => 'total_records_prod_sync'
        )
      );
    } else {
      $wpdb->insert(
        "{$wpdb->prefix}sync_toyota_info",
        array(
          'meta_key' => 'total_records_prod_sync',
          'meta_value' => $total,
        )
      );
    }
  }

  public function updateTotalCountGalleryProd($total)
  {
    global $wpdb;
    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_gallery_prod_sync'");
    if (count($total_record) > 0) {
      $wpdb->update(
        "{$wpdb->prefix}sync_toyota_info",
        array(
          'meta_value' => $total,
        ),
        array(
          'meta_key' => 'total_records_gallery_prod_sync'
        )
      );
    } else {
      $wpdb->insert(
        "{$wpdb->prefix}sync_toyota_info",
        array(
          'meta_key' => 'total_records_gallery_prod_sync',
          'meta_value' => $total,
        )
      );
    }
  }

  public function update_size_per_step($size)
  {

    global $wpdb;

    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='size_per_step_product_sync'");

    if (count($total_record) > 0) {

      $wpdb->update(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_value' => $size,

        ),

        array(

          'meta_key' => 'size_per_step_product_sync'

        )

      );
    } else {

      $wpdb->insert(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_key' => 'size_per_step_product_sync',

          'meta_value' => $size,

        )

      );
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

  public function update_start_at($time)
  {

    global $wpdb;

    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='start_at_product_sync'");

    if (count($total_record) > 0) {

      $wpdb->update(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_value' => $time,

        ),

        array(

          'meta_key' => 'start_at_product_sync'

        )

      );
    } else {

      $wpdb->insert(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_key' => 'start_at_product_sync',

          'meta_value' => $time,

        )

      );
    }
  }

  public function update_end_at($time)
  {

    global $wpdb;

    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='end_at_product_sync'");

    if (count($total_record) > 0) {

      $wpdb->update(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_value' => $time,

        ),

        array(

          'meta_key' => 'end_at_product_sync'

        )

      );
    } else {

      $wpdb->insert(

        "{$wpdb->prefix}sync_toyota_info",

        array(

          'meta_key' => 'end_at_product_sync',

          'meta_value' => $time,

        )

      );
    }
  }

  public function get_total_records()
  {
    global $wpdb;
    $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_prod_sync'");
    if ($total_record) {
      return $total_record->meta_value;
    }
    return 0;
  }

  public function get_total_records_gallery_prod()
  {
    global $wpdb;
    $total_record = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}sync_toyota_info` WHERE `meta_key`='total_records_gallery_prod_sync'");
    if ($total_record) {
      return $total_record->meta_value;
    }
    return 0;
  }

  public function create($product)
  {
    $my_post = array(
      'post_title'    => wp_strip_all_tags($product->commercialName),
      'post_content'  => $product->description,
      'post_status'   => 'publish',
      'post_type'     => 'xe',
    );

    $dongXe = $this->getDongXe($product);
    $metaArr = array(
      'id_toyota' => $product->id,
      'mau_car_chinh' => 0,
      'xe_cu'         => 0,
      'header_text'   => $product->slogan,
    );
    $metaInputArr = array();


    if ($product->modelCar != null) {
      $metaArr['kieu_xe'] = strtolower($product->modelCar->getModelName());
      $metaInputArr['modelId'] = $product->modelCar->getModelId();
    }

    $metaArr['gia_tham_khao'] = $product->vehicleImages[0] ? $product->vehicleImages[0]->price : 0;

    $metaArr['anh_cover'] = Constants::instance()->downloadAImage($product->banner);
    $metaInputArr['anh_cover'] = $product->banner;
    $metaArr['anh_gioi_thieu'] = Constants::instance()->downloadAImage($product->imgDetail);
    $metaInputArr['anh_gioi_thieu'] = $product->imgDetail;
    $metaArr['anh_dai_dien_2'] = Constants::instance()->downloadAImage($product->img45);
    $metaInputArr['anh_dai_dien_2'] = $product->img45;

    $metaArr = array_merge($metaArr, $this->metaProdValues($product->overview));
    $metaInputArr['anh_dai_dien'] = $product->img90;
    $metaInputArr['gradeId'] = $product->gradeId;

    $my_post['meta_input'] = $metaInputArr;



    // Insert the post into the database
    $postID = wp_insert_post($my_post);

    $attachment_id = Constants::instance()->downloadAImage($product->img90);

    set_post_thumbnail($postID, $attachment_id);

    if ($dongXe && $dongXe != null) {
      // $my_post['taxonomies'] = array($dongXe['cateName']);
      wp_set_post_terms($postID, array($dongXe['cateId']), 'dong_xe');
    }

    if ($product->modelCar != null) {
      
      $listMX = $this->getListMauXe($metaInputArr['modelId']);
      $mxList = array();
      foreach ($listMX as $key => $mau) {
        array_push($mxList, $mau->post_id);
      }

      update_field('cac_mau_xe', $mxList, $postID);
    }
    $this->updateMetaField($metaArr, $postID);

    foreach ($product->vehicleImages as $key => $value) {
      $row = array(
        'mau' => $value->hexcode,
        'ten_mau'   => $value->colorName,
        'anh_xe'  => Constants::instance()->downloadAImage($value->imageUrl),
        'model_price' => $value->price,
      );

      add_row('car_color_ngoai_that', $row, $postID);
    }
    foreach ($product->internalColorImages as $key => $value) {
      $row = array(
        'mau' => $value->iHexcode,
        'ten_mau'   => $value->iColorName,
        'anh_xe'  => Constants::instance()->downloadAImage($value->imageUrl),
        // 'model_price' => $metaArr['gia_tham_khao'],
      );

      add_row('car_color_noi_that', $row, $postID);
    }
  }

  public function getListMauXe($modelId) {
    global $wpdb;

    $total_record = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `meta_key`='modelId' AND `meta_value`={$modelId}");

    if (count($total_record) > 0) {
      return $total_record;
    }
    return null;
  }

  public function updateMetaField($metaArr, $postId)
  {
    foreach ($metaArr as $key => $value) {
      update_field($key, $value, $postId);
    }
  }

  public function metaProdValues($overview)
  {
    $metaArr = array();
    foreach ($overview as $key => $value) {
      switch (trim($value->bigGroupName)) {
        case "?????NG C?? & KHUNG XE": // ?????NG C?? & KHUNG XE
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "K??ch th?????c": // K??ch th?????c
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "K??ch th?????c t???ng th??? b??n ngo??i (D x R x C) (mm x mm x mm)": // K??ch th?????c t???ng th??? b??n ngo??i
                      $metaArr['dong_co_khung_xe_kich_thuoc-kich-thuoc-ben-ngoai'] = $valDetail->detailValue;
                      break;
                    case "K??ch th?????c t???ng th??? b??n trong (D x R x C) (mm x mm x mm)": // K??ch th?????c t???ng th??? b??n trong
                      $metaArr['dong_co_khung_xe-kich_thuoc-kich_thuoc_ben_trong'] = $valDetail->detailValue;
                      break;
                    case "Chi???u d??i c?? s??? (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-chieu-dai-co-so'] = $valDetail->detailValue;
                      break;
                    case "Chi???u r???ng c?? s??? (Tr?????c/Sau) (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-chieu-rong-co-so'] = $valDetail->detailValue;
                      break;
                    case "Kho???ng s??ng g???m xe (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-khoang-sang-gam-xe'] = $valDetail->detailValue;
                      break;
                    case "G??c tho??t (Tr?????c/Sau) (?????)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-goc-thoat'] = $valDetail->detailValue;
                      break;
                    case "B??n k??nh v??ng quay t???i thi???u (m)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-ban-kinh-vong-quay-toi-thieu'] = $valDetail->detailValue;
                      break;
                    case "Tr???ng l?????ng kh??ng t???i (kg)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-trong-luong-khong-tai'] = $valDetail->detailValue;
                      break;
                    case "Tr???ng l?????ng to??n t???i (kg)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-trong-luong-toan-tai'] = $valDetail->detailValue;
                      break;
                    case "Dung t??ch b??nh nhi??n li???u (L)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-dung-tich-binh-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Dung t??ch khoang h??nh l?? (L)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-dung-tich-khoang-hanh-ly'] = $valDetail->detailValue;
                      break;
                    case "K??ch th?????c khoang ch??? h??ng (D x R x C) (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-kich-thuoc-khoang-cho-hang'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "?????ng c?? th?????ng": // ?????ng c?? th?????ng
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Lo???i ?????ng c??":
                      $metaArr['loai_dong_co'] = $valDetail->detailValue;
                      $metaArr['dong_co_khung_xe-dong_co-loai-dong-co'] = $valDetail->detailValue;
                      break;
                    case "S??? xy lanh":
                      $metaArr['dong_co_khung_xe-dong_co-so-xy-lanh'] = $valDetail->detailValue;
                      break;
                    case "B??? tr?? xy lanh":
                      $metaArr['dong_co_khung_xe-dong_bo-tri-xy-lanh'] = $valDetail->detailValue;
                      break;
                    case "Dung t??ch xy lanh":
                      $metaArr['dong_co_khung_xe-dong_dung-tich-xy-lanh'] = $valDetail->detailValue;
                      $metaArr['dung_tich_dong_co'] = $valDetail->detailValue;
                      break;
                    case "T??? s??? n??n":
                      $metaArr['dong_co_khung_xe-dong_ty-so-nen'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng nhi??n li???u":
                      $metaArr['dong_co_khung_xe-dong-co-he-thong-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Lo???i nhi??n li???u":
                      $metaArr['dong_co_khung_xe-dong-co-loai-nhien-lieu'] = $valDetail->detailValue;
                      $metaArr['nhien_lieu'] = $valDetail->detailValue;
                      break;
                    case "C??ng su???t t???i ??a":
                      $metaArr['dong_co_khung_xe-dong-co-cong-suat-toi-da'] = $valDetail->detailValue;
                      break;
                    case "M?? men xo???n t???i ??a":
                      $metaArr['dong_co_khung_xe-dong-co-mo-men-xoan-toi-da'] = $valDetail->detailValue;
                      break;
                    case "T???c ????? t???i ??a":
                      $metaArr['dong_co_khung_xe-dong-co-toc-do-toi-da'] = $valDetail->detailValue;
                      break;
                    case "Kh??? n??ng t??ng t???c":
                      $metaArr['dong_co_khung_xe-dong-co-kha-nang-tang-toc'] = $valDetail->detailValue;
                      break;
                    case "H??? s??? c???n kh??ng kh??":
                      $metaArr['dong_co_khung_xe-dong-co-he-so-can-khong-khi'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng ng???t/m??? ?????ng c?? t??? ?????ng":
                      $metaArr['dong_co_khung_xe-dong-co-he-so-ngat-mo-dong-co-tu-dong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "?????ng c?? ??i???n": // ?????ng c?? dien
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                }
                break;
              case "C??c ch??? ????? l??i":
                $metaArr['dong_co_khung_xe-cac-che-do-lai'] = $valGroup->groupValue;
                break;
              case "H??? th???ng truy???n ?????ng": // H??? th???ng truy???n ?????ng
                $metaArr['dong_co_khung_xe-he-thong-truyen-dong'] = $valGroup->groupValue;
                break;
              case "H???p s???": // H???p s???
                $metaArr['dong_co_khung_xe-hop-so'] = $valGroup->groupValue;
                $metaArr['thong_tin_khac'] = $valGroup->groupValue;
                break;
              case "H??? th???ng treo": // H??? th???ng treo
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr?????c":
                      $metaArr['dong_co_khung_xe-he-thong-treo-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['dong_co_khung_xe-he-thong-treo-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "H??? th???ng l??i": // H??? th???ng l??i
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr??? l???c tay l??i":
                      $metaArr['dong_co_khung_xe-he-thong-lai-tro-luc-tay-lai'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng tay l??i t??? s??? truy???n bi???n thi??n (VGRS)":
                      $metaArr['dong_co_khung_xe-he-thong-lai-he-thong-tay-lai-ti-so-truyen-bien-thien'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "V??nh & l???p xe": // V??nh & l???p xe
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Lo???i v??nh":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-loai-vanh'] = $valDetail->detailValue;
                      break;
                    case "K??ch th?????c l???p":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-kich-thuoc-lop'] = $valDetail->detailValue;
                      break;
                    case "L???p d??? ph??ng":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-lop-du-phong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Phanh": // Phanh
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr?????c":
                      $metaArr['dong_co_khung_xe-phanh-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['dong_co_khung_xe-phanh-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Ti??u chu???n kh?? th???i": // Ti??u chu???n kh?? th???i
                $metaArr['dong_co_khung_xe-tieu-chuan-khi-thai'] = $valGroup->groupValue;
                break;

              case "Ti??u th??? nhi??n li???u (L/100km)": // Ti??u th??? nhi??n li???u
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Ngo??i ???? th???":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-ngoai-do-thi'] = $valDetail->detailValue;
                      break;
                    case "K???t h???p":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-ket-hop'] = $valDetail->detailValue;
                      break;
                    case "Trong ???? th???":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-trong-do-thi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "NGO???I TH???T": // NGO???I TH???T
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "C???m ????n tr?????c": // C???m ????n tr?????c
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "????n chi???u g???n":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-gan'] = $valDetail->detailValue;
                      break;
                    case "????n chi???u xa":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-xa'] = $valDetail->detailValue;
                      break;
                    case "????n chi???u s??ng ban ng??y":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-ban-ngay'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng r???a ????n":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-rua-den'] = $valDetail->detailValue;
                      break;
                    case "T??? ?????ng B???t/T???t":
                      $metaArr['ngoai-that-cum-den-truoc-tu-dong-bat-tat'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng nh???c nh??? ????n s??ng":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-nhac-nho-den-sang'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng m??? r???ng g??c chi???u t??? ?????ng":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-mo-rong-goc-chieu-tu-dong'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng c??n b???ng g??c chi???u":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-can-bang-goc-chieu'] = $valDetail->detailValue;
                      break;
                    case "Ch??? ????? ????n ch??? d???n ???????ng":
                      $metaArr['ngoai-that-cum-den-truoc-che-do-den-cho-dan-duong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "C???m ????n sau": // C???m ????n sau
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "????n v??? tr??":
                      $metaArr['ngoai-that-cum-den-truoc-den-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "????n phanh":
                      $metaArr['ngoai-that-cum-den-truoc-den-phanh'] = $valDetail->detailValue;
                      break;
                    case "????n b??o r???":
                      $metaArr['ngoai-that-cum-den-truoc-den-bao-re'] = $valDetail->detailValue;
                      break;
                    case "????n l??i":
                      $metaArr['ngoai-that-cum-den-truoc-den-lui'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "????n b??o phanh tr??n cao (????n phanh th??? ba)": // ????n b??o phanh tr??n cao
                $metaArr['ngoai-that-den-bao-phanh-tren-cao'] = $valGroup->groupValue;
                break;

              case "????n s????ng m??": // ????n s????ng m??
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr?????c":
                      $metaArr['ngoai-that-den-suong-mu-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-den-suong-mu-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "G????ng chi???u h???u ngo??i": // G????ng chi???u h???u ngo??i
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Ch???c n??ng ??i???u ch???nh ??i???n":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-dieu-chinh-dien'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng g???p ??i???n":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-gap-dien'] = $valDetail->detailValue;
                      break;
                    case "T??ch h???p ????n b??o r???":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-tich-hop-den-bao-re'] = $valDetail->detailValue;
                      break;
                    case "T??ch h???p ????n ch??o m???ng":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-tich-hop-den-chao-mung'] = $valDetail->detailValue;
                      break;
                    case "M??u":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-mau'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng t??? ??i???u ch???nh khi l??i":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-tu-dieu-chinh-khi-lui'] = $valDetail->detailValue;
                      break;
                    case "B??? nh??? v??? tr??":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-bo-nho-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng s???y g????ng":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-say-guong'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng ch???ng b??m n?????c":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-chong-bam-nuoc'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng ch???ng ch??i t??? ?????ng":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-chong-choi-tu-dong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "G???t m??a": // G???t m??a
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr?????c":
                      $metaArr['ngoai-that-gat-mua-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-gat-mua-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Ch???c n??ng s???y k??nh sau": // Ch???c n??ng s???y k??nh sau
                $metaArr['ngoai-that-chuc-nang-say-kinh-sau'] = $valGroup->groupValue;
                break;

              case "??ng ten": // ??ng ten
                $metaArr['ngoai-that-ang-ten'] = $valGroup->groupValue;
                break;

              case "Tay n???m c???a ngo??i xe": // Tay n???m c???a ngo??i xe
                $metaArr['ngoai-that-tay-nam-cua-ngoai-xe'] = $valGroup->groupValue;
                break;

              case "B??? qu??y xe th??? thao": // B??? qu??y xe th??? thao
                $metaArr['ngoai-that-bo-quay-xe-the-thao'] = $valGroup->groupValue;
                break;

              case "Thanh c???n (gi???m va ch???m)": // Thanh c???n
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Tr?????c":
                      $metaArr['ngoai-that-gat-mua-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-gat-mua-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "L?????i t???n nhi???t": // L?????i t???n nhi???t
                $metaArr['ngoai-that-luon-tan-nhiet'] = $valGroup->groupValue;
                break;

              case "Ch???n b??n": // Ch???n b??n
                $metaArr['ngoai-that-chan-bun'] = $valGroup->groupValue;
                break;

              case "Ch???n b??n b??n": // Ch???n b??n b??n
                $metaArr['ngoai-that-chan-bun-ben'] = $valGroup->groupValue;
                break;

              case "???ng x??? k??p": // ???ng x??? k??p
                $metaArr['ngoai-that-ong-xa-kep'] = $valGroup->groupValue;
                break;

              case "C??nh h?????ng gi??": // C??nh h?????ng gi??
                $metaArr['ngoai-that-canh-huong-gio'] = $valGroup->groupValue;
                break;

              case "Thanh ????? n??c xe": // Thanh ????? n??c xe
                $metaArr['ngoai-that-thanh-do-noc-xe'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "N???I TH???T": // N???I TH???T
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Tay l??i": // Tay l??i
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Lo???i tay l??i":
                      $metaArr['noi-that-tay-lai-loai-tay-lai'] = $valDetail->detailValue;
                      break;
                    case "Ch???t li???u":
                      $metaArr['noi-that-tay-lai-chat-lieu'] = $valDetail->detailValue;
                      break;
                    case "N??t b???m ??i???u khi???n t??ch h???p":
                      $metaArr['noi-that-tay-lai-nut-bam-dieu-khien-tich-hop'] = $valDetail->detailValue;
                      break;
                    case "??i???u ch???nh":
                      $metaArr['noi-that-tay-lai-dieu-chinh'] = $valDetail->detailValue;
                      break;
                    case "L???y chuy???n s???":
                      $metaArr['noi-that-tay-lai-lay-chuyen-so'] = $valDetail->detailValue;
                      break;
                    case "B??? nh??? v??? tr??":
                      $metaArr['noi-that-tay-lai-bo-chuyen-vi-tri'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "G????ng chi???u h???u trong": // G????ng chi???u h???u trong
                $metaArr['noi-that-guong-chieu-hau-trong'] = $valGroup->groupValue;
                break;

              case "Tay n???m c???a trong xe": // Tay n???m c???a trong xe
                $metaArr['noi-that-tay-nam-cua-trong-xe'] = $valGroup->groupValue;
                break;

              case "C???m ?????ng h???": // C???m ?????ng h???
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Lo???i ?????ng h???":
                      $metaArr['noi-that-cum-dong-ho-loai-dong-ho'] = $valDetail->detailValue;
                      break;
                    case "????n b??o h??? th???ng Hybrid":
                      $metaArr['noi-that-cum-dong-ho-den-bao-he-thong-hybrid'] = $valDetail->detailValue;
                      break;
                    case "????n b??o ch??? ????? Eco":
                      $metaArr['noi-that-cum-dong-ho-den-bao-che-do-eco'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng b??o l?????ng ti??u th??? nhi??n li???u":
                      $metaArr['noi-that-cum-dong-ho-chuc-nang-bao-luong-tieu-thu-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng b??o v??? tr?? c???n s???":
                      $metaArr['noi-that-cum-dong-ho-chuc-nang-bao-vi-tri-can-so'] = $valDetail->detailValue;
                      break;
                    case "M??n h??nh hi???n th??? ??a th??ng tin":
                      $metaArr['noi-that-cum-dong-ho-man-hinh-hien-thi-da-thong-tin'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "M??n h??nh hi???n th??? ??a th??ng tin": // C???a s??? tr???i
                $metaArr['noi-that-cua-so-troi'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "GH???": // GH???
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Ch???t li???u b???c gh???": // Ch???t li???u b???c gh???
                $metaArr['ghe-chat-lieu-boc-ghe'] = $valGroup->groupValue;
                break;

              case "Gh??? tr?????c": // Gh??? tr?????c
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Lo???i gh???":
                      $metaArr['ghe-ghe-truoc-loai-ghe'] = $valDetail->detailValue;
                      break;
                    case "??i???u ch???nh gh??? l??i":
                      $metaArr['ghe-ghe-truoc-dieu-chinh-ghe-lai'] = $valDetail->detailValue;
                      break;
                    case "??i???u ch???nh gh??? h??nh kh??ch":
                      $metaArr['ghe-ghe-truoc-dieu-chinh-ghe-hanh-khach'] = $valDetail->detailValue;
                      break;
                    case "B??? nh??? v??? tr??":
                      $metaArr['ghe-ghe-truoc-bo-nho-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng th??ng gi??":
                      $metaArr['ghe-ghe-truoc-chuc-nang-thong-gio'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng s?????i":
                      $metaArr['ghe-ghe-truoc-chuc-nang-suoi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Gh??? sau": // Gh??? sau
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "H??ng gh??? th??? hai":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-hai'] = $valDetail->detailValue;
                      break;
                    case "H??ng gh??? th??? ba":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-ba'] = $valDetail->detailValue;
                      break;
                    case "H??ng gh??? th??? b???n":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-tu'] = $valDetail->detailValue;
                      break;
                    case "H??ng gh??? th??? n??m":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-nam'] = $valDetail->detailValue;
                      break;
                    case "T???a tay h??ng gh??? sau":
                      $metaArr['ghe-ghe-sau-tua-tay-hang-ghe-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "TI???N NGHI": // TI???N NGHI
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "R??m che n???ng k??nh sau": // R??m che n???ng k??nh sau
                $metaArr['tien-nghi-rem-che-nang-kinh-sau'] = $valGroup->groupValue;
                break;
              case "R??m che n???ng c???a sau": // R??m che n???ng c???a sau
                // $metaArr['tien-nghi-rem-che-nang-kinh-sau'] = $valGroup->groupValue;
                break;
              case "H??? th???ng ??i???u h??a": // H??? th???ng ??i???u h??a
                $metaArr['tien-nghi-he-thong-dieu-hoa'] = $valGroup->groupValue;
                break;
              case "C???a gi?? sau": // C???a gi?? sau
                $metaArr['tien-nghi-cua-gio-sau'] = $valGroup->groupValue;
                break;
              case "H???p l??m m??t": // H???p l??m m??t
                $metaArr['tien-nghi-hop-lam-mat'] = $valGroup->groupValue;
                break;
              case "H??? th???ng ??m thanh": // H??? th???ng ??m thanh
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "?????u ????a":
                      $metaArr['tien-nghi-he-thong-am-thanh-dau-dia'] = $valDetail->detailValue;
                      break;
                    case "S??? loa":
                      $metaArr['tien-nghi-he-thong-am-thanh-so-loa'] = $valDetail->detailValue;
                      break;
                    case "C???ng k???t n???i AUX":
                      $metaArr['tien-nghi-he-thong-am-thanh-cong-ket-noi-aux'] = $valDetail->detailValue;
                      break;
                    case "C???ng k???t n???i USB":
                      $metaArr['tien-nghi-he-thong-am-thanh-cong-ket-noi-usb'] = $valDetail->detailValue;
                      break;
                    case "K???t n???i Bluetooth":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-bluetooth'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng ??i???u khi???n b???ng gi???ng n??i":
                      $metaArr['tien-nghi-he-thong-am-thanh-he-thong-dieu-khien-bang-giong-noi'] = $valDetail->detailValue;
                      break;
                    case "Ch???c n??ng ??i???u khi???n t??? h??ng gh??? sau":
                      $metaArr['tien-nghi-he-thong-am-thanh-chuc-nang-dieu-khien-tu-hang-ghe-sau'] = $valDetail->detailValue;
                      break;
                    case "K???t n???i wifi":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-wifi'] = $valDetail->detailValue;
                      break;
                    case "H??? th???ng ????m tho???i r???nh tay":
                      $metaArr['tien-nghi-he-thong-am-thanh-he-thong-dam-thoai-ranh-tay'] = $valDetail->detailValue;
                      break;
                    case "K???t n???i ??i???n tho???i th??ng minh":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-dien-thoai-thong-minh'] = $valDetail->detailValue;
                      break;
                    case "K???t n???i HDMI":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-hdmi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Ch??a kh??a th??ng minh & kh???i ?????ng b???ng n??t b???m": // Ch??a kh??a th??ng minh & kh???i ?????ng b???ng n??t b???m
                $metaArr['tien-nghi-chia-khoa-thong-minh-khoi-dong-bang-nut-bam'] = $valGroup->groupValue;
                break;

              case "Phanh tay ??i???n t???": // Phanh tay ??i???n t???
                $metaArr['tien-nghi-phanh-tay-dien-tu'] = $valGroup->groupValue;
                break;

              case "Gi??? phanh ??i???n t???": // Gi??? phanh ??i???n t???
                $metaArr['tien-nghi-giu-phanh-dien-tu'] = $valGroup->groupValue;
                break;

              case "H??? th???ng d???n ???????ng": // H??? th???ng d???n ???????ng
                $metaArr['tien-nghi-he-thong-dan-duong'] = $valGroup->groupValue;
                break;

              case "Hi???n th??? th??ng tin tr??n k??nh l??i": // Hi???n th??? th??ng tin tr??n k??nh l??i
                $metaArr['tien-nghi-he-thong-thong-tin-tren-kinh-lai'] = $valGroup->groupValue;
                break;

              case "Kh??a c???a ??i???n": // Kh??a c???a ??i???n
                $metaArr['tien-nghi-khoa-cua-dien'] = $valGroup->groupValue;
                break;

              case "Ch???c n??ng kh??a c???a t??? xa": // Ch???c n??ng kh??a c???a t??? xa
                $metaArr['tien-nghi-chuc-nang-khoa-cua-tu-xa'] = $valGroup->groupValue;
                break;

              case "C???a s??? ??i???u ch???nh ??i???n": // C???a s??? ??i???u ch???nh ??i???n
                $metaArr['tien-nghi-cua-so-dieu-chinh-dien'] = $valGroup->groupValue;
                break;

              case "C???p ??i???u khi???n ??i???n": // C???p ??i???u khi???n ??i???n
                $metaArr['tien-nghi-cop-dieu-chinh-dien'] = $valGroup->groupValue;
                break;

              case "H??? th???ng s???c kh??ng d??y": // H??? th???ng s???c kh??ng d??y
                $metaArr['tien-nghi-he-thong-sac-khong-day'] = $valGroup->groupValue;
                break;

              case "Ga t??? ?????ng": // Ga t??? ?????ng
                $metaArr['tien-nghi-ga-tu-dong'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "AN NINH/H??? TH???NG CH???NG TR???M": // AN NINH/H??? TH???NG CH???NG TR???M
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "H??? th???ng b??o ?????ng": // H??? th???ng b??o ?????ng
                $metaArr['an-ninh-he-thong-chong-trom-he-thong-bao-dong'] = $valGroup->groupValue;
                break;

              case "H??? th???ng m?? h??a kh??a ?????ng c??": // H??? th???ng b??o ?????ng
                $metaArr['an-ninh-he-thong-chong-trom-he-thong-ma-hoa-dong-co'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "AN TO??N CH??? ?????NG": // AN TO??N CH??? ?????NG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "H??? th???ng an to??n Toyota safety sense": // H??? th???ng an to??n Toyota safety sense
                // $metaArr['an-ninh-chu-dong-he-thong-an-toan-toyota-safety-sense-canh-bao-tien-va-cham'] = $valGroup->groupValue;
                break;

              case "H??? th???ng ch???ng b?? c???ng phanh": // H??? th???ng ch???ng b?? c???ng phanh
                $metaArr['an-ninh-chu-dong-he-thong-chong-bo-cung-phanh'] = $valGroup->groupValue;
                break;

              case "H??? th???ng h??? tr??? l???c phanh kh???n c???p": // H??? th???ng h??? tr??? l???c phanh kh???n c???p
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-luc=phanh-khan-cap'] = $valGroup->groupValue;
                break;

              case "H??? th???ng ph??n ph???i l???c phanh ??i???n t???": // H??? th???ng ph??n ph???i l???c phanh ??i???n t???
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-luc=phanh-dien-tu'] = $valGroup->groupValue;
                break;

              case "H??? th???ng c??n b???ng ??i???n t???":
                $metaArr['an-ninh-chu-dong-he-thong-can-bang-dien-tu'] = $valGroup->groupValue;
                break;

              case "H??? th???ng ki???m so??t l???c k??o":
                $metaArr['an-ninh-chu-dong-he-thong-kiem-soat-luc-keo'] = $valGroup->groupValue;
                break;

              case "H??? th???ng h??? tr??? kh???i h??nh ngang d???c":
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-khoi-hanh-ngang-doc'] = $valGroup->groupValue;
                break;

              case "H??? th???ng h??? tr??? ????? ????o":
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-do-deo'] = $valGroup->groupValue;
                break;

              case "H??? th???ng c???nh b??o ??i???m m??":
                $metaArr['an-ninh-chu-dong-he-thong-canh-bao-diem-mu'] = $valGroup->groupValue;
                break;

              case "H??? th???ng l???a ch???n v???n t???c v?????t ?????a h??nh":
                $metaArr['an-ninh-chu-dong-he-thong-lua-chon-van-toc-vuot-dia-hinh'] = $valGroup->groupValue;
                break;

              case "H??? th???ng th??ch nghi ?????a h??nh":
                $metaArr['an-ninh-chu-dong-he-thong-thich-nghi-dia-hinh'] = $valGroup->groupValue;
                break;

              case "????n b??o phanh kh???n c???p":
                $metaArr['an-ninh-chu-dong-den-bao-phanh-khan-cap'] = $valGroup->groupValue;
                break;

              case "H??? th???ng theo d??i ??p su???t l???p":
                $metaArr['an-ninh-chu-dong-he-thong-theo-doi-ap-suat-lop'] = $valGroup->groupValue;
                break;

              case "Camera l??i":
                $metaArr['an-ninh-chu-dong-camera-lui'] = $valGroup->groupValue;
                break;

              case "Camera 360 ?????":
                $metaArr['an-ninh-chu-dong-camera-360-do'] = $valGroup->groupValue;
                break;

              case "C???m bi???n h??? tr??? ????? xe": // C???m bi???n h??? tr??? ????? xe
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Sau":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-sau'] = $valDetail->detailValue;
                      break;
                    case "Tr?????c":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-truoc'] = $valDetail->detailValue;
                      break;
                    case "G??c tr?????c":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-goc-truoc'] = $valDetail->detailValue;
                      break;
                    case "G??c sau":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-goc-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "AN TO??N B??? ?????NG": // AN TO??N B??? ?????NG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "T??i kh??": // T??i kh??
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "T??i kh?? ng?????i l??i & h??nh kh??ch ph??a tr?????c":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-nguoi-lai-hanh-khach-phia-truoc'] = $valDetail->detailValue;
                      break;
                    case "T??i kh?? b??n h??ng ph??a tr?????c":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-ben-hong-phia-truoc'] = $valDetail->detailValue;
                      break;
                    case "T??i kh?? r??m":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-r??m'] = $valDetail->detailValue;
                      break;
                    case "T??i kh?? b??n h??ng ph??a sau":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-ben-hong-phia-sau'] = $valDetail->detailValue;
                      break;
                    case "T??i kh?? ?????u g???i ng?????i l??i":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-dau-goi-nguoi-lai'] = $valDetail->detailValue;
                      break;
                    case "T??i kh?? ?????u g???i h??nh kh??ch":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-dau-goi-hanh-khach'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Khung xe GOA": // Khung xe GOA
                $metaArr['an-ninh-bi-dong-khung-xe-goa'] = $valGroup->groupValue;
                break;

              case "D??y ??ai an to??n": // D??y ??ai an to??n
                $metaArr['an-ninh-bi-dong-day-dai-an-toan'] = $valGroup->groupValue;
                break;

              case "Gh??? c?? c???u tr??c gi???m ch???n th????ng c??? (T???a ?????u gi???m ch???n)": // Gh??? c?? c???u tr??c gi???m ch???n th????ng c???
                $metaArr['an-ninh-bi-dong-ghe-co-cau-truc-giam-chan-thuong-co'] = $valGroup->groupValue;
                break;

              case "C???t l??i t??? ?????": // C???t l??i t??? ?????
                $metaArr['an-ninh-bi-dong-cot-lai-tu-do'] = $valGroup->groupValue;
                break;

              case "Kh??a an to??n tr??? em": // Kh??a an to??n tr??? em
                $metaArr['an-ninh-bi-dong-khoa-an-toan-tre-em'] = $valGroup->groupValue;
                break;

              case "Kh??a c???a an to??n": // Kh??a c???a an to??n
                $metaArr['an-ninh-bi-dong-khoa-cua-an-toan'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "TH??NG TIN CHUNG": // TH??NG TIN CHUNG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "S??? ch???":
                $metaArr['so_cho_ngoi'] = $valGroup->groupValue;
                break;

              case "Ki???u d??ng":
                $metaArr['kieu_dang'] = $valGroup->groupValue;
                break;

              case "Nhi??n li???u":
                $metaArr['nhien_lieu'] = $valGroup->groupValue;
                break;

              case "Xu???t x???":
                $metaArr['xuat_xu'] = $valGroup->groupValue;
                break;
            }
          }
      }
    }
    return $metaArr;
  }

  public function update($product, $idProd)
  {
    // var_dump($idProd);
    $my_post = array(
      'ID'           => $idProd,
      'post_title'   => wp_strip_all_tags($product->commercialName),
      'post_content' => $product->description,
    );



    $dongXe = $this->getDongXe($product);
    $metaArr = array(
      'id_toyota' => $product->id,
      'mau_car_chinh' => 0,
      'xe_cu'         => 0,
      'header_text'   => $product->slogan,
    );
    $metaInputArr = array();


    if ($product->modelCar != null) {
      $metaArr['kieu_xe'] = strtolower($product->modelCar->getModelName());
      $metaInputArr['modelId'] = $product->modelCar->getModelId();

      $listMX = $this->getListMauXe($metaInputArr['modelId']);
      $mxList = array();
      foreach ($listMX as $key => $mau) {
        if($mau->post_id != $idProd);
          array_push($mxList, $mau->post_id);
      }

      update_field('cac_mau_xe', $mxList, $idProd);
    }
    $metaArr['gia_tham_khao'] = $product->vehicleImages[0] ? $product->vehicleImages[0]->price : 0;

    $metaArr['anh_cover'] = Constants::instance()->downloadAImage($product->banner);
    $metaInputArr['anh_cover'] = $product->banner;
    $metaArr['anh_gioi_thieu'] = Constants::instance()->downloadAImage($product->imgDetail);
    $metaInputArr['anh_gioi_thieu'] = $product->imgDetail;
    $metaArr['anh_dai_dien_2'] = Constants::instance()->downloadAImage($product->img45);
    $metaInputArr['anh_dai_dien_2'] = $product->img45;

    $metaArr = array_merge($metaArr, $this->metaProdValues($product->overview));
    $metaInputArr['anh_dai_dien'] = $product->img90;
    $metaInputArr['gradeId'] = $product->gradeId;

    $my_post['meta_input'] = $metaInputArr;



    // Update the post into the database
    wp_update_post($my_post);
    $anh_dai_dien = get_post_meta($idProd, 'anh_dai_dien', true);
    if ($product->img90 != $anh_dai_dien) {
      $attachment_id = Constants::instance()->downloadAImage($product->img90);
      set_post_thumbnail($idProd, $attachment_id);
    }

    if ($dongXe && $dongXe != null) {
      // $my_post['taxonomies'] = array($dongXe['cateName']);
      wp_set_post_terms($idProd, array($dongXe['cateId']), 'dong_xe');
    }

    $this->updateMetaField($metaArr, $idProd);

    // foreach ($product->vehicleImages as $key => $value) {
    //   $row = array(
    //     'mau' => $value->hexcode,
    //     'ten_mau'   => $value->colorName,
    //     'anh_xe'  => Constants::instance()->downloadAImage($value->imageUrl),
    //     'model_price' => $value->price,
    //   );

    //   add_row('car_color_ngoai_that', $row, $idProd);
    // }
    // foreach ($product->internalColorImages as $key => $value) {
    //   $row = array(
    //     'mau' => $value->iHexcode,
    //     'ten_mau'   => $value->iColorName,
    //     'anh_xe'  => Constants::instance()->downloadAImage($value->imageUrl),
    //     // 'model_price' => $metaArr['gia_tham_khao'],
    //   );

    //   add_row('car_color_noi_that', $row, $idProd);
    // }
  }
  public function delete($idProd)
  {
  }

  public function getDongXe($product)
  {
    for ($i = 0; $i < count($product->overview); $i++) {
      if ($product->overview[$i]->bigGroupName == "TH??NG TIN CHUNG") {
        for ($j = 0; $j < count($product->overview[$i]->group); $j++) {
          if ($product->overview[$i]->group[$j]->groupName == "Ki???u d??ng") {
            $groupVal =  trim($product->overview[$i]->group[$j]->groupValue);
            switch ($groupVal) {
              case 'B??n t???i/Pick-up':
                $cateId = 17;
                $cateName = 'B??n t???i';
                break;
              case '??a d???ng':
                $cateId = 15;
                $cateName = '??a d???ng';
                break;
              case 'Hatchback':
                $cateId = 12;
                $cateName = 'Hatchback';
                break;
              case 'Sedan':
                $cateId = 13;
                $cateName = 'Sedan';
                break;
              case 'Th????ng m???i':
                $cateId = 16;
                $cateName = 'Th????ng m???i';
                break;
              case 'SUV':
                $cateId = 14;
                $cateName = 'SUV';
                break;
            }

            if (isset($cateId) && isset($cateName)) {
              return array(
                'cateId' => $cateId,
                'cateName' => $cateName
              );
            }
          }
        }
      }
      break;
    }


    return null;
  }

  public function extractInfoFromData($overview)
  {
  }
}
