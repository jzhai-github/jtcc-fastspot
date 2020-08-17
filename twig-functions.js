var fs = require('fs');

let config = require('./config');
let imageSizes = require('./image-sizes');

let twig_counter = 0;
let twig_counter_namespaces = {};

module.exports = {
	img: function (key) {
		return key in imageSizes ? imageSizes[key] : false;
	},
	config: function (key) {
		return key in config.twig_variables
			? config.twig_variables[key]
			: false;
	},
	navigation: function (key) {
		return key in config.navigation ? config.navigation[key] : false;
	},
	svg_icons: function () {
		let path = 'dist/images/icons.svg';

		if (!fs.existsSync(path)) return '';

		return fs.readFileSync(path).toString();
	},
	icon: function (name) {
		return `
			<svg class="icon icon_${name}">
				<use href="/images/icons.svg#${name}" />
			</svg>
		`;
	},
	tel: function (number) {
		return number.replace(/[^0-9\.]+/g, '');
	},
	uniqid: function (namespace) {
		if (namespace) {
			if (typeof twig_counter_namespaces[namespace] === 'undefined') {
				twig_counter_namespaces[namespace] = 1;
			} else {
				twig_counter_namespaces[namespace]++;
			}

			return namespace + '-' + twig_counter_namespaces[namespace];
		}

		twig_counter++;

		return twig_counter;
	},
	init_uniqid: function () {
		twig_counter = 0;
		twig_counter_namespaces = {};

		return '';
	}
};
