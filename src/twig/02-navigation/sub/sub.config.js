let config = require('../../config.json');

module.exports = {
    context: {
        title: 'Sub Nav Title',
        page: {
            subNav: config.navigation.sub,
        },
    },
};
