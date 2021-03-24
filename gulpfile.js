const gulp = require('gulp');
const axeWebdriver = require('gulp-axe-webdriver');
const gulpif = require('gulp-if');
const rename = require('gulp-rename');
const { exec } = require('child_process');
const del = require('del');
const imagemin = require('imagemin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const imageminPngquant = require('imagemin-pngquant');
const sourcemaps = require('gulp-sourcemaps');
const realFavicon = require('gulp-real-favicon');
const sass = require('gulp-sass');
const sassGlob = require('gulp-sass-glob');
const postcss = require('gulp-postcss');
const svgstore = require('gulp-svgstore');
const svgmin = require('gulp-svgmin');
const named = require('vinyl-named');
const fractal = require('./fractal.config');
const fractalLogger = fractal.cli.console;
const webpack = require('webpack-stream');
const webpackCompiler = require('webpack');

const webpackConfig = require('./webpack.config');
const config = require('./config.json');
const svgoConfig = require('./svgo.config.json');

const CI = !!process.env.CI;
const env = process.env.NODE_ENV || 'development';
const isProduction = env === 'production';
const isDevelopment = !isProduction;
const aliases = {
	dist: 'dist',
	build: 'static-html'
};
const basePath = `${__dirname}/${aliases.dist}`;
const srcPath = `${__dirname}/src`;
const paths = {
	js: {
		src: `${srcPath}/js/site.js`,
		dest: `${basePath}/js`
	},
	templates: {
		path: `${srcPath}/twig`
	},
	css: {
		src: config.css,
		dest: `${basePath}/css`
	},
	icons: {
		src: `${srcPath}/icons/*.svg`,
		dest: `${basePath}/images/src`,
		destSprite: `${basePath}/images`
	},
	images: {
		src: [`${srcPath}/images/*.{jpg,png,svg}`],
		dest: `${basePath}/images`
	},
	favicons: {
		twigFilePath: `${srcPath}/twig/05-partials/_hidden`
	}
};
const context = { isDevelopment, isProduction, basePath, srcPath, paths };

function scripts() {
	return gulp
		.src(paths.js.src)
		.pipe(named())
		.pipe(webpack(webpackConfig(env, context), webpackCompiler))
		.pipe(gulp.dest(paths.js.dest));
}

function styles() {
	return gulp
		.src(paths.css.src)
		.pipe(gulpif(isDevelopment, sourcemaps.init()))
		.pipe(sassGlob())
		.pipe(
			sass({
				outputStyle: isProduction ? 'compressed' : 'expanded',
				includePaths: ['node_modules', paths.css.src]
			}).on('error', sass.logError)
		)
		.pipe(postcss(context))
		.pipe(gulpif(isDevelopment, sourcemaps.write('.')))
		.pipe(gulp.dest(paths.css.dest));
}

function images() {
	return imagemin(paths.images.src, {
		destination: paths.images.dest,
		plugins: [imageminMozjpeg(), imageminPngquant({ quality: [0.6, 0.8] })]
	});
}

function icons() {
	return gulp
		.src(paths.icons.src)
		.pipe(svgmin(svgoConfig))
		.pipe(gulp.dest(paths.icons.dest))
		.pipe(
			svgstore({
				inlineSvg: true
			})
		)
		.pipe(
			rename({
				basename: 'icons'
			})
		)
		.pipe(gulp.dest(paths.icons.destSprite));
}

function axe(cb) {
	let templateFilter = process.env.AXE_FILTER || '';
	let urlBlockList = ['404', 'search-results', 'preview'];

	fractal.load().then(() => {
		let urls = isDevelopment
			? fractal.components
					.filter((item) =>
						item.handle.startsWith(`template-${templateFilter}`)
					)
					.filter(
						(item) =>
							!urlBlockList.some((slug) =>
								item.handle.endsWith(slug)
							)
					)
					.flattenDeep()
					.toArray()
					.map(
						(item) =>
							`http://localhost:3000/components/preview/${item.handle}`
					)
			: `${aliases.build}/components/preview/template*.html`;

		axeWebdriver({
			folderOutputReport: basePath,
			saveOutputIn: 'axe.json',
			urls: urls,
			headless: true,
			showOnlyViolations: true,
			verbose: true
		}).then(cb);
	});
}

function favicons(done) {
	const faviconFile = `${__dirname}/favicons/markup.json`;
	const faviconSettings = require('./favicon-settings')({
		config,
		faviconFile
	});

	realFavicon.generateFavicon(faviconSettings, () => {
		gulp.src(`${paths.favicons.twigFilePath}/favicons.twig`)
			.pipe(
				realFavicon.injectFaviconMarkups(
					require(faviconFile).favicon.html_code
				)
			)
			.pipe(gulp.dest(paths.favicons.twigFilePath));

		done();
	});
}

function clean() {
	return del([
		`${basePath}/css`,
		`${basePath}/images`,
		`${basePath}/js`,
		`${basePath}/static-html`
	]);
}

function watch(cb) {
	gulp.watch([`${srcPath}/css/**/*.scss`], styles);
	gulp.watch([`${srcPath}/js/**/*.js`], scripts);
	gulp.watch([`${srcPath}/icons/*.svg`], icons);
	gulp.watch([`${srcPath}/images/*`], images);
	gulp.watch(['package.json'], gulp.parallel(styles, scripts));

	cb();
}

function fractalSync() {
	const server = fractal.web.server({
		sync: true,
		port: 3000
	});

	server.on('error', (err) => fractalLogger.error(err.message));

	return server.start().then(() => {
		fractalLogger.success(`Fractal server is now running at ${server.url}`);
	});
}

function fractalBuild() {
	const builder = fractal.web.builder();

	if (!CI) {
		builder.on('progress', (completed, total) =>
			fractalLogger.update(
				`Exported ${completed} of ${total} items`,
				'info'
			)
		);
	} else {
		builder.on('start', () => {
			fractalLogger.update(`Exporting items...`, 'info');
		});
	}

	builder.on('error', (err) => fractalLogger.error(err.message));

	return builder.start().then(() => {
		fractalLogger.success('Fractal build completed!');
	});
}

function cmsGitHook() {
	/**
	 * Here we make a git hook for pre-commit so
	 * that when running this task you don't
	 * accidentally commit compiled code
	 */
	exec('git rev-parse --show-toplevel', (error, stdout, stderr) => {
		const dir = stdout.trim();

		fs.writeFile(
			`${dir}/.git/hooks/pre-commit`,
			`#!/usr/bin/php

			<?php
			$base_path = trim(shell_exec("git rev-parse --show-toplevel"));
			exec("git reset ".$base_path."/static-html");
			exec("git reset ".$base_path."/css");
			exec("git reset ".$base_path."/images");
			exec("git reset ".$base_path."/js");
		
			$is_commitable = false;
			$status = shell_exec("git status --porcelain");
			$status_lines = explode("\n", $status);
		
			foreach ($status_lines as $line) {
				$char = substr($line, 0, 1);
		
				if ($char == "A" || $char == "D" || $char == "M") {
					$is_commitable = true;
				}
			}
		
			if (!$is_commitable) {
				echo "No changes available to commit that were not compiled code.";
				die(1);
			}
		`
		);
	});
}

function confirmProductionMode(done) {
	if (!isProduction) {
		return done(
			"@Fastspot - Production mode is not enabled. Please confirm `process.env.NODE_ENV === 'production'`"
		);
	}

	done();
}

const build = gulp.series(clean, gulp.parallel(styles, scripts, icons, images));
const buildAll = gulp.series(build, fractalBuild);
const _cms = gulp.series(
	cmsGitHook,
	gulp.parallel(styles, scripts, icons, images),
	watch
);
const github_build = gulp.series(confirmProductionMode, build, fractalBuild);
const _watch = gulp.series(build, fractalSync, watch);

exports.build = build;
exports.build_all = buildAll;
exports.styles = styles;
exports.scripts = scripts;
exports.axe = axe;
exports.icons = icons;
exports.images = images;
exports.favicons = favicons;
exports.clean = clean;
exports.github_build = github_build;
exports['fractal:sync'] = fractalSync;
exports['fractal:build'] = fractalBuild;
exports.cms = _cms;
exports.watch = _watch;
exports.default = _watch;
