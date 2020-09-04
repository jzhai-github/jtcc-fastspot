let faker = require('faker');
let startCase = require('lodash/startCase');
let random = require('lodash/random');
let data = require(`${process.env.FRACTAL_CWD}/data.json`);

let programs = data.programs;
let pillVariantManifest = ['career-program', 'transfer-program'];

let items = Array.from({ length: programs.length }, (item, index) => ({
	title: startCase(programs[index]),
	subtitle: startCase(faker.random.words(random(2, 6))),
	description: faker.lorem.sentences(random(3, 10)),
	pills: pillVariantManifest.slice(0, random(1, pillVariantManifest.length))
}));

module.exports = {
	context: {
		title: null,
		description: null,
		items: items
	}
};
