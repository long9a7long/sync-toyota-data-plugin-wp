(function($) {

    "use strict";



    /**

     * All of the code for your admin-facing JavaScript source

     * should reside in this file.

     *

     * Note: It has been assumed you will write jQuery code here, so the

     * $ function reference has been prepared for usage within the scope

     * of this function.

     *

     * This enables you to define handlers, for when the DOM is ready:

     *

     * $(function() {

     *

     * });

     *

     * When the window is loaded:

     *

     * $( window ).load(function() {

     *

     * });

     *

     * ...and/or other possibilities.

     *

     * Ideally, it is not considered best practise to attach more than a

     * single DOM-ready or window-load handler for a particular page.

     * Although scripts in the WordPress core, Plugins and Themes may be

     * practising this, we should strive to set a better example in our own work.

     */

    $(function() {
        var current_step_sync_product = 99;
        var current_step_sync_gallery_product = 1;
        var current_step_sync_catalogue_product = 1;
        var current_step_sync_feature_product = 1;
        var current_step_sync_furniture_product = 1;
        var current_step_sync_exterior_product = 1;

        var syncing = false;

        // Change label Size per step when change value range input
        jQuery("#accordionproduct #size_per_step_product_sync").on(
            "change",
            function() {
                jQuery("#accordionproduct #size_per_step_product_sync_value").text(
                    jQuery(this).val()
                );
            }
        );

        // Update total records product

        jQuery("#recheck_total_records_product").on("click", function() {

            if (!syncing) {

                jQuery(this).attr("disabled", "disabled");

                let spinner = jQuery(

                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'

                );

                jQuery(this).prepend(spinner);

                let postData =

                    "action=admin_ajax_request&param=get_total_records_product";

                jQuery.get(ajaxurl, postData, function(res) {

                    jQuery("#recheck_total_records_product").removeAttr("disabled");

                    jQuery("#recheck_total_records_product .spinner-border").remove();

                    let result = JSON.parse(res);

                    if (result.status == 1) {

                        jQuery("#collapseOne .total_records_product").text(result.data);

                        jQuery(".sync-info total_records").text(result.data);

                        jQuery("input#total_records").val(result.data);

                    } else {

                        alert("Có lỗi xảy ra, vui lòng quay lại sau!");

                    }

                });

            }

        });

        // Update size per step product

        jQuery("#form_configs_sync_product").submit(function(e) {

            e.preventDefault();

            let step_size = jQuery("#size_per_step_product_sync").val();

            if (step_size < 1) {

                alert("Vui lòng chọn số lượng hợp lệ");

            } else {

                if (!syncing) {

                    jQuery('#form_configs_sync_product button[type="submit"]').attr(

                        "disabled",

                        "disabled"

                    );

                    let spinner = jQuery(

                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'

                    );

                    jQuery('#form_configs_sync_product button[type="submit"]').prepend(

                        spinner

                    );

                    let postData = {

                        action: "admin_ajax_request",

                        param: "change_size_per_step_product",

                        size_per_step: step_size,

                    };

                    jQuery.post(ajaxurl, postData, function(res) {

                        jQuery(

                            '#form_configs_sync_product button[type="submit"]'

                        ).removeAttr("disabled");

                        jQuery(

                            '#form_configs_sync_product button[type="submit"] .spinner-border'

                        ).remove();

                        let result = JSON.parse(res);

                        if (result.status == 1) {

                            jQuery("input#size").val(result.data);

                            jQuery(".sync-info .size").text(result.data);

                            jQuery("#collapseThree").collapse("show");

                        } else {

                            alert("Có lỗi xảy ra, vui lòng quay lại sau!");

                        }

                    });

                }

            }

        });

        // Sync product

        jQuery("#sync_now_btn").on("click", function() {
            jQuery(this).attr("disabled", "disabled");
            let spinner = jQuery(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'
            );

            jQuery(this).prepend(spinner);
            if (!syncing) {
                sync_product_process();
            }
        });

        function sync_process_ui() {
            jQuery("#collapseThree .alert-warning").css("display", "block");
            jQuery("#collapseThree .alert-success").css("display", "none");
            jQuery("#collapseThree .alert-danger").css("display", "none");
            jQuery("#collapseThree .sync-process").css("display", "block");
            jQuery("#collapseThree .p_end_at").css("display", "none");
        }

        function sync_product_process() {
            syncing = true;
            jQuery("#collapseThree .progress").css("display", "block");
            jQuery(".sync_step").text("Sản phẩm");
            sync_process_ui();
            jQuery("#collapseThree .start_time").text(new Date());
            sync_product_post_data();
        }

        function sync_gallery_prod_process() {
            jQuery("#collapseThree .pros-gallery").css("display", "block");
            sync_process_ui();
            sync_gallery_prod_post_data();
        }

        function sync_catalogue_prod_process() {
            jQuery("#collapseThree .pros-catalogue").css("display", "block");
            sync_process_ui();
            sync_catalogue_prod_post_data();
        }

        function sync_feature_prod_process() {
            jQuery("#collapseThree .pros-feature").css("display", "block");
            sync_process_ui();
            sync_feature_prod_post_data();
        }

        function sync_furniture_prod_process() {
            jQuery("#collapseThree .pros-furniture").css("display", "block");
            sync_process_ui();
            sync_furniture_prod_post_data();
        }

        function sync_exterior_prod_process() {
            jQuery("#collapseThree .pros-exterior").css("display", "block");
            sync_process_ui();
            sync_exterior_prod_post_data();
        }

        function sync_product_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_product",
                step: current_step_sync_product,
            };
            jQuery.post(ajaxurl, postData, function(res) {
                    let result = JSON.parse(res);
                    if (result.status == 1) {
                        update_sync_info();
                        if (current_step_sync_product >= result.data.total_step) {
                            // syncing = false;
                            jQuery("#collapseThree .alert-warning").css("display", "none");
                            jQuery("#collapseThree .alert-success").css("display", "block");
                            jQuery("#collapseThree .alert-danger").css("display", "none");
                            jQuery("#collapseThree .sync-process").css("display", "none");
                            jQuery("#sync_now_btn .spinner-border").remove();
                            jQuery("#collapseThree .progress-bar").css("width", "100%");
                            jQuery("#collapseThree .progress-bar").text("100%");
                            jQuery("#collapseThree .p_end_at").css("display", "block");
                            jQuery("#collapseThree .end_time").text(new Date());

                            sync_gallery_prod_process(); // Start Sync gallery product
                            return;
                        } else {
                            let total_percent = Math.ceil(
                                (current_step_sync_product / result.data.total_step) * 100
                            );
                            jQuery("#collapseThree .progress-bar").css(
                                "width",
                                total_percent + "%"
                            );
                            jQuery("#collapseThree .progress-bar").text(total_percent + "%");
                            current_step_sync_product = result.data.step;
                            sync_product_post_data();
                        }
                    } else {

                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "none");
                        jQuery("#collapseThree .alert-danger").css("display", "block");
                        jQuery("#collapseThree .sync-process").css("display", "none");
                        jQuery("#sync_now_btn").removeAttr("disabled");
                        jQuery("#sync_now_btn .spinner-border").remove();
                        syncing = false;
                        return;
                    }
                })
                .done(function() {})
                .fail(function() {
                    jQuery("#collapseThree .alert-warning").css("display", "none");
                    jQuery("#collapseThree .alert-success").css("display", "none");
                    jQuery("#collapseThree .alert-danger").css("display", "block");
                    jQuery("#collapseThree .sync-process").css("display", "none");
                    jQuery("#sync_now_btn").removeAttr("disabled");
                    jQuery("#sync_now_btn .spinner-border").remove();
                    syncing = false;
                    return;
                });
        }



        function sync_gallery_prod_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_gallery_prod",
                step: current_step_sync_gallery_product,
            };

            jQuery.post(ajaxurl, postData, function(res) {
                let result = JSON.parse(res);
                if (result.status == 1) {
                    jQuery(".sync_step").text("Thư viện hình ảnh sản phẩm");

                    if (current_step_sync_gallery_product >= result.data.total_step) {
                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "block");
                        jQuery("#collapseThree .alert-danger").css("display", "none");
                        // jQuery("#collapseThree .progress-bar-gallery-prod").css("display", "none");
                        jQuery("#collapseThree .sync-process").css("display", "none");
                        jQuery("#sync_now_btn .spinner-border").remove();
                        jQuery("#collapseThree .progress-bar-gallery-prod").css("width", "100%");
                        jQuery("#collapseThree .progress-bar-gallery-prod").text("100%");
                        sync_catalogue_prod_process(); // Start Sync catalogue product

                        return;
                    } else {
                        let total_percent = Math.ceil(
                            (current_step_sync_gallery_product / result.data.total_step) * 100
                        );
                        jQuery("#collapseThree .progress-bar-gallery-prod").css(
                            "width",
                            total_percent + "%"
                        );
                        jQuery("#collapseThree .progress-bar-gallery-prod").text(total_percent + "%");
                        current_step_sync_gallery_product = result.data.step;
                        sync_gallery_prod_post_data();
                    }


                }
            });

        }

        function sync_catalogue_prod_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_catalogue",
                step: current_step_sync_catalogue_product,
            };

            jQuery.post(ajaxurl, postData, function(res) {
                let result = JSON.parse(res);
                if (result.status == 1) {
                    jQuery(".sync_step").text("Catalogue");

                    if (current_step_sync_catalogue_product >= result.data.total_step) {
                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "block");
                        jQuery("#collapseThree .alert-danger").css("display", "none");
                        // jQuery("#collapseThree .progress-bar-gallery-prod").css("display", "none");
                        jQuery("#collapseThree .sync-process").css("display", "none");
                        jQuery("#sync_now_btn .spinner-border").remove();
                        jQuery("#collapseThree .progress-bar-catalogue-prod").css("width", "100%");
                        jQuery("#collapseThree .progress-bar-catalogue-prod").text("100%");
                        sync_feature_prod_process(); // Start Sync feature product

                        return;
                    } else {
                        let total_percent = Math.ceil(
                            (current_step_sync_catalogue_product / result.data.total_step) * 100
                        );
                        jQuery("#collapseThree .progress-bar-catalogue-prod").css(
                            "width",
                            total_percent + "%"
                        );
                        jQuery("#collapseThree .progress-bar-catalogue-prod").text(total_percent + "%");
                        current_step_sync_catalogue_product = result.data.step;
                        sync_catalogue_prod_post_data();
                    }


                }
            });
        }

        function sync_feature_prod_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_feature",
                step: current_step_sync_feature_product,
            };

            jQuery.post(ajaxurl, postData, function(res) {
                let result = JSON.parse(res);
                if (result.status == 1) {
                    jQuery(".sync_step").text("Thông tin Vận hành");

                    if (current_step_sync_feature_product >= result.data.total_step) {
                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "block");
                        jQuery("#collapseThree .alert-danger").css("display", "none");
                        jQuery("#collapseThree .sync-process").css("display", "none");
                        jQuery("#sync_now_btn .spinner-border").remove();
                        jQuery("#collapseThree .progress-bar-feature-prod").css("width", "100%");
                        jQuery("#collapseThree .progress-bar-feature-prod").text("100%");
                        sync_furniture_prod_process(); // Start Sync furniture product

                        return;
                    } else {
                        let total_percent = Math.ceil(
                            (current_step_sync_feature_product / result.data.total_step) * 100
                        );
                        jQuery("#collapseThree .progress-bar-feature-prod").css(
                            "width",
                            total_percent + "%"
                        );
                        jQuery("#collapseThree .progress-bar-feature-prod").text(total_percent + "%");
                        current_step_sync_feature_product = result.data.step;
                        sync_feature_prod_post_data();
                    }


                }
            });
        }

        function sync_furniture_prod_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_furniture",
                step: current_step_sync_furniture_product,
            };

            jQuery.post(ajaxurl, postData, function(res) {
                let result = JSON.parse(res);
                if (result.status == 1) {
                    jQuery(".sync_step").text("Thông tin Nội thất");

                    if (current_step_sync_furniture_product >= result.data.total_step) {
                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "block");
                        jQuery("#collapseThree .alert-danger").css("display", "none");
                        jQuery("#collapseThree .sync-process").css("display", "none");

                        jQuery("#sync_now_btn .spinner-border").remove();
                        jQuery("#collapseThree .progress-bar-furniture-prod").css("width", "100%");
                        jQuery("#collapseThree .progress-bar-furniture-prod").text("100%");
                        sync_exterior_prod_process(); // Start Sync exterior product

                        return;
                    } else {
                        let total_percent = Math.ceil(
                            (current_step_sync_furniture_product / result.data.total_step) * 100
                        );
                        jQuery("#collapseThree .progress-bar-furniture-prod").css(
                            "width",
                            total_percent + "%"
                        );
                        jQuery("#collapseThree .progress-bar-furniture-prod").text(total_percent + "%");
                        current_step_sync_furniture_product = result.data.step;
                        sync_furniture_prod_post_data();
                    }


                }
            });
        }

        function sync_exterior_prod_post_data() {
            let postData = {
                action: "admin_ajax_request",
                param: "sync_exterior",
                step: current_step_sync_exterior_product,
            };

            jQuery.post(ajaxurl, postData, function(res) {
                let result = JSON.parse(res);
                if (result.status == 1) {
                    jQuery(".sync_step").text("Thông tin Ngoại thất");

                    if (current_step_sync_exterior_product >= result.data.total_step) {
                        jQuery("#collapseThree .alert-warning").css("display", "none");
                        jQuery("#collapseThree .alert-success").css("display", "block");
                        jQuery("#collapseThree .alert-danger").css("display", "none");
                        jQuery("#collapseThree .sync-process").css("display", "none");
                        jQuery("#sync_now_btn").removeAttr("disabled");
                        jQuery("#sync_now_btn .spinner-border").remove();
                        jQuery("#collapseThree .progress-bar-exterior-prod").css("width", "100%");
                        jQuery("#collapseThree .progress-bar-exterior-prod").text("100%");
                        syncing = false;

                        return;
                    } else {
                        let total_percent = Math.ceil(
                            (current_step_sync_exterior_product / result.data.total_step) * 100
                        );
                        jQuery("#collapseThree .progress-bar-exterior-prod").css(
                            "width",
                            total_percent + "%"
                        );
                        jQuery("#collapseThree .progress-bar-exterior-prod").text(total_percent + "%");
                        current_step_sync_exterior_product = result.data.step;
                        sync_exterior_prod_post_data();
                    }


                }
            });
        }

        function update_sync_info() {

            let total_records = +jQuery("input#total_records").val();
            let step_size = +jQuery("input#size").val();


            jQuery(".sync-info .count_synced").text(
                step_size * current_step_sync_product
            );
            jQuery(".sync-info .count_remaining").text(
                total_records - step_size * current_step_sync_product
            );
        }

        $("#collapseThree").on("hide.bs.collapse", function(e) {
            if (syncing) {
                e.preventDefault();
                alert("Đang đồng bộ không thể thực hiện hành động khác!");
            }
        });

        window.onbeforeunload = function() {
            if (syncing) {
                return "Đang đồng bộ không được thoát!";
            }
        };
    });
})(jQuery);