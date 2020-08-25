let faker = require('faker');
let random = require('lodash/random');

let getContext = (obj) => {
	return Object.assign(
		{},
		{
			quote: faker.lorem.paragraph(),
			name: faker.name.findName(),
			title: faker.name.jobType(),
			image: random(0, 11)
		},
		obj
	);
};

module.exports = {
	status: 'ready',
	collated: true,
	context: getContext({ theme: 'blue' }),
	default: 'blue',
	variants: [
		{
			name: 'blue',
			context: {
				theme: 'blue',
				quote: `The support at Tyler was a really big factor for me in finding my self-confidence. You always have people tell you, ‘You can do it!’`,
				name: 'Ravonte Campbell',
				title: 'Mechanical Engineering',
				image: 4
			}
		},
		{
			name: 'maroon',
			context: getContext({
				theme: 'maroon'
			})
		},
		{
			name: 'gold',
			context: getContext({ theme: 'gold' })
		}
	]
};
