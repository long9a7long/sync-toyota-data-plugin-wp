<div class="alert alert-warning">

    <strong>Cảnh báo!</strong> Với số lượng bản ghi mỗi lần càng lớn thì tỉ lệ thành công đồng bộ càng thấp. Hãy chọn từ

    5-10 là phù hợp!

</div>

<form action="/" id="form_configs_sync_model_prod">

    <div class="form-group">

        <label for="email">Số lượng bản ghi mỗi lần đồng bộ: <span

                id="size_per_step_model_prod_sync_value"><?php echo $size_per_step; ?></span></label>

        <input type="range" class="custom-range" id="size_per_step_model_prod_sync" name="size_per_step_model_prod_sync"

            min="0" max="50" step="1" value="<?php echo $size_per_step; ?>">

    </div>

    <button type="submit" class="btn btn-primary">Tiếp tục</button>

</form>