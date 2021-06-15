/* --------------------------------------------------------------------------
	Module
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	let root = document.documentElement;
	let $Header = $('.header_ribbon');

	function setHeaderProps() {
		if ($Header.length) {
			let HeaderHeight = Math.round($Header.outerHeight());

			root.style.setProperty('--header_height', HeaderHeight + 'px');
		}
	}

	function setProps() {
		setHeaderProps();
	}

	function addEventBindings() {
		$(window).on('load', setProps);

		Site.OnResize.push(setProps);
	}

	function init() {
		setProps();
		addEventBindings();
	}

	Site.OnInit.push(init);
})(jQuery, Site);
