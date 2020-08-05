/* --------------------------------------------------------------------------
	Background Video
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $Wrapper, VideoPlayer;

	function init() {
		$Wrapper = $('.js-background-video-wrapper');

		if (!$Wrapper.length) {
			return;
		}

		$.getScript('//player.vimeo.com/api/player.js').done(function (script) {
			VideoPlayer = new Vimeo.Player($('.js-background-video-iframe'));

			resizeVideo();

			$('.js-page-header-bg-video-play-button')
				.attr('aria-pressed', 'true')
				.addClass('pressed')
				.on('click', onPlayClick);
			$('.js-page-header-bg-video-pause-button')
				.attr('aria-pressed', 'false')
				.on('click', onPauseClick);

			VideoPlayer.on('loaded', function () {
				setTimeout(function () {
					$Wrapper.addClass('loaded');
				}, 1000);
			});

			Site.OnResize.push(onResize);
		});
	}

	function onResize() {
		resizeVideo();
	}

	function resizeVideo() {
		$Wrapper.each(function () {
			var wrapperWidth = $Wrapper.width();
			var wrapperHeight = $Wrapper.height();

			if (wrapperHeight / wrapperWidth <= 0.5625) {
				$(this)
					.find('.js-background-video-iframe')
					.css({
						width: '100%',
						height: wrapperWidth * 0.5625
					});
			} else {
				$(this)
					.find('.js-background-video-iframe')
					.css({
						width: wrapperHeight / 0.5625,
						height: '100%'
					});
			}
		});
	}

	function onPlayClick() {
		var $videoWrapper = $(this).closest('.js-background-video-wrapper');

		VideoPlayer.play();

		$(this).addClass('pressed').attr('aria-pressed', 'true');

		$videoWrapper
			.find('.js-page-header-bg-video-pause-button')
			.removeClass('pressed')
			.attr('aria-pressed', 'false');
	}

	function onPauseClick() {
		var $videoWrapper = $(this).closest('.js-background-video-wrapper');

		VideoPlayer.pause();

		$(this).addClass('pressed').attr('aria-pressed', 'true');

		$videoWrapper
			.find('.js-page-header-bg-video-play-button')
			.removeClass('pressed')
			.attr('aria-pressed', 'false');
	}

	Site.OnInit.push(init);
})(jQuery, Site);
