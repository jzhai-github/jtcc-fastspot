let items = [
	{
		image: '1',
		title: 'Ornare Egestas',
		description:
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. Vivamus ac turpis quis eros consectetur ornare. '
	},
	{
		image: '2',
		stat: {
			figure: '100%',
			context: 'of undergraduate students'
		},
		description:
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. <a href='#'>Vivamus ac turpis</a> quis eros consectetur ornare. "
	},
	{
		image: '3',
		title: 'Ornare Egestas',
		description:
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. Vivamus ac turpis quis eros consectetur ornare. '
	},
	{
		image: '4',
		stat: {
			figure: '100%',
			context: 'of undergraduate students'
		},
		description:
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. Vivamus ac turpis quis eros consectetur ornare. '
	},
	{
		image: '1',
		title: 'Ornare Egestas',
		description:
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. Vivamus ac turpis quis eros consectetur ornare. '
	},
	{
		image: '2',
		stat: {
			figure: '100%',
			context: 'of undergraduate students'
		},
		description:
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget est sed ex pretium fringilla. <a href='#'>Vivamus ac turpis</a> quis eros consectetur ornare. "
	}
];

module.exports = {
	context: {
		title: 'Facts & Figures',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auct.',
		items: items
	},
	default: 'six-up',
	variants: [
		{
			name: 'six-up',
			context: {
				items: items.slice(0, 6)
			}
		},
		{
			name: 'five-up',
			context: {
				items: items.slice(0, 5)
			}
		},
		{
			name: 'four-up',
			context: {
				items: items.slice(0, 4)
			}
		},
		{
			name: 'three-up',
			context: {
				items: items.slice(0, 3)
			}
		},
		{
			name: 'two-up',
			context: {
				items: items.slice(0, 2)
			}
		},
		{
			name: 'one-up',
			context: {
				items: items.slice(0, 1)
			}
		}
	]
};
