/* --------------------------------------------------------------------------
	Menu Button
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $Menu,
		$MenuTrigger,
		$MenuPanel,
		$MenuLink,
		$CurrentTrigger,
		$CurrentPanel;

	function init() {
		$Menu = $('.js-menu-item');

		if (!$Menu.length) {
			return;
		}

		$MenuTrigger = $('.js-menu-trigger');
		$MenuPanel = $('.js-menu-panel');
		$MenuLink = $('.js-menu-panel-link');

		$MenuTrigger
			.attr({
				'aria-haspopup': 'true',
				'aria-expanded': 'false'
			})
			.on('activate.swap', onTriggerActivate)
			.on('deactivate.swap', onTriggerDeactivate)
			.on('keyup', onTriggerKeyup);

		$MenuPanel.attr('aria-hidden', 'true').on('keyup', onPanelKeyup);

		$MenuLink.attr('tabindex', '-1');
	}

	function onTriggerActivate() {
		$CurrentTrigger = $(this);
		$CurrentPanel = $CurrentTrigger
			.closest('.js-menu-item')
			.find('.js-menu-panel');

		openPopup();
	}

	function onTriggerDeactivate() {
		$CurrentTrigger = $(this);
		$CurrentPanel = $CurrentTrigger
			.closest('.js-menu-item')
			.find('.js-menu-panel');

		closePopup();
	}

	function onTriggerKeyup(e) {
		if (e.keyCode === 38) {
			// up
			e.preventDefault();

			if ($(this).attr('aria-expanded') == 'false') {
				$(this).swap('activate');
				$CurrentPanel.find('.js-menu-panel-link').last().focus();
			}
		}

		if (e.keyCode === 40) {
			// down
			e.preventDefault();

			if ($(this).attr('aria-expanded') == 'false') {
				$(this).swap('activate');
			}
		}
	}

	function onPanelKeyup(e) {
		var $focusedElement = $(':focus');
		var focusedIndex = $focusedElement
			.closest('.js-menu-panel-item')
			.index();
		var $triggerOpensLink = $CurrentPanel.find('.js-menu-panel-link');

		if ([27, 38, 40, 36, 35].indexOf(e.keyCode) > -1) {
			e.preventDefault();
		}

		if (e.keyCode === 27) {
			// escape

			$(this)
				.closest('.js-menu-item')
				.find('.js-menu-trigger')
				.swap('deactivate');

			closePopup();
		}

		if (e.keyCode === 38) {
			// up
			if (focusedIndex > 0) {
				$triggerOpensLink.eq(focusedIndex - 1).focus();
			} else {
				$triggerOpensLink.last().focus();
			}
		}

		if (e.keyCode === 40) {
			// down
			if (
				!$focusedElement
					.closest('.js-menu-panel-item')
					.is(':last-of-type')
			) {
				$triggerOpensLink.eq(focusedIndex + 1).focus();
			} else {
				$triggerOpensLink.first().focus();
			}
		}

		if (e.keyCode === 36) {
			// home
			$triggerOpensLink.first().focus();
		}

		if (e.keyCode === 35) {
			// end
			$triggerOpensLink.last().focus();
		}
	}

	function openPopup() {
		$CurrentTrigger.attr('aria-expanded', 'true');

		$CurrentPanel
			.attr('aria-hidden', 'false')
			.find('.js-menu-panel-link')
			.removeAttr('tabindex')
			.first()
			.focus();
	}

	function closePopup() {
		$CurrentTrigger.attr('aria-expanded', 'false').focus();

		$CurrentPanel
			.attr('aria-hidden', 'true')
			.find('.js-menu-panel-item')
			.attr('tabindex', '-1');
	}

	Site.OnInit.push(init);
})(jQuery, Site);
