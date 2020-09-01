module.exports = {
	context: {
		label: 'Directory',
		active: 'search',
		action_category: '#',
		action_search: '#',
		tools: [
			{
				label: 'Filter by Degree & Certificate Type',
				options: [
					{
						label: 'All Degrees & Certificates',
						selected: true
					},
					{
						label: 'Category One'
					},
					{
						label: 'Another Category'
					}
				]
			},
			{
				label: 'Filter by Career Cluster',
				options: [
					{
						label: 'All Career Clusters',
						selected: true
					},
					{
						label: 'Category One'
					},
					{
						label: 'Another Category'
					}
				]
			},
			{
				label: 'Filter by Modes of Study',
				options: [
					{
						label: 'All Modes of Study',
						selected: true
					},
					{
						label: 'Category One'
					},
					{
						label: 'Another Category'
					}
				]
			}
		],
		search_placeholder: 'Search by keyword',
		results: '12',
		category: 'Information Technology'
	}
};
