/* --------------------------------------------------------------------------
	Sub Nav
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $SubNavBody, $SubNavInner, $SubNavList, $SubNavToggle;

	function init() {
		$SubNavBody = $('.js-sub-nav-body');

		if (!$SubNavBody.length) {
			return;
		}

		$SubNavInner = $('.js-sub-nav-body-inner');
		$SubNavList = $('.js-sub-nav-list');
		$SubNavToggle = $('.js-sub-nav-toggle');

		$SubNavToggle
			.attr('aria-expanded', 'false')
			.attr('aria-haspopup', 'true')
			.on('activate.swap', onSubSwapActivate)
			.on('deactivate.swap', onSubSwapDeactivate);

		Site.OnResize.push(onResize);

		Site.OnRespond.push(onRespond);
	}

	function onResize() {
		var height = $SubNavInner.innerHeight();

		$SubNavBody.data('height', height);

		if ($SubNavToggle.hasClass('fs-swap-active')) {
			$SubNavBody.css('height', height);
		}
	}

	function onRespond() {
		if ($(window).width() >= Site.MinLG) {
			$SubNavList
				.attr('aria-hidden', 'false')
				.find('a')
				.removeAttr('tabindex');
			$SubNavBody.removeAttr('height');
		} else {
			if ($SubNavToggle.hasClass('fs-swap-active')) {
				$SubNavList
					.attr('aria-hidden', 'false')
					.find('a')
					.removeAttr('tabindex');
			} else {
				$SubNavList
					.attr('aria-hidden', 'true')
					.find('a')
					.attr('tabindex', '-1');
			}
		}
	}

	function onSubSwapActivate() {
		$(this).attr('aria-expanded', 'true');

		$SubNavList
			.attr('aria-hidden', 'false')
			.find('a')
			.removeAttr('tabindex');

		$SubNavBody.css('height', $SubNavBody.data('height'));
	}

	function onSubSwapDeactivate() {
		$(this).attr('aria-expanded', 'false');

		$SubNavList
			.attr('aria-hidden', 'true')
			.find('a')
			.attr('tabindex', '-1');

		$SubNavBody.css('height', '0');
	}

	Site.OnInit.push(init);
})(jQuery, Site);
