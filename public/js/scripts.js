/* global jQuery */
if (typeof jQuery !== "undefined") {

    (function($) {

        $(document).ready(function() {

            $("a.dts_smplshare").hover(function() {
                $(this).siblings("a.dts_smplshare").addClass("dts_grayscale");
            }, function() {
                $("a.dts_smplshare").removeClass("dts_grayscale");
            });

            $("a.dts_smplshare_sharelink").on("click", function(e) {

                try {

                    var url = $(this).attr("href"),
                        title = $(this).attr("title"),
                        name = $(this).attr("data-name"),
                        w = 600,
                        h = 400;

                    switch (name) {
                        case "facebook":
                            w = 600;
                            h = 650;
                            break;
                        case "twitter":
                            w = 600;
                            h = 450;
                            break;
                        case "linkedin":
                            w = 600;
                            h = 550;
                            break;
                        case "googleplus":
                            w = 400;
                            h = 500;
                            break;
                        case "email":
                            w = 1024;
                            h = 640;
                            break;
                        case "reddit":
                            w = 1024;
                            h = 640;
                            break;
                    }

                    popupCenter(url, title, w, h);

                    e.preventDefault();
                    return false;
                } catch (e) {
                    return true;
                }

            });
        });

        /* popupCenter() thanks to Thomas Frost: http://www.xtf.dk/2011/08/center-new-popup-window-even-on.html */
        var popupCenter = function(url, title, w, h) {

            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var left = ((width / 2) - (w / 2)) + dualScreenLeft;
            var top = ((height / 2) - (h / 2)) + dualScreenTop;
            var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

            if (window.focus) {
                newWindow.focus();
            }
        };

    })(jQuery);
}