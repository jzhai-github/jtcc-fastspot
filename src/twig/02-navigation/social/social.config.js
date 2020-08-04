let config = require('../../config.json');

module.exports = {
    context: {
        title: 'Social Nav Title',
        links: config.navigation.social,
    },
};
