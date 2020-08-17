let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	context: {
		links: config.navigation.secondary
	}
};
