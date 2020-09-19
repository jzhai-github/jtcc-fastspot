/* --------------------------------------------------------------------------
	Home
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function($, Site) {
	var offset;

	function init() {
		setOffset();

		Site.OnResize.push(onResize);
	}

	function setOffset() {
		offset = $(".logo")[0].getBoundingClientRect().left;

		document.documentElement.style.setProperty('--page-offset', offset + "px");
	}

	function onResize() {
		setOffset();
	}

	Site.OnInit.push(init);

})(jQuery, Site);
