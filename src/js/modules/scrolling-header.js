/* --------------------------------------------------------------------------
	Home
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function($, Site) {

	var $Header,
		WindowHeight,
		scrollTrigger,
		Raf,
		LastScrollTop,
		roundWindowHeight;

	function init() {
		$Header = $(".header");

		if (!$Header.length) {
			return;
		}

		Raf = window.requestAnimationFrame;
		LastScrollTop = window.pageYOffset;

		onResize();
		scrollLoop();
		onScroll();

		Site.OnResize.push(onResize);
	}

	function scrollLoop() {
		var scrollTop = window.pageYOffset;

		if (LastScrollTop === scrollTop) {
			Raf(scrollLoop);

			return;
		} else {
			LastScrollTop = scrollTop;

			onScroll();
			Raf(scrollLoop);
		}
	}

	function onScroll() {
		if (window.scrollY > 80) {
			$Header.addClass("scrolling");
		} else {
			$Header.removeClass("scrolling");
		}
	}

	function onResize() {
		onScroll();
	}

	Site.OnInit.push(init);

})(jQuery, Site);
