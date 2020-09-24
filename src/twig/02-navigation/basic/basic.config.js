let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
	variants: [
		{
			name: 'actions',
			context: {
				class: 'actions_nav',
				links: config.navigation.actions,
				title: 'Actions',
				icon: 'caret_right'
			}
		},
		{
			name: 'audience',
			context: {
				class: 'audience_nav',
				links: config.navigation.audience,
				title: 'Resources for…'
			}
		},
		{
			name: 'auxilary',
			context: {
				class: 'auxilary_nav',
				links: config.navigation.auxilary,
				title: 'Auxilary',
				icon: 'caret_right'
			}
		},
		{
			name: 'footer',
			context: {
				links: config.navigation.footer,
				title: 'Footer',
				icon: 'caret_right'
			}
		},
		{
			name: 'main',
			context: {
				class: 'main_nav',
				title: 'Site',
				toggle_icon: 'caret_down',
				links: config.navigation.main,
				title: 'Main'
			}
		},
		{
			name: 'secondary',
			context: {
				class: 'secondary_nav',
				links: config.navigation.secondary,
				title: 'Secondary'
			}
		}
	]
};
