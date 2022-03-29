<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'entities/base-model.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'constants/api-urls.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'custom-curl.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'constants/table-name.php';

class ProductData extends BaseModel
{
  private $connection;
  public function __construct($connection)
  {
    $this->connection = $connection;
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

    if (count($total_record) > 0) {
      return $total_record->post_id;
    }
    return null;
  }

  public function createGalleryProduct($image)
  {
    $postId = $this->getProductIdByGradeId($image->gradeId);
    if ($postId != null) {
      $value = get_field("thu_vien_anh_ngoai_that", $postId);
      $imageId = Constants::instance()->downloadAImage($image->url);
      $oldVal = array();
      foreach ($value as $key => $val) {
        array_push($oldVal, $val['id']);
      }
      if (count($oldVal)> 0)
        $newVal = array_push($oldVal, $imageId);
      else $newVal = array($imageId);
      update_field("thu_vien_anh_ngoai_that", $newVal, $postId);

      $imgArr = array(
        array(
          'libraryId' => $image->libraryId,
          'url' => $image->url,
        )
      );
      $current_thu_vien_text = get_post_meta($postId,'thu_vien_anh_ngoai_that_toyota');
      if(count($current_thu_vien_text) > 0) {
        $current_thu_vien = json_decode($current_thu_vien_text[0]);
        array_push($imgArr, $current_thu_vien);
      }
      update_post_meta($postId, 'thu_vien_anh_ngoai_that_toyota', json_encode($imgArr));
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


    if($product->modelCar != null) {
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
        case "ĐỘNG CƠ & KHUNG XE": // ĐỘNG CƠ & KHUNG XE
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Kích thước": // Kích thước
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Kích thước tổng thể bên ngoài (D x R x C) (mm x mm x mm)": // Kích thước tổng thể bên ngoài
                      $metaArr['dong_co_khung_xe_kich_thuoc-kich-thuoc-ben-ngoai'] = $valDetail->detailValue;
                      break;
                    case "Kích thước tổng thể bên trong (D x R x C) (mm x mm x mm)": // Kích thước tổng thể bên trong
                      $metaArr['dong_co_khung_xe-kich_thuoc-kich_thuoc_ben_trong'] = $valDetail->detailValue;
                      break;
                    case "Chiều dài cơ sở (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-chieu-dai-co-so'] = $valDetail->detailValue;
                      break;
                    case "Chiều rộng cơ sở (Trước/Sau) (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-chieu-rong-co-so'] = $valDetail->detailValue;
                      break;
                    case "Khoảng sáng gầm xe (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-khoang-sang-gam-xe'] = $valDetail->detailValue;
                      break;
                    case "Góc thoát (Trước/Sau) (độ)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-goc-thoat'] = $valDetail->detailValue;
                      break;
                    case "Bán kính vòng quay tối thiểu (m)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-ban-kinh-vong-quay-toi-thieu'] = $valDetail->detailValue;
                      break;
                    case "Trọng lượng không tải (kg)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-trong-luong-khong-tai'] = $valDetail->detailValue;
                      break;
                    case "Trọng lượng toàn tải (kg)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-trong-luong-toan-tai'] = $valDetail->detailValue;
                      break;
                    case "Dung tích bình nhiên liệu (L)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-dung-tich-binh-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Dung tích khoang hành lý (L)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-dung-tich-khoang-hanh-ly'] = $valDetail->detailValue;
                      break;
                    case "Kích thước khoang chở hàng (D x R x C) (mm)":
                      $metaArr['dong_co_khung_xe-kich_thuoc-kich-thuoc-khoang-cho-hang'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Động cơ thường": // Động cơ thường
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Loại động cơ":
                      $metaArr['loai_dong_co'] = $valDetail->detailValue;
                      $metaArr['dong_co_khung_xe-dong_co-loai-dong-co'] = $valDetail->detailValue;
                      break;
                    case "Số xy lanh":
                      $metaArr['dong_co_khung_xe-dong_co-so-xy-lanh'] = $valDetail->detailValue;
                      break;
                    case "Bố trí xy lanh":
                      $metaArr['dong_co_khung_xe-dong_bo-tri-xy-lanh'] = $valDetail->detailValue;
                      break;
                    case "Dung tích xy lanh":
                      $metaArr['dong_co_khung_xe-dong_dung-tich-xy-lanh'] = $valDetail->detailValue;
                      $metaArr['dung_tich_dong_co'] = $valDetail->detailValue;
                      break;
                    case "Tỉ số nén":
                      $metaArr['dong_co_khung_xe-dong_ty-so-nen'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống nhiên liệu":
                      $metaArr['dong_co_khung_xe-dong-co-he-thong-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Loại nhiên liệu":
                      $metaArr['dong_co_khung_xe-dong-co-loai-nhien-lieu'] = $valDetail->detailValue;
                      $metaArr['nhien_lieu'] = $valDetail->detailValue;
                      break;
                    case "Công suất tối đa":
                      $metaArr['dong_co_khung_xe-dong-co-cong-suat-toi-da'] = $valDetail->detailValue;
                      break;
                    case "Mô men xoắn tối đa":
                      $metaArr['dong_co_khung_xe-dong-co-mo-men-xoan-toi-da'] = $valDetail->detailValue;
                      break;
                    case "Tốc độ tối đa":
                      $metaArr['dong_co_khung_xe-dong-co-toc-do-toi-da'] = $valDetail->detailValue;
                      break;
                    case "Khả năng tăng tốc":
                      $metaArr['dong_co_khung_xe-dong-co-kha-nang-tang-toc'] = $valDetail->detailValue;
                      break;
                    case "Hệ số cản không khí":
                      $metaArr['dong_co_khung_xe-dong-co-he-so-can-khong-khi'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống ngắt/mở động cơ tự động":
                      $metaArr['dong_co_khung_xe-dong-co-he-so-ngat-mo-dong-co-tu-dong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Động cơ điện": // Động cơ dien
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                }
                break;
              case "Các chế độ lái":
                $metaArr['dong_co_khung_xe-cac-che-do-lai'] = $valGroup->groupValue;
                break;
              case "Hệ thống truyền động": // Hệ thống truyền động
                $metaArr['dong_co_khung_xe-he-thong-truyen-dong'] = $valGroup->groupValue;
                break;
              case "Hộp số": // Hộp số
                $metaArr['dong_co_khung_xe-hop-so'] = $valGroup->groupValue;
                $metaArr['thong_tin_khac'] = $valGroup->groupValue;
                break;
              case "Hệ thống treo": // Hệ thống treo
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trước":
                      $metaArr['dong_co_khung_xe-he-thong-treo-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['dong_co_khung_xe-he-thong-treo-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Hệ thống lái": // Hệ thống lái
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trợ lực tay lái":
                      $metaArr['dong_co_khung_xe-he-thong-lai-tro-luc-tay-lai'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống tay lái tỉ số truyền biến thiên (VGRS)":
                      $metaArr['dong_co_khung_xe-he-thong-lai-he-thong-tay-lai-ti-so-truyen-bien-thien'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Vành & lốp xe": // Vành & lốp xe
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Loại vành":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-loai-vanh'] = $valDetail->detailValue;
                      break;
                    case "Kích thước lốp":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-kich-thuoc-lop'] = $valDetail->detailValue;
                      break;
                    case "Lốp dự phòng":
                      $metaArr['dong_co_khung_xe-vanh-lop-xe-lop-du-phong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Phanh": // Phanh
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trước":
                      $metaArr['dong_co_khung_xe-phanh-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['dong_co_khung_xe-phanh-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Tiêu chuẩn khí thải": // Tiêu chuẩn khí thải
                $metaArr['dong_co_khung_xe-tieu-chuan-khi-thai'] = $valGroup->groupValue;
                break;

              case "Tiêu thụ nhiên liệu (L/100km)": // Tiêu thụ nhiên liệu
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Ngoài đô thị":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-ngoai-do-thi'] = $valDetail->detailValue;
                      break;
                    case "Kết hợp":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-ket-hop'] = $valDetail->detailValue;
                      break;
                    case "Trong đô thị":
                      $metaArr['dong_co_khung_xe-tieu-thu-nhien-lieu-trong-do-thi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "NGOẠI THẤT": // NGOẠI THẤT
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Cụm đèn trước": // Cụm đèn trước
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Đèn chiếu gần":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-gan'] = $valDetail->detailValue;
                      break;
                    case "Đèn chiếu xa":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-xa'] = $valDetail->detailValue;
                      break;
                    case "Đèn chiếu sáng ban ngày":
                      $metaArr['ngoai-that-cum-den-truoc-den-chieu-ban-ngay'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống rửa đèn":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-rua-den'] = $valDetail->detailValue;
                      break;
                    case "Tự động Bật/Tắt":
                      $metaArr['ngoai-that-cum-den-truoc-tu-dong-bat-tat'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống nhắc nhở đèn sáng":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-nhac-nho-den-sang'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống mở rộng góc chiếu tự động":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-mo-rong-goc-chieu-tu-dong'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống cân bằng góc chiếu":
                      $metaArr['ngoai-that-cum-den-truoc-he-thong-can-bang-goc-chieu'] = $valDetail->detailValue;
                      break;
                    case "Chế độ đèn chờ dẫn đường":
                      $metaArr['ngoai-that-cum-den-truoc-che-do-den-cho-dan-duong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Cụm đèn sau": // Cụm đèn sau
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Đèn vị trí":
                      $metaArr['ngoai-that-cum-den-truoc-den-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "Đèn phanh":
                      $metaArr['ngoai-that-cum-den-truoc-den-phanh'] = $valDetail->detailValue;
                      break;
                    case "Đèn báo rẽ":
                      $metaArr['ngoai-that-cum-den-truoc-den-bao-re'] = $valDetail->detailValue;
                      break;
                    case "Đèn lùi":
                      $metaArr['ngoai-that-cum-den-truoc-den-lui'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Đèn báo phanh trên cao (Đèn phanh thứ ba)": // Đèn báo phanh trên cao
                $metaArr['ngoai-that-den-bao-phanh-tren-cao'] = $valGroup->groupValue;
                break;

              case "Đèn sương mù": // Đèn sương mù
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trước":
                      $metaArr['ngoai-that-den-suong-mu-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-den-suong-mu-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Gương chiếu hậu ngoài": // Gương chiếu hậu ngoài
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Chức năng điều chỉnh điện":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-dieu-chinh-dien'] = $valDetail->detailValue;
                      break;
                    case "Chức năng gập điện":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-gap-dien'] = $valDetail->detailValue;
                      break;
                    case "Tích hợp đèn báo rẽ":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-tich-hop-den-bao-re'] = $valDetail->detailValue;
                      break;
                    case "Tích hợp đèn chào mừng":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-tich-hop-den-chao-mung'] = $valDetail->detailValue;
                      break;
                    case "Màu":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-mau'] = $valDetail->detailValue;
                      break;
                    case "Chức năng tự điều chỉnh khi lùi":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-tu-dieu-chinh-khi-lui'] = $valDetail->detailValue;
                      break;
                    case "Bộ nhớ vị trí":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-bo-nho-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "Chức năng sấy gương":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-say-guong'] = $valDetail->detailValue;
                      break;
                    case "Chức năng chống bám nước":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-chong-bam-nuoc'] = $valDetail->detailValue;
                      break;
                    case "Chức năng chống chói tự động":
                      $metaArr['ngoai-that-guong-chieu-hau-ngoai-chuc-nang-chong-choi-tu-dong'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Gạt mưa": // Gạt mưa
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trước":
                      $metaArr['ngoai-that-gat-mua-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-gat-mua-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Chức năng sấy kính sau": // Chức năng sấy kính sau
                $metaArr['ngoai-that-chuc-nang-say-kinh-sau'] = $valGroup->groupValue;
                break;

              case "Ăng ten": // Ăng ten
                $metaArr['ngoai-that-ang-ten'] = $valGroup->groupValue;
                break;

              case "Tay nắm cửa ngoài xe": // Tay nắm cửa ngoài xe
                $metaArr['ngoai-that-tay-nam-cua-ngoai-xe'] = $valGroup->groupValue;
                break;

              case "Bộ quây xe thể thao": // Bộ quây xe thể thao
                $metaArr['ngoai-that-bo-quay-xe-the-thao'] = $valGroup->groupValue;
                break;

              case "Thanh cản (giảm va chạm)": // Thanh cản
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Trước":
                      $metaArr['ngoai-that-gat-mua-truoc'] = $valDetail->detailValue;
                      break;
                    case "Sau":
                      $metaArr['ngoai-that-gat-mua-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Lưới tản nhiệt": // Lưới tản nhiệt
                $metaArr['ngoai-that-luon-tan-nhiet'] = $valGroup->groupValue;
                break;

              case "Chắn bùn": // Chắn bùn
                $metaArr['ngoai-that-chan-bun'] = $valGroup->groupValue;
                break;

              case "Chắn bùn bên": // Chắn bùn bên
                $metaArr['ngoai-that-chan-bun-ben'] = $valGroup->groupValue;
                break;

              case "Ống xả kép": // Ống xả kép
                $metaArr['ngoai-that-ong-xa-kep'] = $valGroup->groupValue;
                break;

              case "Cánh hướng gió": // Cánh hướng gió
                $metaArr['ngoai-that-canh-huong-gio'] = $valGroup->groupValue;
                break;

              case "Thanh đỡ nóc xe": // Thanh đỡ nóc xe
                $metaArr['ngoai-that-thanh-do-noc-xe'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "NỘI THẤT": // NỘI THẤT
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Tay lái": // Tay lái
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Loại tay lái":
                      $metaArr['noi-that-tay-lai-loai-tay-lai'] = $valDetail->detailValue;
                      break;
                    case "Chất liệu":
                      $metaArr['noi-that-tay-lai-chat-lieu'] = $valDetail->detailValue;
                      break;
                    case "Nút bấm điều khiển tích hợp":
                      $metaArr['noi-that-tay-lai-nut-bam-dieu-khien-tich-hop'] = $valDetail->detailValue;
                      break;
                    case "Điều chỉnh":
                      $metaArr['noi-that-tay-lai-dieu-chinh'] = $valDetail->detailValue;
                      break;
                    case "Lẫy chuyển số":
                      $metaArr['noi-that-tay-lai-lay-chuyen-so'] = $valDetail->detailValue;
                      break;
                    case "Bộ nhớ vị trí":
                      $metaArr['noi-that-tay-lai-bo-chuyen-vi-tri'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Gương chiếu hậu trong": // Gương chiếu hậu trong
                $metaArr['noi-that-guong-chieu-hau-trong'] = $valGroup->groupValue;
                break;

              case "Tay nắm cửa trong xe": // Tay nắm cửa trong xe
                $metaArr['noi-that-tay-nam-cua-trong-xe'] = $valGroup->groupValue;
                break;

              case "Cụm đồng hồ": // Cụm đồng hồ
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Loại đồng hồ":
                      $metaArr['noi-that-cum-dong-ho-loai-dong-ho'] = $valDetail->detailValue;
                      break;
                    case "Đèn báo hệ thống Hybrid":
                      $metaArr['noi-that-cum-dong-ho-den-bao-he-thong-hybrid'] = $valDetail->detailValue;
                      break;
                    case "Đèn báo chế độ Eco":
                      $metaArr['noi-that-cum-dong-ho-den-bao-che-do-eco'] = $valDetail->detailValue;
                      break;
                    case "Chức năng báo lượng tiêu thụ nhiên liệu":
                      $metaArr['noi-that-cum-dong-ho-chuc-nang-bao-luong-tieu-thu-nhien-lieu'] = $valDetail->detailValue;
                      break;
                    case "Chức năng báo vị trí cần số":
                      $metaArr['noi-that-cum-dong-ho-chuc-nang-bao-vi-tri-can-so'] = $valDetail->detailValue;
                      break;
                    case "Màn hình hiển thị đa thông tin":
                      $metaArr['noi-that-cum-dong-ho-man-hinh-hien-thi-da-thong-tin'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Màn hình hiển thị đa thông tin": // Cửa sổ trời
                $metaArr['noi-that-cua-so-troi'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "GHẾ": // GHẾ
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Chất liệu bọc ghế": // Chất liệu bọc ghế
                $metaArr['ghe-chat-lieu-boc-ghe'] = $valGroup->groupValue;
                break;

              case "Ghế trước": // Ghế trước
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Loại ghế":
                      $metaArr['ghe-ghe-truoc-loai-ghe'] = $valDetail->detailValue;
                      break;
                    case "Điều chỉnh ghế lái":
                      $metaArr['ghe-ghe-truoc-dieu-chinh-ghe-lai'] = $valDetail->detailValue;
                      break;
                    case "Điều chỉnh ghế hành khách":
                      $metaArr['ghe-ghe-truoc-dieu-chinh-ghe-hanh-khach'] = $valDetail->detailValue;
                      break;
                    case "Bộ nhớ vị trí":
                      $metaArr['ghe-ghe-truoc-bo-nho-vi-tri'] = $valDetail->detailValue;
                      break;
                    case "Chức năng thông gió":
                      $metaArr['ghe-ghe-truoc-chuc-nang-thong-gio'] = $valDetail->detailValue;
                      break;
                    case "Chức năng sưởi":
                      $metaArr['ghe-ghe-truoc-chuc-nang-suoi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Ghế sau": // Ghế sau
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Hàng ghế thứ hai":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-hai'] = $valDetail->detailValue;
                      break;
                    case "Hàng ghế thứ ba":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-ba'] = $valDetail->detailValue;
                      break;
                    case "Hàng ghế thứ bốn":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-tu'] = $valDetail->detailValue;
                      break;
                    case "Hàng ghế thứ năm":
                      $metaArr['ghe-ghe-sau-hang-ghe-thu-nam'] = $valDetail->detailValue;
                      break;
                    case "Tựa tay hàng ghế sau":
                      $metaArr['ghe-ghe-sau-tua-tay-hang-ghe-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "TIỆN NGHI": // TIỆN NGHI
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Rèm che nắng kính sau": // Rèm che nắng kính sau
                $metaArr['tien-nghi-rem-che-nang-kinh-sau'] = $valGroup->groupValue;
                break;
              case "Rèm che nắng cửa sau": // Rèm che nắng cửa sau
                // $metaArr['tien-nghi-rem-che-nang-kinh-sau'] = $valGroup->groupValue;
                break;
              case "Hệ thống điều hòa": // Hệ thống điều hòa
                $metaArr['tien-nghi-he-thong-dieu-hoa'] = $valGroup->groupValue;
                break;
              case "Cửa gió sau": // Cửa gió sau
                $metaArr['tien-nghi-cua-gio-sau'] = $valGroup->groupValue;
                break;
              case "Hộp làm mát": // Hộp làm mát
                $metaArr['tien-nghi-hop-lam-mat'] = $valGroup->groupValue;
                break;
              case "Hệ thống âm thanh": // Hệ thống âm thanh
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Đầu đĩa":
                      $metaArr['tien-nghi-he-thong-am-thanh-dau-dia'] = $valDetail->detailValue;
                      break;
                    case "Số loa":
                      $metaArr['tien-nghi-he-thong-am-thanh-so-loa'] = $valDetail->detailValue;
                      break;
                    case "Cổng kết nối AUX":
                      $metaArr['tien-nghi-he-thong-am-thanh-cong-ket-noi-aux'] = $valDetail->detailValue;
                      break;
                    case "Cổng kết nối USB":
                      $metaArr['tien-nghi-he-thong-am-thanh-cong-ket-noi-usb'] = $valDetail->detailValue;
                      break;
                    case "Kết nối Bluetooth":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-bluetooth'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống điều khiển bằng giọng nói":
                      $metaArr['tien-nghi-he-thong-am-thanh-he-thong-dieu-khien-bang-giong-noi'] = $valDetail->detailValue;
                      break;
                    case "Chức năng điều khiển từ hàng ghế sau":
                      $metaArr['tien-nghi-he-thong-am-thanh-chuc-nang-dieu-khien-tu-hang-ghe-sau'] = $valDetail->detailValue;
                      break;
                    case "Kết nối wifi":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-wifi'] = $valDetail->detailValue;
                      break;
                    case "Hệ thống đàm thoại rảnh tay":
                      $metaArr['tien-nghi-he-thong-am-thanh-he-thong-dam-thoai-ranh-tay'] = $valDetail->detailValue;
                      break;
                    case "Kết nối điện thoại thông minh":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-dien-thoai-thong-minh'] = $valDetail->detailValue;
                      break;
                    case "Kết nối HDMI":
                      $metaArr['tien-nghi-he-thong-am-thanh-ket-noi-hdmi'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Chìa khóa thông minh & khởi động bằng nút bấm": // Chìa khóa thông minh & khởi động bằng nút bấm
                $metaArr['tien-nghi-chia-khoa-thong-minh-khoi-dong-bang-nut-bam'] = $valGroup->groupValue;
                break;

              case "Phanh tay điện tử": // Phanh tay điện tử
                $metaArr['tien-nghi-phanh-tay-dien-tu'] = $valGroup->groupValue;
                break;

              case "Giữ phanh điện tử": // Giữ phanh điện tử
                $metaArr['tien-nghi-giu-phanh-dien-tu'] = $valGroup->groupValue;
                break;

              case "Hệ thống dẫn đường": // Hệ thống dẫn đường
                $metaArr['tien-nghi-he-thong-dan-duong'] = $valGroup->groupValue;
                break;

              case "Hiển thị thông tin trên kính lái": // Hiển thị thông tin trên kính lái
                $metaArr['tien-nghi-he-thong-thong-tin-tren-kinh-lai'] = $valGroup->groupValue;
                break;

              case "Khóa cửa điện": // Khóa cửa điện
                $metaArr['tien-nghi-khoa-cua-dien'] = $valGroup->groupValue;
                break;

              case "Chức năng khóa cửa từ xa": // Chức năng khóa cửa từ xa
                $metaArr['tien-nghi-chuc-nang-khoa-cua-tu-xa'] = $valGroup->groupValue;
                break;

              case "Cửa sổ điều chỉnh điện": // Cửa sổ điều chỉnh điện
                $metaArr['tien-nghi-cua-so-dieu-chinh-dien'] = $valGroup->groupValue;
                break;

              case "Cốp điều khiển điện": // Cốp điều khiển điện
                $metaArr['tien-nghi-cop-dieu-chinh-dien'] = $valGroup->groupValue;
                break;

              case "Hệ thống sạc không dây": // Hệ thống sạc không dây
                $metaArr['tien-nghi-he-thong-sac-khong-day'] = $valGroup->groupValue;
                break;

              case "Ga tự động": // Ga tự động
                $metaArr['tien-nghi-ga-tu-dong'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "AN NINH/HỆ THỐNG CHỐNG TRỘM": // AN NINH/HỆ THỐNG CHỐNG TRỘM
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Hệ thống báo động": // Hệ thống báo động
                $metaArr['an-ninh-he-thong-chong-trom-he-thong-bao-dong'] = $valGroup->groupValue;
                break;

              case "Hệ thống mã hóa khóa động cơ": // Hệ thống báo động
                $metaArr['an-ninh-he-thong-chong-trom-he-thong-ma-hoa-dong-co'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "AN TOÀN CHỦ ĐỘNG": // AN TOÀN CHỦ ĐỘNG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Hệ thống an toàn Toyota safety sense": // Hệ thống an toàn Toyota safety sense
                // $metaArr['an-ninh-chu-dong-he-thong-an-toan-toyota-safety-sense-canh-bao-tien-va-cham'] = $valGroup->groupValue;
                break;

              case "Hệ thống chống bó cứng phanh": // Hệ thống chống bó cứng phanh
                $metaArr['an-ninh-chu-dong-he-thong-chong-bo-cung-phanh'] = $valGroup->groupValue;
                break;

              case "Hệ thống hỗ trợ lực phanh khẩn cấp": // Hệ thống hỗ trợ lực phanh khẩn cấp
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-luc=phanh-khan-cap'] = $valGroup->groupValue;
                break;

              case "Hệ thống phân phối lực phanh điện tử": // Hệ thống phân phối lực phanh điện tử
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-luc=phanh-dien-tu'] = $valGroup->groupValue;
                break;

              case "Hệ thống cân bằng điện tử":
                $metaArr['an-ninh-chu-dong-he-thong-can-bang-dien-tu'] = $valGroup->groupValue;
                break;

              case "Hệ thống kiểm soát lực kéo":
                $metaArr['an-ninh-chu-dong-he-thong-kiem-soat-luc-keo'] = $valGroup->groupValue;
                break;

              case "Hệ thống hỗ trợ khởi hành ngang dốc":
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-khoi-hanh-ngang-doc'] = $valGroup->groupValue;
                break;

              case "Hệ thống hỗ trợ đổ đèo":
                $metaArr['an-ninh-chu-dong-he-thong-ho-tro-do-deo'] = $valGroup->groupValue;
                break;

              case "Hệ thống cảnh báo điểm mù":
                $metaArr['an-ninh-chu-dong-he-thong-canh-bao-diem-mu'] = $valGroup->groupValue;
                break;

              case "Hệ thống lựa chọn vận tốc vượt địa hình":
                $metaArr['an-ninh-chu-dong-he-thong-lua-chon-van-toc-vuot-dia-hinh'] = $valGroup->groupValue;
                break;

              case "Hệ thống thích nghi địa hình":
                $metaArr['an-ninh-chu-dong-he-thong-thich-nghi-dia-hinh'] = $valGroup->groupValue;
                break;

              case "Đèn báo phanh khẩn cấp":
                $metaArr['an-ninh-chu-dong-den-bao-phanh-khan-cap'] = $valGroup->groupValue;
                break;

              case "Hệ thống theo dõi áp suất lốp":
                $metaArr['an-ninh-chu-dong-he-thong-theo-doi-ap-suat-lop'] = $valGroup->groupValue;
                break;

              case "Camera lùi":
                $metaArr['an-ninh-chu-dong-camera-lui'] = $valGroup->groupValue;
                break;

              case "Camera 360 độ":
                $metaArr['an-ninh-chu-dong-camera-360-do'] = $valGroup->groupValue;
                break;

              case "Cảm biến hỗ trợ đỗ xe": // Cảm biến hỗ trợ đỗ xe
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Sau":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-sau'] = $valDetail->detailValue;
                      break;
                    case "Trước":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-truoc'] = $valDetail->detailValue;
                      break;
                    case "Góc trước":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-goc-truoc'] = $valDetail->detailValue;
                      break;
                    case "Góc sau":
                      $metaArr['an-ninh-chu-dong-cam-bien-ho-tro-do-xe-goc-sau'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;
            }
          }
          break;

        case "AN TOÀN BỊ ĐỘNG": // AN TOÀN BỊ ĐỘNG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Túi khí": // Túi khí
                foreach ($valGroup->detail as $keyDetail => $valDetail) {
                  switch (trim($valDetail->detailName)) {
                    case "Túi khí người lái & hành khách phía trước":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-nguoi-lai-hanh-khach-phia-truoc'] = $valDetail->detailValue;
                      break;
                    case "Túi khí bên hông phía trước":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-ben-hong-phia-truoc'] = $valDetail->detailValue;
                      break;
                    case "Túi khí rèm":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-rèm'] = $valDetail->detailValue;
                      break;
                    case "Túi khí bên hông phía sau":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-ben-hong-phia-sau'] = $valDetail->detailValue;
                      break;
                    case "Túi khí đầu gối người lái":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-dau-goi-nguoi-lai'] = $valDetail->detailValue;
                      break;
                    case "Túi khí đầu gối hành khách":
                      $metaArr['an-ninh-bi-dong-tui-khi-tui-khi-dau-goi-hanh-khach'] = $valDetail->detailValue;
                      break;
                  }
                }
                break;

              case "Khung xe GOA": // Khung xe GOA
                $metaArr['an-ninh-bi-dong-khung-xe-goa'] = $valGroup->groupValue;
                break;

              case "Dây đai an toàn": // Dây đai an toàn
                $metaArr['an-ninh-bi-dong-day-dai-an-toan'] = $valGroup->groupValue;
                break;

              case "Ghế có cấu trúc giảm chấn thương cổ (Tựa đầu giảm chấn)": // Ghế có cấu trúc giảm chấn thương cổ
                $metaArr['an-ninh-bi-dong-ghe-co-cau-truc-giam-chan-thuong-co'] = $valGroup->groupValue;
                break;

              case "Cột lái tự đổ": // Cột lái tự đổ
                $metaArr['an-ninh-bi-dong-cot-lai-tu-do'] = $valGroup->groupValue;
                break;

              case "Khóa an toàn trẻ em": // Khóa an toàn trẻ em
                $metaArr['an-ninh-bi-dong-khoa-an-toan-tre-em'] = $valGroup->groupValue;
                break;

              case "Khóa cửa an toàn": // Khóa cửa an toàn
                $metaArr['an-ninh-bi-dong-khoa-cua-an-toan'] = $valGroup->groupValue;
                break;
            }
          }
          break;

        case "THÔNG TIN CHUNG": // THÔNG TIN CHUNG
          foreach ($value->group as $keyGroup => $valGroup) {
            switch (trim($valGroup->groupName)) {
              case "Số chỗ":
                $metaArr['so_cho_ngoi'] = $valGroup->groupValue;
                break;

              case "Kiểu dáng":
                $metaArr['kieu_dang'] = $valGroup->groupValue;
                break;

              case "Nhiên liệu":
                $metaArr['nhien_lieu'] = $valGroup->groupValue;
                break;

              case "Xuất xứ":
                $metaArr['xuat_xu'] = $valGroup->groupValue;
                break;
            }
          }
      }
    }
    return $metaArr;
  }

  public function update($idProd)
  {
  }
  public function delete($idProd)
  {
  }

  public function getDongXe($product)
  {
    for ($i = 0; $i < count($product->overview); $i++) {
      if ($product->overview[$i]->bigGroupName == "THÔNG TIN CHUNG") {
        for ($j = 0; $j < count($product->overview[$i]->group); $j++) {
          if ($product->overview[$i]->group[$j]->groupName == "Kiểu dáng") {
            $groupVal =  trim($product->overview[$i]->group[$j]->groupValue);
            switch ($groupVal) {
              case 'Bán tải/Pick-up':
                $cateId = 17;
                $cateName = 'Bán tải';
                break;
              case 'Đa dụng':
                $cateId = 15;
                $cateName = 'Đa dụng';
                break;
              case 'Hatchback':
                $cateId = 12;
                $cateName = 'Hatchback';
                break;
              case 'Sedan':
                $cateId = 13;
                $cateName = 'Sedan';
                break;
              case 'Thương mại':
                $cateId = 16;
                $cateName = 'Thương mại';
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
