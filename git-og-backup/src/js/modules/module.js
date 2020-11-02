/* --------------------------------------------------------------------------
	Module
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	function init() {
		if (!$('.selector').length) {
			return;
		}
	}

	Site.OnInit.push(init);
})(jQuery, Site);
