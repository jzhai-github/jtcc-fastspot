let config = require('./config');
let imageSizes = require('./image-sizes');

module.exports = {
    context: {
        grid: true,
        cell: 'fs-lg-10 fs-xl-8 fs-lg-justify-center',
        page: {
            title: 'Page Title',
			description: "Dis facilisis tellus ultricies vestibulum cubilia risus, blandit commodo hac ut posuere ex cursus, class libero imperdiet nullam odio.",
            layout: 'default',
            theme: 'default',
            activePage: 0,
            subNav: false,
            classes: [],
        },
        img: imageSizes,
        config: config.twig_variables,
        navigation: config.navigation,
    },
};
