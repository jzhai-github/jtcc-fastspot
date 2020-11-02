/* --------------------------------------------------------------------------
	Audience Nav
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $AudienceLink,
		$AudienceNav,
		$AudienceToggle,
		$CurrentPanel,
		$CurrentTrigger;

	function init() {
		$AudienceToggle = $('.js-audience-toggle');

		if (!$AudienceToggle.length) {
			return;
		}

		$AudienceNav = $('.js-audience-nav');
		$AudienceLink = $('.js-audience-nav-link');

		$AudienceToggle
			.on('activate.swap', onToggleActivate)
			.on('deactivate.swap', onToggleDeactivate)
			.on('keydown', onToggleKeydown)
			.on('click', onToggleClick)
			.attr({
				'aria-haspopup': 'true',
				'aria-expanded': 'false'
			});

		$AudienceNav.attr('aria-hidden', 'true').on('keydown', onNavKeydown);
		$AudienceLink.attr('tabindex', '-1');

		$(document).on('click touchstart', onDocumentClick);
	}

	function closePopup() {
		$CurrentTrigger.attr('aria-expanded', 'false');
		$CurrentPanel.attr('aria-hidden', 'true');
		$CurrentPanel.find('.js-menu-panel-item').attr('tabindex', '-1');
	}

	function onDocumentClick(e) {
		if (!$(e.target).closest('.js-audience-group').length) {
			$AudienceToggle.swap('deactivate');
		}
	}

	function onNavKeydown(e) {
		if ([27, 38, 40, 36, 35].indexOf(e.keyCode) === -1) {
			return;
		}

		var $focusedElement = $(':focus'),
			focusedIndex = $focusedElement
				.closest('.js-audience-nav-item')
				.index(),
			$triggerOpensLink = $CurrentPanel.find('.js-audience-nav-link');

		e.preventDefault();

		switch (e.keyCode) {
			// tab
			case 9:
				$(this)
					.closest('.js-audience-group')
					.find('.js-audience-toggle')
					.swap('deactivate');
				closePopup();

				break;
			// escape
			case 27:
				$(this)
					.closest('.js-audience-group')
					.find('.js-audience-toggle')
					.swap('deactivate');
				closePopup();
				$CurrentTrigger.focus();

				break;
			// up
			case 38:
				if (focusedIndex > 0) {
					$triggerOpensLink.eq(focusedIndex - 1).focus();
				} else {
					$triggerOpensLink.last().focus();
				}

				break;
			// down
			case 40:
				if (
					!$focusedElement
						.closest('.js-audience-nav-item')
						.is(':last-of-type')
				) {
					$triggerOpensLink.eq(focusedIndex + 1).focus();
				} else {
					$triggerOpensLink.first().focus();
				}

				break;
			// home
			case 36:
				$triggerOpensLink.first().focus();

				break;
			// end
			case 35:
				$triggerOpensLink.last().focus();
		}
	}

	function onToggleActivate() {
		$CurrentTrigger = $(this);
		$CurrentTrigger.attr('aria-expanded', 'true');

		$CurrentPanel = $CurrentTrigger
			.closest('.js-audience-group')
			.find('.js-audience-nav');
		$CurrentPanel.attr('aria-hidden', 'false');
		$CurrentPanel
			.find('.js-menu-panel-link')
			.removeAttr('tabindex')
			.first()
			.focus();
	}

	function onToggleClick() {
		var $group = $(this).closest('.js-audience-group');

		$group.find('.js-audience-nav').transition(
			{
				always: false,
				property: 'opacity'
			},
			function () {
				$group.find('.js-audience-nav-link').first().focus();
			}
		);
	}

	function onToggleDeactivate() {
		$CurrentTrigger = $(this);
		$CurrentPanel = $CurrentTrigger
			.closest('.js-audience-group')
			.find('.js-audience-nav');

		closePopup();
	}

	function onToggleKeydown(e) {
		var key = e.keyCode;

		if (key !== 38 && key !== 40) {
			return;
		}

		var $group = $(this).closest('.js-audience-group');

		e.preventDefault();
		$(this).swap('activate');

		switch (key) {
			// up
			case 38:
				if ($(this).attr('aria-expanded') === 'true') {
					$group.find('.js-audience-nav').transition(
						{
							always: false,
							property: 'opacity'
						},
						function () {
							$group.find('.js-audience-nav-link').last().focus();
						}
					);
				}

				break;
			// down
			case 40:
				if ($(this).attr('aria-expanded') === 'true') {
					$group.find('.js-audience-nav').transition(
						{
							always: false,
							property: 'opacity'
						},
						function () {
							$group
								.find('.js-audience-nav-link')
								.first()
								.focus();
						}
					);
				}

				break;
		}
	}

	Site.OnInit.push(init);
})(jQuery, Site);
