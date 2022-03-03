<h3>Product Sync</h3>

<div class="container">

    <!-- Input hidden -->

    <input type="hidden" id="total_records" value="<?php echo $total_records; ?>" />

    <input type="hidden" id="size" value="<?php echo $size_per_step; ?>" />

    <div class="accordion" id="accordionproduct">

        <div class="card">

            <div class="card-header" id="headingOne">

                <h2 class="mb-0">

                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne"

                        aria-expanded="true" aria-controls="collapseOne">

                        Bước 1: Kiểm tra tổng số bản ghi

                    </button>

                </h2>

            </div>



            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionproduct">

                <div class="card-body">

                    <p>Tổng số bản ghi cần đồng bộ là: <b

                            class="total_records_product"><?php echo $total_records; ?></b></p>

                    <button type="button" class="btn btn-primary" id="recheck_total_records_product"></span>Kiểm tra

                        lại</button>

                </div>

            </div>

        </div>

        <div class="card">

            <div class="card-header" id="headingTwo">

                <h2 class="mb-0">

                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"

                        data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                        Bước 2: Điều chỉnh thông số

                    </button>

                </h2>

            </div>

            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionproduct">

                <div class="card-body">

                    <?php

                    include SYNC_TOYOTA_DATA_PLUGIN_PATH."admin/partials/product/form-configs.php";

                    ?>

                </div>

            </div>

        </div>

        <div class="card">

            <div class="card-header" id="headingThree">

                <h2 class="mb-0">

                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"

                        data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                        Bước 3: Đồng bộ

                    </button>

                </h2>

            </div>

            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionproduct">

                <div class="card-body">

                    <?php

                    include SYNC_TOYOTA_DATA_PLUGIN_PATH."admin/partials/product/process-syning.php";

                ?>

                </div>

            </div>

        </div>

    </div>

</div>