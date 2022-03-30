<div class="alert alert-warning">

    <strong>Cảnh báo!</strong> Vui lòng không thoát khỏi trang trong quá trình đồng bộ đang diễn ra!

</div>

<div class="alert alert-success">

    <strong>Đồng bộ thành công!</strong> Toàn bộ dữ liệu Khách hàng từ Toyota đã được đồng bộ thành công với Woocommerce!

</div>

<div class="alert alert-danger">

    <strong>Có lỗi xảy ra!</strong> Vui lòng quay lại sau!

</div>

<!-- Process bar -->

<div class="progress">
    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
</div>
<div class="progress pros-gallery">
    <div class="progress-bar-gallery-prod bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
</div>
<div class="progress pros-catalogue">
    <div class="progress-bar-catalogue-prod bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
</div>

<div class="container mt-3 sync-info">
    <p><b>Đang đồng bộ: </b><span class="sync_step"></span></p>
    <p><b>Tổng số bản ghi: </b><span class="total_records"><?php echo $total_records; ?></span></p>

    <p><b>Số lượng bản ghi mỗi lần gọi: </b><span class="size"><?php echo $size_per_step; ?></p>

    <p><b>Số bản ghi đã đồng bộ: </b><span class="count_synced">0</span></p>

    <p><b>Số bản ghi còn lại: </b><span class="count_remaining"><?php echo ($total_records); ?></span>

    </p>

    <p><b>Thời gian bắt đầu đồng bộ: </b><span class="start_time">0</span></p>

    <p class="p_end_at"><b>Thời gian kết thúc đồng bộ: </b><span class="end_time">0</span></p>

    <div class="text-center mx-auto align-middle ">

        <button class="btn btn-primary" id="sync_now_btn">Đồng bộ ngay</button>

    </div>

    <div class="text-center mx-auto align-middle sync-process">

        <div class="spinner-border text-primary"></div><b class="status-label">Đang đồng bộ</b>

    </div>

</div>