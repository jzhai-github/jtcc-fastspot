import { dirs } from '@/helpers/helpers';

/* --------------------------------------------------------------------------
	Site
-------------------------------------------------------------------------- */

/* globals jQuery */

var Site = (function ($) {
	var HasTouch = false,
		Namespace = '',
		MinXS = '320',
		MinSM = '500',
		MinMD = '740',
		MinLG = '980',
		MinXL = '1220',
		MinXXL = '1330',
		OnInit = [],
		OnRespond = [],
		OnResize = [],
		OnScroll = [];
	var $FixedHeader = null,
		FixedHeaderHeight = null,
		ScrollYPosition = 0;

	function init(ns) {
		Namespace = ns;
		HasTouch = $('html').hasClass('touchevents');

		if (typeof $.mediaquery !== 'undefined') {
			$.mediaquery({
				minWidth: [MinXS, MinSM, MinMD, MinLG, MinXL, MinXXL]
			});
		}

		if (typeof $.cookie !== 'undefined') {
			$.cookie({ path: '/' });
		}

		iterate(OnInit);

		$(window)
			.on('mqchange.mediaquery', function () {
				iterate(OnRespond);
			})
			.on('resize.' + Namespace, function () {
				iterate(OnResize);
			})
			.on('scroll.' + Namespace, function () {
				iterate(OnScroll);
			});

		iterate(OnResize);
		iterate(OnRespond);

		// If you want a fixed header, set $FixedHeader to the DOM element
		// $FixedHeader = $(".header");
		// calculateFixedHeader();

		$('.js-toggle')
			.not('.js-bound')
			.on('click', '.js-toggle-handle', onToggleClick)
			.addClass('js-bound');

		$('.js-scroll-to')
			.not('.js-bound')
			.on('click', onScrollTo)
			.addClass('js-bound');

		// Fix IE SVG references to target an embedded <svg> tag in the DOM rather than externally referenced IDs
		var ua = window.navigator.userAgent,
			msie = ua.indexOf('MSIE ');

		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
			$.get(STATIC_ROOT + 'images/icons.svg', function (data) {
				var $div = $('<div>')
					.hide()
					.html(
						new XMLSerializer().serializeToString(
							data.documentElement
						)
					)
					.appendTo('body');

				$('svg use').each(function () {
					var parts = $(this).attr('xlink:href').split('#');

					$(this).attr('xlink:href', '#' + parts[1]);
				});
			});
		}

		// Wrap iframe videos for responsiveness
		$(
			"iframe[src*='vimeo.com'], iframe[src*='youtube.com']",
			'.typography'
		).each(function () {
			$(this).wrap('<div class="video_frame"></div>');
		});

		// Wrap tables for responsive tables
		$('.typography table').wrap(
			'<div class="table_wrapper"><div class="table_wrapper_inner"></div></div>'
		);

		Formstone.Ready(onPageLoad);
		Site.OnResize.push(onResize);
	}

	// Returns icon markup
	function icon(icon) {
		var markup = '<svg class="icon icon_' + icon + '">',
			ua = window.navigator.userAgent,
			msie = ua.indexOf('MSIE ');

		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
			markup += '<use xlink:href="#' + icon + '">';
		} else {
			markup +=
				'<use xlink:href="' + dirs.STATIC_ROOT + '#' + icon + '">';
		}

		return markup + '</use></svg>';
	}

	// Loop through callbacks
	function iterate(items) {
		for (var i = 0; i < items.length; i++) {
			items[i]();
		}
	}

	function killEvent(e) {
		if (e && e.preventDefault) {
			e.preventDefault();
			e.stopPropagation();
		}
	}

	function onResize() {
		tableOverflow();

		if ($FixedHeader) {
			calculateFixedHeader();
		}
	}

	function calculateFixedHeader() {
		var bt_bar_height = $('#bigtree_bar').outerHeight();
		var wp_bar_height = $('#wpadminbar').outerHeight();

		FixedHeaderHeight = $FixedHeader.outerHeight();

		if (typeof bt_bar_height !== 'undefined' && bt_bar_height) {
			$FixedHeader.css('top', bt_bar_height);

			FixedHeaderHeight = FixedHeaderHeight + bt_bar_height;
		} else if (typeof wp_bar_height !== 'undefined' && wp_bar_height) {
			$FixedHeader.css('top', wp_bar_height);

			FixedHeaderHeight = FixedHeaderHeight + wp_bar_height;
		}
	}

	function onPageLoad() {
		$('body').removeClass('preload').addClass('loaded');
		$(window).trigger('resize');

		if (window.location.hash) {
			scrollToElement(window.location.hash);
		}
	}

	function onScrollTo(e) {
		Site.killEvent(e);

		scrollToElement($(e.delegateTarget).attr('href'));
	}

	function onToggleClick(e) {
		Site.killEvent(e);

		$(e.delegateTarget).toggleClass('js-toggle-active');
	}

	function scrollToElement(id) {
		var offset = $(id).offset();

		if (typeof offset !== 'undefined') {
			scrollToPosition(offset.top);
		}
	}

	function scrollToPosition(top) {
		$('html, body').animate({
			scrollTop: top - FixedHeaderHeight
		});
	}

	function tableOverflow() {
		$('.table_wrapper').each(function () {
			var $inner = $(this).find('.table_wrapper_inner'),
				scrollWidth = $inner.get(0).scrollWidth,
				clientWidth = $inner.get(0).clientWidth;

			if (scrollWidth > clientWidth) {
				$(this)
					.addClass('table_wrapper_overflow')
					.attr({ tabindex: '0', role: 'group' });
			} else {
				$(this)
					.removeClass('table_wrapper_overflow')
					.removeAttr('tabindex role');
			}
		});
	}

	function saveScrollYPosition() {
		ScrollYPosition = window.pageYOffset;

		$('body').css({
			width: '100%',
			position: 'fixed',
			top: ScrollYPosition * -1
		});
	}

	function restoreScrollYPosition() {
		$('body').css({
			width: '',
			position: '',
			top: ''
		});

		$('html, body').scrollTop(ScrollYPosition);
	}

	function getScrollbarWidth() {
		var $outer = $('<div>')
			.css({
				visibility: 'hidden',
				width: '100px',
				msOverflowStyle: 'scrollbar'
			})
			.appendTo('body');

		var no_scroll_width = $outer.outerWidth();

		// force scrollbars
		$outer.css({ overflow: 'scroll' });

		// add inner div and calculate width difference
		var $inner = $('<div>').css({ width: '100%' }).appendTo($outer);
		var width = no_scroll_width - $inner.outerWidth();

		// remove divs
		$outer.remove();

		return width;
	}

	return {
		HasTouch: HasTouch,
		Namespace: Namespace,
		MinXS: MinXS,
		MinSM: MinSM,
		MinMD: MinMD,
		MinLG: MinLG,
		MinXL: MinXL,
		MinXXL: MinXXL,
		OnInit: OnInit,
		OnResize: OnResize,
		OnRespond: OnRespond,
		OnScroll: OnScroll,
		icon: icon,
		init: init,
		killEvent: killEvent,
		getScrollbarWidth: getScrollbarWidth,
		saveScrollYPosition: saveScrollYPosition,
		restoreScrollYPosition: restoreScrollYPosition
	};
})(jQuery);

export default Site;
