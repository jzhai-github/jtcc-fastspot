'use strict';

const pkgJson = require('./package.json');
const fractal = (module.exports = require('@frctl/fractal').create());
const mandelbrot = require('@frctl/mandelbrot');
const config = require('./config.json');
const twigAdapter = require('@frctl/twig')({
	importContext: true,
	namespaces: {
		'@components': './01-components',
		'@navigation': './02-navigation',
		'@layouts': './03-layouts',
		'@templates': './04-templates',
		'@partials': './05-partials',
		'@accessibility': './06-accessibility'
	},
	filters: {},
	functions: require('./twig-functions.js'),
	tags: {}
});
const env = process.env.NODE_ENV || 'development';
const isProduction = env === 'production';
const isDevelopment = !isProduction;
const customizedTheme = mandelbrot({
	skin: 'black',
	highlightStyles:
		'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/styles/gruvbox-dark.min.css',
	favicon: '/favicons/favicon.ico'
});

process.env.FRACTAL_CWD = __dirname;

/* Set the title of the project */
fractal.set('project.title', `${config.twig_variables.name} Component Library`);
fractal.set('project.version', pkgJson.version);
fractal.set('project.author', 'Fastspot');

fractal.docs.set('path', __dirname + '/src/docs');
fractal.docs.set('default.status', 'draft');

fractal.components.set('statuses', {
	prototype: {
		label: 'Prototype',
		description: 'Do not implement.',
		color: '#E53E3E'
	},
	framework: {
		label: 'Framework',
		description: 'Unmodified from Framework',
		color: '#3182CE'
	},
	wip: {
		label: 'WIP',
		description: 'Work in progress. Implement with caution.',
		color: '#DD6B20'
	},
	ready: {
		label: 'Ready',
		description: 'Ready to implement.',
		color: '#38A169'
	}
});

fractal.set('builder.urls', ['test']);
// 'builder.ext'?: string;
// 'builder.urls'?: WebBuilderUrls;
// 'builder.urls.ext'?: string;

fractal.components.engine(twigAdapter);
fractal.components.set('ext', '.twig');
fractal.components.set('path', __dirname + '/src/twig');
fractal.components.set('default.preview', '@preview');
fractal.components.set('default.status', 'wip');
fractal.components.set('default.collator', function (markup, item) {
	const headingStyle =
		item.preview === '@preview-dark' ? 'color: #fff;' : 'color: #000;';
	const bgStyle =
		item.preview === '@preview-dark'
			? 'background-color: #000;'
			: 'background-color: #fff;';

	return `
		<br><br>

        <div style="${bgStyle}">
            <h2 style="heading-h2 ${headingStyle}">
                ${item.title}
            </h2>

			<br>

            <div>
                ${markup}
            </div>
        </div>
    `;
});

fractal.web.theme(customizedTheme);
fractal.web.set('static.path', __dirname + '/dist');
fractal.web.set('builder.dest', __dirname + '/static-html');

// add custom commands

require('./fractal.commands');

module.exports = fractal;
