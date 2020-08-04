/* --------------------------------------------------------------------------
	Page Video
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {

	var $Iframe,
	    VideoParameters,
		VideoQueryString;

	function init() {
		$Iframe = $(".js-page-header-iframe");

		if (!$Iframe.length) {
			return;
		}

		var src = $Iframe.attr("src");

		VideoQueryString = src.split("?");
		VideoParameters = new URLSearchParams("?" + VideoQueryString[1]);

		Site.OnRespond.push(onRespond);
	}

	function onRespond() {
		var autoplay = VideoParameters.get("autoplay");

		if ($(window).width() >= Site.MinLG) {
			if (!autoplay || autoplay === "0" || autoplay === "false") {
				VideoParameters.set("autoplay", "1");

				$Iframe.attr("src", VideoQueryString[0] + "?" + VideoParameters.toString());
			}
		} else {
			if (parseInt(autoplay) === 1 || autoplay === "true") {
				VideoParameters.set("autoplay", "0");

				$Iframe.attr("src", VideoQueryString[0] + "?" + VideoParameters.toString());
			}
		}
	}

	Site.OnInit.push(init);

})(jQuery, Site);