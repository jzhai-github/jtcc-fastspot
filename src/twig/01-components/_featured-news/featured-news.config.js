module.exports = {
	context: {
		title: "Happening Here",
		contentTitle: "The Latest News",
		articles: [
			{
				date: "April 21, 2020",
				title: "State Board for Community Colleges to Consider 2020-21 Fees at May Meeting"
			},
			{
				date: "April 15, 2020",
				title: "JTCC adds new Maymester Session to Meet the Needs of Students Impacted by the Stay at Home Order"
			},
			{
				date: "March 09, 2020",
				title: "Workshops and Activities Designed to Help you Succeed"
			}
		]
	},
	variants: [
		{
			name: 'default'
		},
		{
			name: 'featured',
			context: {
				theme: 'featured',
				title: "News"
			}
		},
		{
			name: 'standalone',
			context: {
				theme: 'featured',
				title: 'News',
				articles: ''
			}
		}
	]
}
