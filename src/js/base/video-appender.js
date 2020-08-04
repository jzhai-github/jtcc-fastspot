/*-------------------------------------------
	Video Appender
-------------------------------------------*/

(function($, Site) {

	function init() {
		$(".js-video-appender").on("click", insertVideo);
	}

	function insertVideo(event) {
		var $item = $(this);
		var embed = null;
		var url = $item.attr("href");
		var video_id = null;

		event.preventDefault();

		if (url.indexOf("youtube") !== -1) {
			video_id = parse_youtube_url(url);

			if (video_id) {
				embed = "<iframe class='video_item_iframe' src='https://www.youtube.com/embed/" + video_id + "?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;enablejsapi=1&amp;playsinline=1' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
			}
		} else if (url.indexOf("vimeo") !== -1) {
			video_id = parse_vimeo_url(url);

			if (video_id) {
				embed = "<iframe class='video_item_iframe' src='//player.vimeo.com/video/" + video_id + "?autoplay=1&title=0&byline=0&portrait=0' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><script src='//player.vimeo.com/api/player.js'></script>";
			}
		}

		if (embed) {
			event.preventDefault();
			$item.after(embed).remove();
		}
	}

	function parse_vimeo_url(url) {
		var regExp = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
		var parsedUrl = regExp.exec(url);

		return parsedUrl[5];
	}

	function parse_youtube_url(url) {
		var regExp = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
		var match = url.match(regExp);

		return (match&&match[1].length==11) ? match[1] : false;
	}

	Site.OnInit.push(init);

})(jQuery, Site);
