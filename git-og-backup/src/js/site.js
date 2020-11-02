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

$(document).ready(function () {
	Site.init('framework');
});
