let items = [
	{
		image: '1',
		date: {
			from: {
				begin: '2017-05-31 17:00:00',
				end: '2017-05-31 19:00:00'
			},
			to: {
				begin: '2017-06-13 17:00:00',
				end: '2017-06-13 19:00:00'
			}
		},
		title: 'In Enim Justo Rhoncus Ut',
		url: 'page-event-detail.html',
		description:
			'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.',
		location: 'Fastspot, 2980 Long Name of Road Rd. Baltimore, MD 21218',
		categories: ['Category One'],
		category_url: 'page-event-category.html'
	},
	{
		image: '4',
		date: {
			from: {
				begin: '2017-05-31 17:00:00'
			}
		},
		title: 'Aenean commodo ligula eget dolor',
		url: 'page-event-detail.html',
		description:
			'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.',
		location: 'Location',
		categories: ['Category One'],
		category_url: 'page-event-category.html'
	},
	{
		image: '2',
		date: {
			from: {
				begin: '2017-05-31 17:00:00',
				end: '2017-05-31 19:00:00'
			},
			to: {
				begin: '2017-06-13 17:00:00',
				end: '2017-06-13 19:00:00'
			}
		},
		title: 'In Enim Justo Rhoncus Ut',
		url: 'page-event-detail.html',
		location: 'Location',
		categories: ['Category One'],
		category_url: 'page-event-category.html'
	},
	{
		image: '3',
		date: {
			from: {
				begin: '2017-06-13 17:00:00',
				end: '2017-06-13 19:00:00'
			}
		},
		title: 'Aenean commodo ligula eget dolor',
		url: 'page-event-detail.html',
		description:
			'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.',
		location: 'Location',
		categories: ['Category One'],
		category_url: 'page-event-category.html'
	}
];

module.exports = {
	context: {
		title: 'Related Events',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auct.',
		items: items
	},
	default: 'four-up',
	variants: [
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
