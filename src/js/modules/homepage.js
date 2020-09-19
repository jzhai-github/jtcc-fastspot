/* --------------------------------------------------------------------------
	Home
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function($, Site) {

	var $Home,
		WindowHeight,
		scrollTrigger,
		Raf,
		LastScrollTop,
		roundWindowHeight;

	function init() {
		$Home = $(".body_layout_home");

		if (!$Home.length) {
			return;
		}

		Raf = window.requestAnimationFrame;
		LastScrollTop = window.pageYOffset;
		roundWindowHeight = Math.round(window.innerHeight);

		document.documentElement.style.setProperty('--window_height', roundWindowHeight + "px");

		if ("IntersectionObserver" in window &&
			"IntersectionObserverEntry" in window &&
			"intersectionRatio" in window.IntersectionObserverEntry.prototype) {

			var observer = new IntersectionObserver(function(entries) {
				entries.forEach(function(entry) {
					if (entry.isIntersecting === true) {
						entry.target.classList.add("in_view");
					}
				});
			}, {
				threshold: 0.4
			});

			var items = document.querySelectorAll(".js-view-test");

			items.forEach(function(item, index) {
				observer.observe(item, index);
			});
		} else {
			$(".js-view-test").addClass("in_view");
		}

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
		var feature = $(".spotlight")[0].getBoundingClientRect();
		var videoOpacity = 1;

		if ((200 - (feature.top * -1)) >= 0) {
			videoOpacity = (200 - (feature.top * -1)) / 200;
		} else {
			videoOpacity = 0;
		}

		if (feature.bottom <= 0) {
			$(".spotlight_background").css("display", "none")
		} else {
			$(".spotlight_background").css("display", "block")
		}

		$(".spotlight_background").css("opacity", videoOpacity);

		var header = $(".spotlight_header_inner")[0].getBoundingClientRect();
		var stories = $(".stories")[0].getBoundingClientRect();

		if (header.bottom + 40 > stories.top) {
			$(".stories").addClass("visible");
			$(".spotlight_details_link").addClass("visible");
		} else {
			$(".stories").removeClass("visible");
			$(".spotlight_details_link").removeClass("visible");
		}
	}

	function onResize() {
		WindowHeight = $(window).height();
		document.documentElement.style.setProperty('--window_height', WindowHeight + "px");

		var pillow = (WindowHeight / 2) - ($(".spotlight_header_body").innerHeight() / 2) + ($(".header").innerHeight() / 2);
		var space = $(".logo")[0].getBoundingClientRect().left;

		$(".spotlight_header_inner").css({
			paddingTop: pillow
		});
		$(".spotlight_title_group").css({
			left: space * -1
		});
		$(".spotlight_title_label").css({
			paddingLeft: space
		});

		if ($(window).width() >= Site.MinMD) {
			scrollTrigger = 0.25;
		} else {
			scrollTrigger = 0.4;
		}

		onScroll();
	}

	Site.OnInit.push(init);

})(jQuery, Site);
