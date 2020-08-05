module.exports = {
	collated: true,
	context: {
		title: 'Button Text',
		class: 'button',
		link_url: '#',
		link_icon: 'caret_right'
	},
	default: 'primary',
	variants: [
		{
			name: 'base',
			context: {
				title: 'Button Text - Base'
			}
		},
		{
			name: 'primary',
			context: {
				title: 'Button Text - Primary',
				class: 'button_primary'
			}
		},
		{
			name: 'secondary',
			context: {
				title: 'Button Text - Secondary',
				class: 'button_secondary'
			}
		}
	]
};
