module.exports = function (context) {
	let { isProduction } = context.options;

	return {
		plugins: [
			require('postcss-pxtorem')({
				rootValue: 16,
				replace: isProduction ? true : false,
				propList: [
					'font-size',
					'line-height',
					'letter-spacing',
					'*margin*',
					'*padding*',
					'*width*',
					'*height*'
				]
			}),
			require('autoprefixer')
		]
	};
};
