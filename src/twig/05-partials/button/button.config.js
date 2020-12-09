let faker = require('faker');
let startCase = require('lodash/startCase');

module.exports = {
	collated: true,
	context: {
		title: startCase(faker.random.words(3)),
		class: 'button',
		url: '#',
		icon: 'caret_right'
	},
	default: 'primary',
	variants: [
		{
			name: 'primary',
			context: {
				class: 'button_primary'
			}
		},
		{
			name: 'secondary',
			context: {
				class: 'button_secondary'
			}
		}
	]
};
