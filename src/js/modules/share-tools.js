/* --------------------------------------------------------------------------
	Share Tools
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {

	var $Options,
	    $Toggle,
	    $Tools,
	    Description,
	    Title,
	    URL;

	function init() {
		$Tools = $(".js-share-tools");

		if (!$Tools.length) {
			return;
		}

		$Options = $(".js-share-tools-list");
		$Toggle = $(".js-share-tools-toggle");

		URL = window.location.href;
		Title = encodeURIComponent($("title").text());
		Description = $('meta[property="og:description"]').attr("content");

		$(".js-share-facebook").attr("href", "//www.facebook.com/sharer/sharer.php?u=" + URL);
		$(".js-share-twitter").attr("href", "//twitter.com/intent/tweet?text=" + Title + "&url=" + URL);
		$(".js-share-linkedin").attr("href", "//www.linkedin.com/shareArticle?mini=true&url=" + URL + "&title=" + Title);

		$Toggle.on("click", onToggleClick)
		       .on("keydown", onToggleKeydown);

		if (typeof navigator.share === "undefined") {
			$Toggle.attr({ "aria-haspopup": "true", "aria-expanded": "false"}).swap();
			$Options.attr("aria-hidden", "true").on("keydown", onOptionsKeydown);
			$(".js-share-tool").attr("tabindex", "-1");
		}

		function onToggleClick() {
			if (typeof navigator.share === "undefined") {
				setTimeout(function () {
					if ($Tools.hasClass("fs-swap-active")) {
						openOptions();

						$(".js-share-tool-item:first-child").transition({ always: false, property: "opacity" }, function () {
							$Options.find(".js-share-tool").first().focus();
						});
					} else {
						closeOptions();
					}
				}, 0);
			} else {
				navigator.share({
					title: Title,
					text: Description,
					url: URL
				})
			}
		}

		function onToggleKeydown(e) {
			var key = e.keyCode;

			if (key !== 38 && key !== 40) {
				return;
			}

			if (typeof navigator.share === "undefined") {
				e.preventDefault();

				if (!$Tools.hasClass("fs-swap-active")) {
					$Toggle.swap("activate");

					openOptions();
				}

				// up
				if (key === 38) {
					if ($Tools.hasClass("fs-swap-active")) {
						$(".js-share-tool-item:last-child").transition({ always: false, property: "opacity" }, function () {
							$Options.find(".js-share-tool").last().focus();
						});
					}
				// down
				} else {
					if ($Tools.hasClass("fs-swap-active")) {
						$(".js-share-tool-item:first-child").transition({ always: false, property: "opacity" }, function () {
							$Options.find(".js-share-tool").first().focus();
						});
					}
				}
			}
		}

		function onOptionsKeydown(e) {
			var key = e.keyCode;
			var focusedIndex = $(":focus").closest(".js-share-tool-item").index();

			if ([27, 38, 40, 36, 35].indexOf(key) === -1) {
				return;
			}

			e.preventDefault();

			switch (key) {
				// escape
				case 27:
					$Toggle.swap("deactivate").focus();
					closeOptions();

					break;
				// up
				case 38:
					if (focusedIndex > 0) {
						$(".js-share-tool").eq(focusedIndex - 1).focus();
					} else {
						$(".js-share-tool").last().focus();
					}

					break;
				// down
				case 40:
					if (!$(":focus").closest(".js-share-tool-item").is(":last-of-type")) {
						$(".js-share-tool").eq(focusedIndex + 1).focus();
					} else {
						$(".js-share-tool").first().focus();
					}

					break;
				// home
				case 36:
					$(".js-share-tool").first().focus();

					break;
				// end
				case 35:
					$(".js-share-tool").last().focus();

					break;
			}
		}

		function openOptions() {
			$Toggle.attr("aria-expanded", "true");

			$Options.attr("aria-hidden", "false")
			        .find(".js-share-tool").removeAttr("tabindex");
		}

		function closeOptions() {
			$Toggle.attr("aria-expanded", "false").focus();

			$Options.attr("aria-hidden", "true")
			        .find(".js-share-tool").attr("tabindex", "-1");
		}
	}

	Site.OnInit.push(init);

})(jQuery, Site);
