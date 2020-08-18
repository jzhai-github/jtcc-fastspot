let faker = require('faker');

let getContext = (obj) => {
	return Object.assign(
		{},
		{
			quote: faker.lorem.paragraph(),
			name: faker.name.findName(),
			title: faker.name.jobType()
		},
		obj
	);
};

module.exports = {
	collated: true,
	context: getContext({ theme: 'blue', image: 4 }),
	default: 'blue',
	variants: [
		{
			name: 'blue',
			context: {
				theme: 'blue',
				quote: `The support at Tyler was a really big factor for me in finding my self-confidence. You always have people tell you, ‘You can do it!’`,
				name: 'Ravonte Campbell',
				title: 'Mechanical Engineering'
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
