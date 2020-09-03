let faker = require('faker');
let startCase = require('lodash/startCase');
let random = require('lodash/random');

let items = Array.from({ length: 10 }, () => ({
	title: startCase(faker.random.words(random(3, 12))),
	subtitle: startCase(faker.random.words(random(2, 6))),
	description: faker.lorem.sentences(random(3, 10))
}));

module.exports = {
	context: {
		title: null,
		description: null,
		items: items
	}
};
