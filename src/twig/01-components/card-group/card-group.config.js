let items = [
	{
		image: '1',
		categories: ['Emergency Medical Services', 'Next Up'],
		category_url: 'page-news-category.html',
		title: 'Next Up: Fire Captain',
		url: '#',
		date: '2019-01-01 17:00:00',
		description: 'After graduating from Tyler’s emergency medical services – paramedic program, Damian Winn’s career is heating up. He’s gone from volunteer to full-time firefighter to paramedic mentor. With his sought-after paramedic skills, he’s not just putting out fires. He’s the crucial link in keeping people alive.'
	},
	{
		image: '2',
		categories: ['Academics', 'Announcement'],
		category_url: 'page-news-category.html',
		title: 'Tyler Announces New Student Success Webinars',
		url: '#',
		date: '2019-01-01 17:00:00',
		description: 'Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Maecenas sed diam eget risus varius blandit sit amet non magna. Maecenas faucibus mollis.'
	},
	{
		image: '3',
		categories: ['Graphic Design', 'Noteworthy Stories'],
		category_url: 'page-news-category.html',
		title: 'Tyler Grad Designs Logo Supporting Frontline COVID Workers',
		url: '#',
		date: '2019-01-01 17:00:00',
		description: 'Mark Van Der Hyde is part of a family devoted to public service. His wife is a critical care nurse at Lowell General Hospital near Boston, while his Dad and brother are nurses, his sister works in a medical office, and his mom works in food service.'
	},
	{
		image: '4',
		categories: ['Academics', 'Announcement'],
		category_url: 'page-news-category.html',
		title: 'John Tyler Community College Announces its Annual Student Award Winners',
		url: '#',
		date: '2019-01-01 17:00:00',
		description: 'Each year, JTCC honors its students for their academic excellence, leadership, school pride, community service and more through its Student Awards Celebration. This year’s ceremony could not be held in person due to the pandemic, so the college announced award winners during a virtual ceremony, shared on social media.'
	}
];

module.exports = {
	context: {
		title: 'Related News',
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
