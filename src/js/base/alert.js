/* --------------------------------------------------------------------------
	Alert - BigTree
-------------------------------------------------------------------------- */

/* globals AlertURL, jQuery, Site */

(function ($, Site) {
	var $Alert,
		$CloseButton,
		$OpenButton,
		$SkipLink,
		CookieName,
		CookieValue,
		Time;

	function init() {
		// Do not use this Javascript if we're using AJAX alerts
		if (typeof AlertURL !== 'undefined') {
			return;
		}

		$Alert = $('.js-alert');
		$SkipLink = $('.js-skip-alert');

		if (!$Alert.length) {
			$SkipLink.hide();

			return;
		}

		$Alert.addClass('enabled');
		$CloseButton = $('.js-alert-close');
		$OpenButton = $('.js-alert-open');
		Time = $Alert.data('time');
		CookieName = 'framework-alert';
		CookieValue = $.cookie(CookieName);

		if (CookieValue) {
			CookieValue = JSON.parse(CookieValue);
		} else {
			CookieValue = [];
		}

		$SkipLink.on('click', onOpenClick);
		$CloseButton.on('click', onCloseClick);
		$OpenButton.addClass('enabled').on('click', onOpenClick);

		// Not hidden, show it and hide the open button
		if (CookieValue.indexOf(Time) === -1) {
			alertOpen();
		} else {
			alertClose();
		}
	}

	function alertClose() {
		$OpenButton.addClass('visible');
		$Alert.removeClass('visible').attr('aria-hidden', 'true');
		$Alert.find('a, button').attr('tabindex', '-1');
	}

	function alertOpen() {
		$OpenButton.removeClass('visible');
		$Alert.addClass('visible').attr('aria-hidden', 'false');
		$Alert.find('a, button').removeAttr('tabindex');
	}

	function onCloseClick(ev) {
		ev.preventDefault();

		CookieValue.push(Time);

		$.cookie(CookieName, JSON.stringify(CookieValue), {
			path: '/',
			expires: 1000 * 365 * 24 * 60 * 60
		});

		alertClose();
		$CloseButton.blur();
	}

	function onOpenClick(ev) {
		ev.preventDefault();

		// Allow for the skip link to jump to this even if it's already open
		if ($Alert.hasClass('visible')) {
			$Alert.focus();

			return;
		}

		var cleaned_cookie_val = [];

		for (var i = 0; i < CookieValue.length; i++) {
			if (CookieValue[i] !== Time) {
				cleaned_cookie_val.push(CookieValue[i]);
			}
		}

		CookieValue = cleaned_cookie_val;

		$.cookie(CookieName, CookieValue, {
			path: '/',
			expires: 1000 * 365 * 24 * 60 * 60
		});

		alertOpen();

		$Alert.transition(
			{
				always: false,
				property: 'transform'
			},
			function () {
				$Alert.focus();
			}
		);
	}

	Site.OnInit.push(init);
})(jQuery, Site);
