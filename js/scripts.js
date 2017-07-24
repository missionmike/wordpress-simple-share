/* global jQuery */
if (typeof jQuery !== "undefined") {

    (function($) {
    
        $(document).ready(function() {
        	
        	$("a.dts_smplshare").hover(function() {
        		$(this).siblings("a.dts_smplshare").addClass("dts_grayscale");
        	}, function() {
        		$("a.dts_smplshare").removeClass("dts_grayscale");
        	});

        });

    })(jQuery);
}
