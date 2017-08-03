/* global jQuery */
if (typeof jQuery !== "undefined") {

    (function($) {
    
        $(document).ready(function() {

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

        });

    })(jQuery);
}
