let faker = require('faker');

module.exports = {
	collated: true,
	context: {
		//
	},
	default: 'dijon',
	variants: [
		{
			name: 'dijon',
			context: {
				theme: 'dijon',
				text: 'Career Program'
			}
		},
		{
			name: 'green_blue',
			context: {
				theme: 'green_blue',
				text: 'Transfer Program'
			}
		}
	]
};
