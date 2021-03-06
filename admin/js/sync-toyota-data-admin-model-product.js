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

        var current_step_sync_model_prod = 1;

        var syncing = false;

        // Change label Size per step when change value range input

        jQuery("#accordionmodel_prod #size_per_step_model_prod_sync").on(

            "change",

            function() {

                jQuery("#accordionmodel_prod #size_per_step_model_prod_sync_value").text(

                    jQuery(this).val()

                );

            }

        );



        // Update total records model_prod

        jQuery("#recheck_total_records_model_prod").on("click", function() {

            if (!syncing) {

                jQuery(this).attr("disabled", "disabled");

                let spinner = jQuery(

                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'

                );

                jQuery(this).prepend(spinner);

                let postData =

                    "action=admin_ajax_request&param=get_total_records_model_prod";

                jQuery.get(ajaxurl, postData, function(res) {

                    jQuery("#recheck_total_records_model_prod").removeAttr("disabled");

                    jQuery("#recheck_total_records_model_prod .spinner-border").remove();

                    let result = JSON.parse(res);

                    if (result.status == 1) {

                        jQuery("#collapseOne .total_records_model_prod").text(result.data);

                        jQuery(".sync-info total_records").text(result.data);

                        jQuery("input#total_records").val(result.data);

                    } else {

                        alert("C?? l???i x???y ra, vui l??ng quay l???i sau!");

                    }

                });

            }

        });



        // Update size per step model_prod

        jQuery("#form_configs_sync_model_prod").submit(function(e) {

            e.preventDefault();

            let step_size = jQuery("#size_per_step_model_prod_sync").val();

            if (step_size < 1) {

                alert("Vui l??ng ch???n s??? l?????ng h???p l???");

            } else {

                if (!syncing) {

                    jQuery('#form_configs_sync_model_prod button[type="submit"]').attr(

                        "disabled",

                        "disabled"

                    );

                    let spinner = jQuery(

                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'

                    );

                    jQuery('#form_configs_sync_model_prod button[type="submit"]').prepend(

                        spinner

                    );

                    let postData = {

                        action: "admin_ajax_request",

                        param: "change_size_per_step_model_prod",

                        size_per_step: step_size,

                    };

                    jQuery.post(ajaxurl, postData, function(res) {

                        jQuery(

                            '#form_configs_sync_model_prod button[type="submit"]'

                        ).removeAttr("disabled");

                        jQuery(

                            '#form_configs_sync_model_prod button[type="submit"] .spinner-border'

                        ).remove();

                        let result = JSON.parse(res);

                        if (result.status == 1) {

                            jQuery("input#size").val(result.data);

                            jQuery(".sync-info .size").text(result.data);

                            jQuery("#collapseThree").collapse("show");

                        } else {

                            alert("C?? l???i x???y ra, vui l??ng quay l???i sau!");

                        }

                    });

                }

            }

        });



        // Sync model_prod

        jQuery("#sync_now_btn").on("click", function() {

            jQuery(this).attr("disabled", "disabled");

            let spinner = jQuery(

                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">'

            );

            jQuery(this).prepend(spinner);

            if (!syncing) {

                sync_model_prod_process();

            }

        });



        function sync_model_prod_process() {

            syncing = true;

            jQuery("#collapseThree .progress").css("display", "block");

            jQuery("#collapseThree .alert-warning").css("display", "block");

            jQuery("#collapseThree .alert-success").css("display", "none");

            jQuery("#collapseThree .alert-danger").css("display", "none");

            jQuery("#collapseThree .sync-process").css("display", "block");

            jQuery("#collapseThree .p_end_at").css("display", "none");

            jQuery("#collapseThree .start_time").text(new Date());



            sync_model_prod_post_data(current_step_sync_model_prod);

        }



        function sync_model_prod_post_data() {

            let postData = {

                action: "admin_ajax_request",

                param: "sync_model_prod",

                step: current_step_sync_model_prod,

            };

            jQuery

                .post(ajaxurl, postData, function(res) {

                let result = JSON.parse(res);

                if (result.status == 1) {

                    update_sync_info();

                    if (current_step_sync_model_prod >= result.data.total_step) {

                        syncing = false;

                        jQuery("#collapseThree .alert-warning").css("display", "none");

                        jQuery("#collapseThree .alert-success").css("display", "block");

                        jQuery("#collapseThree .alert-danger").css("display", "none");



                        jQuery("#collapseThree .sync-process").css("display", "none");

                        jQuery("#sync_now_btn").removeAttr("disabled");

                        jQuery("#sync_now_btn .spinner-border").remove();

                        jQuery("#collapseThree .progress-bar").css("width", "100%");

                        jQuery("#collapseThree .progress-bar").text("100%");

                        jQuery("#collapseThree .p_end_at").css("display", "block");

                        jQuery("#collapseThree .end_time").text(new Date());

                        return;

                    }



                    let total_percent = Math.ceil(

                        (current_step_sync_model_prod / result.data.total_step) * 100

                    );

                    jQuery("#collapseThree .progress-bar").css(

                        "width",

                        total_percent + "%"

                    );

                    jQuery("#collapseThree .progress-bar").text(total_percent + "%");

                    current_step_sync_model_prod = result.data.step;



                    sync_model_prod_post_data(current_step_sync_model_prod);

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



        function update_sync_info() {

            let total_records = +jQuery("input#total_records").val();

            let step_size = +jQuery("input#size").val();

            jQuery(".sync-info .count_synced").text(

                step_size * current_step_sync_model_prod

            );

            jQuery(".sync-info .count_remaining").text(

                total_records - step_size * current_step_sync_model_prod

            );

        }



        $("#collapseThree").on("hide.bs.collapse", function(e) {

            if (syncing) {

                e.preventDefault();

                alert("??ang ?????ng b??? kh??ng th??? th???c hi???n h??nh ?????ng kh??c!");

            }

        });



        window.onbeforeunload = function() {

            if (syncing) {

                return "??ang ?????ng b??? kh??ng ???????c tho??t!";

            }

        };

    });

})(jQuery);