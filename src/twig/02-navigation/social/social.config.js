let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	context: {
		title: 'Social',
		links: config.navigation.social
	}
};
