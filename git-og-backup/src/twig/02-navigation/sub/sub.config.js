let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	preview: '@preview-dark',
	context: {
		title: 'Sub Nav Title',
		page: {
			subNav: config.navigation.sub,
			icon: 'caret_right'
		}
	}
};
