module.exports = {
	collated: true,
	context: {
		title: 'In the Classroom',
		format: 'unbalanced',
		rows: [
			[
				{
					image: '2',
					caption:
						"In her favorite class, Language Arts for Young Children, she practiced reading a children's book to her classmates, enunciating carefully and adding character voices as if she were reading to children."
				},
				{
					image: '3',
					caption:
						'Madeline Chase teaching in her cozy corner reading. nook.'
				}
			],
			[
				{
					image: '6',
					caption:
						'Madeline Chase teaching in her cozy corner reading. nook.'
				},
				{
					image: '5',
					caption: ''
				}
			]
		]
	},
	variants: [
		{
			name: 'equal',
			context: {
				format: 'equal'
			}
		},
		{
			name: 'flipped',
			context: {
				flipped: true
			}
		},
		{
			name: 'vertical-only',
			context: {
				format: 'vertical_only',
				rows: [
					[
						{
							image: '2',
							caption:
								"In her favorite class, Language Arts for Young Children, she practiced reading a children's book to her classmates, enunciating carefully and adding character voices as if she were reading to children."
						},
						{
							image: '3',
							caption:
								'Madeline Chase teaching in her cozy corner reading. nook.'
						},
						{
							image: '4',
							caption:
								"In her favorite class, Language Arts for Young Children, she practiced reading a children's book to her classmates, enunciating carefully and adding character voices as if she were reading to children."
						}
					],
					[
						{
							image: '6',
							caption:
								'Madeline Chase teaching in her cozy corner reading. nook.'
						},
						{
							image: '5',
							caption:
								"In her favorite class, Language Arts for Young Children, she practiced reading a children's book to her classmates, enunciating carefully and adding character voices as if she were reading to children."
						}
					]
				]
			}
		}
	]
};
