let img = require(`${process.env.FRACTAL_CWD}/image-sizes.json`);

module.exports = {
	context: {
		class: 'media',
		alt: '',
		itemprop: '',
		image: 1,
		sources: [img.wide.med, img.wide.sml, img.wide.xsml, img.wide.xxsml]
	}
};
