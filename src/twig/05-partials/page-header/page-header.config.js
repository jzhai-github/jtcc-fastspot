module.exports = {
	context: {
		title: "Contact Us"
	},
	default: 'default',
	variants: [
		{
			name: 'default',
			label: 'Default'
		},
		{
			name: 'description',
			context: {
				description: "Keeping connected to the JTCC community is an important way that we can provide student support, and we are always here to help – whether you are looking to get involved or looking to visit us. Just reach out!"
			}
		},
		{
			name: 'no-nav',
			context: {
				subNav: false
			}
		}
	]
};
