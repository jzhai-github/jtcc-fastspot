let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	variants: [
		{
			name: 'footer',
			context: {
				links: config.navigation.footer,
				title: "Footer",
				icon: "caret_right"
			}
		},
		{
			name: 'auxilary',
			context: {
				links: config.navigation.auxilary,
				title: "Auxilary",
				icon: "caret_right"
			}
		}
	]
};
