module.exports = {
	context: {
		page: {
			title: 'Search Results',
			subNav: false
		},
		show_results: true,
		search_term: 'About'
	},
	default: 'results',
	variants: [
		{
			name: 'results'
		},
		{
			name: 'no-results',
			context: {
				show_results: false
			}
		}
	]
};
