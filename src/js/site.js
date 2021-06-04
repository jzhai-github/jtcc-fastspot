! function(e) { var t = {};

				function i(n) { if (t[n]) return t[n].exports; var a = t[n] = { i: n, l: !1, exports: {} }; return e[n].call(a.exports, a, a.exports, i), a.l = !0, a.exports } i.m = e, i.c = t, i.d = function(e, t, n) { i.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: n }) }, i.r = function(e) { "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 }) }, i.t = function(e, t) { if (1 & t && (e = i(e)), 8 & t) return e; if (4 & t && "object" == typeof e && e && e.__esModule) return e; var n = Object.create(null); if (i.r(n), Object.defineProperty(n, "default", { enumerable: !0, value: e }), 2 & t && "string" != typeof e)
												for (var a in e) i.d(n, a, function(t) { return e[t] }.bind(null, a)); return n }, i.n = function(e) { var t = e && e.__esModule ? function() { return e.default } : function() { return e }; return i.d(t, "a", t), t }, i.o = function(e, t) { return Object.prototype.hasOwnProperty.call(e, t) }, i.p = "", i(i.s = 8) }([function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [core.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1)], void 0 === (o = "function" == typeof(n = function(e) { "use strict"; var t, i, n, a = "undefined" != typeof window ? window : this,
												o = a.document,
												s = function() { this.Version = "1.4.18", this.Plugins = {}, this.DontConflict = !1, this.Conflicts = { fn: {} }, this.ResizeHandlers = [], this.RAFHandlers = [], this.window = a, this.$window = e(a), this.document = o, this.$document = e(o), this.$body = null, this.windowWidth = 0, this.windowHeight = 0, this.fallbackWidth = 1024, this.fallbackHeight = 768, this.userAgent = window.navigator.userAgent || window.navigator.vendor || window.opera, this.isFirefox = /Firefox/i.test(this.userAgent), this.isChrome = /Chrome/i.test(this.userAgent), this.isSafari = /Safari/i.test(this.userAgent) && !this.isChrome, this.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(this.userAgent), this.isIEMobile = /IEMobile/i.test(this.userAgent), this.isFirefoxMobile = this.isFirefox && this.isMobile, this.transform = null, this.transition = null, this.support = { file: !!(window.File && window.FileList && window.FileReader), history: !!(window.history && window.history.pushState && window.history.replaceState), matchMedia: !(!window.matchMedia && !window.msMatchMedia), pointer: !!window.PointerEvent, raf: !(!window.requestAnimationFrame || !window.cancelAnimationFrame), touch: !!("ontouchstart" in window || window.DocumentTouch && document instanceof window.DocumentTouch), transition: !1, transform: !1 } },
												r = { killEvent: function(e, t) { try { e.preventDefault(), e.stopPropagation(), t && e.stopImmediatePropagation() } catch (e) {} }, killGesture: function(e) { try { e.preventDefault() } catch (e) {} }, lockViewport: function(i) { f[i] = !0, e.isEmptyObject(f) || h || (t.length ? t.attr("content", n) : t = e("head").append('<meta name="viewport" content="' + n + '">'), l.$body.on(u.gestureChange, r.killGesture).on(u.gestureStart, r.killGesture).on(u.gestureEnd, r.killGesture), h = !0) }, unlockViewport: function(n) { "undefined" !== e.type(f[n]) && delete f[n], e.isEmptyObject(f) && h && (t.length && (i ? t.attr("content", i) : t.remove()), l.$body.off(u.gestureChange).off(u.gestureStart).off(u.gestureEnd), h = !1) }, startTimer: function(e, t, i, n) { return r.clearTimer(e), n ? setInterval(i, t) : setTimeout(i, t) }, clearTimer: function(e, t) { e && (t ? clearInterval(e) : clearTimeout(e), e = null) }, sortAsc: function(e, t) { return parseInt(e, 10) - parseInt(t, 10) }, sortDesc: function(e, t) { return parseInt(t, 10) - parseInt(e, 10) }, decodeEntities: function(e) { var t = l.document.createElement("textarea"); return t.innerHTML = e, t.value }, parseQueryString: function(e) { for (var t = {}, i = e.slice(e.indexOf("?") + 1).split("&"), n = 0; n < i.length; n++) { var a = i[n].split("=");
																								t[a[0]] = a[1] } return t } },
												l = new s,
												c = e.Deferred(),
												d = { base: "{ns}", element: "{ns}-element" },
												u = { namespace: ".{ns}", beforeUnload: "beforeunload.{ns}", blur: "blur.{ns}", change: "change.{ns}", click: "click.{ns}", dblClick: "dblclick.{ns}", drag: "drag.{ns}", dragEnd: "dragend.{ns}", dragEnter: "dragenter.{ns}", dragLeave: "dragleave.{ns}", dragOver: "dragover.{ns}", dragStart: "dragstart.{ns}", drop: "drop.{ns}", error: "error.{ns}", focus: "focus.{ns}", focusIn: "focusin.{ns}", focusOut: "focusout.{ns}", gestureChange: "gesturechange.{ns}", gestureStart: "gesturestart.{ns}", gestureEnd: "gestureend.{ns}", input: "input.{ns}", keyDown: "keydown.{ns}", keyPress: "keypress.{ns}", keyUp: "keyup.{ns}", load: "load.{ns}", mouseDown: "mousedown.{ns}", mouseEnter: "mouseenter.{ns}", mouseLeave: "mouseleave.{ns}", mouseMove: "mousemove.{ns}", mouseOut: "mouseout.{ns}", mouseOver: "mouseover.{ns}", mouseUp: "mouseup.{ns}", panStart: "panstart.{ns}", pan: "pan.{ns}", panEnd: "panend.{ns}", resize: "resize.{ns}", scaleStart: "scalestart.{ns}", scaleEnd: "scaleend.{ns}", scale: "scale.{ns}", scroll: "scroll.{ns}", select: "select.{ns}", swipe: "swipe.{ns}", touchCancel: "touchcancel.{ns}", touchEnd: "touchend.{ns}", touchLeave: "touchleave.{ns}", touchMove: "touchmove.{ns}", touchStart: "touchstart.{ns}" },
												p = null,
												f = [],
												h = !1;

								function g(e, t, i, n) { var a, o = { raw: {} }; for (a in n = n || {}) n.hasOwnProperty(a) && ("classes" === e ? (o.raw[n[a]] = t + "-" + n[a], o[n[a]] = "." + t + "-" + n[a]) : (o.raw[a] = n[a], o[a] = n[a] + "." + t)); for (a in i) i.hasOwnProperty(a) && ("classes" === e ? (o.raw[a] = i[a].replace(/{ns}/g, t), o[a] = i[a].replace(/{ns}/g, "." + t)) : (o.raw[a] = i[a].replace(/.{ns}/g, ""), o[a] = i[a].replace(/{ns}/g, t))); return o }

								function m() { l.windowWidth = l.$window.width(), l.windowHeight = l.$window.height(), p = r.startTimer(p, 20, v) }

								function v() { for (var e in l.ResizeHandlers) l.ResizeHandlers.hasOwnProperty(e) && l.ResizeHandlers[e].callback.call(window, l.windowWidth, l.windowHeight) }

								function b(e, t) { return parseInt(e.priority) - parseInt(t.priority) } return s.prototype.NoConflict = function() { for (var t in l.DontConflict = !0, l.Plugins) l.Plugins.hasOwnProperty(t) && (e[t] = l.Conflicts[t], e.fn[t] = l.Conflicts.fn[t]) }, s.prototype.Ready = function(e) { "complete" === l.document.readyState || "loading" !== l.document.readyState && !l.document.documentElement.doScroll ? e() : l.document.addEventListener("DOMContentLoaded", e) }, s.prototype.Plugin = function(t, i) { return l.Plugins[t] = function(t, i) { var n = "fs-" + t,
																								a = "fs" + t.replace(/(^|\s)([a-z])/g, (function(e, t, i) { return t + i.toUpperCase() }));

																				function o(n) { var o, r, l, c = "object" === e.type(n),
																												d = Array.prototype.slice.call(arguments, c ? 1 : 0),
																												u = this,
																												p = e(); for (n = e.extend(!0, {}, i.defaults || {}, c ? n : {}), r = 0, l = u.length; r < l; r++)
																												if (!s(o = u.eq(r))) { i.guid++; var f = "__" + i.guid,
																																				h = i.classes.raw.base + f,
																																				g = o.data(t + "-options"),
																																				m = e.extend(!0, { $el: o, guid: f, numGuid: i.guid, rawGuid: h, dotGuid: "." + h }, n, "object" === e.type(g) ? g : {});
																																o.addClass(i.classes.raw.element).data(a, m), i.methods._construct.apply(o, [m].concat(d)), p = p.add(o) } for (r = 0, l = p.length; r < l; r++) o = p.eq(r), i.methods._postConstruct.apply(o, [s(o)]); return u }

																				function s(e) { return e.data(a) } return i.initialized = !1, i.priority = i.priority || 10, i.classes = g("classes", n, d, i.classes), i.events = g("events", t, u, i.events), i.functions = e.extend({ getData: s, iterate: function(t) { for (var i = this, n = Array.prototype.slice.call(arguments, 1), a = 0, o = i.length; a < o; a++) { var r = i.eq(a),
																																				l = s(r) || {}; "undefined" !== e.type(l.$el) && t.apply(r, [l].concat(n)) } return i } }, r, i.functions), i.methods = e.extend(!0, { _construct: e.noop, _postConstruct: e.noop, _destruct: e.noop, _resize: !1, destroy: function(e) { i.functions.iterate.apply(this, [i.methods._destruct].concat(Array.prototype.slice.call(arguments, 1))), this.removeClass(i.classes.raw.element).removeData(a) } }, i.methods), i.utilities = e.extend(!0, { _initialize: !1, _delegate: !1, defaults: function(t) { i.defaults = e.extend(!0, i.defaults, t || {}) } }, i.utilities), i.widget && (l.Conflicts.fn[t] = e.fn[t], e.fn[a] = function(t) { if (this instanceof e) { var n = i.methods[t]; if ("object" === e.type(t) || !t) return o.apply(this, arguments); if (n && 0 !== t.indexOf("_")) { var a = [n].concat(Array.prototype.slice.call(arguments, 1)); return i.functions.iterate.apply(this, a) } return this } }, l.DontConflict || (e.fn[t] = e.fn[a])), l.Conflicts[t] = e[t], e[a] = i.utilities._delegate || function(t) { var n = i.utilities[t] || i.utilities._initialize || !1; if (n) { var a = Array.prototype.slice.call(arguments, "object" === e.type(t) ? 0 : 1); return n.apply(window, a) } }, l.DontConflict || (e[t] = e[a]), i.namespace = t, i.namespaceClean = a, i.guid = 0, i.methods._resize && (l.ResizeHandlers.push({ namespace: t, priority: i.priority, callback: i.methods._resize }), l.ResizeHandlers.sort(b)), i.methods._raf && (l.RAFHandlers.push({ namespace: t, priority: i.priority, callback: i.methods._raf }), l.RAFHandlers.sort(b)), i }(t, i), l.Plugins[t] }, l.$window.on("resize.fs", m), m(),
												function e() { if (l.support.raf)
																				for (var t in l.window.requestAnimationFrame(e), l.RAFHandlers) l.RAFHandlers.hasOwnProperty(t) && l.RAFHandlers[t].callback.call(window) }(), l.Ready((function() { l.$body = e("body"), e("html").addClass(l.support.touch ? "touchevents" : "no-touchevents"), t = e('meta[name="viewport"]'), i = !!t.length && t.attr("content"), n = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0", c.resolve() })), u.clickTouchStart = u.click + " " + u.touchStart,
												function() { var e, t = { WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "otransitionend", transition: "transitionend" },
																				i = ["transition", "-webkit-transition"],
																				n = { transform: "transform", MozTransform: "-moz-transform", OTransform: "-o-transform", msTransform: "-ms-transform", webkitTransform: "-webkit-transform" },
																				a = "transitionend",
																				o = "",
																				s = "",
																				r = document.createElement("div"); for (e in t)
																				if (t.hasOwnProperty(e) && e in r.style) { a = t[e], l.support.transition = !0; break } for (e in u.transitionEnd = a + ".{ns}", i)
																				if (i.hasOwnProperty(e) && i[e] in r.style) { o = i[e]; break } for (e in l.transition = o, n)
																				if (n.hasOwnProperty(e) && n[e] in r.style) { l.support.transform = !0, s = n[e]; break } l.transform = s }(), window.Formstone = l, l }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t) { e.exports = jQuery }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [mediaquery.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() {! function() { for (var e in u = { unit: s.unit }, h)
																				if (h.hasOwnProperty(e))
																								for (var t in f[e])
																												if (f[e].hasOwnProperty(t)) { var i = "Infinity" === t ? 1 / 0 : parseInt(t, 10),
																																				n = e.indexOf("max") > -1;
																																f[e][t].matches && (n ? (!u[e] || i < u[e]) && (u[e] = i) : (!u[e] || i > u[e]) && (u[e] = i)) } }(), l.trigger(r.mqChange, [u]) }

								function n(e) { var t = a(e.media),
																i = p[t],
																n = e.matches,
																o = n ? r.enter : r.leave; if (i && (i.active || !i.active && n)) { for (var s in i[o]) i[o].hasOwnProperty(s) && i[o][s].apply(i.mq);
																i.active = !0 } }

								function a(e) { return e.replace(/[^a-z0-9\s]/gi, "").replace(/[_\s]/g, "").replace(/^\s+|\s+$/g, "") } var o = t.Plugin("mediaquery", { utilities: { _initialize: function(t) { for (var n in t = t || {}, h) h.hasOwnProperty(n) && (s[n] = t[n] ? e.merge(t[n], s[n]) : s[n]); for (var a in (s = e.extend(s, t)).minWidth.sort(d.sortDesc), s.maxWidth.sort(d.sortAsc), s.minHeight.sort(d.sortDesc), s.maxHeight.sort(d.sortAsc), h)
																												if (h.hasOwnProperty(a))
																																for (var o in f[a] = {}, s[a])
																																				if (s[a].hasOwnProperty(o)) { var r = window.matchMedia("(" + h[a] + ": " + (s[a][o] === 1 / 0 ? 1e5 : s[a][o]) + s.unit + ")");
																																								r.addListener(i), f[a][s[a][o]] = r } i() }, state: function() { return u }, bind: function(e, t, i) { var o = c.matchMedia(t),
																												s = a(o.media); for (var l in p[s] || (p[s] = { mq: o, active: !0, enter: {}, leave: {} }, p[s].mq.addListener(n)), i) i.hasOwnProperty(l) && p[s].hasOwnProperty(l) && (p[s][l][e] = i[l]); var d = p[s],
																												u = o.matches;
																								u && d[r.enter].hasOwnProperty(e) ? (d[r.enter][e].apply(o), d.active = !0) : !u && d[r.leave].hasOwnProperty(e) && (d[r.leave][e].apply(o), d.active = !1) }, unbind: function(e, t) { if (e)
																												if (t) { var i = a(t);
																																p[i] && (p[i].enter[e] && delete p[i].enter[e], p[i].leave[e] && delete p[i].leave[e]) } else
																																for (var n in p) p.hasOwnProperty(n) && (p[n].enter[e] && delete p[n].enter[e], p[n].leave[e] && delete p[n].leave[e]) } }, events: { mqChange: "mqchange" } }),
												s = { minWidth: [0], maxWidth: [1 / 0], minHeight: [0], maxHeight: [1 / 0], unit: "px" },
												r = e.extend(o.events, { enter: "enter", leave: "leave" }),
												l = t.$window,
												c = l[0],
												d = o.functions,
												u = null,
												p = [],
												f = {},
												h = { minWidth: "min-width", maxWidth: "max-width", minHeight: "min-height", maxHeight: "max-height" } }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { "use strict";
				i.d(t, "a", (function() { return n })), i.d(t, "b", (function() { return a })); var n = { STATIC_ROOT: "/", WWW_ROOT: "/" },
								a = function(e) { e.keys().forEach(e) } }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [transition.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i(t) { t.stopPropagation(), t.preventDefault(); var i = t.data,
																a = t.originalEvent,
																o = i.target ? i.$target : i.$el;
												i.property && a.propertyName !== i.property || !e(a.target).is(o) || n(i) }

								function n(e) { e.always || e.$el[o.namespaceClean]("destroy"), e.callback.apply(e.$el) }

								function a(t) { var i, n, a, o = {}; if (t instanceof e && (t = t[0]), l.getComputedStyle)
																for (var s = 0, r = (i = l.getComputedStyle(t, null)).length; s < r; s++) n = i[s], a = i.getPropertyValue(n), o[n] = a;
												else if (t.currentStyle)
																for (n in i = t.currentStyle) o[n] = i[n]; return o } var o = t.Plugin("transition", { widget: !0, defaults: { always: !1, property: null, target: null }, methods: { _construct: function(o, l) { if (l) { o.$target = this.find(o.target), o.$check = o.target ? o.$target : this, o.callback = l, o.styles = a(o.$check), o.timer = null; var c = o.$check.css(t.transition + "-duration"),
																																d = parseFloat(c);
																												t.support.transition && c && d ? this.on(s.transitionEnd, o, i) : o.timer = r.startTimer(o.timer, 50, (function() {! function(t) { var i = a(t.$check);
																																				(function(t, i) { if (e.type(t) !== e.type(i)) return !1; for (var n in t) { if (!t.hasOwnProperty(n)) return !1; if (!t.hasOwnProperty(n) || !i.hasOwnProperty(n) || t[n] !== i[n]) return !1 } return !0 })(t.styles, i) || n(t), t.styles = i }(o) }), !0) } }, _destruct: function(e) { r.clearTimer(e.timer, !0), this.off(s.namespace) }, resolve: n } }),
												s = o.events,
												r = o.functions,
												l = t.window }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { "use strict";
				i.r(t); var n = i(3),
								a = function(e) { var t = !1,
																i = "",
																o = [],
																s = [],
																r = [],
																l = [],
																c = null,
																d = 0;

												function u(e) { for (var t = 0; t < e.length; t++) e[t]() }

												function p() { e(".table_wrapper").each((function() { var t = e(this).find(".table_wrapper_inner");
																				t.get(0).scrollWidth > t.get(0).clientWidth ? e(this).addClass("table_wrapper_overflow").attr({ tabindex: "0", role: "group" }) : e(this).removeClass("table_wrapper_overflow").removeAttr("tabindex role") })) }

												function f() { e("body").removeClass("preload").addClass("loaded"), e(window).trigger("resize"), window.location.hash && m(window.location.hash) }

												function h(t) { a.killEvent(t), m(e(t.delegateTarget).attr("href")) }

												function g(t) { a.killEvent(t), e(t.delegateTarget).toggleClass("js-toggle-active") }

												function m(t) { var i, n = e(t).offset();
																void 0 !== n && (i = n.top, e("html, body").animate({ scrollTop: i - c })) } return { HasTouch: t, Namespace: i, MinXS: "320", MinSM: "500", MinMD: "740", MinLG: "980", MinXL: "1220", MinXXL: "1330", OnInit: o, OnResize: r, OnRespond: s, OnScroll: l, icon: function(e) { var t = '<svg class="icon icon_' + e + '">'; return window.navigator.userAgent.indexOf("MSIE ") > 0 || navigator.userAgent.match(/Trident.*rv\:11\./) ? t += '<use xlink:href="#' + e + '">' : t += '<use xlink:href="' + n.a.STATIC_ROOT + "#" + e + '">', t + "</use></svg>" }, init: function(n) { i = n, t = e("html").hasClass("touchevents"), void 0 !== e.mediaquery && e.mediaquery({ minWidth: ["320", "500", "740", "980", "1220", "1330"] }), void 0 !== e.cookie && e.cookie({ path: "/" }), u(o), e(window).on("mqchange.mediaquery", (function() { u(s) })).on("resize." + i, (function() { u(r) })).on("scroll." + i, (function() { u(l) })), u(r), u(s), e(".js-toggle").not(".js-bound").on("click", ".js-toggle-handle", g).addClass("js-bound"), e(".js-scroll-to").not(".js-bound").on("click", h).addClass("js-bound"), (window.navigator.userAgent.indexOf("MSIE ") > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) && e.get(STATIC_ROOT + "images/icons.svg", (function(t) { e("<div>").hide().html((new XMLSerializer).serializeToString(t.documentElement)).appendTo("body");
																								e("svg use").each((function() { var t = e(this).attr("xlink:href").split("#");
																												e(this).attr("xlink:href", "#" + t[1]) })) })), e("iframe[src*='vimeo.com'], iframe[src*='youtube.com']", ".typography").each((function() { e(this).wrap('<div class="video_frame"></div>') })), e(".typography table").wrap('<div class="table_wrapper"><div class="table_wrapper_inner"></div></div>'), Formstone.Ready(f), a.OnResize.push(p) }, killEvent: function(e) { e && e.preventDefault && (e.preventDefault(), e.stopPropagation()) }, getScrollbarWidth: function() { var t = e("<div>").css({ visibility: "hidden", width: "100px", msOverflowStyle: "scrollbar" }).appendTo("body"),
																								i = t.outerWidth();
																				t.css({ overflow: "scroll" }); var n = i - e("<div>").css({ width: "100%" }).appendTo(t).outerWidth(); return t.remove(), n }, saveScrollYPosition: function() { d = window.pageYOffset, e("body").css({ width: "100%", position: "fixed", top: -1 * d }) }, restoreScrollYPosition: function() { e("body").css({ width: "", position: "", top: "" }), e("html, body").scrollTop(d) } } }(jQuery);
				t.default = a }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [touch.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i(e) { e.preventManipulation && e.preventManipulation(); var t = e.data,
																i = e.originalEvent; if (i.type.match(/(up|end|cancel)$/i)) o(e);
												else { if (i.pointerId) { var s = !1; for (var r in t.touches) t.touches[r].id === i.pointerId && (s = !0, t.touches[r].pageX = i.pageX, t.touches[r].pageY = i.pageY);
																				s || t.touches.push({ id: i.pointerId, pageX: i.pageX, pageY: i.pageY }) } else t.touches = i.touches;
																i.type.match(/(down|start)$/i) ? n(e) : i.type.match(/move$/i) && a(e) } }

								function n(n) { var s = n.data,
																r = "undefined" !== e.type(s.touches) && s.touches.length ? s.touches[0] : null;
												r && s.$el.off(h.mouseDown), s.touching || (s.startE = n.originalEvent, s.startX = r ? r.pageX : n.pageX, s.startY = r ? r.pageY : n.pageY, s.startT = (new Date).getTime(), s.scaleD = 1, s.passedAxis = !1), s.$links && s.$links.off(h.click); var u = l(s.scale ? h.scaleStart : h.panStart, n, s.startX, s.startY, s.scaleD, 0, 0, "", ""); if (s.scale && s.touches && s.touches.length >= 2) { var p = s.touches;
																s.pinch = { startX: c(p[0].pageX, p[1].pageX), startY: c(p[0].pageY, p[1].pageY), startD: d(p[1].pageX - p[0].pageX, p[1].pageY - p[0].pageY) }, u.pageX = s.startX = s.pinch.startX, u.pageY = s.startY = s.pinch.startY } s.touching || (s.touching = !0, s.pan && !r && m.on(h.mouseMove, s, a).on(h.mouseUp, s, o), t.support.pointer ? m.on([h.pointerMove, h.pointerUp, h.pointerCancel].join(" "), s, i) : m.on([h.touchMove, h.touchEnd, h.touchCancel].join(" "), s, i), s.$el.trigger(u)) }

								function a(t) { var i = t.data,
																n = "undefined" !== e.type(i.touches) && i.touches.length ? i.touches[0] : null,
																a = n ? n.pageX : t.pageX,
																s = n ? n.pageY : t.pageY,
																r = a - i.startX,
																u = s - i.startY,
																p = r > 0 ? "right" : "left",
																f = u > 0 ? "down" : "up",
																m = Math.abs(r) > i.threshold,
																v = Math.abs(u) > i.threshold; if (!i.passedAxis && i.axis && (i.axisX && v || i.axisY && m)) o(t);
												else {!i.passedAxis && (!i.axis || i.axis && i.axisX && m || i.axisY && v) && (i.passedAxis = !0), i.passedAxis && (g.killEvent(t), g.killEvent(i.startE)); var b = !0,
																				y = l(i.scale ? h.scale : h.pan, t, a, s, i.scaleD, r, u, p, f); if (i.scale)
																				if (i.touches && i.touches.length >= 2) { var w = i.touches;
																								i.pinch.endX = c(w[0].pageX, w[1].pageX), i.pinch.endY = c(w[0].pageY, w[1].pageY), i.pinch.endD = d(w[1].pageX - w[0].pageX, w[1].pageY - w[0].pageY), i.scaleD = i.pinch.endD / i.pinch.startD, y.pageX = i.pinch.endX, y.pageY = i.pinch.endY, y.scale = i.scaleD, y.deltaX = i.pinch.endX - i.pinch.startX, y.deltaY = i.pinch.endY - i.pinch.startY } else i.pan || (b = !1);
																b && i.$el.trigger(y) } }

								function o(t) { var i = t.data,
																a = "undefined" !== e.type(i.touches) && i.touches.length ? i.touches[0] : null,
																o = a ? a.pageX : t.pageX,
																r = a ? a.pageY : t.pageY,
																c = o - i.startX,
																d = r - i.startY,
																u = (new Date).getTime(),
																p = i.scale ? h.scaleEnd : h.panEnd,
																f = c > 0 ? "right" : "left",
																v = d > 0 ? "down" : "up",
																b = Math.abs(c) > 1,
																y = Math.abs(d) > 1; if (i.swipe && u - i.startT < i.time && Math.abs(c) > i.threshold && (p = h.swipe), i.axis && (i.axisX && y || i.axisY && b) || b || y) { i.$links = i.$el.find("a"); for (var w = 0, $ = i.$links.length; w < $; w++) s(i.$links.eq(w), i) } var x = l(p, t, o, r, i.scaleD, c, d, f, v);
												m.off([h.touchMove, h.touchEnd, h.touchCancel, h.mouseMove, h.mouseUp, h.pointerMove, h.pointerUp, h.pointerCancel].join(" ")), i.$el.trigger(x), i.touches = [], i.scale, a && (i.touchTimer = g.startTimer(i.touchTimer, 5, (function() { i.$el.on(h.mouseDown, i, n) }))), i.touching = !1 }

								function s(t, i) { t.on(h.click, i, r); var n = e._data(t[0], "events").click;
												n.unshift(n.pop()) }

								function r(e) { g.killEvent(e, !0), e.data.$links.off(h.click) }

								function l(t, i, n, a, o, s, r, l, c) { return e.Event(t, { originalEvent: i, bubbles: !0, pageX: n, pageY: a, scale: o, deltaX: s, deltaY: r, directionX: l, directionY: c }) }

								function c(e, t) { return (e + t) / 2 }

								function d(e, t) { return Math.sqrt(e * e + t * t) }

								function u(e, t) { e.css({ "-ms-touch-action": t, "touch-action": t }) } var p = !t.window.PointerEvent,
												f = t.Plugin("touch", { widget: !0, defaults: { axis: !1, pan: !1, scale: !1, swipe: !1, threshold: 10, time: 50 }, methods: { _construct: function(e) { if (e.touches = [], e.touching = !1, this.on(h.dragStart, g.killEvent), e.swipe && (e.pan = !0), e.scale && (e.axis = !1), e.axisX = "x" === e.axis, e.axisY = "y" === e.axis, t.support.pointer) { var a = "";!e.axis || e.axisX && e.axisY ? a = "none" : (e.axisX && (a += " pan-y"), e.axisY && (a += " pan-x")), u(this, a), this.on(h.pointerDown, e, i) } else this.on(h.touchStart, e, i).on(h.mouseDown, e, n) }, _destruct: function(e) { this.off(h.namespace), u(this, "") } }, events: { pointerDown: p ? "MSPointerDown" : "pointerdown", pointerUp: p ? "MSPointerUp" : "pointerup", pointerMove: p ? "MSPointerMove" : "pointermove", pointerCancel: p ? "MSPointerCancel" : "pointercancel" } }),
												h = f.events,
												g = f.functions,
												m = t.$window;
								h.pan = "pan", h.panStart = "panstart", h.panEnd = "panend", h.scale = "scale", h.scaleStart = "scalestart", h.scaleEnd = "scaleend", h.swipe = "swipe" }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [viewer.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(4)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { S.scrollTop(), t.windowHeight }

								function n() {
												(P = e(T.base)).length ? z.lockViewport(_) : z.unlockViewport(_) }

								function a(t, i, n) { t.isAnimating || (t.isAnimating = !0, t.$container = e('<div class="' + M.container + '"><img></div>'), t.$image = t.$container.find("img"), t.$viewport.append(t.$container), t.$image.one(I.load, (function() {! function(e) {
																				(function(e) { var t, i, n, a = (t = e.$image, i = t[0], n = new Image, void 0 !== i.naturalHeight ? { naturalHeight: i.naturalHeight, naturalWidth: i.naturalWidth } : "img" === i.tagName.toLowerCase() && (n.src = i.src, { naturalHeight: n.height, naturalWidth: n.width }));
																								e.naturalHeight = a.naturalHeight, e.naturalWidth = a.naturalWidth, e.ratioHorizontal = e.naturalHeight / e.naturalWidth, e.ratioVertical = e.naturalWidth / e.naturalHeight, e.isWide = e.naturalWidth > e.naturalHeight })(e), s(e), e.containerTop = e.viewportHeight / 2, e.containerLeft = e.viewportWidth / 2, l(e), e.imageHeight = e.naturalHeight, e.imageWidth = e.naturalWidth,
																								function(e) { e.imageHeight = e.imageMinHeight, e.imageWidth = e.imageMinWidth }(e), r(e), c(e), d(e), u(e); var t = { containerTop: e.containerTop, containerLeft: e.containerLeft, imageHeight: e.imageHeight, imageWidth: e.imageWidth, imageTop: e.imageTop, imageLeft: e.imageLeft };
																				m(e, t), e.isRendering = !0 }(t), t.isAnimating = !1, t.$container.fsTransition({ property: "opacity" }, (function() {})), t.$el.removeClass(M.loading), t.$container.fsTouch({ pan: !0, scale: !0 }).on(I.scaleStart, t, v).on(I.scaleEnd, t, y).on(I.scale, t, b), t.$el.trigger(I.loaded) })).one(I.error, t, o).attr("src", i).addClass(M.image), (t.$image[0].complete || 4 === t.$image[0].readyState) && t.$image.trigger(I.load), t.source = i) }

								function o(e) { e.data.$el.trigger(I.error) }

								function s(e) { e.viewportHeight = e.$viewport.outerHeight(), e.viewportWidth = e.$viewport.outerWidth() }

								function r(e) { e.imageHeight <= e.viewportHeight ? (e.containerMinTop = e.viewportHeight / 2, e.containerMaxTop = e.viewportHeight / 2) : (e.containerMinTop = e.viewportHeight - e.imageHeight / 2, e.containerMaxTop = e.imageHeight / 2), e.imageWidth <= e.viewportWidth ? (e.containerMinLeft = e.viewportWidth / 2, e.containerMaxLeft = e.viewportWidth / 2) : (e.containerMinLeft = e.viewportWidth - e.imageWidth / 2, e.containerMaxLeft = e.imageWidth / 2) }

								function l(e) { e.isWide ? (e.imageMinWidth = e.viewportWidth, e.imageMinHeight = e.imageMinWidth * e.ratioHorizontal, e.imageMinHeight > e.viewportHeight && (e.imageMinHeight = e.viewportHeight, e.imageMinWidth = e.imageMinHeight * e.ratioVertical)) : (e.imageMinHeight = e.viewportHeight, e.imageMinWidth = e.imageMinHeight * e.ratioVertical, e.imageMinWidth > e.viewportWidth && (e.imageMinWidth = e.viewportWidth, e.imageMinHeight = e.imageMinWidth * e.ratioHorizontal)), (e.imageMinWidth > e.naturalWidth || e.imageMinHeight > e.naturalHeight) && (e.imageMinHeight = e.naturalHeight, e.imageMinWidth = e.naturalWidth), e.imageMaxHeight = e.naturalHeight, e.imageMaxWidth = e.naturalWidth }

								function c(e) { e.imageTop = -e.imageHeight / 2, e.imageLeft = -e.imageWidth / 2 }

								function d(e) { e.lastContainerTop = e.containerTop, e.lastContainerLeft = e.containerLeft, e.lastImageHeight = e.imageHeight, e.lastImageWidth = e.imageWidth, e.lastImageTop = e.imageTop, e.lastImageLeft = e.imageLeft }

								function u(e) { e.renderContainerTop = e.lastContainerTop, e.renderContainerLeft = e.lastContainerLeft, e.renderImageTop = e.lastImageTop, e.renderImageLeft = e.lastImageLeft, e.renderImageHeight = e.lastImageHeight, e.renderImageWidth = e.lastImageWidth }

								function p(e) { e.imageHeight < e.imageMinHeight && (e.imageHeight = e.imageMinHeight), e.imageHeight > e.imageMaxHeight && (e.imageHeight = e.imageMaxHeight), e.imageWidth < e.imageMinWidth && (e.imageWidth = e.imageMinWidth), e.imageWidth > e.imageMaxWidth && (e.imageWidth = e.imageMaxWidth) }

								function f(e) { e.containerTop < e.containerMinTop && (e.containerTop = e.containerMinTop), e.containerTop > e.containerMaxTop && (e.containerTop = e.containerMaxTop), e.containerLeft < e.containerMinLeft && (e.containerLeft = e.containerMinLeft), e.containerLeft > e.containerMaxLeft && (e.containerLeft = e.containerMaxLeft) }

								function h(e) { null === e.tapTimer ? e.tapTimer = z.startTimer(e.tapTimer, 500, (function() { g(e) })) : (g(e), function(e) { var t = e.imageHeight > e.imageMinHeight + 1;
																e.isZooming = !0, d(e), u(e), t ? (e.imageHeight = e.imageMinHeight, e.imageWidth = e.imageMinWidth) : (e.imageHeight = e.imageMaxHeight, e.imageWidth = e.imageMaxWidth), r(e), f(e), c(e), e.isRendering = !0 }(e)) }

								function g(e) { z.clearTimer(e.tapTimer), e.tapTimer = null }

								function m(e, i) { if (t.transform) { var n = i.imageWidth / e.naturalWidth,
																				a = i.imageHeight / e.naturalHeight;
																e.$container.css(t.transform, "translate3d(" + i.containerLeft + "px, " + i.containerTop + "px, 0)"), e.$image.css(t.transform, "translate3d(-50%, -50%, 0) scale(" + n + "," + a + ")") } else e.$container.css({ top: i.containerTop, left: i.containerLeft }), e.$image.css({ height: i.imageHeight, width: i.imageWidth, top: i.imageTop, left: i.imageLeft }) }

								function v(e) { var t = e.data;
												h(t), d(t) }

								function b(e) { var t = e.data;
												g(t), t.isRendering = !1, t.isZooming = !1, t.imageHeight, t.imageMinHeight, t.containerTop = t.lastContainerTop + e.deltaY, t.containerLeft = t.lastContainerLeft + e.deltaX, t.imageHeight = t.lastImageHeight * e.scale, t.imageWidth = t.lastImageWidth * e.scale, r(t), f(t), p(t), c(t), m(t, { containerTop: t.containerTop, containerLeft: t.containerLeft, imageHeight: t.imageHeight, imageWidth: t.imageWidth, imageTop: t.imageTop, imageLeft: t.imageLeft }) }

								function y(e) { var t = e.data;
												t.isZooming || (d(t), u(t), t.isRendering = !0) }

								function w(t) { z.killEvent(t); var i = e(t.currentTarget),
																n = t.data; "out" == (i.hasClass(M.control_zoom_out) ? "out" : "in") ? function(e) { e.keyDownTime = 1, e.action = "zoom_out" }(n) : function(e) { e.keyDownTime = 1, e.action = "zoom_in" }(n) }

								function $(e) { e.data.action = !1 }

								function x(e) { if (e.isRendering) { if (e.action) { e.keyDownTime += e.zoomIncrement; var t = ("zoom_out" === e.action ? -1 : 1) * Math.round(e.imageWidth * e.keyDownTime - e.imageWidth);
																				t > e.zoomDelta && (t = e.zoomDelta), e.isWide ? (e.imageWidth += t, e.imageHeight = Math.round(e.imageWidth / e.ratioVertical)) : (e.imageHeight += t, e.imageWidth = Math.round(e.imageHeight / e.ratioHorizontal)), p(e), c(e), r(e), f(e) } e.renderContainerTop += Math.round((e.containerTop - e.renderContainerTop) * e.zoomEnertia), e.renderContainerLeft += Math.round((e.containerLeft - e.renderContainerLeft) * e.zoomEnertia), e.renderImageTop += Math.round((e.imageTop - e.renderImageTop) * e.zoomEnertia), e.renderImageLeft += Math.round((e.imageLeft - e.renderImageLeft) * e.zoomEnertia), e.renderImageHeight += Math.round((e.imageHeight - e.renderImageHeight) * e.zoomEnertia), e.renderImageWidth += Math.round((e.imageWidth - e.renderImageWidth) * e.zoomEnertia), m(e, { containerTop: e.renderContainerTop, containerLeft: e.renderContainerLeft, imageHeight: e.renderImageHeight, imageWidth: e.renderImageWidth, imageTop: e.renderImageTop, imageLeft: e.renderImageLeft }) } }

								function C(e, t) { e.isAnimating || (g(e), e.isAnimating = !0, e.isRendering = !1, e.isZooming = !1, function(e) { e.$container && e.$container.length && e.$container.fsTouch("destroy").off(I.scaleStart, v).off(I.scaleEnd, y).off(I.scale, b) }(e), e.$container.fsTransition({ property: "opacity" }, (function() { e.isAnimating = !1, e.$container.remove(), "function" == typeof t && t.call(window, e) })), e.$el.addClass(M.loading)) }

								function k(t) { z.killEvent(t); var i = e(t.currentTarget),
																n = t.data,
																o = n.index + (i.hasClass(M.control_next) ? 1 : -1);
												n.isAnimating || (o < 0 && (o = 0), o > n.total && (o = n.total), o !== n.index && (n.index = o, C(n, (function() { a(n, n.images[n.index]) }))), H(n)) }

								function H(e) { e.$controlItems.removeClass(M.control_disabled), 0 === e.index && e.$controlPrevious.addClass(M.control_disabled), e.index === e.images.length - 1 && e.$controlNext.addClass(M.control_disabled) }

								function j(e) { s(e), r(e), l(e), c(e), r(e), f(e), p(e) } var W = t.Plugin("viewer", { widget: !0, defaults: { controls: !0, customClass: "", labels: { count: "of", next: "Next", previous: "Previous", zoom_in: "Zoom In", zoom_out: "Zoom Out" }, theme: "fs-light", zoomDelta: 100, zoomEnertia: .2, zoomIncrement: .01 }, classes: ["source", "wrapper", "viewport", "container", "image", "gallery", "loading_icon", "controls", "controls_custom", "control", "control_previous", "control_next", "control_zoom_in", "control_zoom_out", "control_disabled", "loading"], events: { loaded: "loaded", ready: "ready" }, methods: { _construct: function(t) { var i, o = "",
																												s = [M.control, M.control_previous].join(" "),
																												r = [M.control, M.control_next].join(" "),
																												l = [M.control, M.control_zoom_in].join(" "),
																												c = [M.control, M.control_zoom_out].join(" ");
																								t.thisClasses = [M.base, M.loading, t.customClass, t.theme], t.images = [], t.source = !1, t.gallery = !1, t.tapTimer = null, t.action = !1, t.isRendering = !1, t.isZooming = !1, t.isAnimating = !1, t.keyDownTime = 1, t.$images = this.find("img").addClass(M.source), t.index = 0, t.total = t.$images.length - 1, t.customControls = "object" === e.type(t.controls) && t.controls.zoom_in && t.controls.zoom_out, t.$images.length > 1 && (t.gallery = !0, t.thisClasses.push(M.gallery), !t.customControls || t.controls.previous && t.controls.next || (t.customControls = !1)); for (var d = 0; d < t.$images.length; d++) i = t.$images.eq(d), t.images.push(i.attr("src"));
																								o += '<div class="' + M.wrapper + '">', o += '<div class="' + M.loading_icon + '"></div>', o += '<div class="' + M.viewport + '"></div>', o += "</div>", t.controls && !t.customControls && (o += '<div class="' + M.controls + '">', o += '<button type="button" class="' + s + '">' + t.labels.previous + "</button>", o += '<button type="button" class="' + c + '">' + t.labels.zoom_out + "</button>", o += '<button type="button" class="' + l + '">' + t.labels.zoom_in + "</button>", o += '<button type="button" class="' + r + '">' + t.labels.next + "</button>", o += "</div>"), this.addClass(t.thisClasses.join(" ")).prepend(o), t.$wrapper = this.find(T.wrapper), t.$viewport = this.find(T.viewport), t.customControls ? (t.$controls = e(t.controls.container).addClass([M.controls, M.controls_custom].join(" ")), t.$controlPrevious = e(t.controls.previous).addClass(s), t.$controlNext = e(t.controls.next).addClass(r), t.$controlZoomIn = e(t.controls.zoom_in).addClass(l), t.$controlZoomOut = e(t.controls.zoom_out).addClass(c)) : (t.$controls = this.find(T.controls), t.$controlPrevious = this.find(T.control_previous), t.$controlNext = this.find(T.control_next), t.$controlZoomIn = this.find(T.control_zoom_in), t.$controlZoomOut = this.find(T.control_zoom_out)), t.$controlItems = t.$controlPrevious.add(t.$controlNext), t.$controlZooms = t.$controlZoomIn.add(t.$controlZoomOut), n(), t.$controlItems.on(I.click, t, k), t.$controlZooms.on([I.touchStart, I.mouseDown].join(" "), t, w).on([I.touchEnd, I.mouseUp].join(" "), t, $), a(t, t.images[t.index]), H(t) }, _destruct: function(e) { e.$wrapper.remove(), e.$image.removeClass(M.source), e.controls && !e.customControls && e.$controls.remove(), e.customControls && (e.$controls.removeClass([M.controls, M.controls_custom].join(" ")), e.$controlItems.off(I.click).removeClass([M.control, M.control_previous, M.control_next].join(" ")), e.$controlZooms.off([I.touchStart, I.mouseDown].join(" ")).off([I.touchStart, I.mouseDown].join(" ")).off([I.touchEnd, I.mouseUp].join(" ")).removeClass([M.control, M.control_zoom_in, M.control_zoom_out].join(" "))), this.removeClass(e.thisClasses.join(" ")).off(I.namespace), n() }, _resize: function() { z.iterate.call(P, j) }, _raf: function() { z.iterate.call(P, x) }, resize: j, load: function(e, t) { e.index = 0, "string" == typeof t ? (e.total = 0, e.images = [t], e.gallery = !1, e.$el.removeClass(M.gallery)) : (e.total = t.length - 1, e.images = t, t.length > 1 && (e.gallery = !0, e.$el.addClass(M.gallery)), t = e.images[e.index]), C(e, (function() { a(e, t) })) }, unload: function(e) { C(e) } } }),
												_ = W.namespace,
												T = W.classes,
												M = T.raw,
												I = W.events,
												z = W.functions,
												S = (t.window, t.$window),
												P = [];
								t.Ready((function() { i(), S.on("scroll", i), t.$body })) }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { e.exports = i(43) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [analytics.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(2)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { h.scrollDepth && l() }

								function n(t) {!w && v && v.length && (w = !0, (h = e.extend(h, t || {})).autoEvents && v.find("a").not("[" + x + "]").each(o), h.scrollDepth && (l(), m.on(y.scroll, s).one(y.load, i)), v.on(y.click, "*[" + x + "]", c)) }

								function a() { w && v && v.length && (m.off(y.namespace), v.off(y.namespace), w = !1) }

								function o() { var t, i = e(this),
																n = "undefined" !== e.type(i[0].href) ? i[0].href : "",
																a = document.domain.split(".").reverse(),
																o = null !== n.match(a[1] + "." + a[0]);
												n.match(/^mailto\:/i) ? t = "Email, Click, " + n.replace(/^mailto\:/i, "") : n.match(/^tel\:/i) ? t = "Telephone, Click, " + n.replace(/^tel\:/i, "") : n.match(h.fileTypes) ? t = "File, Download:" + (/[.]/.exec(n) ? /[^.]+$/.exec(n) : void 0)[0] + ", " + n.replace(/ /g, "-") : o || (t = "ExternalLink, Click, " + n), t && i.attr(x, t) }

								function s(e) { b.startTimer(k, 250, r) }

								function r() { for (var i, n = m.scrollTop() + t.windowHeight, a = 1 / h.scrollStops, o = a, s = 1; s <= h.scrollStops; s++) i = Math.round(100 * o).toString(), !C[H][i].passed && n > C[H][i].edge && (C[H][i].passed = !0, d(e.extend(h.scrollFields, { eventCategory: "ScrollDepth", eventAction: H, eventLabel: i, nonInteraction: !0 }))), o += a }

								function l() { var t, i = e.mediaquery("state"),
																n = v.outerHeight(),
																a = {},
																o = 1 / h.scrollStops,
																s = o,
																r = 0;
												i.minWidth && (H = "MinWidth:" + i.minWidth + "px"); for (var l = 1; l <= h.scrollStops; l++) r = parseInt(n * s), a[t = Math.round(100 * s).toString()] = { edge: "100" === t ? r - 10 : r, passsed: !(!C[H] || !C[H][t]) && C[H][t].passed }, s += o;
												C[H] = a }

								function c(t) { var i = e(this),
																n = i.attr("href"),
																a = i.data($).split(","); for (var o in h.eventCallback && t.preventDefault(), a) a.hasOwnProperty(o) && (a[o] = e.trim(a[o]));
												d({ eventCategory: a[0], eventAction: a[1], eventLabel: a[2] || n, eventValue: a[3], nonInteraction: a[4] }, i) }

								function d(t, i) { g.location; var n = e.extend({ hitType: "event" }, t); if ("undefined" !== e.type(i) && !i.attr("data-analytics-stop")) { var a = "undefined" !== e.type(i[0].href) ? i[0].href : "",
																				o = !a.match(/^mailto\:/i) && !a.match(/^tel\:/i) && a.indexOf(":") < 0 ? g.location.protocol + "//" + g.location.hostname + "/" + a : a; if ("" !== o) { var s = i.attr("target");
																				s ? g.open(o, s) : h.eventCallback && (n.hitCallback = function() { j && (b.clearTimer(j), function(e) { document.location = e }(o)) }, j = b.startTimer(j, h.eventTimeout, n.hitCallback)) } } p(n) }

								function u(t) { p(e.extend({ hitType: "pageview" }, t)) }

								function p(t) { if ("function" === e.type(g.ga) && "function" === e.type(g.ga.getAll))
																for (var i = g.ga.getAll(), n = 0, a = i.length; n < a; n++) g.ga(i[n].get("name") + ".send", t) } var f = t.Plugin("analytics", { methods: { _resize: i }, utilities: { _delegate: function() { if (arguments.length && "object" !== e.type(arguments[0]))
																												if ("destroy" === arguments[0]) a.apply(this);
																												else { var t = Array.prototype.slice.call(arguments, 1); switch (arguments[0]) {
																																				case "pageview":
																																								u.apply(this, t); break;
																																				case "event":
																																								d.apply(this, t) } } else n.apply(this, arguments); return null } } }),
												h = { autoEvents: !1, fileTypes: /\.(zip|exe|dmg|pdf|doc.*|xls.*|ppt.*|mp3|txt|rar|wma|mov|avi|wmv|flv|wav)$/i, eventCallback: !1, eventTimeout: 1e3, scrollDepth: !1, scrollStops: 5, scrollFields: {} },
												g = t.window,
												m = t.$window,
												v = null,
												b = f.functions,
												y = f.events,
												w = !1,
												$ = "analytics-event",
												x = "data-" + $,
												C = {},
												k = null,
												H = "Site",
												j = null;
								t.Ready((function() { v = t.$body })) }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [background.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(4)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() {
												(k = C.scrollTop() + t.windowHeight) < 0 && (k = 0), $.iterate.call(j, g) }

								function n() { H = e(b.base), j = e(b.lazy), $.iterate.call(j, h) }

								function a(e) { if (e.visible) { var t = e.source;
																e.source = null, o(e, t, !0) } }

								function o(t, i, n) { if (i !== t.source && t.visible) { if (t.source = i, t.responsive = !1, t.isYouTube = !1, "object" === e.type(i) && "string" === e.type(i.video)) { var a = i.video.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i);
																				a && a.length >= 1 && (t.isYouTube = !0, t.videoId = a[1]) } var o = !t.isYouTube && "object" === e.type(i) && (i.hasOwnProperty("mp4") || i.hasOwnProperty("ogg") || i.hasOwnProperty("webm")); if (t.video = t.isYouTube || o, t.playing = !1, t.isYouTube) t.playerReady = !1, t.posterLoaded = !1, l(t, i, n);
																else if ("object" === e.type(i) && i.hasOwnProperty("poster")) ! function(t, i, n) { t.source && t.source.poster && (r(t, t.source.poster, !0, !0), n = !1); var a = '<div class="' + [y.media, y.video, !0 !== n ? y.animated : ""].join(" ") + '" aria-hidden="true">';
																				a += "<video playsinline", t.loop && (a += " loop"), t.mute && (a += " muted"), t.autoPlay && (a += " autoplay"), a += ">", t.source.webm && (a += '<source src="' + t.source.webm + '" type="video/webm" />'), t.source.mp4 && (a += '<source src="' + t.source.mp4 + '" type="video/mp4" />'), t.source.ogg && (a += '<source src="' + t.source.ogg + '" type="video/ogg" />'), a += "</video>"; var o = e(a += "</div>");
																				o.find("video").one(w.loadedMetaData, (function(e) { o.fsTransition({ property: "opacity" }, (function() { c(t) })).css({ opacity: 1 }), f(t), t.$el.trigger(w.loaded), t.autoPlay && u(t) })), t.$container.append(o) }(t, 0, n);
																else { var d = i; if ("object" === e.type(i)) { var p, h = [],
																												g = []; for (p in i) i.hasOwnProperty(p) && g.push(p); for (p in g.sort($.sortAsc), g) g.hasOwnProperty(p) && h.push({ width: parseInt(g[p]), url: i[g[p]], mq: x.matchMedia("(min-width: " + parseInt(g[p]) + "px)") });
																								t.responsive = !0, t.sources = h, d = s(t) } r(t, d, !1, n) } } else t.$el.trigger(w.loaded) }

								function s(e) { var i = e.source; if (e.responsive)
																for (var n in i = e.sources[0].url, e.sources) e.sources.hasOwnProperty(n) && (t.support.matchMedia ? e.sources[n].mq.matches && (i = e.sources[n].url) : e.sources[n].width < t.fallbackWidth && (i = e.sources[n].url)); return i }

								function r(t, i, n, a) { var o = [y.media, y.image, !0 !== a ? y.animated : ""].join(" "),
																s = e('<div class="' + o + '" aria-hidden="true"><img alt="' + t.alt + '"></div>'),
																r = s.find("img"),
																l = i;
												r.one(w.load, (function() { W && s.addClass(y.native).css({ backgroundImage: "url('" + l + "')" }), s.fsTransition({ property: "opacity" }, (function() { n || c(t) })).css({ opacity: 1 }), f(t), n && !a || t.$el.trigger(w.loaded) })).one(w.error, t, d).attr("src", l), t.responsive && s.addClass(y.responsive), t.$container.append(s), (r[0].complete || 4 === r[0].readyState) && r.trigger(w.load), t.currentSource = l }

								function l(t, i, n) { if (!t.videoId) { var a = i.match(/^.*(?:youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/);
																t.videoId = a[1] } if (t.posterLoaded || (t.source.poster || (t.source.poster = "//img.youtube.com/vi/" + t.videoId + "/0.jpg"), t.posterLoaded = !0, r(t, t.source.poster, !0, n), n = !1), e("script[src*='youtube.com/iframe_api']").length || e("head").append('<script src="//www.youtube.com/iframe_api"><\/script>'), _) { var o = t.guid + "_" + t.youTubeGuid++,
																				s = '<div class="' + [y.media, y.embed, !0 !== n ? y.animated : ""].join(" ") + '" aria-hidden="true">';
																s += '<div id="' + o + '"></div>'; var l = e(s += "</div>"),
																				u = e.extend(!0, {}, { controls: 0, rel: 0, showinfo: 0, wmode: "transparent", enablejsapi: 1, version: 3, playerapiid: o, loop: t.loop ? 1 : 0, autoplay: 1, mute: 1, origin: x.location.protocol + "//" + x.location.host }, t.youtubeOptions);
																u.autoplay = 1, t.$container.append(l), t.player && (t.oldPlayer = t.player, t.player = null), t.player = new x.YT.Player(o, { videoId: t.videoId, playerVars: u, events: { onReady: function(e) { t.playerReady = !0, t.mute && t.player.mute(), t.autoPlay ? t.player.playVideo() : t.player.pauseVideo() }, onStateChange: function(e) { t.playing || e.data !== x.YT.PlayerState.PLAYING ? t.loop && t.playing && e.data === x.YT.PlayerState.ENDED && t.player.playVideo() : (t.playing = !0, l.fsTransition({ property: "opacity" }, (function() { c(t) })).css({ opacity: 1 }), f(t), t.$el.trigger(w.loaded)), t.$el.find(b.embed).addClass(y.ready) }, onPlaybackQualityChange: function(e) {}, onPlaybackRateChange: function(e) {}, onError: function(e) { d({ data: t }) }, onApiChange: function(e) {} } }), f(t) } else T.push({ data: t, source: i }) }

								function c(e) { var t = e.$container.find(b.media);
												t.length >= 1 && (t.not(":last").remove(), e.oldPlayer = null) }

								function d(e) { e.data.$el.trigger(w.error) }

								function u(e) { if (e.video && !e.playing)
																if (e.isYouTube) e.playerReady ? e.player.playVideo() : e.autoPlay = !0;
																else { var t = e.$container.find("video");
																				t.length && t[0].play(), e.playing = !0 } }

								function p(e) { if (e.visible)
																if (e.responsive) { var t = s(e);
																				t !== e.currentSource ? r(e, t, !1, !0) : f(e) } else f(e) }

								function f(e) { for (var t = e.$container.find(b.media), i = 0, n = t.length; i < n; i++) { var a = t.eq(i),
																				o = e.isYouTube ? "iframe" : a.find("video").length ? "video" : "img",
																				s = a.find(o); if (s.length && ("img" !== o || !W)) { var r = e.$el.outerWidth(),
																								l = e.$el.outerHeight(),
																								c = m(e, s);
																				e.width = c.width, e.height = c.height, e.left = 0, e.top = 0; var d = e.isYouTube ? e.embedRatio : e.width / e.height;
																				e.height = l, e.width = e.height * d, e.width < r && (e.width = r, e.height = e.width / d), e.left = -(e.width - r) / 2, e.top = -(e.height - l) / 2, a.css({ height: e.height, width: e.width, left: e.left, top: e.top }) } } }

								function h(e) { e.scrollTop = e.$el.offset().top }

								function g(e) {!e.visible && e.scrollTop < k + e.lazyEdge && (e.visible = !0, a(e)) }

								function m(t, i) { if (t.isYouTube) return { height: 500, width: 500 / t.embedRatio }; if (i.is("img")) { var n = i[0]; if ("undefined" !== e.type(n.naturalHeight)) return { height: n.naturalHeight, width: n.naturalWidth }; var a = new Image; return a.src = n.src, { height: a.height, width: a.width } } return { height: i[0].videoHeight, width: i[0].videoWidth } } var v = t.Plugin("background", { widget: !0, defaults: { alt: "", autoPlay: !0, customClass: "", embedRatio: 1.777777, lazy: !1, lazyEdge: 100, loop: !0, mute: !0, source: null, youtubeOptions: {} }, classes: ["container", "media", "animated", "responsive", "native", "fixed", "ready", "lazy"], events: { loaded: "loaded", ready: "ready", loadedMetaData: "loadedmetadata" }, methods: { _construct: function(t) { t.youTubeGuid = 0, t.$container = e('<div class="' + y.container + '"></div>').appendTo(this), t.thisClasses = [y.base, t.customClass], t.visible = !0, t.lazy && (t.visible = !1, t.thisClasses.push(y.lazy)), this.addClass(t.thisClasses.join(" ")), n(), t.lazy ? (h(t), g(t)) : a(t) }, _destruct: function(e) { e.$container.remove(), this.removeClass(e.thisClasses.join(" ")).off(w.namespace), n() }, _resize: function() { $.iterate.call(H, p), $.iterate.call(j, h), $.iterate.call(j, g) }, play: u, pause: function(e) { if (e.video && e.playing) { if (e.isYouTube) e.playerReady ? e.player.pauseVideo() : e.autoPlay = !1;
																												else { var t = e.$container.find("video");
																																t.length && t[0].pause() } e.playing = !1 } }, mute: function(e) { if (e.video)
																												if (e.isYouTube && e.playerReady) e.player.mute();
																												else { var t = e.$container.find("video");
																																t.length && (t[0].muted = !0) } e.mute = !0 }, unmute: function(e) { if (e.video) { if (e.isYouTube && e.playerReady) e.player.unMute();
																												else { var t = e.$container.find("video");
																																t.length && (t[0].muted = !1) } e.playing = !0 } e.mute = !1 }, resize: f, load: o, unload: function(e) { var t = e.$container.find(b.media);
																								t.length >= 1 && t.fsTransition({ property: "opacity" }, (function() { t.remove(), delete e.source })).css({ opacity: 0 }) } } }),
												b = v.classes,
												y = b.raw,
												w = v.events,
												$ = v.functions,
												x = t.window,
												C = t.$window,
												k = 0,
												H = [],
												j = [],
												W = "backgroundSize" in t.document.documentElement.style,
												_ = !1,
												T = [];
								t.Ready((function() { i(), C.on("scroll", i) })), x.onYouTubeIframeAPIReady = function() { for (var e in _ = !0, T) T.hasOwnProperty(e) && l(T[e].data, T[e].source);
												T = [] } }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [carousel.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(2), i(6)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { O = e(I.base) }

								function n(e) { e.enabled && (P.clearTimer(e.autoTimer), e.enabled = !1, e.$subordinate.off(S.update), this.removeClass([z.enabled, z.animated].join(" ")).off(S.namespace), e.$canister.fsTouch("destroy").off(S.namespace).attr("style", "").css(L, "none"), e.$items.css({ width: "", height: "" }).removeClass([z.visible, I.item_previous, I.item_next].join(" ")), e.$images.off(S.namespace), e.$controlItems.off(S.namespace), e.$pagination.html("").off(S.namespace), g(e), e.useMargin ? e.$canister.css({ marginLeft: "" }) : e.$canister.css(E, ""), e.index = 0) }

								function a(e) { e.enabled || (e.enabled = !0, this.addClass(z.enabled), e.$controlItems.on(S.click, e, p), e.$pagination.on(S.click, I.page, e, f), e.$items.on(S.click, e, C), e.$subordinate.on(S.update, e, H), H({ data: e }, 0), e.touchEvents && e.$canister.fsTouch({ axis: "x", pan: !0, swipe: !0 }).on(S.panStart, e, b).on(S.pan, e, y).on(S.panEnd, e, w).on(S.swipe, e, $).on(S.focusIn, e, k).css(L, ""), s(e), e.$images.on(S.load, e, u), e.autoAdvance && (e.autoTimer = P.startTimer(e.autoTimer, e.autoTime, (function() {! function(e) { var t = e.index + 1;
																				t >= e.pageCount && (t = 0), h(e, t) }(e) }), !0)), o.call(this, e)) }

								function o(i) { if (i.enabled) { var n, a, o, s, r; if (i.count = i.$items.length, i.count < 1) return g(i), void i.$canister.css({ height: "" }); if (this.removeClass(z.animated), i.containerWidth = i.$container.outerWidth(!1), i.visible = function(i) { var n = 1; if (i.single) return n; if ("array" === e.type(i.show))
																												for (var a in i.show) i.show.hasOwnProperty(a) && (t.support.matchMedia ? i.show[a].mq.matches && (n = i.show[a].count) : i.show[a].width < t.fallbackWidth && (n = i.show[a].count));
																								else n = i.show; return i.fill && i.count < n ? i.count : n }(i), i.perPage = i.paged ? 1 : i.visible, i.itemMarginLeft = parseInt(i.$items.eq(0).css("marginLeft")), i.itemMarginRight = parseInt(i.$items.eq(0).css("marginRight")), i.itemMargin = i.itemMarginLeft + i.itemMarginRight, isNaN(i.itemMargin) && (i.itemMargin = 0), i.itemWidth = (i.containerWidth - i.itemMargin * (i.visible - 1)) / i.visible, i.itemHeight = 0, i.pageWidth = i.paged ? i.itemWidth : i.containerWidth, i.matchWidth) i.canisterWidth = i.single ? i.containerWidth : (i.itemWidth + i.itemMargin) * i.count;
																else
																				for (i.canisterWidth = 0, i.$canister.css({ width: 1e6 }), n = 0; n < i.count; n++) i.canisterWidth += i.$items.eq(n).outerWidth(!0);
																i.$canister.css({ width: i.canisterWidth, height: "" }), i.$items.css({ width: i.matchWidth ? i.itemWidth : "", height: "" }).removeClass([z.visible, z.item_previous, z.item_next].join(" ")), i.pages = [], i.items = []; var l, c = 0,
																				d = 0,
																				u = 0; for (o = 0, s = 0, a = e(), n = 0; n < i.count; n++) l = i.$items.eq(n), c = i.matchWidth ? i.itemWidth + i.itemMargin : l.outerWidth(!0), d = l.outerHeight(), u = l.position().left, i.items.push({ $el: l, width: c, left: i.rtl ? u - (i.canisterWidth - c) : u }), (a.length && o + c > i.containerWidth + i.itemMargin || i.paged && n > 0) && (r = (i.rtl ? a.eq(a.length - 1) : a.eq(0)).position().left, i.pages.push({ left: i.rtl ? r - (i.canisterWidth - o) : r, height: s, width: o, $items: a }), a = e(), s = 0, o = 0), a = a.add(l), o += c, d > s && (s = d), s > i.itemHeight && (i.itemHeight = s);
																i.rtl ? a.eq(a.length - 1) : a.eq(0), r = i.canisterWidth - i.containerWidth - (i.rtl ? i.itemMarginLeft : i.itemMarginRight), i.pages.push({ left: i.rtl ? -r : r, height: s, width: o, $items: a }), i.pageCount = i.pages.length, i.paged && (i.pageCount -= i.count % i.visible), i.pageCount <= 0 && (i.pageCount = 1), i.maxMove = -i.pages[i.pageCount - 1].left, i.autoHeight ? i.$canister.css({ height: i.pages[0].height }) : i.matchHeight && i.$items.css({ height: i.itemHeight }); var p = ""; for (n = 0; n < i.pageCount; n++) p += '<button type="button" class="' + z.page + '">' + (n + 1) + "</button>";
																i.$pagination.html(p), i.pageCount <= 1 ? g(i) : function(e) { e.$controls.addClass(z.visible), e.$controlItems.addClass(z.visible), e.$pagination.addClass(z.visible), v(e, e.$controlItems) }(i), i.$paginationItems = i.$pagination.find(I.page), h(i, i.index, !1), setTimeout((function() { i.$el.addClass(z.animated) }), 5) } }

								function s(e) { e.$items = e.$canister.children().not(":hidden").addClass(z.item), e.$images = e.$canister.find("img"), e.totalImages = e.$images.length }

								function r(e, t) { e.$images.off(S.namespace), !1 !== t && e.$canister.html(t), e.index = 0, s(e), o.call(this, e) }

								function l(e, t, i, n, a) { e.enabled && (n || P.clearTimer(e.autoTimer), void 0 === a && (a = !0), h(e, t - 1, a, i, n)) }

								function c(e) { var t = e.index - 1;
												e.infinite && t < 0 && (t = e.pageCount - 1), h(e, t) }

								function d(e) { var t = e.index + 1;
												e.infinite && t >= e.pageCount && (t = 0), h(e, t) }

								function u(e) { var t = e.data;
												t.resizeTimer = P.startTimer(t.resizeTimer, 1, (function() { o.call(t.$el, t) })) }

								function p(t) { P.killEvent(t); var i = t.data,
																n = i.index + (e(t.currentTarget).hasClass(z.control_next) ? 1 : -1);
												P.clearTimer(i.autoTimer), h(i, n) }

								function f(t) { P.killEvent(t); var i = t.data,
																n = i.$paginationItems.index(e(t.currentTarget));
												P.clearTimer(i.autoTimer), h(i, n) }

								function h(t, i, n, a, o) { if (i < 0 && (i = t.infinite ? t.pageCount - 1 : 0), i >= t.pageCount && (i = t.infinite ? 0 : t.pageCount - 1), !(t.count < 1)) { if (t.pages[i] && (t.leftPosition = -t.pages[i].left), t.leftPosition = j(t, t.leftPosition), t.useMargin ? t.$canister.css({ marginLeft: t.leftPosition }) : !1 === n ? (t.$canister.css(L, "none").css(E, "translateX(" + t.leftPosition + "px)"), setTimeout((function() { t.$canister.css(L, "") }), 5)) : t.$canister.css(E, "translateX(" + t.leftPosition + "px)"), t.$items.removeClass([z.visible, z.item_previous, z.item_next].join(" ")), t.single)
																				for (var s = 0, r = t.pages.length; s < r; s++) s === i ? t.pages[s].$items.addClass(z.visible).attr("aria-hidden", "false") : t.pages[s].$items.not(t.pages[i].$items).addClass(s < i ? z.item_previous : z.item_next).attr("aria-hidden", "true");
																else
																				for (s = 0; s < t.count; s++) { var l = t.rtl ? -1 : 1,
																												c = t.leftPosition * l + t.items[s].left * l,
																												d = c + t.items[s].width,
																												u = t.containerWidth + t.itemMargin + 1;
																								c >= -1 && d <= u ? t.items[s].$el.addClass(z.visible).attr("aria-hidden", "false") : c < 0 ? t.items[s].$el.addClass(z.item_previous).attr("aria-hidden", "false") : t.items[s].$el.addClass(z.item_next).attr("aria-hidden", "false") } t.autoHeight && t.$canister.css({ height: t.pages[i].height }), !1 !== n && !0 !== a && i !== t.index && (t.infinite || i > -1 && i < t.pageCount) && t.$el.trigger(S.update, [i]), t.index = i, t.linked && !0 !== o && e(t.linked).not(t.$el)[M]("jumpPage", t.index + 1, !0, !0),
																				function(e) { e.$paginationItems.removeClass(z.active).eq(e.index).addClass(z.active), e.infinite ? (e.$controlItems.addClass(z.visible), v(e, e.$controlItems)) : e.pageCount < 1 ? (e.$controlItems.removeClass(z.visible), m(e, e.$controlItems)) : (e.$controlItems.addClass(z.visible), v(e, e.$controlItems), e.index <= 0 ? (e.$controlPrevious.removeClass(z.visible), m(e, e.$controlPrevious)) : (e.index >= e.pageCount - 1 || !e.single && e.leftPosition === e.maxMove) && (e.$controlNext.removeClass(z.visible), m(e, e.$controlNext))) }(t) } }

								function g(e) { e.$controls.removeClass(z.visible), e.$controlItems.removeClass(z.visible), e.$pagination.removeClass(z.visible), m(e, e.$controlItems) }

								function m(e, t) { e.customControls || t.prop("disabled", !0) }

								function v(e, t) { e.customControls || t.prop("disabled", !1) }

								function b(t, i) { var n = t.data; if (P.clearTimer(n.autoTimer), !n.single) { if (n.useMargin) n.leftPosition = parseInt(n.$canister.css("marginLeft"));
																else { var a = n.$canister.css(E).split(",");
																				n.leftPosition = parseInt(a[4]) } if (n.$canister.css(L, "none").css("will-change", "transform"), y(t), n.linked && !0 !== i) { var o = t.deltaX / n.pageWidth;
																				n.rtl && (o *= -1), e(n.linked).not(n.$el)[M]("panStart", o) } } n.isTouching = !0 }

								function y(t, i) { var n = t.data; if (!n.single && (n.touchLeft = j(n, n.leftPosition + t.deltaX), n.useMargin ? n.$canister.css({ marginLeft: n.touchLeft }) : n.$canister.css(E, "translateX(" + n.touchLeft + "px)"), n.linked && !0 !== i)) { var a = t.deltaX / n.pageWidth;
																n.rtl && (a *= -1), e(n.linked).not(n.$el)[M]("pan", a) } }

								function w(t, i) { var n = t.data,
																a = Math.abs(t.deltaX),
																o = W(n, t),
																s = !1; if (n.didPan = !1, 0 == o) s = n.index;
												else { if (!n.single) { var r, l, c = Math.abs(n.touchLeft),
																								d = !1,
																								u = n.rtl ? "right" : "left"; if (t.directionX === u)
																								for (r = 0, l = n.pages.length; r < l; r++) d = n.pages[r], c > Math.abs(d.left) + 20 && (s = r + 1);
																				else
																								for (r = n.pages.length - 1, l = 0; r >= l; r--) d = n.pages[r], c < Math.abs(d.left) && (s = r - 1) }!1 === s && (s = a < 50 ? n.index : n.index + o) } s !== n.index && (n.didPan = !0), n.linked && !0 !== i && e(n.linked).not(n.$el)[M]("panEnd", s), x(n, s) }

								function $(t, i) { var n = t.data,
																a = W(n, t),
																o = n.index + a;
												n.linked && !0 !== i && e(n.linked).not(n.$el)[M]("swipe", t.directionX), x(n, o) }

								function x(e, t) { e.$canister.css(L, "").css("will-change", ""), h(e, t), e.isTouching = !1 }

								function C(t) { var i = t.data,
																n = e(t.currentTarget); if (!i.didPan && (n.trigger(S.itemClick), i.controller)) { var a = i.$items.index(n);
																H(t, a), i.$subordinate[M]("jumpPage", a + 1, !0) } }

								function k(t) { var i = t.data; if (i.enabled && !i.isTouching) { P.clearTimer(i.autoTimer), i.$container.scrollLeft(0); var n, a = e(t.target);
																a.hasClass(z.item) ? n = a : a.parents(I.item).length && (n = a.parents(I.item).eq(0)); for (var o = 0; o < i.pageCount; o++)
																				if (i.pages[o].$items.is(n)) { h(i, o); break } } }

								function H(e, t) { var i = e.data; if (i.controller) { var n = i.$items.eq(t);
																i.$items.removeClass(z.active), n.addClass(z.active); for (var a = 0; a < i.pageCount; a++)
																				if (i.pages[a].$items.is(n)) { h(i, a, !0, !0); break } } }

								function j(e, t) { return isNaN(t) ? t = 0 : e.rtl ? (t > e.maxMove && (t = e.maxMove), t < 0 && (t = 0)) : (t < e.maxMove && (t = e.maxMove), t > 0 && (t = 0)), t }

								function W(e, t) { return Math.abs(t.deltaX) < Math.abs(t.deltaY) ? 0 : e.rtl ? "right" === t.directionX ? 1 : -1 : "left" === t.directionX ? 1 : -1 } var _ = t.Plugin("carousel", { widget: !0, defaults: { autoAdvance: !1, autoHeight: !1, autoTime: 8e3, contained: !0, controls: !0, customClass: "", fill: !1, infinite: !1, labels: { next: "Next", previous: "Previous", controls: "Carousel {guid} Controls", pagination: "Carousel {guid} Pagination" }, matchHeight: !1, matchWidth: !0, maxWidth: 1 / 0, minWidth: "0px", paged: !1, pagination: !0, rtl: !1, show: 1, single: !1, theme: "fs-light", touch: !0, useMargin: !1 }, classes: ["ltr", "rtl", "viewport", "wrapper", "container", "canister", "item", "item_previous", "item_next", "controls", "controls_custom", "control", "control_previous", "control_next", "pagination", "page", "animated", "enabled", "visible", "active", "auto_height", "contained", "single"], events: { itemClick: "itemClick", update: "update" }, methods: { _construct: function(o) { var r;
																								o.didPan = !1, o.carouselClasses = [z.base, o.theme, o.customClass, o.rtl ? z.rtl : z.ltr], o.maxWidth = o.maxWidth === 1 / 0 ? "100000px" : o.maxWidth, o.mq = "(min-width:" + o.minWidth + ") and (max-width:" + o.maxWidth + ")", o.customControls = "object" === e.type(o.controls) && o.controls.previous && o.controls.next, o.customPagination = "string" === e.type(o.pagination), o.id = this.attr("id"), o.id ? o.ariaId = o.id : (o.ariaId = o.rawGuid, this.attr("id", o.ariaId)), t.support.transform || (o.useMargin = !0); var l = "",
																												c = "",
																												d = [z.control, z.control_previous].join(" "),
																												u = [z.control, z.control_next].join(" ");
																								o.controls && !o.customControls && (o.labels.controls = o.labels.controls.replace("{guid}", o.numGuid), l += '<div class="' + z.controls + '" aria-label="' + o.labels.controls + '" aria-controls="' + o.ariaId + '">', l += '<button type="button" class="' + d + '" aria-label="' + o.labels.previous + '">' + o.labels.previous + "</button>", l += '<button type="button" class="' + u + '" aria-label="' + o.labels.next + '">' + o.labels.next + "</button>", l += "</div>"), o.pagination && !o.customPagination && (o.labels.pagination = o.labels.pagination.replace("{guid}", o.numGuid), c += '<div class="' + z.pagination + '" aria-label="' + o.labels.pagination + '" aria-controls="' + o.ariaId + '" role="navigation">', c += "</div>"), o.autoHeight && o.carouselClasses.push(z.auto_height), o.contained && o.carouselClasses.push(z.contained), o.single && o.carouselClasses.push(z.single), this.addClass(o.carouselClasses.join(" ")).wrapInner('<div class="' + z.wrapper + '" aria-live="polite"><div class="' + z.container + '"><div class="' + z.canister + '"></div></div></div>').append(l).wrapInner('<div class="' + z.viewport + '"></div>').append(c), o.$viewport = this.find(I.viewport).eq(0), o.$container = this.find(I.container).eq(0), o.$canister = this.find(I.canister).eq(0), o.$pagination = this.find(I.pagination).eq(0), o.$controlPrevious = o.$controlNext = e(""), o.customControls ? (o.$controls = e(o.controls.container).addClass([z.controls, z.controls_custom].join(" ")), o.$controlPrevious = e(o.controls.previous).addClass(d), o.$controlNext = e(o.controls.next).addClass(u)) : (o.$controls = this.find(I.controls).eq(0), o.$controlPrevious = o.$controls.find(I.control_previous), o.$controlNext = o.$controls.find(I.control_next)), o.$controlItems = o.$controlPrevious.add(o.$controlNext), o.customPagination && (o.$pagination = e(o.pagination).addClass([z.pagination])), o.$paginationItems = o.$pagination.find(I.page), o.index = 0, o.enabled = !1, o.leftPosition = 0, o.autoTimer = null, o.resizeTimer = null; var p = this.data(T + "-linked");
																								o.linked = !!p && "[data-" + T + '-linked="' + p + '"]', o.linked && (o.paged = !0); var f = this.data(T + "-controller-for") || ""; if (o.$subordinate = e(f), o.$subordinate.length && (o.controller = !0), "object" === e.type(o.show)) { var h = o.show,
																																g = [],
																																m = []; for (r in h) h.hasOwnProperty(r) && m.push(r); for (r in m.sort(P.sortAsc), m) m.hasOwnProperty(r) && g.push({ width: parseInt(m[r]), count: h[m[r]], mq: window.matchMedia("(min-width: " + parseInt(m[r]) + "px)") });
																												o.show = g } s(o), e.fsMediaquery("bind", o.rawGuid, o.mq, { enter: function() { a.call(o.$el, o) }, leave: function() { n.call(o.$el, o) } }), i(), o.carouselClasses.push(z.enabled), o.carouselClasses.push(z.animated) }, _destruct: function(t) { P.clearTimer(t.autoTimer), P.clearTimer(t.resizeTimer), n.call(this, t), e.fsMediaquery("unbind", t.rawGuid), t.id !== t.ariaId && this.removeAttr("id"), t.$controlItems.removeClass([I.control, z.control_previous, I.control_next, I.visible].join(" ")).off(S.namespace), v(t, t.$controlItems), t.$images.off(S.namespace), t.$canister.fsTouch("destroy"), t.$items.removeClass([z.item, z.visible, I.item_previous, I.item_next].join(" ")).unwrap().unwrap().unwrap().unwrap(), t.controls && !t.customControls && t.$controls.remove(), t.customControls && t.$controls.removeClass([z.controls, z.controls_custom, z.visible].join(" ")), t.pagination && !t.customPagination && t.$pagination.remove(), t.customPagination && t.$pagination.html("").removeClass([z.pagination, z.visible].join(" ")), this.removeClass(t.carouselClasses.join(" ")), i() }, _resize: function(e) { P.iterate.call(O, o) }, disable: n, enable: a, jump: l, previous: c, next: d, jumpPage: l, previousPage: c, nextPage: d, jumpItem: function(e, t, i, n, a) { if (e.enabled) { P.clearTimer(e.autoTimer); var o = e.$items.eq(t - 1);
																												void 0 === a && (a = !0); for (var s = 0; s < e.pageCount; s++)
																																if (e.pages[s].$items.is(o)) { h(e, s, a, i, n); break } } }, reset: function(e) { e.enabled && r.call(this, e, !1) }, resize: o, update: r, panStart: function(e, t) { if (P.clearTimer(e.autoTimer), !e.single) { if (e.rtl && (t *= -1), e.useMargin) e.leftPosition = parseInt(e.$canister.css("marginLeft"));
																												else { var i = e.$canister.css(E).split(",");
																																e.leftPosition = parseInt(i[4]) } e.$canister.css(L, "none"), y({ data: e, deltaX: e.pageWidth * t }, !0) } e.isTouching = !0 }, pan: function(e, t) { if (!e.single) { e.rtl && (t *= -1); var i = e.pageWidth * t;
																												e.touchLeft = j(e, e.leftPosition + i), e.useMargin ? e.$canister.css({ marginLeft: e.touchLeft }) : e.$canister.css(E, "translateX(" + e.touchLeft + "px)") } }, panEnd: function(e, t) { x(e, t) }, swipe: function(e, t) { $({ data: e, directionX: t }, !0) } } }),
												T = _.namespace,
												M = _.namespaceClean,
												I = _.classes,
												z = I.raw,
												S = _.events,
												P = _.functions,
												O = [],
												E = t.transform,
												L = t.transition }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [checkpoint.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { f = p.height(), u.iterate.call(m, o) }

								function n() { m = e(l.base), i() }

								function a(e) { if (e.hasParent) { var t = e.$parent.scrollTop();
																t !== e.parentScroll && (s(e), e.parentScroll = t) } }

								function o(e) { if (e.initialized) { switch (e.parentHeight = e.hasParent ? e.$parent.outerHeight(!1) : f, e.windowIntersect) {
																				case "top":
																								e.windowCheck = 0 - e.offset; break;
																				case "middle":
																				case "center":
																								e.windowCheck = e.parentHeight / 2 - e.offset; break;
																				case "bottom":
																								e.windowCheck = e.parentHeight - e.offset } switch (e.elOffset = e.$target.offset(), e.elIntersect) {
																				case "top":
																								e.elCheck = e.elOffset.top; break;
																				case "middle":
																								e.elCheck = e.elOffset.top + e.$target.outerHeight() / 2; break;
																				case "bottom":
																								e.elCheck = e.elOffset.top + e.$target.outerHeight() } if (e.hasParent) { var t = e.$parent.offset();
																				e.elCheck -= t.top } s(e) } }

								function s(e) { e.initialized && (e.windowCheck + (e.hasParent ? e.parentScroll : h) >= e.elCheck ? (e.active || e.$el.trigger(d.activate), e.active = !0, e.$el.addClass(c.active)) : e.reverse && (e.active && e.$el.trigger(d.deactivate), e.active = !1, e.$el.removeClass(c.active))) } var r = t.Plugin("checkpoint", { widget: !0, defaults: { intersect: "bottom-top", offset: 0, reverse: !1 }, classes: ["active"], events: { activate: "activate", deactivate: "deactivate" }, methods: { _construct: function(t) { t.initialized = !1; var i = e(t.$el.data("checkpoint-parent")),
																												n = e(t.$el.data("checkpoint-container")),
																												a = t.$el.data("checkpoint-intersect"),
																												s = t.$el.data("checkpoint-offset");
																								a && (t.intersect = a), s && (t.offset = s); var r = t.intersect.split("-");
																								t.windowIntersect = r[0], t.elIntersect = r[1], t.visible = !1, t.$target = n.length ? n : t.$el, t.hasParent = i.length > 0, t.$parent = i; var l = t.$target.find("img");
																								l.length && l.on(d.load, t, o), t.$el.addClass(c.base), t.initialized = !0 }, _postConstruct: function(e) { n(), i() }, _destruct: function(e) { e.$el.removeClass(c.base), n() }, _resize: i, _raf: function() {
																								(h = p.scrollTop()) < 0 && (h = 0), h !== g && (u.iterate.call(m, s), g = h), u.iterate.call(m, a) }, resize: o } }),
												l = (r.namespace, r.classes),
												c = l.raw,
												d = r.events,
												u = r.functions,
												p = (t.window, t.$window),
												f = 0,
												h = 0,
												g = 0,
												m = [] }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [cookie.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i(t, i, n) { var o = !1,
																s = new Date;
												n.expires && "number" === e.type(n.expires) && (s.setTime(s.getTime() + n.expires), o = s.toGMTString()); var r = n.domain ? "; domain=" + n.domain : "",
																l = o ? "; expires=" + o : "",
																c = o ? "; max-age=" + n.expires / 1e3 : "",
																d = n.path ? "; path=" + n.path : "",
																u = n.secure ? "; secure" : "";
												a.cookie = t + "=" + i + l + c + r + d + u } t.Plugin("cookie", { utilities: { _delegate: function(t, o, s) { if ("object" === e.type(t)) n = e.extend(n, t);
																				else if (s = e.extend({}, n, s || {}), "undefined" !== e.type(t)) { if ("undefined" === e.type(o)) return function(e) { for (var t = e + "=", i = a.cookie.split(";"), n = 0; n < i.length; n++) { for (var o = i[n];
																																				" " === o.charAt(0);) o = o.substring(1, o.length); if (0 === o.indexOf(t)) return o.substring(t.length, o.length) } return null }(t);
																								null === o ? function(t, n) { i(t, "", e.extend({}, n, { expires: -6048e5 })) }(t, s) : i(t, o, s) } return null } } }); var n = { domain: null, expires: 6048e5, path: null, secure: null },
												a = t.document }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [equalize.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(2)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { u = e(l.element) }

								function n(e) { if (e.data && (e = e.data), e.enabled)
																for (var t, i, n, a = 0; a < e.target.length; a++) { t = 0, i = 0, (n = e.$el.find(e.target[a])).css(e.property, ""); for (var o = 0; o < n.length; o++)(i = n.eq(o)[e.type]()) > t && (t = i);
																				n.css(e.property, t) } }

								function a(e) { e.enabled && (e.enabled = !1, s(e)) }

								function o(e) { if (!e.enabled) { e.enabled = !0; var t = e.$el.find("img");
																t.length && t.on(c.load, e, n), n(e) } }

								function s(e) { for (var t = 0; t < e.target.length; t++) e.$el.find(e.target[t]).css(e.property, "");
												e.$el.find("img").off(c.namespace) } var r = t.Plugin("equalize", { widget: !0, priority: 5, defaults: { maxWidth: 1 / 0, minWidth: "0px", property: "height", target: null }, methods: { _construct: function(t) { t.maxWidth = t.maxWidth === 1 / 0 ? "100000px" : t.maxWidth, t.mq = "(min-width:" + t.minWidth + ") and (max-width:" + t.maxWidth + ")", t.type = "height" === t.property ? "outerHeight" : "outerWidth", t.target ? e.isArray(t.target) || (t.target = [t.target]) : t.target = ["> *"], i(), e.fsMediaquery("bind", t.rawGuid, t.mq, { enter: function() { o.call(t.$el, t) }, leave: function() { a.call(t.$el, t) } }) }, _destruct: function(t) { s(t), e.fsMediaquery("unbind", t.rawGuid), i() }, _resize: function(e) { d.iterate.call(u, n) }, resize: n } }),
												l = r.classes,
												c = (l.raw, r.events),
												d = r.functions,
												u = [] }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [lightbox.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(6), i(4), i(7)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i(i) { if (!N) { var n = i.data;!0 === n.overlay && (n.mobile = !0), (N = e.extend({}, { visible: !1, gallery: { active: !1 }, isMobile: t.isMobile || n.mobile, isTouch: t.support.touch, isAnimating: !0, isZooming: !1, oldContentHeight: 0, oldContentWidth: 0, metaHeight: 0, thumbnailHeight: 0, captionOpen: !1, thumbnailsOpen: !1, tapTimer: null }, n)).isViewer = N.isMobile && n.viewer && void 0 !== e.fn.fsViewer; var o = n.$el,
																				s = n.$object,
																				c = o && o[0].href && o[0].href || "",
																				u = o && o[0].hash && o[0].hash || "",
																				f = (c.toLowerCase().split(".").pop().split(/\#|\?/), o ? o.data(z + "-type") : ""),
																				h = "image" === f || c.match(n.fileTypes) || "data:image" === c.substr(0, 10),
																				m = T(c),
																				y = "url" === f || !h && !m && "http" === c.substr(0, 4) && !u,
																				w = "element" === f || !h && !m && !y && "#" === u.substr(0, 1),
																				k = void 0 !== s; if (w && (c = u), !(h || m || y || w || k)) return void(N = null);
																L.killEvent(i), N.margin *= 2, N.type = h ? "image" : m ? "video" : "element"; var W = o.data(z + "-gallery");
																W && (N.gallery.active = !0, N.gallery.id = W, N.gallery.$items = e("a[data-lightbox-gallery= " + N.gallery.id + "], a[rel= " + N.gallery.id + "]"), N.gallery.index = N.gallery.$items.index(N.$el), N.gallery.total = N.gallery.$items.length - 1), N.thumbnails && (h || m) && N.gallery.active || (N.thumbnails = !1); var _ = "";
																N.isMobile || (_ += '<div class="' + [O.overlay, N.theme, N.customClass].join(" ") + '"></div>'); var I = [O.base, O.loading, O.animating, N.theme, N.customClass]; if (N.fixed && I.push(O.fixed), N.isMobile && I.push(O.mobile), N.isTouch && I.push(O.touch), y && I.push(O.iframed), (w || k) && I.push(O.inline), N.thumbnails && I.push(O.thumbnailed), N.labels.lightbox = N.labels.lightbox.replace("{guid}", n.numGuid), _ += '<div class="' + I.join(" ") + '" role="dialog" aria-label="' + N.labels.lightbox + '" tabindex="-1">', _ += '<button type="button" class="' + O.close + '">' + N.labels.close + "</button>", _ += '<span class="' + O.loading_icon + '"></span>', _ += '<div class="' + O.container + '">', N.gallery.active && N.thumbnails) { var S, A;
																				_ += '<div class="' + [O.thumbnails] + '">', _ += '<div class="' + [O.thumbnail_container] + '">'; for (var R = 0, Y = N.gallery.$items.length; R < Y; R++)(A = (S = N.gallery.$items.eq(R)).data("lightbox-thumbnail")) || (A = S.find("img").attr("src")), _ += '<button class="' + [O.thumbnail_item] + '">', _ += '<img src="' + A + '" alt="">', _ += "</button>";
																				_ += "</div></div>" } _ += '<div class="' + O.content + '"></div>', h || m ? (_ += '<div class="' + O.tools + '">', _ += '<div class="' + O.controls + '">', N.gallery.active && (_ += '<button type="button" class="' + [O.control, O.control_previous].join(" ") + '">' + N.labels.previous + "</button>", _ += '<button type="button" class="' + [O.control, O.control_next].join(" ") + '">' + N.labels.next + "</button>"), N.isMobile && N.isTouch && (_ += '<button type="button" class="' + [O.toggle, O.caption_toggle].join(" ") + '">' + N.labels.captionClosed + "</button>", N.gallery.active && N.thumbnails && (_ += '<button type="button" class="' + [O.toggle, O.thumbnail_toggle].join(" ") + '">' + N.labels.thumbnailsClosed + "</button>")), _ += "</div>", _ += '<div class="' + O.meta + '">', _ += '<div class="' + O.meta_content + '">', N.gallery.active && (_ += '<p class="' + O.position + '"', N.gallery.total < 1 && (_ += ' style="display: none;"'), _ += ">", _ += '<span class="' + O.position_current + '">' + (N.gallery.index + 1) + "</span> ", _ += N.labels.count, _ += ' <span class="' + O.position_total + '">' + (N.gallery.total + 1) + "</span>", _ += "</p>"), _ += '<div class="' + O.caption + '">', _ += N.formatter.call(o, n), _ += "</div></div></div>", _ += "</div>") : N.gallery.active && (_ += '<div class="' + O.tools + '">', _ += '<div class="' + O.controls + '">', _ += '<button type="button" class="' + [O.control, O.control_previous].join(" ") + '">' + N.labels.previous + "</button>", _ += '<button type="button" class="' + [O.control, O.control_next].join(" ") + '">' + N.labels.next + "</button>", _ += "</div>", _ += "</div>"), _ += "</div></div>", q.append(_), N.$overlay = e(P.overlay), N.$lightbox = e(P.base), N.$close = e(P.close), N.$container = e(P.container), N.$content = e(P.content), N.$tools = e(P.tools), N.$meta = e(P.meta), N.$metaContent = e(P.meta_content), N.$position = e(P.position), N.$caption = e(P.caption), N.$controlBox = e(P.controls), N.$controls = e(P.control), N.$thumbnails = e(P.thumbnails), N.$thumbnailContainer = e(P.thumbnail_container), N.$thumbnailItems = e(P.thumbnail_item), N.isMobile ? (N.paddingVertical = N.$close.outerHeight(), N.paddingHorizontal = 0, N.mobilePaddingVertical = parseInt(N.$content.css("paddingTop"), 10) + parseInt(N.$content.css("paddingBottom"), 10), N.mobilePaddingHorizontal = parseInt(N.$content.css("paddingLeft"), 10) + parseInt(N.$content.css("paddingRight"), 10)) : (N.paddingVertical = parseInt(N.$lightbox.css("paddingTop"), 10) + parseInt(N.$lightbox.css("paddingBottom"), 10), N.paddingHorizontal = parseInt(N.$lightbox.css("paddingLeft"), 10) + parseInt(N.$lightbox.css("paddingRight"), 10), N.mobilePaddingVertical = 0, N.mobilePaddingHorizontal = 0), N.contentHeight = N.$lightbox.outerHeight() - N.paddingVertical, N.contentWidth = N.$lightbox.outerWidth() - N.paddingHorizontal, N.controlHeight = N.$controls.outerHeight(), V = r(), N.$lightbox.css({ top: N.fixed ? 0 : V.top }), N.gallery.active && (N.$lightbox.addClass(O.has_controls), $()), D.on(E.keyDown, x), q.on(E.click, [P.overlay, P.close].join(", "), a).on([E.focus, E.focusIn].join(" "), M), N.gallery.active && N.$lightbox.on(E.click, P.control, v), N.thumbnails && N.$lightbox.on(E.click, P.thumbnail_item, b), N.isMobile && N.isTouch && N.$lightbox.on(E.click, P.caption_toggle, l).on(E.click, P.thumbnail_toggle, d), N.$lightbox.fsTransition({ property: "opacity" }, (function() { h ? p(c) : m ? g(c) : y ? H(c) : w ? C(c) : k && j(N.$object) })).addClass(O.open), N.$overlay.addClass(O.open) } var V }

								function n(e) { "object" != typeof e && (N.targetHeight = arguments[0], N.targetWidth = arguments[1]), "element" === N.type ? W(N.$content.find("> :first-child")) : "image" === N.type ? f() : "video" === N.type && m(), s() }

								function a(e) { L.killEvent(e), N && (N.$lightbox.fsTransition("destroy"), N.$content.fsTransition("destroy"), N.$lightbox.addClass(O.animating).fsTransition({ property: "opacity" }, (function(e) { void 0 !== N.$inlineTarget && N.$inlineTarget.length && k(), N.isViewer && N.$imageContainer && N.$imageContainer.length && N.$imageContainer.fsViewer("destroy"), N.$lightbox.off(E.namespace), N.$container.off(E.namespace), D.off(E.keyDown), q.off(E.namespace), q.off(E.namespace), N.$overlay.remove(), N.$lightbox.remove(), void 0 !== N.$el && N.$el && N.$el.length && N.$el.focus(), N = null, D.trigger(E.close) })), N.$lightbox.removeClass(O.open), N.$overlay.removeClass(O.open), N.isMobile && (R.removeClass(O.lock), L.unlockViewport(z))) }

								function o() { var t = r();
												N.isMobile || N.duration, N.isMobile ? L.lockViewport(z) : N.$controls.css({ marginTop: (N.contentHeight - N.controlHeight - N.metaHeight + N.thumbnailHeight) / 2 }), "" === N.$caption.html() ? (N.$caption.hide(), N.$lightbox.removeClass(O.has_caption), N.gallery.active || N.$tools.hide()) : (N.$caption.show(), N.$lightbox.addClass(O.has_caption)), N.$lightbox.fsTransition({ property: N.contentHeight !== N.oldContentHeight ? "height" : "width" }, (function() { var t;
																N.$content.fsTransition({ property: "opacity" }, (function() { N.$lightbox.removeClass(O.animating), N.isAnimating = !1 })), N.$lightbox.removeClass(O.loading).addClass(O.ready), N.visible = !0, D.trigger(E.open, [{ instance: N }]), N.gallery.active && ("element" == N.type || (t = "", N.gallery.index > 0 && (T(t = N.gallery.$items.eq(N.gallery.index - 1).attr("href")) || e('<img src="' + t + '">')), N.gallery.index < N.gallery.total && (T(t = N.gallery.$items.eq(N.gallery.index + 1).attr("href")) || e('<img src="' + t + '">')), y(), function() { if (N.thumbnails) { var e = N.$thumbnailItems.eq(N.gallery.index),
																												t = e.position().left + e.outerWidth(!1) / 2 - N.$thumbnailContainer.outerWidth(!0) / 2;
																								N.$thumbnailContainer.stop().animate({ scrollLeft: t }, 200, "linear") } }())), N.$lightbox.focus() })), N.isMobile || N.$lightbox.css({ height: N.contentHeight + N.paddingVertical, width: N.contentWidth + N.paddingHorizontal, top: N.fixed ? 0 : t.top }); var i = N.oldContentHeight !== N.contentHeight || N.oldContentWidth !== N.contentWidth;!N.isMobile && i || N.$lightbox.fsTransition("resolve"), N.oldContentHeight = N.contentHeight, N.oldContentWidth = N.contentWidth, N.isMobile && R.addClass(O.lock) }

								function s() { if (N.visible && !N.isMobile) { var e = r();
																N.$controls.css({ marginTop: (N.contentHeight - N.controlHeight - N.metaHeight + N.thumbnailHeight) / 2 }), N.$lightbox.css({ height: N.contentHeight + N.paddingVertical, width: N.contentWidth + N.paddingHorizontal, top: N.fixed ? 0 : e.top }), N.oldContentHeight = N.contentHeight, N.oldContentWidth = N.contentWidth } }

								function r() { if (N.isMobile) return { left: 0, top: 0 }; var e = { left: (t.windowWidth - N.contentWidth - N.paddingHorizontal) / 2, top: N.top <= 0 ? (t.windowHeight - N.contentHeight - N.paddingVertical) / 2 : N.top }; return !0 !== N.fixed && (e.top += D.scrollTop()), e }

								function l(e) { if (L.killEvent(e), N.captionOpen) c();
												else { u(); var t = parseInt(N.$metaContent.outerHeight(!0));
																t += parseInt(N.$meta.css("padding-top")), t += parseInt(N.$meta.css("padding-bottom")), N.$meta.css({ height: t }), N.$lightbox.addClass(O.caption_open).find(P.caption_toggle).text(N.labels.captionOpen), N.captionOpen = !0 } }

								function c() { N.$lightbox.removeClass(O.caption_open).find(P.caption_toggle).text(N.labels.captionClosed), N.captionOpen = !1 }

								function d(e) { L.killEvent(e), N.thumbnailsOpen ? u() : (c(), N.$lightbox.addClass(O.thumbnails_open).find(P.thumbnail_toggle).text(N.labels.thumbnailsOpen), N.thumbnailsOpen = !0) }

								function u() { N.$lightbox.removeClass(O.thumbnails_open).find(P.thumbnail_toggle).text(N.labels.thumbnailsClosed), N.thumbnailsOpen = !1 }

								function p(t) { N.isViewer ? (N.$imageContainer = e('<div class="' + O.image_container + '"><img></div>'), N.$image = N.$imageContainer.find("img"), N.$image.attr("src", t).addClass(O.image), N.$content.prepend(N.$imageContainer), f(), N.$imageContainer.one("loaded.viewer", (function() { o() })).fsViewer({ theme: N.theme })) : (N.$imageContainer = e('<div class="' + O.image_container + '"><img></div>'), N.$image = N.$imageContainer.find("img"), N.$image.one(E.load, (function() { var e, t, i, n = (e = N.$image, t = e[0], i = new Image, void 0 !== t.naturalHeight ? { naturalHeight: t.naturalHeight, naturalWidth: t.naturalWidth } : "img" === t.tagName.toLowerCase() && (i.src = t.src, { naturalHeight: i.height, naturalWidth: i.width }));
																N.naturalHeight = n.naturalHeight, N.naturalWidth = n.naturalWidth, N.retina && (N.naturalHeight /= 2, N.naturalWidth /= 2), N.$content.prepend(N.$imageContainer), f(), o() })).on(E.error, _).attr("src", t).addClass(O.image), (N.$image[0].complete || 4 === N.$image[0].readyState) && N.$image.trigger(E.load)) }

								function f() { if (N.$image) { var e = 0; for (N.windowHeight = N.viewportHeight = t.windowHeight - N.mobilePaddingVertical - N.paddingVertical, N.windowWidth = N.viewportWidth = t.windowWidth - N.mobilePaddingHorizontal - N.paddingHorizontal, N.contentHeight = 1 / 0, N.contentWidth = 1 / 0, N.imageMarginTop = 0, N.imageMarginLeft = 0; N.contentHeight > N.viewportHeight && e < 2;) N.imageHeight = 0 === e ? N.naturalHeight : N.$image.outerHeight(), N.imageWidth = 0 === e ? N.naturalWidth : N.$image.outerWidth(), N.metaHeight = 0 === e ? 0 : N.metaHeight, N.spacerHeight = 0 === e ? 0 : N.spacerHeight, N.thumbnailHeight = 0 === e ? 0 : N.thumbnailHeight, 0 === e && (N.ratioHorizontal = N.imageHeight / N.imageWidth, N.ratioVertical = N.imageWidth / N.imageHeight, N.isWide = N.imageWidth > N.imageHeight), N.imageHeight < N.minHeight && (N.minHeight = N.imageHeight), N.imageWidth < N.minWidth && (N.minWidth = N.imageWidth), N.isMobile ? (N.isTouch ? (N.$controlBox.css({ width: t.windowWidth }), N.spacerHeight = N.$controls.outerHeight(!0)) : (N.$tools.css({ width: t.windowWidth }), N.spacerHeight = N.$tools.outerHeight(!0)), N.contentHeight = N.viewportHeight, N.contentWidth = N.viewportWidth, N.isTouch || N.$content.css({ height: N.contentHeight - N.spacerHeight }), N.$thumbnails.length && (N.spacerHeight += N.$thumbnails.outerHeight(!0)), N.spacerHeight += 10, h(), N.imageMarginTop = (N.contentHeight - N.targetImageHeight - N.spacerHeight) / 2, N.imageMarginLeft = (N.contentWidth - N.targetImageWidth) / 2) : (0 === e && (N.viewportHeight -= N.margin + N.paddingVertical, N.viewportWidth -= N.margin + N.paddingHorizontal), N.viewportHeight -= N.metaHeight, N.thumbnails && (N.viewportHeight -= N.thumbnailHeight), h(), N.contentHeight = N.targetImageHeight, N.contentWidth = N.targetImageWidth), N.isMobile || N.isTouch || N.$meta.css({ width: N.contentWidth }), N.$image.css({ height: N.targetImageHeight, width: N.targetImageWidth, marginTop: N.imageMarginTop, marginLeft: N.imageMarginLeft }), N.isMobile || (N.metaHeight = N.$meta.outerHeight(!0), N.contentHeight += N.metaHeight), N.thumbnails && (N.thumbnailHeight = N.$thumbnails.outerHeight(!0), N.contentHeight += N.thumbnailHeight), e++ } }

								function h() { var e = N.isMobile ? N.contentHeight - N.spacerHeight : N.viewportHeight,
																t = N.isMobile ? N.contentWidth : N.viewportWidth;
												N.isWide ? (N.targetImageWidth = t, N.targetImageHeight = N.targetImageWidth * N.ratioHorizontal, N.targetImageHeight > e && (N.targetImageHeight = e, N.targetImageWidth = N.targetImageHeight * N.ratioVertical)) : (N.targetImageHeight = e, N.targetImageWidth = N.targetImageHeight * N.ratioVertical, N.targetImageWidth > t && (N.targetImageWidth = t, N.targetImageHeight = N.targetImageWidth * N.ratioHorizontal)), (N.targetImageWidth > N.imageWidth || N.targetImageHeight > N.imageHeight) && (N.targetImageHeight = N.imageHeight, N.targetImageWidth = N.imageWidth), (N.targetImageWidth < N.minWidth || N.targetImageHeight < N.minHeight) && (N.targetImageWidth < N.minWidth ? (N.targetImageWidth = N.minWidth, N.targetImageHeight = N.targetImageWidth * N.ratioHorizontal) : (N.targetImageHeight = N.minHeight, N.targetImageWidth = N.targetImageHeight * N.ratioVertical)) }

								function g(t) { var i = T(t),
																n = t.split("?"),
																a = "&origin=" + encodeURIComponent(window.location.protocol + "//" + window.location.hostname);
												i ? (n.length >= 2 && (i += "?" + n.slice(1)[0].trim()), N.$videoWrapper = e('<div class="' + O.video_wrapper + '"></div>'), N.$video = e('<iframe class="' + O.video + '" frameborder="0" seamless="seamless" allowfullscreen></iframe>'), N.$video.attr("src", i + "&enablejsapi=1" + a).addClass(O.video).prependTo(N.$videoWrapper), N.$content.prepend(N.$videoWrapper), m(), o()) : _() }

								function m() { N.windowHeight = N.viewportHeight = t.windowHeight - N.mobilePaddingVertical - N.paddingVertical, N.windowWidth = N.viewportWidth = t.windowWidth - N.mobilePaddingHorizontal - N.paddingHorizontal, N.videoMarginTop = 0, N.videoMarginLeft = 0, N.isMobile ? (N.isTouch ? (N.$controlBox.css({ width: t.windowWidth }), N.spacerHeight = N.$controls.outerHeight(!0) + 10) : (N.$tools.css({ width: t.windowWidth }), N.spacerHeight = N.$tools.outerHeight(!0), N.spacerHeight += N.$thumbnails.outerHeight(!0) + 10), N.viewportHeight -= N.spacerHeight, N.targetVideoWidth = N.viewportWidth, N.targetVideoHeight = N.targetVideoWidth * N.videoRatio, N.targetVideoHeight > N.viewportHeight && (N.targetVideoHeight = N.viewportHeight, N.targetVideoWidth = N.targetVideoHeight / N.videoRatio), N.videoMarginTop = (N.viewportHeight - N.targetVideoHeight) / 2, N.videoMarginLeft = (N.viewportWidth - N.targetVideoWidth) / 2) : (N.viewportHeight = N.windowHeight - N.margin, N.viewportWidth = N.windowWidth - N.margin, N.targetVideoWidth = N.videoWidth > N.viewportWidth ? N.viewportWidth : N.videoWidth, N.targetVideoWidth < N.minWidth && (N.targetVideoWidth = N.minWidth), N.targetVideoHeight = N.targetVideoWidth * N.videoRatio, N.contentHeight = N.targetVideoHeight, N.contentWidth = N.targetVideoWidth), N.isMobile || N.isTouch || N.$meta.css({ width: N.contentWidth }), N.$videoWrapper.css({ height: N.targetVideoHeight, width: N.targetVideoWidth, marginTop: N.videoMarginTop, marginLeft: N.videoMarginLeft }), N.isMobile || (N.metaHeight = N.$meta.outerHeight(!0), N.contentHeight += N.metaHeight), N.thumbnails && (N.thumbnailHeight = N.$thumbnails.outerHeight(!0), N.contentHeight += N.thumbnailHeight) }

								function v(t) { L.killEvent(t); var i = e(t.currentTarget);
												N.isAnimating || i.hasClass(O.control_disabled) || (N.isAnimating = !0, c(), N.gallery.index += i.hasClass(O.control_next) ? 1 : -1, N.gallery.index > N.gallery.total && (N.gallery.index = N.infinite ? 0 : N.gallery.total), N.gallery.index < 0 && (N.gallery.index = N.infinite ? N.gallery.total : 0), y(), N.$lightbox.addClass(O.animating), N.$content.fsTransition({ property: "opacity" }, w), N.$lightbox.addClass(O.loading)) }

								function b(t) { L.killEvent(t); var i = e(t.currentTarget);
												N.isAnimating || i.hasClass(O.active) || (N.isAnimating = !0, c(), N.gallery.index = N.$thumbnailItems.index(i), y(), N.$lightbox.addClass(O.animating), N.$content.fsTransition({ property: "opacity" }, w), N.$lightbox.addClass(O.loading)) }

								function y() { if (N.thumbnails) { var e = N.$thumbnailItems.eq(N.gallery.index);
																N.$thumbnailItems.removeClass(O.active), e.addClass(O.active) } }

								function w() { if ("element" == N.type ? void 0 !== N.$inlineTarget && N.$inlineTarget.length && k() : (void 0 !== N.$imageContainer && (N.isViewer && N.$imageContainer.fsViewer("destroy"), N.$imageContainer.remove()), void 0 !== N.$videoWrapper && N.$videoWrapper.remove()), N.$el = N.gallery.$items.eq(N.gallery.index), "element" == N.type) { var e = N.$el[0].href; "http" === e.substr(0, 4) ? H(e) : C(e) } else N.$caption.html(N.formatter.call(N.$el, N)), N.$position.find(P.position_current).html(N.gallery.index + 1), T(e) ? (N.type = "video", g(e)) : (N.type = "image", p(e));
												$() }

								function $() { N.$controls.removeClass(O.control_disabled), N.infinite || (0 === N.gallery.index && N.$controls.filter(P.control_previous).addClass(O.control_disabled), N.gallery.index === N.gallery.total && N.$controls.filter(P.control_next).addClass(O.control_disabled)) }

								function x(e) {!N.gallery.active || 37 !== e.keyCode && 39 !== e.keyCode ? 27 === e.keyCode && N.$close.trigger(E.click) : (L.killEvent(e), N.$controls.filter(37 === e.keyCode ? P.control_previous : P.control_next).trigger(E.click)) }

								function C(t) { N.$inlineTarget = e(t), N.$inlineContents = N.$inlineTarget.children().detach(), j(N.$inlineContents) }

								function k() { N.$inlineTarget.append(N.$inlineContents.detach()) }

								function H(t) { t += t.indexOf("?") > -1 ? "&" + N.requestKey + "=true" : "?" + N.requestKey + "=true", j(e('<iframe class="' + O.iframe + '" src="' + t + '"></iframe>')) }

								function j(e) { N.$content.append(e), W(e), o() }

								function W(e) { N.windowHeight = t.windowHeight - N.mobilePaddingVertical - N.paddingVertical, N.windowWidth = t.windowWidth - N.mobilePaddingHorizontal - N.paddingHorizontal, N.objectHeight = e.outerHeight(!0), N.objectWidth = e.outerWidth(!0), N.targetHeight = N.targetHeight || (N.$el ? N.$el.data(z + "-height") : null), N.targetWidth = N.targetWidth || (N.$el ? N.$el.data(z + "-width") : null), N.maxHeight = N.windowHeight < 0 ? N.minHeight : N.windowHeight, N.isIframe = e.is("iframe"), N.objectMarginTop = 0, N.objectMarginLeft = 0, N.isMobile || (N.windowHeight -= N.margin, N.windowWidth -= N.margin), N.contentHeight = N.targetHeight ? N.targetHeight : N.isIframe || N.isMobile ? N.windowHeight : N.objectHeight, N.contentWidth = N.targetWidth ? N.targetWidth : N.isIframe || N.isMobile ? N.windowWidth : N.objectWidth, (N.isIframe || N.isObject) && N.isMobile ? (N.contentHeight = N.windowHeight, N.contentWidth = N.windowWidth) : N.isObject && (N.contentHeight = N.contentHeight > N.windowHeight ? N.windowHeight : N.contentHeight, N.contentWidth = N.contentWidth > N.windowWidth ? N.windowWidth : N.contentWidth), N.isMobile || (N.contentHeight > N.maxHeight && (N.contentHeight = N.maxHeight), N.contentWidth > N.maxWidth && (N.contentWidth = N.maxWidth)) }

								function _() { var t = e('<div class="' + O.error + '"><p>Error Loading Resource</p></div>');
												N.type = "element", N.$tools.remove(), N.$image.off(E.namespace), j(t), D.trigger(E.error) }

								function T(e) { var t, i = N.videoFormatter; for (var n in i)
																if (i.hasOwnProperty(n) && null !== (t = e.match(i[n].pattern))) return i[n].format.call(N, t); return !1 }

								function M(t) { var i = t.target;
												e.contains(N.$lightbox[0], i) || i === N.$lightbox[0] || i === N.$overlay[0] || (L.killEvent(t), N.$lightbox.focus()) } var I = t.Plugin("lightbox", { widget: !0, defaults: { customClass: "", fileTypes: /\.(jpg|sjpg|jpeg|png|gif)/i, fixed: !1, formatter: function() { var e = this.attr("title"),
																												t = !(void 0 === e || !e) && e.replace(/^\s+|\s+$/g, ""); return t ? '<p class="caption">' + t + "</p>" : "" }, infinite: !1, labels: { close: "Close", count: "of", next: "Next", previous: "Previous", captionClosed: "View Caption", captionOpen: "Close Caption", thumbnailsClosed: "View Thumbnails", thumbnailsOpen: "Close Thumbnails", lightbox: "Lightbox {guid}" }, margin: 50, maxHeight: 1e4, maxWidth: 1e4, minHeight: 100, minWidth: 100, mobile: !1, overlay: !1, retina: !1, requestKey: "fs-lightbox", theme: "fs-light", thumbnails: !1, top: 0, videoFormatter: { youtube: { pattern: /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/, format: function(e) { return "//www.youtube.com/embed/" + e[1] } }, vimeo: { pattern: /(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/, format: function(e) { return "//player.vimeo.com/video/" + e[3] } } }, videoRatio: .5625, videoWidth: 800, viewer: !0 }, classes: ["loading", "animating", "fixed", "mobile", "touch", "inline", "iframed", "open", "ready", "overlay", "close", "loading_icon", "container", "content", "image", "image_container", "video", "video_wrapper", "tools", "meta", "meta_content", "controls", "control", "control_previous", "control_next", "control_disabled", "position", "position_current", "position_total", "toggle", "caption_toggle", "caption", "caption_open", "thumbnailed", "thumbnails_open", "thumbnail_toggle", "thumbnails", "thumbnail_container", "thumbnail_item", "active", "has_controls", "has_caption", "iframe", "error", "active", "lock"], events: { open: "open", close: "close" }, methods: { _construct: function(e) { this.on(E.click, e, i); var t = this.data(z + "-gallery");!V && Y && t === Y && (V = !0, this.trigger(E.click)) }, _destruct: function(e) { a(), this.off(E.namespace) }, _resize: function() { N && n() }, resize: n }, utilities: { _initialize: function(t, n) { t instanceof e && i.apply(A, [{ data: e.extend(!0, {}, { $object: t }, S, n || {}) }]) }, close: a } }),
												z = I.namespace,
												S = I.defaults,
												P = I.classes,
												O = P.raw,
												E = I.events,
												L = I.functions,
												A = t.window,
												D = t.$window,
												q = null,
												R = null,
												Y = !1,
												V = !1,
												N = null;
								t.Ready((function() { q = t.$body, R = e("html, body"), Y = t.window.location.hash.replace("#", "") })) }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [sticky.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(2)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i() { f.iterate.call(v, s) }

								function n() { v = e(d.base), i() }

								function a(e) { e.enabled = !0, e.$el.addClass(u.enabled), s(e) }

								function o(e) { e.enabled = !1, e.$el.css({ height: "", width: "", top: "", bottom: "", marginBottom: "" }).removeClass(u.enabled), e.$stickys.removeClass([u.passed, u.stuck].join(" ")) }

								function s(e) { if (e.enabled) { if (r(e), e.$container.length) { var t = e.$container.offset();
																				e.min = t.top - e.margin, e.max = e.min + e.$container.outerHeight(!1) - e.height } else { var i = (e.stuck ? e.$clone : e.$el).offset();
																				e.min = i.top - e.margin, e.max = !1 } l(e) } }

								function r(e) { var t;
												t = e.stuck ? e.$clone : e.$el, e.margin = parseInt(t.css("margin-top"), 10), e.$container.length ? e.containerMargin = parseInt(e.$container.css("margin-top"), 10) : e.containerMargin = 0, e.height = t.outerHeight(), e.width = t.outerWidth() }

								function l(e) { if (e.enabled) { var t = g + e.offset; if (t >= e.min) { e.stuck = !0, e.$stickys.addClass(u.stuck), e.stuck || (e.$el.trigger(p.stuck), r(e)); var i = e.offset,
																								n = "";
																				e.max && t > e.max ? (e.passed || e.$el.trigger(p.passed), e.passed = !0, e.$stickys.addClass(u.passed), i = "", n = 0) : (e.passed = !1, e.$stickys.removeClass(u.passed)), e.$el.css({ height: e.height, width: e.width, top: i, bottom: n, marginBottom: 0 }) } else e.stuck = !1, e.$stickys.removeClass(u.stuck).removeClass(u.passed), e.stuck && e.$el.trigger(p.unstuck), e.$el.css({ height: "", width: "", top: "", bottom: "", marginBottom: "" }) } } var c = t.Plugin("sticky", { widget: !0, defaults: { maxWidth: 1 / 0, minWidth: "0px", offset: 0 }, classes: ["enabled", "sticky", "stuck", "clone", "container", "passed"], events: { passed: "passed", stuck: "stuck", unstuck: "unstuck" }, methods: { _construct: function(t) { t.enabled = !1, t.stuck = !1, t.passed = !0, t.$clone = t.$el.clone(), t.container = t.$el.data("sticky-container"), t.$container = e(t.container), t.$el.addClass(u.base), t.$clone.removeClass(u.element).addClass(u.clone), t.$container.addClass(u.container), t.$stickys = e().add(t.$el).add(t.$clone), t.$el.after(t.$clone); var i = e().add(t.$el.find("img")).add(t.$container.find("img"));
																								i.length && i.on(p.load, t, s), t.maxWidth = t.maxWidth === 1 / 0 ? "100000px" : t.maxWidth, t.mq = "(min-width:" + t.minWidth + ") and (max-width:" + t.maxWidth + ")", e.fsMediaquery("bind", t.rawGuid, t.mq, { enter: function() { a.call(t.$el, t) }, leave: function() { o.call(t.$el, t) } }) }, _postConstruct: function(e) { n(), i() }, _destruct: function(e) { e.$clone.remove(), e.$container.removeClass(u.container), e.$el.css({ height: "", width: "", top: "", bottom: "", marginBottom: "" }).removeClass(u.base), n() }, _resize: i, _raf: function() {
																								(g = h.scrollTop()) < 0 && (g = 0), g !== m && (f.iterate.call(v, l), m = g) }, disable: o, enable: a, reset: s, resize: s } }),
												d = (c.namespace, c.classes),
												u = d.raw,
												p = c.events,
												f = c.functions,
												h = (t.window, t.$window),
												g = 0,
												m = 0,
												v = [] }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) { var n, a, o; /*! formstone v1.4.18 [swap.js] 2020-10-06 | GPL-3.0 License | formstone.it */
				a = [i(1), i(0), i(2)], void 0 === (o = "function" == typeof(n = function(e, t) { "use strict";

								function i(t, i) { if (t.enabled && !t.active) { t.group && !i && e(t.group).not(t.$el).not(t.linked)[r.namespaceClean]("deactivate", !0); var n = t.group ? e(t.group).index(t.$el) : null;
																t.$swaps.addClass(t.classes.raw.active), i || t.linked && e(t.linked).not(t.$el)[r.namespaceClean]("activate", !0), this.trigger(d.activate, [n]), t.active = !0 } }

								function n(t, i) { t.enabled && t.active && (t.$swaps.removeClass(t.classes.raw.active), i || t.linked && e(t.linked).not(t.$el)[r.namespaceClean]("deactivate", !0), this.trigger(d.deactivate), t.active = !1) }

								function a(t, a) { t.enabled || (t.enabled = !0, t.$swaps.addClass(t.classes.raw.enabled), a || e(t.linked).not(t.$el)[r.namespaceClean]("enable"), this.trigger(d.enable), t.onEnable ? (t.active = !1, i.call(this, t)) : (t.active = !0, n.call(this, t))) }

								function o(t, i) { t.enabled && (t.enabled = !1, t.$swaps.removeClass([t.classes.raw.enabled, t.classes.raw.active].join(" ")), i || e(t.linked).not(t.$el)[r.namespaceClean]("disable"), this.trigger(d.disable)) }

								function s(e) { u.killEvent(e); var t = e.data;
												t.active && t.collapse ? n.call(t.$el, t) : i.call(t.$el, t) } var r = t.Plugin("swap", { widget: !0, defaults: { collapse: !0, maxWidth: 1 / 0 }, classes: ["target", "enabled", "active"], events: { activate: "activate", deactivate: "deactivate", enable: "enable", disable: "disable" }, methods: { _construct: function(t) { t.enabled = !1, t.active = !1, t.classes = e.extend(!0, {}, c, t.classes), t.target = this.data(l + "-target"), t.$target = e(t.target).addClass(t.classes.raw.target), t.mq = "(max-width:" + (t.maxWidth === 1 / 0 ? "100000px" : t.maxWidth) + ")"; var i = this.data(l + "-linked");
																								t.linked = !!i && "[data-" + l + '-linked="' + i + '"]'; var n = this.data(l + "-group");
																								t.group = !!n && "[data-" + l + '-group="' + n + '"]', t.$swaps = e().add(this).add(t.$target), this.on(d.click + t.dotGuid, t, s) }, _postConstruct: function(t) { t.collapse || !t.group || e(t.group).filter("[data-" + l + "-active]").length || e(t.group).eq(0).attr("data-" + l + "-active", "true"), t.onEnable = this.data(l + "-active") || !1, e.fsMediaquery("bind", t.rawGuid, t.mq, { enter: function() { a.call(t.$el, t, !0) }, leave: function() { o.call(t.$el, t, !0) } }) }, _destruct: function(t) { e.fsMediaquery("unbind", t.rawGuid), t.$swaps.removeClass([t.classes.raw.enabled, t.classes.raw.active].join(" ")).off(d.namespace) }, activate: i, deactivate: n, enable: a, disable: o } }),
												l = r.namespace,
												c = r.classes,
												d = r.events,
												u = r.functions }) ? n.apply(t, a) : n) || (e.exports = o) }, function(e, t, i) {
				/**
				 * what-input - A global utility for tracking the current input method (mouse, keyboard or touch).
				 * @version v5.2.10
				 * @link https://github.com/ten1seven/what-input
				 * @license MIT
				 */
				var n;
				n = function() { return function(e) { var t = {};

												function i(n) { if (t[n]) return t[n].exports; var a = t[n] = { exports: {}, id: n, loaded: !1 }; return e[n].call(a.exports, a, a.exports, i), a.loaded = !0, a.exports } return i.m = e, i.c = t, i.p = "", i(0) }([function(e, t) { "use strict";
												e.exports = function() { if ("undefined" == typeof document || "undefined" == typeof window) return { ask: function() { return "initial" }, element: function() { return null }, ignoreKeys: function() {}, specificKeys: function() {}, registerOnChange: function() {}, unRegisterOnChange: function() {} }; var e = document.documentElement,
																				t = null,
																				i = "initial",
																				n = i,
																				a = Date.now(),
																				o = "false",
																				s = ["button", "input", "select", "textarea"],
																				r = [],
																				l = [16, 17, 18, 91, 93],
																				c = [],
																				d = { keydown: "keyboard", keyup: "keyboard", mousedown: "mouse", mousemove: "mouse", MSPointerDown: "pointer", MSPointerMove: "pointer", pointerdown: "pointer", pointermove: "pointer", touchstart: "touch", touchend: "touch" },
																				u = !1,
																				p = { x: null, y: null },
																				f = { 2: "touch", 3: "touch", 4: "mouse" },
																				h = !1; try { var g = Object.defineProperty({}, "passive", { get: function() { h = !0 } });
																				window.addEventListener("test", null, g) } catch (e) {} var m = function() { var e = !!h && { passive: !0 };
																								document.addEventListener("DOMContentLoaded", v), window.PointerEvent ? (window.addEventListener("pointerdown", b), window.addEventListener("pointermove", w)) : window.MSPointerEvent ? (window.addEventListener("MSPointerDown", b), window.addEventListener("MSPointerMove", w)) : (window.addEventListener("mousedown", b), window.addEventListener("mousemove", w), "ontouchstart" in window && (window.addEventListener("touchstart", b, e), window.addEventListener("touchend", b))), window.addEventListener(j(), w, e), window.addEventListener("keydown", b), window.addEventListener("keyup", b), window.addEventListener("focusin", $), window.addEventListener("focusout", x) },
																				v = function() { if (o = !(e.getAttribute("data-whatpersist") || "false" === document.body.getAttribute("data-whatpersist"))) try { window.sessionStorage.getItem("what-input") && (i = window.sessionStorage.getItem("what-input")), window.sessionStorage.getItem("what-intent") && (n = window.sessionStorage.getItem("what-intent")) } catch (e) {} y("input"), y("intent") },
																				b = function(e) { var t = e.which,
																												a = d[e.type]; "pointer" === a && (a = k(e)); var o = !c.length && -1 === l.indexOf(t),
																												r = c.length && -1 !== c.indexOf(t),
																												u = "keyboard" === a && t && (o || r) || "mouse" === a || "touch" === a; if (H(a) && (u = !1), u && i !== a && (C("input", i = a), y("input")), u && n !== a) { var p = document.activeElement;
																												p && p.nodeName && (-1 === s.indexOf(p.nodeName.toLowerCase()) || "button" === p.nodeName.toLowerCase() && !T(p, "form")) && (C("intent", n = a), y("intent")) } },
																				y = function(t) { e.setAttribute("data-what" + t, "input" === t ? i : n), W(t) },
																				w = function(e) { var t = d[e.type]; "pointer" === t && (t = k(e)), _(e), (!u && !H(t) || u && "wheel" === e.type || "mousewheel" === e.type || "DOMMouseScroll" === e.type) && n !== t && (C("intent", n = t), y("intent")) },
																				$ = function(i) { i.target.nodeName ? (t = i.target.nodeName.toLowerCase(), e.setAttribute("data-whatelement", t), i.target.classList && i.target.classList.length && e.setAttribute("data-whatclasses", i.target.classList.toString().replace(" ", ","))) : x() },
																				x = function() { t = null, e.removeAttribute("data-whatelement"), e.removeAttribute("data-whatclasses") },
																				C = function(e, t) { if (o) try { window.sessionStorage.setItem("what-" + e, t) } catch (e) {} },
																				k = function(e) { return "number" == typeof e.pointerType ? f[e.pointerType] : "pen" === e.pointerType ? "touch" : e.pointerType },
																				H = function(e) { var t = Date.now(),
																												n = "mouse" === e && "touch" === i && t - a < 200; return a = t, n },
																				j = function() { return "onwheel" in document.createElement("div") ? "wheel" : void 0 !== document.onmousewheel ? "mousewheel" : "DOMMouseScroll" },
																				W = function(e) { for (var t = 0, a = r.length; t < a; t++) r[t].type === e && r[t].fn.call(void 0, "input" === e ? i : n) },
																				_ = function(e) { p.x !== e.screenX || p.y !== e.screenY ? (u = !1, p.x = e.screenX, p.y = e.screenY) : u = !0 },
																				T = function(e, t) { var i = window.Element.prototype; if (i.matches || (i.matches = i.msMatchesSelector || i.webkitMatchesSelector), i.closest) return e.closest(t);
																								do { if (e.matches(t)) return e;
																												e = e.parentElement || e.parentNode } while (null !== e && 1 === e.nodeType); return null }; return "addEventListener" in window && Array.prototype.indexOf && (d[j()] = "mouse", m()), { ask: function(e) { return "intent" === e ? n : i }, element: function() { return t }, ignoreKeys: function(e) { l = e }, specificKeys: function(e) { c = e }, registerOnChange: function(e, t) { r.push({ fn: e, type: t || "input" }) }, unRegisterOnChange: function(e) { var t = function(e) { for (var t = 0, i = r.length; t < i; t++)
																																if (r[t].fn === e) return t }(e);
																								(t || 0 === t) && r.splice(t, 1) }, clearStorage: function() { window.sessionStorage.clear() } } }() }]) }, e.exports = n()
}, function(e, t) {
				function i(e) { return (i = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) { return typeof e } : function(e) { return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e })(e) }
				/*! modernizr 3.6.0 (Custom Build) | MIT *
				 * https://modernizr.com/download/?-csstransforms-csstransforms3d-supports-svgclippaths-touchevents-setclasses !*/
				! function(e, t, n) {
								function a(e, t) { return i(e) === t }

								function o() { return "function" != typeof t.createElement ? t.createElement(arguments[0]) : C ? t.createElementNS.call(t, "http://www.w3.org/2000/svg", arguments[0]) : t.createElement.apply(t, arguments) }

								function s(e, i, n, a) { var s, r, l, c, d = "modernizr",
																u = o("div"),
																p = function() { var e = t.body; return e || ((e = o(C ? "svg" : "body")).fake = !0), e }(); if (parseInt(n, 10))
																for (; n--;)(l = o("div")).id = a ? a[n] : d + (n + 1), u.appendChild(l); return (s = o("style")).type = "text/css", s.id = "s" + d, (p.fake ? p : u).appendChild(s), p.appendChild(u), s.styleSheet ? s.styleSheet.cssText = e : s.appendChild(t.createTextNode(e)), u.id = d, p.fake && (p.style.background = "", p.style.overflow = "hidden", c = x.style.overflow, x.style.overflow = "hidden", x.appendChild(p)), r = i(u, e), p.fake ? (p.parentNode.removeChild(p), x.style.overflow = c, x.offsetHeight) : u.parentNode.removeChild(u), !!r }

								function r(e, t) { return !!~("" + e).indexOf(t) }

								function l(e) { return e.replace(/([a-z])-([a-z])/g, (function(e, t, i) { return t + i.toUpperCase() })).replace(/^-/, "") }

								function c(e, t) { return function() { return e.apply(t, arguments) } }

								function d(e) { return e.replace(/([A-Z])/g, (function(e, t) { return "-" + t.toLowerCase() })).replace(/^ms-/, "-ms-") }

								function u(t, i, n) { var a; if ("getComputedStyle" in e) { a = getComputedStyle.call(e, t, i); var o = e.console; if (null !== a) n && (a = a.getPropertyValue(n));
																else if (o) { o[o.error ? "error" : "log"].call(o, "getComputedStyle returning null, its possible modernizr test results are inaccurate") } } else a = !i && t.currentStyle && t.currentStyle[n]; return a }

								function p(t, i) { var a = t.length; if ("CSS" in e && "supports" in e.CSS) { for (; a--;)
																				if (e.CSS.supports(d(t[a]), i)) return !0; return !1 } if ("CSSSupportsRule" in e) { for (var o = []; a--;) o.push("(" + d(t[a]) + ":" + i + ")"); return s("@supports (" + (o = o.join(" or ")) + ") { #modernizr { position: absolute; } }", (function(e) { return "absolute" == u(e, null, "position") })) } return n }

								function f(e, t, i, s) {
												function c() { u && (delete I.style, delete I.modElem) } if (s = !a(s, "undefined") && s, !a(i, "undefined")) { var d = p(e, i); if (!a(d, "undefined")) return d } for (var u, f, h, g, m, v = ["modernizr", "tspan", "samp"]; !I.style && v.length;) u = !0, I.modElem = o(v.shift()), I.style = I.modElem.style; for (h = e.length, f = 0; h > f; f++)
																if (g = e[f], m = I.style[g], r(g, "-") && (g = l(g)), I.style[g] !== n) { if (s || a(i, "undefined")) return c(), "pfx" != t || g; try { I.style[g] = i } catch (e) {} if (I.style[g] != m) return c(), "pfx" != t || g } return c(), !1 }

								function h(e, t, i, n, o) { var s = e.charAt(0).toUpperCase() + e.slice(1),
																r = (e + " " + _.join(s + " ") + s).split(" "); return a(t, "string") || a(t, "undefined") ? f(r, t, n, o) : function(e, t, i) { var n; for (var o in e)
																				if (e[o] in t) return !1 === i ? e[o] : a(n = t[e[o]], "function") ? c(n, i || t) : n; return !1 }(r = (e + " " + T.join(s + " ") + s).split(" "), t, i) }

								function g(e, t, i) { return h(e, n, n, t, i) } var m = [],
												v = [],
												b = { _version: "3.6.0", _config: { classPrefix: "", enableClasses: !0, enableJSClass: !0, usePrefixes: !0 }, _q: [], on: function(e, t) { var i = this;
																				setTimeout((function() { t(i[e]) }), 0) }, addTest: function(e, t, i) { v.push({ name: e, fn: t, options: i }) }, addAsyncTest: function(e) { v.push({ name: null, fn: e }) } },
												y = function() {};
								y.prototype = b, y = new y; var w = "CSS" in e && "supports" in e.CSS,
												$ = "supportsCSS" in e;
								y.addTest("supports", w || $); var x = t.documentElement,
												C = "svg" === x.nodeName.toLowerCase(),
												k = b._config.usePrefixes ? " -webkit- -moz- -o- -ms- ".split(" ") : ["", ""];
								b._prefixes = k; var H = {}.toString;
								y.addTest("svgclippaths", (function() { return !!t.createElementNS && /SVGClipPath/.test(H.call(t.createElementNS("http://www.w3.org/2000/svg", "clipPath"))) })); var j = b.testStyles = s;
								y.addTest("touchevents", (function() { var i; if ("ontouchstart" in e || e.DocumentTouch && t instanceof DocumentTouch) i = !0;
												else { var n = ["@media (", k.join("touch-enabled),("), "heartz", ")", "{#modernizr{top:9px;position:absolute}}"].join("");
																j(n, (function(e) { i = 9 === e.offsetTop })) } return i })); var W = "Moz O ms Webkit",
												_ = b._config.usePrefixes ? W.split(" ") : [];
								b._cssomPrefixes = _; var T = b._config.usePrefixes ? W.toLowerCase().split(" ") : [];
								b._domPrefixes = T; var M = { elem: o("modernizr") };
								y._q.push((function() { delete M.elem })); var I = { style: M.elem.style };
								y._q.unshift((function() { delete I.style })), b.testAllProps = h, b.testAllProps = g, y.addTest("csstransforms", (function() { return -1 === navigator.userAgent.indexOf("Android 2.") && g("transform", "scale(1)", !0) })), y.addTest("csstransforms3d", (function() { return !!g("perspective", "1px", !0) })),
												function() { var e, t, i, n, o, s; for (var r in v)
																				if (v.hasOwnProperty(r)) { if (e = [], (t = v[r]).name && (e.push(t.name.toLowerCase()), t.options && t.options.aliases && t.options.aliases.length))
																												for (i = 0; i < t.options.aliases.length; i++) e.push(t.options.aliases[i].toLowerCase()); for (n = a(t.fn, "function") ? t.fn() : t.fn, o = 0; o < e.length; o++) 1 === (s = e[o].split(".")).length ? y[s[0]] = n : (!y[s[0]] || y[s[0]] instanceof Boolean || (y[s[0]] = new Boolean(y[s[0]])), y[s[0]][s[1]] = n), m.push((n ? "" : "no-") + s.join("-")) } }(),
												function(e) { var t = x.className,
																				i = y._config.classPrefix || ""; if (C && (t = t.baseVal), y._config.enableJSClass) { var n = new RegExp("(^|\\s)" + i + "no-js(\\s|$)");
																				t = t.replace(n, "$1" + i + "js$2") } y._config.enableClasses && (t += " " + i + e.join(" " + i), C ? x.className.baseVal = t : x.className = t) }(m), delete b.addTest, delete b.addAsyncTest; for (var z = 0; z < y._q.length; z++) y._q[z]();
								e.Modernizr = y }(window, document) }, function(e, t, i) {
				(function(e) {
								function t(e) { return (t = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) { return typeof e } : function(e) { return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e })(e) } var i, n;
								window.lazySizesConfig = window.lazySizesConfig || {}, window.lazySizesConfig.lazyClass = "js-lazyload", window.lazySizesConfig.loadingClass = "js-lazyloading", window.lazySizesConfig.loadedClass = "js-lazyloaded", window.lazySizesConfig.loadHidden = !1, i = window, n = function(e, t) { "use strict"; if (t.getElementsByClassName) { var i, n, a = t.documentElement,
																				o = e.Date,
																				s = e.HTMLPictureElement,
																				r = "addEventListener",
																				l = "getAttribute",
																				c = e[r],
																				d = e.setTimeout,
																				u = e.requestAnimationFrame || d,
																				p = e.requestIdleCallback,
																				f = /^picture$/i,
																				h = ["load", "error", "lazyincluded", "_lazyloaded"],
																				g = {},
																				m = Array.prototype.forEach,
																				v = function(e, t) { return g[t] || (g[t] = new RegExp("(\\s|^)" + t + "(\\s|$)")), g[t].test(e[l]("class") || "") && g[t] },
																				b = function(e, t) { v(e, t) || e.setAttribute("class", (e[l]("class") || "").trim() + " " + t) },
																				y = function(e, t) { var i;
																								(i = v(e, t)) && e.setAttribute("class", (e[l]("class") || "").replace(i, " ")) },
																				w = function e(t, i, n) { var a = n ? r : "removeEventListener";
																								n && e(t, i), h.forEach((function(e) { t[a](e, i) })) },
																				$ = function(e, n, a, o, s) { var r = t.createEvent("Event"); return a || (a = {}), a.instance = i, r.initEvent(n, !o, !s), r.detail = a, e.dispatchEvent(r), r },
																				x = function(t, i) { var a;!s && (a = e.picturefill || n.pf) ? (i && i.src && !t[l]("srcset") && t.setAttribute("srcset", i.src), a({ reevaluate: !0, elements: [t] })) : i && i.src && (t.src = i.src) },
																				C = function(e, t) { return (getComputedStyle(e, null) || {})[t] },
																				k = function(e, t, i) { for (i = i || e.offsetWidth; i < n.minSize && t && !e._lazysizesWidth;) i = t.offsetWidth, t = t.parentNode; return i },
																				H = function() { var e, i, n = [],
																												a = [],
																												o = n,
																												s = function() { var t = o; for (o = n.length ? a : n, e = !0, i = !1; t.length;) t.shift()();
																																e = !1 },
																												r = function(n, a) { e && !a ? n.apply(this, arguments) : (o.push(n), i || (i = !0, (t.hidden ? d : u)(s))) }; return r._lsFlush = s, r }(),
																				j = function(e, t) { return t ? function() { H(e) } : function() { var t = this,
																																i = arguments;
																												H((function() { e.apply(t, i) })) } },
																				W = function(e) { var t, i = 0,
																												a = n.throttleDelay,
																												s = n.ricTimeout,
																												r = function() { t = !1, i = o.now(), e() },
																												l = p && s > 49 ? function() { p(r, { timeout: s }), s !== n.ricTimeout && (s = n.ricTimeout) } : j((function() { d(r) }), !0); return function(e) { var n;
																												(e = !0 === e) && (s = 33), t || (t = !0, (n = a - (o.now() - i)) < 0 && (n = 0), e || n < 9 ? l() : d(l, n)) } },
																				_ = function(e) { var t, i, n = function() { t = null, e() },
																												a = function e() { var t = o.now() - i;
																																t < 99 ? d(e, 99 - t) : (p || n)(n) }; return function() { i = o.now(), t || (t = d(a, 99)) } };! function() { var t, i = { lazyClass: "lazyload", loadedClass: "lazyloaded", loadingClass: "lazyloading", preloadClass: "lazypreload", errorClass: "lazyerror", autosizesClass: "lazyautosizes", srcAttr: "data-src", srcsetAttr: "data-srcset", sizesAttr: "data-sizes", minSize: 40, customMedia: {}, init: !0, expFactor: 1.5, hFac: .8, loadMode: 2, loadHidden: !0, ricTimeout: 0, throttleDelay: 125 }; for (t in n = e.lazySizesConfig || e.lazysizesConfig || {}, i) t in n || (n[t] = i[t]);
																				e.lazySizesConfig = n, d((function() { n.init && I() })) }(); var T = function() { var s, u, p, h, g, k, T, I, z, S, P, O, E, L, A = /^img$/i,
																												D = /^iframe$/i,
																												q = "onscroll" in e && !/(gle|ing)bot/.test(navigator.userAgent),
																												R = 0,
																												Y = 0,
																												V = -1,
																												N = function e(t) { Y--, t && t.target && w(t.target, e), (!t || Y < 0 || !t.target) && (Y = 0) },
																												X = function(e, i) { var n, o = e,
																																				s = "hidden" == C(t.body, "visibility") || "hidden" != C(e.parentNode, "visibility") && "hidden" != C(e, "visibility"); for (I -= i, P += i, z -= i, S += i; s && (o = o.offsetParent) && o != t.body && o != a;)(s = (C(o, "opacity") || 1) > 0) && "visible" != C(o, "overflow") && (n = o.getBoundingClientRect(), s = S > n.left && z < n.right && P > n.top - 1 && I < n.bottom + 1); return s },
																												F = function() { var e, o, r, c, d, p, f, g, m, v = i.elements; if ((h = n.loadMode) && Y < 8 && (e = v.length)) { o = 0, V++, null == E && ("expand" in n || (n.expand = a.clientHeight > 500 && a.clientWidth > 500 ? 500 : 370), O = n.expand, E = O * n.expFactor), R < E && Y < 1 && V > 2 && h > 2 && !t.hidden ? (R = E, V = 0) : R = h > 1 && V > 1 && Y < 6 ? O : 0; for (; o < e; o++)
																																								if (v[o] && !v[o]._lazyRace)
																																												if (q)
																																																if ((g = v[o][l]("data-expand")) && (p = 1 * g) || (p = R), m !== p && (k = innerWidth + p * L, T = innerHeight + p, f = -1 * p, m = p), r = v[o].getBoundingClientRect(), (P = r.bottom) >= f && (I = r.top) <= T && (S = r.right) >= f * L && (z = r.left) <= k && (P || S || z || I) && (n.loadHidden || "hidden" != C(v[o], "visibility")) && (u && Y < 3 && !g && (h < 3 || V < 4) || X(v[o], p))) { if (J(v[o]), d = !0, Y > 9) break } else !d && u && !c && Y < 4 && V < 4 && h > 2 && (s[0] || n.preloadAfterLoad) && (s[0] || !g && (P || S || z || I || "auto" != v[o][l](n.sizesAttr))) && (c = s[0] || v[o]);
																																				else J(v[o]);
																																				c && !d && J(c) } },
																												G = W(F),
																												B = function(e) { b(e.target, n.loadedClass), y(e.target, n.loadingClass), w(e.target, U), $(e.target, "lazyloaded") },
																												Q = j(B),
																												U = function(e) { Q({ target: e.target }) },
																												Z = function(e) { var t, i = e[l](n.srcsetAttr);
																																(t = n.customMedia[e[l]("data-media") || e[l]("media")]) && e.setAttribute("media", t), i && e.setAttribute("srcset", i) },
																												K = j((function(e, t, i, a, o) { var s, r, c, u, h, g;
																																(h = $(e, "lazybeforeunveil", t)).defaultPrevented || (a && (i ? b(e, n.autosizesClass) : e.setAttribute("sizes", a)), r = e[l](n.srcsetAttr), s = e[l](n.srcAttr), o && (u = (c = e.parentNode) && f.test(c.nodeName || "")), g = t.firesLoad || "src" in e && (r || s || u), h = { target: e }, g && (w(e, N, !0), clearTimeout(p), p = d(N, 2500), b(e, n.loadingClass), w(e, U, !0)), u && m.call(c.getElementsByTagName("source"), Z), r ? e.setAttribute("srcset", r) : s && !u && (D.test(e.nodeName) ? function(e, t) { try { e.contentWindow.location.replace(t) } catch (i) { e.src = t } }(e, s) : e.src = s), o && (r || u) && x(e, { src: s })), e._lazyRace && delete e._lazyRace, y(e, n.lazyClass), H((function() {
																																				(!g || e.complete && e.naturalWidth > 1) && (g ? N(h) : Y--, B(h)) }), !0) })),
																												J = function(e) { var t, i = A.test(e.nodeName),
																																				a = i && (e[l](n.sizesAttr) || e[l]("sizes")),
																																				o = "auto" == a;
																																(!o && u || !i || !e[l]("src") && !e.srcset || e.complete || v(e, n.errorClass) || !v(e, n.lazyClass)) && (t = $(e, "lazyunveilread").detail, o && M.updateElem(e, !0, e.offsetWidth), e._lazyRace = !0, Y++, K(e, t, o, a, i)) },
																												ee = function e() { if (!u) { if (o.now() - g < 999) return void d(e, 999); var t = _((function() { n.loadMode = 3, G() }));
																																				u = !0, n.loadMode = 3, G(), c("scroll", (function() { 3 == n.loadMode && (n.loadMode = 2), t() }), !0) } }; return { _: function() { g = o.now(), i.elements = t.getElementsByClassName(n.lazyClass), s = t.getElementsByClassName(n.lazyClass + " " + n.preloadClass), L = n.hFac, c("scroll", G, !0), c("resize", G, !0), e.MutationObserver ? new MutationObserver(G).observe(a, { childList: !0, subtree: !0, attributes: !0 }) : (a[r]("DOMNodeInserted", G, !0), a[r]("DOMAttrModified", G, !0), setInterval(G, 999)), c("hashchange", G, !0), ["focus", "mouseover", "click", "load", "transitionend", "animationend", "webkitAnimationEnd"].forEach((function(e) { t[r](e, G, !0) })), /d$|^c/.test(t.readyState) ? ee() : (c("load", ee), t[r]("DOMContentLoaded", G), d(ee, 2e4)), i.elements.length ? (F(), H._lsFlush()) : G() }, checkElems: G, unveil: J } }(),
																				M = function() { var e, i = j((function(e, t, i, n) { var a, o, s; if (e._lazysizesWidth = n, n += "px", e.setAttribute("sizes", n), f.test(t.nodeName || ""))
																																				for (o = 0, s = (a = t.getElementsByTagName("source")).length; o < s; o++) a[o].setAttribute("sizes", n);
																																i.detail.dataAttr || x(e, i.detail) })),
																												a = function(e, t, n) { var a, o = e.parentNode;
																																o && (n = k(e, o, n), a = $(e, "lazybeforesizes", { width: n, dataAttr: !!t }), a.defaultPrevented || (n = a.detail.width) && n !== e._lazysizesWidth && i(e, o, a, n)) },
																												o = _((function() { var t, i = e.length; if (i)
																																				for (t = 0; t < i; t++) a(e[t]) })); return { _: function() { e = t.getElementsByClassName(n.autosizesClass), c("resize", o) }, checkElems: o, updateElem: a } }(),
																				I = function e() { e.i || (e.i = !0, M._(), T._()) }; return i = { cfg: n, autoSizer: M, loader: T, init: I, uP: x, aC: b, rC: y, hC: v, fire: $, gW: k, rAF: H } } }(i, i.document), i.lazySizes = n, "object" == t(e) && e.exports && (e.exports = n) }).call(this, i(21)(e)) }, function(e, t) { e.exports = function(e) { return e.webpackPolyfill || (e.deprecate = function() {}, e.paths = [], e.children || (e.children = []), Object.defineProperty(e, "loaded", { enumerable: !0, get: function() { return e.l } }), Object.defineProperty(e, "id", { enumerable: !0, get: function() { return e.i } }), e.webpackPolyfill = 1), e } }, function(e, t, i) { var n = { "./Site.js": 5, "./ajax-alert.js": 23, "./alert.js": 24, "./audience-nav.js": 25, "./background-video.js": 26, "./formstone.js": 27, "./menu.js": 28, "./nav.js": 29, "./page-video.js": 30, "./page.js": 31, "./sub-nav.js": 32, "./video-appender.js": 33 };

				function a(e) { var t = o(e); return i(t) }

				function o(e) { if (!i.o(n, e)) { var t = new Error("Cannot find module '" + e + "'"); throw t.code = "MODULE_NOT_FOUND", t } return n[e] } a.keys = function() { return Object.keys(n) }, a.resolve = o, e.exports = a, a.id = 22 }, function(e, t) {! function(e, t) { var i, n, a, o, s, r, l;

								function c() { a.addClass("visible"), i.removeClass("visible").attr("aria-hidden", "true"), i.find("a, button").attr("tabindex", "-1") }

								function d() { a.removeClass("visible"), i.addClass("visible").attr("aria-hidden", "false"), i.find("a, button").removeAttr("tabindex") }

								function u(t) { t.preventDefault(), r.push(l), e.cookie(s, JSON.stringify(r), { path: "/", expires: 31536e6 }), c(), n.blur() }

								function p(t) { if (t.preventDefault(), i.hasClass("visible")) i.focus();
												else { for (var n = [], a = 0; a < r.length; a++) r[a] !== l && n.push(r[a]);
																r = n, e.cookie(s, r, { path: "/", expires: 31536e6 }), d(), i.transition({ always: !1, property: "transform" }, (function() { i.focus() })) } } t.OnInit.push((function() { o = e(".js-skip-alert").hide(), "undefined" != typeof AlertURL && (s = t.Namespace + "-alert", r = (r = e.cookie(s)) ? JSON.parse(r) : [], e.ajax(AlertURL).done((function(t) { t && (e(".js-alert-wrapper").html(t), i = e(".js-alert").addClass("enabled"), n = e(".js-alert-close"), a = e(".js-alert-open"), l = i.data("time"), n.on("click", u), o.show().on("click", p), a.addClass("enabled").on("click", p), -1 === r.indexOf(l) ? d() : c()) }))) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o, s, r, l;

								function c() { a.addClass("visible"), i.removeClass("visible").attr("aria-hidden", "true"), i.find("a, button").attr("tabindex", "-1") }

								function d() { a.removeClass("visible"), i.addClass("visible").attr("aria-hidden", "false"), i.find("a, button").removeAttr("tabindex") }

								function u(t) { t.preventDefault(), r.push(l), e.cookie(s, JSON.stringify(r), { path: "/", expires: 31536e6 }), c(), n.blur() }

								function p(t) { if (t.preventDefault(), i.hasClass("visible")) i.focus();
												else { for (var n = [], a = 0; a < r.length; a++) r[a] !== l && n.push(r[a]);
																r = n, e.cookie(s, r, { path: "/", expires: 31536e6 }), d(), i.transition({ always: !1, property: "transform" }, (function() { i.focus() })) } } t.OnInit.push((function() { "undefined" == typeof AlertURL && (i = e(".js-alert"), o = e(".js-skip-alert"), i.length ? (i.addClass("enabled"), n = e(".js-alert-close"), a = e(".js-alert-open"), l = i.data("time"), s = t.Namespace + "-alert", r = (r = e.cookie(s)) ? JSON.parse(r) : [], o.on("click", p), n.on("click", u), a.addClass("enabled").on("click", p), -1 === r.indexOf(l) ? d() : c()) : o.hide()) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o, s;

								function r() { s.attr("aria-expanded", "false"), o.attr("aria-hidden", "true"), o.find(".js-menu-panel-item").attr("tabindex", "-1") }

								function l(t) { e(t.target).closest(".js-audience-group").length || a.swap("deactivate") }

								function c(t) { if (-1 !== [27, 38, 40, 36, 35].indexOf(t.keyCode)) { var i = e(":focus"),
																				n = i.closest(".js-audience-nav-item").index(),
																				a = o.find(".js-audience-nav-link"); switch (t.preventDefault(), t.keyCode) {
																				case 9:
																								e(this).closest(".js-audience-group").find(".js-audience-toggle").swap("deactivate"), r(); break;
																				case 27:
																								e(this).closest(".js-audience-group").find(".js-audience-toggle").swap("deactivate"), r(), s.focus(); break;
																				case 38:
																								n > 0 ? a.eq(n - 1).focus() : a.last().focus(); break;
																				case 40:
																								i.closest(".js-audience-nav-item").is(":last-of-type") ? a.first().focus() : a.eq(n + 1).focus(); break;
																				case 36:
																								a.first().focus(); break;
																				case 35:
																								a.last().focus() } } }

								function d() {
												(s = e(this)).attr("aria-expanded", "true"), (o = s.closest(".js-audience-group").find(".js-audience-nav")).attr("aria-hidden", "false"), o.find(".js-menu-panel-link").removeAttr("tabindex").first().focus() }

								function u() { var t = e(this).closest(".js-audience-group");
												t.find(".js-audience-nav").transition({ always: !1, property: "opacity" }, (function() { t.find(".js-audience-nav-link").first().focus() })) }

								function p() { s = e(this), o = s.closest(".js-audience-group").find(".js-audience-nav"), r() }

								function f(t) { var i = t.keyCode; if (38 === i || 40 === i) { var n = e(this).closest(".js-audience-group"); switch (t.preventDefault(), e(this).swap("activate"), i) {
																				case 38:
																								"true" === e(this).attr("aria-expanded") && n.find(".js-audience-nav").transition({ always: !1, property: "opacity" }, (function() { n.find(".js-audience-nav-link").last().focus() })); break;
																				case 40:
																								"true" === e(this).attr("aria-expanded") && n.find(".js-audience-nav").transition({ always: !1, property: "opacity" }, (function() { n.find(".js-audience-nav-link").first().focus() })) } } } t.OnInit.push((function() {
												(a = e(".js-audience-toggle")).length && (n = e(".js-audience-nav"), i = e(".js-audience-nav-link"), a.on("activate.swap", d).on("deactivate.swap", p).on("keydown", f).on("click", u).attr({ "aria-haspopup": "true", "aria-expanded": "false" }), n.attr("aria-hidden", "true").on("keydown", c), i.attr("tabindex", "-1"), e(document).on("click touchstart", l)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n;

								function a() { i.addClass("loaded"); try { n.setMuted(!0), n.play() } catch (e) {} }

								function o() { s() }

								function s() { i.each((function() { var t = i.width(),
																				n = i.height();
																n / t <= .5625 ? e(this).find(".js-background-video-iframe").css({ width: "100%", height: .5625 * t }) : e(this).find(".js-background-video-iframe").css({ width: n / .5625, height: "100%" }) })) }

								function r() { var t = e(this).closest(".js-background-video-wrapper");
												n.play(), e(this).addClass("pressed").attr("aria-pressed", "true"), t.find(".page_header_bg_video_pause_link").removeClass("pressed").attr("aria-pressed", "false") }

								function l() { var t = e(this).closest(".js-background-video-wrapper");
												n.pause(), e(this).addClass("pressed").attr("aria-pressed", "true"), t.find(".page_header_bg_video_play_link").removeClass("pressed").attr("aria-pressed", "false") } t.OnInit.push((function() {
												(i = e(".js-background-video-wrapper")).length && e.getScript("//player.vimeo.com/api/player.js").done((function(i) { n = new Vimeo.Player(e(".js-background-video-iframe")), s(), e(".page_header_bg_video_play_link").attr("aria-pressed", "true").addClass("pressed").on("click", r), e(".page_header_bg_video_pause_link").attr("aria-pressed", "false").on("click", l), n.on("loaded", (function() { setTimeout(a, 1e3) })), t.OnResize.push(o) })) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o;

								function s() { var t = e(this).closest(".js-background-video");
												t.background("play"), t.find(".fs-background-control-play").addClass("fs-background-control-active").attr("aria-pressed", "true"), t.find(".fs-background-control-pause").removeClass("fs-background-control-active").attr("aria-pressed", "false") }

								function r() { var t = e(this).closest(".js-background-video");
												t.background("pause"), t.find(".fs-background-control-pause").addClass("fs-background-control-active").attr("aria-pressed", "true"), t.find(".fs-background-control-play").removeClass("fs-background-control-active").attr("aria-pressed", "false") }

								function l() { var t = e(this);
												t.find(".fs-carousel-control").attr("disabled", ""), t.find(".fs-carousel-item a, .fs-carousel-item button").attr("tabindex", "-1"), setTimeout((function() { t.find(".fs-carousel-control.fs-carousel-visible").removeAttr("disabled"), t.find(".fs-carousel-item.fs-carousel-visible a, .fs-carousel-item.fs-carousel-visible button").removeAttr("tabindex") }), 0) } t.OnInit.push((function() { o = "caret_left", a = "caret_right", i = { labels: { play: "Play", pause: "Pause" }, icons: { play: t.icon("video_play"), pause: t.icon("video_pause") } }, n = { videoWidth: 1e3, labels: { close: "<span class='fs-lightbox-icon-close'>" + t.icon("close") + "</span>", previous: "<span class='fs-lightbox-icon-previous'>" + t.icon(o) + "</span>", count: "<span class='fs-lightbox-meta-divider'></span>", next: "<span class='fs-lightbox-icon-next'>" + t.icon(a) + "</span>" } }, e(".js-background").on("loaded.background", (function() { var t;
																e(this).addClass("fs-background-loaded"), (t = e(this)).hasClass("js-background-video") && (0 === t.find(".fs-background-controls").length && e("<div class='fs-background-controls'><button class='fs-background-control fs-background-control-play fs-background-control-active' aria-pressed='true' aria-label='play'><span class='fs-background-control-icon'>" + i.icons.play + "</span><span class='fs-background-control-label'>" + i.labels.play + "</span></button><button class='fs-background-control fs-background-control-pause' aria-pressed='false' aria-label='pause'><span class='fs-background-control-icon'>" + i.icons.pause + "</span><span class='fs-background-control-label'>" + i.labels.pause + "</span></button></div>").appendTo(t), t.find(".fs-background-control-play").on("click", s), t.find(".fs-background-control-pause").on("click", r)) })).background(), e(".js-equalize").equalize(), e(".js-lightbox").lightbox(n), e(".js-swap").swap(); var c = e(".js-carousel");
												c.carousel().on("update.carousel", l), c.each((function() { var i = e(this).find(".fs-carousel-control_previous"),
																				n = i.text(),
																				s = e(this).find(".fs-carousel-control_next"),
																				r = s.text(),
																				l = e(this).find(".fs-carousel-item");
																i.attr("disabled", "").html("<span class='fs-carousel-control-icon'>" + t.icon(o) + "</span><span class='fs-carousel-control-label'>" + n + "</span>"), s.attr("disabled", "").html("<span class='fs-carousel-control-icon'>" + t.icon(a) + "</span><span class='fs-carousel-control-label'>" + r + "</span>"), i.is(".fs-carousel-visible") && i.removeAttr("disabled"), s.is(".fs-carousel-visible") && s.removeAttr("disabled"), l.find("a, button").attr("tabindex", "-1"), e(this).find(".fs-carousel-item.fs-carousel-visible a, .fs-carousel-item.fs-carousel-visible button").removeAttr("tabindex") })) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o, s;

								function r(t) { e("body").hasClass(s) && (e(t.target).closest(".js-menu").length || a.swap("deactivate")) }

								function l() { e("body").addClass(s), t.saveScrollYPosition(), a.attr("aria-expanded", "true"), i.attr({ "aria-hidden": "false", tabindex: "0" }).transition({ always: !1, property: "opacity" }, (function() { i.focus() })).find(".js-nav-link, button, input").removeAttr("tabindex"), o.css("padding-right", t.getScrollbarWidth()), i.css({ "margin-right": "", width: "" }) }

								function c() { e("body").removeClass(s), t.restoreScrollYPosition(), i.attr("aria-hidden", "true").removeAttr("tabindex").find(".js-nav-link, button, input").attr("tabindex", "-1"), a.attr("aria-expanded", "false").focus(), o.css("padding-right", ""), i.css({ "margin-right": -1 * t.getScrollbarWidth(), width: "calc(100% + " + t.getScrollbarWidth() + "px)" }) }

								function d(e) { 9 === e.keyCode && (e.shiftKey || i.focus()) }

								function u(e) { i.is(":focus") && 9 === e.keyCode && e.shiftKey && (e.preventDefault(), n.focus()) }

								function p(e) { 27 === e.keyCode && a.swap("deactivate") } t.OnInit.push((function() {
												(i = e(".js-menu")).length && (a = e(".js-menu-toggle"), n = e(".js-menu-close-toggle"), o = e(".header, .page, .footer"), s = "fs-page-lock", i.find(".js-nav-link, button, input").attr("tabindex", "-1"), i.attr({ role: "dialog", "aria-modal": "true" }).on("keydown", u).on("keyup", p).attr("aria-hidden", "true"), a.on("activate.swap", l).on("deactivate.swap", c).attr({ "aria-expanded": "false", role: "button" }), n.on("keydown", d).on("click", c), e(document).on("click touchstart", r)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a;

								function o(t) { var i = t.keyCode; if (-1 !== [27, 38, 40].indexOf(i)) { var a = e(this).closest(".js-nav-child-item"); switch (t.preventDefault(), i) {
																				case 9:
																								n.swap("deactivate"); break;
																				case 27:
																								n.swap("deactivate").focus(); break;
																				case 38:
																								a.prev(".js-nav-child-item").find(".js-nav-child-link").focus(); break;
																				case 40:
																								a.next(".js-nav-child-item").find(".js-nav-child-link").focus() } } }

								function s(t) { var i = t.keyCode;
												27 === i && 40 === i && (t.preventDefault(), 27 === i ? e(this).swap("deactivate") : e(this).swap("activate")) }

								function r() { n = e(this), i = n.closest(".js-nav-item"), n.attr("aria-expanded", "true"), i.find(".js-nav-children").attr("aria-hidden", "false").transition({ always: !1, property: "opacity" }, (function() { i.find(".js-nav-child-link").first().focus() })), i.find(".js-nav-child-link").removeAttr("tabindex").first().focus() }

								function l() { n = e(this), i = n.closest(".js-nav-item"), n.attr("aria-expanded", "false"), i.find(".js-nav-children").attr("aria-hidden", "true"), i.find(".js-nav-child-link").attr("tabindex", "-1") } t.OnInit.push((function() { e(".js-nav").length && (e(".js-nav-children").attr("aria-hidden", "true"), (a = e(".js-nav-child-link")).attr("tabindex", "-1"), a.on("keydown", o), e(".js-nav-toggle").on("activate.swap", r).on("deactivate.swap", l).on("keydown", s)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a;

								function o() { var o = n.get("autoplay");
												e(window).width() >= t.MinLG ? o && "0" !== o && "false" !== o || (n.set("autoplay", "1"), i.attr("src", a[0] + "?" + n.toString())) : 1 !== parseInt(o) && "true" !== o || (n.set("autoplay", "0"), i.attr("src", a[0] + "?" + n.toString())) } t.OnInit.push((function() { if ((i = e(".js-page-header-iframe")).length) { var s = i.attr("src");
																a = s.split("?"), n = new URLSearchParams("?" + a[1]), t.OnRespond.push(o) } })) }(jQuery, Site) }, function(e, t) {! function(e, t) { t.OnInit.push((function() {})) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o;

								function s() { var e = n.innerHeight();
												i.data("height", e), o.hasClass("fs-swap-active") && i.css("height", e) }

								function r() { e(window).width() >= t.MinLG ? (a.attr("aria-hidden", "false").find("a").removeAttr("tabindex"), i.removeAttr("height")) : o.hasClass("fs-swap-active") ? a.attr("aria-hidden", "false").find("a").removeAttr("tabindex") : a.attr("aria-hidden", "true").find("a").attr("tabindex", "-1") }

								function l() { e(this).attr("aria-expanded", "true"), a.attr("aria-hidden", "false").find("a").removeAttr("tabindex"), i.css("height", i.data("height")) }

								function c() { e(this).attr("aria-expanded", "false"), a.attr("aria-hidden", "true").find("a").attr("tabindex", "-1"), i.css("height", "0") } t.OnInit.push((function() {
												(i = e(".js-sub-nav-body")).length && (n = e(".js-sub-nav-body-inner"), a = e(".js-sub-nav-list"), (o = e(".js-sub-nav-toggle")).attr("aria-expanded", "false").attr("aria-haspopup", "true").on("activate.swap", l).on("deactivate.swap", c), t.OnResize.push(s), t.OnRespond.push(r)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) {
								function i(t) { var i = e(this),
																n = null,
																a = i.attr("href"),
																o = null;
												t.preventDefault(), -1 !== a.indexOf("youtube") ? (o = function(e) { var t = e.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/); return !(!t || 11 != t[1].length) && t[1] }(a)) && (n = "<iframe class='video_item_iframe' src='https://www.youtube.com/embed/" + o + "?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;enablejsapi=1&amp;playsinline=1' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>") : -1 !== a.indexOf("vimeo") && (o = function(e) { return /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/.exec(e)[5] }(a)) && (n = "<iframe class='video_item_iframe' src='//player.vimeo.com/video/" + o + "?autoplay=1&title=0&byline=0&portrait=0' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><script src='//player.vimeo.com/api/player.js'><\/script>"), n && (t.preventDefault(), i.after(n).remove()) } t.OnInit.push((function() { e(".js-video-appender").on("click", i) })) }(jQuery, Site) }, function(e, t, i) { var n = { "./accordion.js": 35, "./gallery.js": 36, "./homepage.js": 37, "./module.js": 38, "./page-offset.js": 39, "./pagination.js": 40, "./scrolling-header.js": 41, "./share-tools.js": 42 };

				function a(e) { var t = o(e); return i(t) }

				function o(e) { if (!i.o(n, e)) { var t = new Error("Cannot find module '" + e + "'"); throw t.code = "MODULE_NOT_FOUND", t } return n[e] } a.keys = function() { return Object.keys(n) }, a.resolve = o, e.exports = a, a.id = 34 }, function(e, t) {! function(e, t) { var i, n, a;

								function o(t) { e(t).find(".js-accordion-item:first-of-type .js-accordion-swap").focus() }

								function s(t) { e(t).find(".js-accordion-item:last-of-type .js-accordion-swap").focus() }

								function r() { e(this).attr("aria-expanded", "true"), i.find(e(this).data("swap-target") + " .js-accordion-content").attr("aria-hidden", "false") }

								function l() { e(this).attr("aria-expanded", "false"), i.find(e(this).data("swap-target") + " .js-accordion-content").attr("aria-hidden", "true") }

								function c(t) { if (-1 !== [36, 35, 38, 40].indexOf(t.keyCode)) { var i = e(":focus").closest(".js-accordion-item"),
																				n = e(this).closest(".js-accordion"); switch (t.preventDefault(), t.keyCode) {
																				case 36:
																								o(n); break;
																				case 35:
																								s(n); break;
																				case 38:
																								i.prev().length > 0 ? e(i).prev().find(".js-accordion-swap").focus() : s(n); break;
																				case 40:
																								i.next().length > 0 ? function(t) { e(t).next().find(".js-accordion-swap").focus() }(i) : o(n) } } } t.OnInit.push((function() {
												(i = e(".js-accordion")).length && (n = e(".js-accordion-content"), a = e(".js-accordion-swap"), n.attr("aria-hidden", "true"), a.attr("aria-expanded", "false"), i.each((function() { e(this).find(".fs-swap-active").length && (e(this).find(".js-accordion-content:eq(0)").attr("aria-hidden", "false"), e(this).find(".js-accordion-item:eq(0) .js-accordion-swap").attr("aria-expanded", "true")) })), a.on("activate.swap", r).on("deactivate.swap", l).on("keydown", c)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n;

								function a() { o(n.find(".js-gallery-item-video.fs-carousel-visible").last().next(".js-gallery-item-video")), e(".js-gallery-iframe-youtube").each((function() { e(this)[0].contentWindow.postMessage('{"event": "command", "func": "pauseVideo", "args": ""}', "*") })), e(".js-gallery-iframe-vimeo").each((function() { e(this)[0].contentWindow.postMessage({ method: "pause" }, e(this)[0].src) })) }

								function o(t) { if (!e(t).hasClass("video_loaded")) { var i = e(t).data("video-embed");
																e(t).find(".js-gallery-iframe").attr("src", i), e(t).addClass("video_loaded") } } t.OnInit.push((function() {
												(i = e(".js-gallery")).length && ((n = i.find(".js-carousel")).on("update.carousel", a).find(".js-gallery-item-video").each((function() { var t = e(this).data("video-embed"); "youtube" == e(this).data("video-service") ? e(this).data("video-embed", t + "?enablejsapi=1") : "vimeo" == e(this).data("video-service") && e(this).data("video-embed", t + "?color=ffffff&title=0&byline=0&portrait=0") })), o(e(".js-gallery-item-video.fs-carousel-visible")), o(e(".js-gallery-item.fs-carousel-visible").last().next(".js-gallery-item-video"))) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o;

								function s() { var e = window.pageYOffset;
												a !== e ? (a = e, r(), n(s)) : n(s) }

								function r() { var t = e(".spotlight")[0].getBoundingClientRect(),
																i = 1;
												i = 200 - -1 * t.top >= 0 ? (200 - -1 * t.top) / 200 : 0, t.bottom <= 0 ? e(".spotlight_background").css("display", "none") : e(".spotlight_background").css("display", "block"), e(".spotlight_background").css("opacity", i); var n = e(".spotlight_header_inner")[0].getBoundingClientRect(),
																a = e(".stories")[0].getBoundingClientRect();
												n.bottom + 40 > a.top ? (e(".stories").addClass("visible"), e(".spotlight_details_link").addClass("visible")) : (e(".stories").removeClass("visible"), e(".spotlight_details_link").removeClass("visible")) }

								function l() { i = e(window).height(), document.documentElement.style.setProperty("--window_height", i + "px"); var n = i / 2 - e(".spotlight_header_body").innerHeight() / 2 + e(".header").innerHeight() / 2,
																a = e(".logo")[0].getBoundingClientRect().left;
												e(".spotlight_header_inner").css({ paddingTop: n }), e(".spotlight_title_group").css({ left: -1 * a }), e(".spotlight_title_label").css({ paddingLeft: a }), e(window).width() >= t.MinMD ? .25 : .4, r() } t.OnInit.push((function() { if (e(".body_layout_home").length) { if (n = window.requestAnimationFrame, a = window.pageYOffset, o = Math.round(window.innerHeight), document.documentElement.style.setProperty("--window_height", o + "px"), "IntersectionObserver" in window && "IntersectionObserverEntry" in window && "intersectionRatio" in window.IntersectionObserverEntry.prototype) { var i = new IntersectionObserver((function(e) { e.forEach((function(e) {!0 === e.isIntersecting && e.target.classList.add("in_view") })) }), { threshold: .4 });
																				document.querySelectorAll(".js-view-test").forEach((function(e, t) { i.observe(e, t) })) } else e(".js-view-test").addClass("in_view");
																l(), s(), r(), t.OnResize.push(l) } })) }(jQuery, Site) }, function(e, t) {! function(e, t) { t.OnInit.push((function() { e(".selector").length })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i;

								function n() { i = e(".logo")[0].getBoundingClientRect().left, document.documentElement.style.setProperty("--page-offset", i + "px") }

								function a() { n() } t.OnInit.push((function() { e(".logo").length && (n(), t.OnResize.push(a)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i = document.querySelectorAll(".js-pagination-via-url"); if (i.length) { t.OnInit.push((function() { i.forEach((function(e) {
																				(function(e) { var t = { $Element: e, $Form: e.querySelector("form") }; return t.$Select = t.$Form.querySelector("select"), { onSubmit: function(e) { try { e.preventDefault(); var i = t.$Select.value;
																																				window.location = i } catch (e) { return !0 } }, bindUI: function() { t.$Form.addEventListener("submit", this.onSubmit.bind(this)) }, init: function() { this.bindUI() } } })(e).init() })) })) } }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a;

								function o() { var e = window.pageYOffset;
												a !== e ? (a = e, s(), n(o)) : n(o) }

								function s() { window.scrollY > 80 ? i.addClass("scrolling") : i.removeClass("scrolling") }

								function r() { s() } t.OnInit.push((function() {
												(i = e(".header")).length && (n = window.requestAnimationFrame, a = window.pageYOffset, r(), o(), s(), t.OnResize.push(r)) })) }(jQuery, Site) }, function(e, t) {! function(e, t) { var i, n, a, o, s, r;
								t.OnInit.push((function() {
												function t() { n.attr("aria-expanded", "true"), i.attr("aria-hidden", "false").find(".js-share-tool").removeAttr("tabindex") }

												function l() { n.attr("aria-expanded", "false").focus(), i.attr("aria-hidden", "true").find(".js-share-tool").attr("tabindex", "-1") }(a = e(".js-share-tools")).length && (i = e(".js-share-tools-list"), n = e(".js-share-tools-toggle"), r = window.location.href, s = encodeURIComponent(e("title").text()), o = e('meta[property="og:description"]').attr("content"), e(".js-share-facebook").attr("href", "//www.facebook.com/sharer/sharer.php?u=" + r), e(".js-share-twitter").attr("href", "//twitter.com/intent/tweet?text=" + s + "&url=" + r), e(".js-share-linkedin").attr("href", "//www.linkedin.com/shareArticle?mini=true&url=" + r + "&title=" + s), n.on("click", (function() { void 0 === navigator.share ? setTimeout((function() { a.hasClass("fs-swap-active") ? (t(), e(".js-share-tool-item:first-child").transition({ always: !1, property: "opacity" }, (function() { i.find(".js-share-tool").first().focus() }))) : l() }), 0) : navigator.share({ title: s, text: o, url: r }) })).on("keydown", (function(o) { var s = o.keyCode; if (38 !== s && 40 !== s) return;
																void 0 === navigator.share && (o.preventDefault(), a.hasClass("fs-swap-active") || (n.swap("activate"), t()), 38 === s ? a.hasClass("fs-swap-active") && e(".js-share-tool-item:last-child").transition({ always: !1, property: "opacity" }, (function() { i.find(".js-share-tool").last().focus() })) : a.hasClass("fs-swap-active") && e(".js-share-tool-item:first-child").transition({ always: !1, property: "opacity" }, (function() { i.find(".js-share-tool").first().focus() }))) })), void 0 === navigator.share && (n.attr({ "aria-haspopup": "true", "aria-expanded": "false" }).swap(), i.attr("aria-hidden", "true").on("keydown", (function(t) { var i = t.keyCode,
																				a = e(":focus").closest(".js-share-tool-item").index(); if (-1 === [27, 38, 40, 36, 35].indexOf(i)) return; switch (t.preventDefault(), i) {
																				case 27:
																								n.swap("deactivate").focus(), l(); break;
																				case 38:
																								a > 0 ? e(".js-share-tool").eq(a - 1).focus() : e(".js-share-tool").last().focus(); break;
																				case 40:
																								e(":focus").closest(".js-share-tool-item").is(":last-of-type") ? e(".js-share-tool").first().focus() : e(".js-share-tool").eq(a + 1).focus(); break;
																				case 36:
																								e(".js-share-tool").first().focus(); break;
																				case 35:
																								e(".js-share-tool").last().focus() } })), e(".js-share-tool").attr("tabindex", "-1"))) })) }(jQuery, Site) }, function(e, t, i) { "use strict";
				i.r(t);
				i(0), i(2), i(9), i(10), i(11), i(12), i(13), i(14), i(15), i(16), i(17), i(6), i(4), i(7), i(18), i(19), i(20);

				function n() { var e, t = function() { var t = e.extend({ onLoad: !1, position: "bottom-left", useCookies: !1 }, window.FSGridBookmarkletConfig); if (e(".fs-grid").length < 1) alert("Grid Not Found.\nYou'll need to include Grid before using this bookmarklet.\n\nLearn more: https://formstone.it/grid/");
												else { if (e("#fs-grid-styles").length < 1 && e("body").append('<link id="fs-grid-styles" rel="stylesheet" href="//formstone.it/bookmarklet/grid.css" type="text/css" media="all">'), e("#fs-grid-overlay").length < 1) { var i = "";
																				i += '<aside id="fs-grid-menu" class="' + t.position + '">', i += '<div class="fs-grid-statuses">', i += '<span class="fs-grid-status fs-grid-status-xs">xs</span>', i += '<span class="fs-grid-status fs-grid-status-sm">sm</span>', i += '<span class="fs-grid-status fs-grid-status-md">md</span>', i += '<span class="fs-grid-status fs-grid-status-lg">lg</span>', i += '<span class="fs-grid-status fs-grid-status-xl">xl</span>', i += '<span class="fs-grid-status fs-grid-status-xxl">xxl</span>', i += "</div>", i += '<button class="fs-grid-show fs-grid-option">Show Grid</button>', i += '<button class="fs-grid-icon fs-grid-remove">Close</button>', i += "</aside>", i += '<section id="fs-grid-overlay" aria-label="Grid Overlay">', i += '<div class="fs-row">'; for (var n = 0; n < 15; n++) i += '<div class="fs-cell fs-sm-1 fs-md-1 fs-lg-1"><span></span></div>';
																				i += "</div>", i += "</section>", e("body").append(i), this.$menu = e("#fs-grid-menu"), this.$menuItems = this.$menu.find("span"), this.$overlay = e("#fs-grid-overlay"), this.$menu.on("click", ".fs-grid-option", e.proxy(this.onClick, this)).on("click", ".fs-grid-remove", e.proxy(this.remove, this)), (t.onLoad || !0 === t.useCookies && "true" === this.readCookie("fs-grid-active")) && this.$menuItems.filter(".fs-grid-show").trigger("click") } } };

								function i() { new t }

								function n() { var t = document.createElement("script");
												t.id = "grid-jquery", t.src = "//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js", t.onload = function() { e = jQuery.noConflict(!0), i() }, document.body.appendChild(t) } if (t.prototype = { onClick: function(t) { var i = e(t.currentTarget);
																				i.hasClass("fs-grid-active") || i.hasClass("fs-grid-remove") ? (i.removeClass("fs-grid-active").html("Show Grid"), this.$overlay.removeClass("fs-grid-visible"), this.eraseCookie("fs-grid-active")) : (i.addClass("fs-grid-active").html("Hide Grid"), this.$overlay.addClass("fs-grid-visible"), this.createCookie("fs-grid-active", "true", 7)) }, remove: function(e) { this.$menu.remove(), this.$overlay.remove() }, createCookie: function(e, t, i) { var n = new Date,
																								a = "; domain=" + document.domain;
																				n.setTime(n.getTime() + 24 * i * 60 * 60 * 1e3), i = "; expires=" + n.toGMTString(), document.cookie = e + "=" + t + i + a + "; path=/" }, readCookie: function(e) { for (var t = e + "=", i = document.cookie.split(";"), n = 0; n < i.length; n++) { for (var a = i[n];
																												" " === a.charAt(0);) a = a.substring(1, a.length); if (0 === a.indexOf(t)) return a.substring(t.length, a.length) } return null }, eraseCookie: function(e) { this.createCookie(e, "", -1) } }, "undefined" == typeof jQuery) n();
								else { var a = jQuery.fn.jquery.split(".");
												parseInt(a[1], 10) < 7 ? n() : (e = jQuery, i()) } } var a = i(5),
								o = i(3);
				window.Site = a.default, Object(o.b)(i(22)), Object(o.b)(i(34)), "localhost" === window.location.hostname && void 0 !== n && n(), $(document).ready((function() { a.default.init("framework") })) }]); ===
=== =
import '@/vendor/formstone';
import '@/vendor/modernizr-custom';
import '@/vendor/lazysizes';
import FSGridBookmarklet from '@/grid';
import Site from '@/base/Site';
import { requireAll } from '@/helpers/helpers';

window.Site = Site;

requireAll(require.context('./base/', true, /\.js$/));
requireAll(require.context('./modules/', true, /\.js$/));

if (
				window.location.hostname === 'localhost' &&
				typeof FSGridBookmarklet !== 'undefined'
) {
				FSGridBookmarklet();
}

$(document).ready(function() {
				Site.init('framework');
});