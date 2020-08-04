/* --------------------------------------------------------------------------
	Menu
-------------------------------------------------------------------------- */

/* globals jQuery, Page, Site */

(function($, Site) {

	var $Menu,
	    $MenuClose,
	    $MenuToggle,
	    $ShiftingElements,
	    LockClass;

	function init() {
		$Menu = $(".js-menu");

		if (!$Menu.length) {
			return;
		}

		$MenuToggle = $(".js-menu-toggle");
		$MenuClose = $(".js-menu-close-toggle");
		$ShiftingElements = $(".header, .page, .footer");
		LockClass = "fs-page-lock";

		$Menu.find(".js-nav-link, button, input").attr("tabindex", "-1");
		$Menu.attr({
		     "role": "dialog",
		     "aria-modal": "true"
		     })
		     .on("keydown", onMenuKeydown)
		     .on("keyup", onMenuKeyup)
		     .attr("aria-hidden", "true");

		$MenuToggle.on("activate.swap", onMenuSwapActivate)
		           .on("deactivate.swap", onMenuSwapDeactivate)
		           .attr({ "aria-expanded": "false", "role": "button" });

		$MenuClose.on("keydown", onCloseKeydown)
		          .on("click", onMenuSwapDeactivate);

		$(document).on("click touchstart", onDocumentClick);
	}

	function onDocumentClick(e) {
		if ($("body").hasClass(LockClass)) {
			if (!$(e.target).closest(".js-menu").length) {
				$MenuToggle.swap("deactivate");
			}
		}
	}

	function onMenuSwapActivate() {
		$("body").addClass(LockClass);

		Page.saveScrollYPosition();

		$MenuToggle.attr("aria-expanded", "true");

		$Menu.attr({ "aria-hidden": "false", "tabindex": "0" })
		     .transition({ always: false, property: "opacity" }, function() { $Menu.focus(); })
		     .find(".js-nav-link, button, input").removeAttr("tabindex");

		$ShiftingElements.css("padding-right", Page.getScrollbarWidth());

		$Menu.css({
			"margin-right": "",
			"width": ""
		});
	}

	function onMenuSwapDeactivate() {
		$("body").removeClass(LockClass);

		Page.restoreScrollYPosition();

		$Menu.attr("aria-hidden", "true").removeAttr("tabindex")
		     .find(".js-nav-link, button, input").attr("tabindex", "-1");

		$MenuToggle.attr("aria-expanded", "false").focus();

		$ShiftingElements.css("padding-right", "");

		$Menu.css({
			"margin-right": Page.getScrollbarWidth() * -1,
			"width": "calc(100% + " + Page.getScrollbarWidth() + "px)"
		});
	}

	function onCloseKeydown(e) {
		// tab
		if (e.keyCode === 9) {
			if (!(e.shiftKey)) {
				$Menu.focus();
			}
		}
	}

	function onMenuKeydown(e) {
		if ($Menu.is(":focus")) {
			// tab
			if (e.keyCode === 9) {
				if (e.shiftKey) {
					e.preventDefault();

					$MenuClose.focus();
				}
			}
		}
	}

	function onMenuKeyup(e) {
		// escape
		if (e.keyCode === 27) {
			$MenuToggle.swap("deactivate");
		}
	}

	Site.OnInit.push(init);

})(jQuery, Site);