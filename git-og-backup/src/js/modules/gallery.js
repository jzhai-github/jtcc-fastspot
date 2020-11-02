/* --------------------------------------------------------------------------
	Gallery
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $Gallery, $Carousel;

	function init() {
		$Gallery = $('.js-gallery');

		if (!$Gallery.length) {
			return;
		}

		$Carousel = $Gallery.find('.js-carousel');

		$Carousel
			.on('update.carousel', onCarouselUpdate)
			.find('.js-gallery-item-video')
			.each(function () {
				var embed = $(this).data('video-embed');

				if ($(this).data('video-service') == 'youtube') {
					$(this).data('video-embed', embed + '?enablejsapi=1');
				} else if ($(this).data('video-service') == 'vimeo') {
					$(this).data(
						'video-embed',
						embed + '?color=ffffff&title=0&byline=0&portrait=0'
					);
				}
			});

		insertVideo($('.js-gallery-item-video.fs-carousel-visible'));
		insertVideo(
			$('.js-gallery-item.fs-carousel-visible')
				.last()
				.next('.js-gallery-item-video')
		);
	}

	function onCarouselUpdate() {
		insertVideo(
			$Carousel
				.find('.js-gallery-item-video.fs-carousel-visible')
				.last()
				.next('.js-gallery-item-video')
		);

		// pause videos
		$('.js-gallery-iframe-youtube').each(function () {
			$(this)[0].contentWindow.postMessage(
				'{"event": "command", "func": "pauseVideo", "args": ""}',
				'*'
			);
		});

		$('.js-gallery-iframe-vimeo').each(function () {
			$(this)[0].contentWindow.postMessage(
				{
					method: 'pause'
				},
				$(this)[0].src
			);
		});
	}

	function insertVideo(element) {
		if (!$(element).hasClass('video_loaded')) {
			var embed = $(element).data('video-embed');

			$(element).find('.js-gallery-iframe').attr('src', embed);
			$(element).addClass('video_loaded');
		}
	}

	Site.OnInit.push(init);
})(jQuery, Site);
