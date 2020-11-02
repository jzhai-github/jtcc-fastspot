/* --------------------------------------------------------------------------
	Formstone
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var BackgroundOptions, LightboxOptions, NextIcon, PrevIcon;

	function init() {
		PrevIcon = 'caret_left';
		NextIcon = 'caret_right';

		BackgroundOptions = {
			labels: {
				play: 'Play',
				pause: 'Pause'
			},
			icons: {
				play: Site.icon('video_play'),
				pause: Site.icon('video_pause')
			}
		};

		LightboxOptions = {
			videoWidth: 1000,
			labels: {
				close:
					"<span class='fs-lightbox-icon-close'>" +
					Site.icon('close') +
					'</span>',
				previous:
					"<span class='fs-lightbox-icon-previous'>" +
					Site.icon(PrevIcon) +
					'</span>',
				count: "<span class='fs-lightbox-meta-divider'></span>",
				next:
					"<span class='fs-lightbox-icon-next'>" +
					Site.icon(NextIcon) +
					'</span>'
			}
		};

		$('.js-background')
			.on('loaded.background', function () {
				$(this).addClass('fs-background-loaded');
				backgroundVideo(this);
			})
			.background();

		$('.js-equalize').equalize();
		$('.js-lightbox').lightbox(LightboxOptions);
		$('.js-swap').swap();

		var $carousel = $('.js-carousel');
		$carousel.carousel().on('update.carousel', onCarouselUpdate);
		carouselSetup($carousel);
	}

	function backgroundVideo(element) {
		var $background = $(element);

		if ($background.hasClass('js-background-video')) {
			if ($background.find('.fs-background-controls').length === 0) {
				$(
					"<div class='fs-background-controls'>" +
						"<button class='fs-background-control fs-background-control-play fs-background-control-active' aria-pressed='true' aria-label='play'>" +
						"<span class='fs-background-control-icon'>" +
						BackgroundOptions.icons.play +
						'</span>' +
						"<span class='fs-background-control-label'>" +
						BackgroundOptions.labels.play +
						'</span>' +
						'</button>' +
						"<button class='fs-background-control fs-background-control-pause' aria-pressed='false' aria-label='pause'>" +
						"<span class='fs-background-control-icon'>" +
						BackgroundOptions.icons.pause +
						'</span>' +
						"<span class='fs-background-control-label'>" +
						BackgroundOptions.labels.pause +
						'</span>' +
						'</button>' +
						'</div>'
				).appendTo($background);
			}

			$background
				.find('.fs-background-control-play')
				.on('click', onPlayClick);
			$background
				.find('.fs-background-control-pause')
				.on('click', onPauseClick);
		}
	}

	function onPlayClick() {
		var $background = $(this).closest('.js-background-video');

		$background.background('play');

		$background
			.find('.fs-background-control-play')
			.addClass('fs-background-control-active')
			.attr('aria-pressed', 'true');

		$background
			.find('.fs-background-control-pause')
			.removeClass('fs-background-control-active')
			.attr('aria-pressed', 'false');
	}

	function onPauseClick() {
		var $background = $(this).closest('.js-background-video');

		$background.background('pause');

		$background
			.find('.fs-background-control-pause')
			.addClass('fs-background-control-active')
			.attr('aria-pressed', 'true');

		$background
			.find('.fs-background-control-play')
			.removeClass('fs-background-control-active')
			.attr('aria-pressed', 'false');
	}

	function carouselSetup($element) {
		$element.each(function () {
			var $previous_button = $(this).find(
					'.fs-carousel-control_previous'
				),
				previous_text = $previous_button.text(),
				$next_button = $(this).find('.fs-carousel-control_next'),
				next_text = $next_button.text(),
				$gallery_item = $(this).find('.fs-carousel-item');

			$previous_button
				.attr('disabled', '')
				.html(
					"<span class='fs-carousel-control-icon'>" +
						Site.icon(PrevIcon) +
						'</span>' +
						"<span class='fs-carousel-control-label'>" +
						previous_text +
						'</span>'
				);

			$next_button
				.attr('disabled', '')
				.html(
					"<span class='fs-carousel-control-icon'>" +
						Site.icon(NextIcon) +
						'</span>' +
						"<span class='fs-carousel-control-label'>" +
						next_text +
						'</span>'
				);

			if ($previous_button.is('.fs-carousel-visible')) {
				$previous_button.removeAttr('disabled');
			}

			if ($next_button.is('.fs-carousel-visible')) {
				$next_button.removeAttr('disabled');
			}

			$gallery_item.find('a, button').attr('tabindex', '-1');

			$(this)
				.find(
					'.fs-carousel-item.fs-carousel-visible a, .fs-carousel-item.fs-carousel-visible button'
				)
				.removeAttr('tabindex');
		});
	}

	function onCarouselUpdate() {
		var $carousel = $(this);

		$carousel.find('.fs-carousel-control').attr('disabled', '');

		$carousel
			.find('.fs-carousel-item a, .fs-carousel-item button')
			.attr('tabindex', '-1');

		setTimeout(function () {
			$carousel
				.find('.fs-carousel-control.fs-carousel-visible')
				.removeAttr('disabled');

			$carousel
				.find(
					'.fs-carousel-item.fs-carousel-visible a, .fs-carousel-item.fs-carousel-visible button'
				)
				.removeAttr('tabindex');
		}, 0);
	}

	Site.OnInit.push(init);
})(jQuery, Site);
