let img = require('../../../twig.config').context.img;

module.exports = {
    context: {
        class: 'media',
        alt: '',
        itemprop: '',
        image: 1,
        sources: [img.wide.med, img.wide.sml, img.wide.xsml, img.wide.xxsml],
    },
};
