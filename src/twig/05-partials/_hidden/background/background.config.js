let img = require(`${process.env.FRACTAL_CWD}/image-sizes.json`);

module.exports = {
	context: {
		// custom_class: 'js-class',
		img: img,
		class: 'flex_callout',
		image: 1,
		alt: '',
		sources: {
			'0px': img.square.sml,
			'740px': img.ultrawide.med,
			'980px': img.ultrawide.lrg,
			'1220px': img.ultrawide.xlrg
		}
	}
};
