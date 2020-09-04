let faker = require('faker');
let startCase = require('lodash/startCase');

module.exports = {
	context: {
		text: startCase(faker.random.words(3)),
		icon: 'caret_right'
	}
};
