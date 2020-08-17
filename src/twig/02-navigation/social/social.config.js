let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	context: {
		title: 'Social Nav Title',
		links: config.navigation.social
	}
};
