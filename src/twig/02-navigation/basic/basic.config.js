let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	variants: [
		{
			name: 'actions',
			context: {
				links: config.navigation.actions,
				title: "Actions",
				icon: "caret_right"
			}
		},
		{
			name: 'audience',
			context: {
				links: config.navigation.audience,
				title: "Resources for…"
			}
		},
		{
			name: 'auxilary',
			context: {
				links: config.navigation.auxilary,
				title: "Auxilary",
				icon: "caret_right"
			}
		},
		{
			name: 'footer',
			context: {
				links: config.navigation.footer,
				title: "Footer",
				icon: "caret_right"
			}
		},
		{
			name: 'main',
			context: {
				links: config.navigation.main,
				title: "Main"
			}
		},
		{
			name: 'secondary',
			context: {
				links: config.navigation.secondary,
				title: "Secondary"
			}
		}
	]
};
