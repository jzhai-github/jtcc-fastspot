import '@/vendor/formstone';
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
