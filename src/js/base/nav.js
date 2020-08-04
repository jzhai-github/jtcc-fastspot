/* --------------------------------------------------------------------------
	Navigation
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function($, Site) {

	var $ActiveItem,
		$ActiveToggle,
		$Children,
		$ChildLink,
	    $Toggle;

	function init() {
		if (!$(".js-nav").length) {
			return;
		}

		$Children = $(".js-nav-children");
		$Children.attr("aria-hidden", "true");

		$ChildLink = $(".js-nav-child-link");
		$ChildLink.attr("tabindex", "-1");
		$ChildLink.on("keydown", onChildLinkKeydown);

		$Toggle = $(".js-nav-toggle");
		$Toggle.on("activate.swap", onToggleSwapActivate)
		       .on("deactivate.swap", onToggleSwapDeactivate)
		       .on("keydown", onToggleKeydown);
	}

	function onChildLinkKeydown(e) {
		var key = e.keyCode;

		if ([27, 38, 40].indexOf(key) === -1) {
			return;
		}

		var $childItem = $(this).closest(".js-nav-child-item");

		e.preventDefault();

		switch (key) {
			// tab
			case 9:
				$ActiveToggle.swap("deactivate");

				break;
			// escape
			case 27:
				$ActiveToggle.swap("deactivate").focus();

				break;
			// up
			case 38:
				$childItem.prev(".js-nav-child-item").find(".js-nav-child-link").focus();

				break;
			// down
			case 40:
				$childItem.next(".js-nav-child-item").find(".js-nav-child-link").focus();

				break;
		}
	}

	function onToggleKeydown(e) {
		var key = e.keyCode;

		if (key !== 27 || key !== 40) {
			return;
		}

		e.preventDefault();

		// escape
		if (key === 27) {
			$(this).swap("deactivate");
		// down
		} else {
			$(this).swap("activate");
		}
	}

	function onToggleSwapActivate() {
		$ActiveToggle = $(this);
		$ActiveItem = $ActiveToggle.closest(".js-nav-item");

		$ActiveToggle.attr("aria-expanded", "true");
		$ActiveItem.find(".js-nav-children")
		           .attr("aria-hidden", "false")
		           .transition({ always: false, property: "opacity" }, function() {
		               $ActiveItem.find(".js-nav-child-link").first().focus();
		           });
		$ActiveItem.find(".js-nav-child-link").removeAttr("tabindex").first().focus();
	}

	function onToggleSwapDeactivate() {
		$ActiveToggle = $(this);
		$ActiveItem = $ActiveToggle.closest(".js-nav-item");

		$ActiveToggle.attr("aria-expanded", "false");
		$ActiveItem.find(".js-nav-children").attr("aria-hidden", "true");
		$ActiveItem.find(".js-nav-child-link").attr("tabindex", "-1");
	}

	Site.OnInit.push(init);

})(jQuery, Site);