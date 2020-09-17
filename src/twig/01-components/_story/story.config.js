module.exports = {
	display: {
		'max-width': '400px'
	},
	context: {
		item: {
			theme: 'peach',
			wideImage: '12',
			label: 'Featured News',
			title: 'Getting Ready for a Successful Spring Semester',
			description:
				'It’s a new year, and a new semester is about to begin. While this is an exciting time, it can also be a hectic time for students. Here are a few tips to help you get ready for spring.',
			link: 'Read The Story'
		}
	},
	variants: [
		{
			name: 'default'
		},
		{
			name: 'turquoise',
			context: {
				item: {
					theme: 'turquoise'
				}
			}
		},
		{
			name: 'standalone',
			context: {
				item: {
					theme: 'turquoise',
					layout: 'standalone'
				}
			}
		}
	]
};
