/* global jQuery */
if (typeof jQuery !== "undefined") {

    (function($) {

        $(document).ready(function() {


            $("#dts-sortable li").hover(function() {
                $(this).siblings("li").addClass("dts_grayscale");
            }, function() {
                $("#dts-sortable li").removeClass("dts_grayscale");
            });


            $(".dts_sharebar_style_radio").on("change", function() {
                /* Total styles: 2 */
                for (var i = 1; i <= 2; i += 1) {
                    $("#admin_sharebar_example").removeClass("dts_sharebar_style_v" + i);
                }

                $("#admin_sharebar_example").addClass($(this).val());
            });


            $(".dts_sharebar_platform_checkbox").on("change", function() {

                if ($(this).is(":checked")) {
                    $(".dts_smplshare[data-name=\"" + $(this).attr("data-name")).fadeIn();
                } else {
                    $(".dts_smplshare[data-name=\"" + $(this).attr("data-name")).fadeOut();
                }
            });


            $(".dts_sharebar_platform_checkbox").each(function() {

                if ($(this).is(":checked")) {
                    $(".dts_smplshare[data-name=\"" + $(this).attr("data-name")).show();
                } else {
                    $(".dts_smplshare[data-name=\"" + $(this).attr("data-name")).hide();
                }
            });


            $("#dts-sortable").sortable({
                update: function(e, ui) {
                    var order = [];
                    $("#dts-sortable li").each(function(i, el) {
                        var d = $(this).attr("data-name");
                        order[i] = d;
                    });

                    var data = {
                        action: "dts_smplshare_setorder",
                        data: JSON.stringify(order)
                    };

                    $.post(ajaxurl, data, function(response) {

                        $("#dts_order_status").show().removeClass("success error");

                        if (response !== "error") {
                            $("#dts_order_status").html("Saved").addClass("success");
                        } else {
                            $("#dts_order_status").html("Error").addClass("error");
                        }

                        $("#dts_order_status").delay(1000).fadeOut("slow");
                    });
                }
            });

        });

    })(jQuery);
}