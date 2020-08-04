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

    function init(ns) {
        Namespace = ns;
        HasTouch = $('html').hasClass('touchevents');

        if (typeof $.mediaquery !== 'undefined') {
            $.mediaquery({
                minWidth: [MinXS, MinSM, MinMD, MinLG, MinXL, MinXXL],
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
                '<use xlink:href="' +
                dirs.STATIC_ROOT +
                'images/icons.svg#' +
                icon +
                '">';
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
    };
})(jQuery);

export default Site;
