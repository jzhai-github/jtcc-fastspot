module.exports = {
	variants: [
		{
			name: 'chester',
			context: {
				item: {
					title: "Chester Campus",
					phone_numbers: [
						{
							label: 'mobile',
							type: 'telephone',
							number: '804-796-4000'
						}
					],
					email: 'studentactivities@jtcc.edu',
					location: '13101 Jefferson Davis Highway<br>Chester, Virginia 23831-5316',
					office_hours: [
						{
							label: "Monday – Friday",
							hours: "9:00 am – 7:00 pm"
						},
						{
							label: "Saturday",
							hours: "11:00 am – 2:00 am"
						}
					],
					social_links: [
						{
							title: 'Facebook',
							url: '#'
						},
						{
							title: 'Twitter',
							url: '#'
						}
					]
				}
			}
		},
		{
			name: 'midlothian',
			context: {
				item: {
					title: "Midlothian Campus",
					phone_numbers: [
						{
							label: 'mobile',
							type: 'telephone',
							number: '804-796-4000'
						}
					],
					email: 'studentactivities@jtcc.edu',
					location: 'T Building, Room 100',
					office_hours: [
						{
							label: "Monday – Friday",
							hours: "9:00 am – 7:00 pm"
						}
					]
				}
			}
		}
	]
};
