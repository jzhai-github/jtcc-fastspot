import 'formstone/dist/js/core';
import 'formstone/dist/js/mediaquery';
import 'formstone/dist/js/analytics';
import 'formstone/dist/js/background';
import 'formstone/dist/js/carousel';
import 'formstone/dist/js/checkpoint';
import 'formstone/dist/js/cookie';
import 'formstone/dist/js/equalize';
import 'formstone/dist/js/lightbox';
import 'formstone/dist/js/sticky';
import 'formstone/dist/js/swap';
import 'formstone/dist/js/touch';
import 'formstone/dist/js/transition';
import 'formstone/dist/js/viewer';
import 'what-input/dist/what-input.js';
import '@/vendor/modernizr-custom';
import '@/vendor/lazysizes';
import Site from '@/base/Site';
import { requireAll } from '@/helpers/helpers';

window.Site = Site;

requireAll(require.context('./base/', true, /\.js$/));
requireAll(require.context('./modules/', true, /\.js$/));

$(document).ready(function () {
	Site.init('framework');
});
