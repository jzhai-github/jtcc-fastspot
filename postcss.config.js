module.exports = function (context) {
	let { isProduction } = context.options;

	return {
		plugins: [
			require('postcss-pxtorem')({
				rootValue: 16,
				replace: isProduction ? true : false,
				propList: [
					'*border*',
					'font-size',
					'*height*',
					'letter-spacing',
					'line-height',
					'*margin*',
					'*padding*',
					'*width*'
				]
			}),
			require('autoprefixer')
		]
	};
};
