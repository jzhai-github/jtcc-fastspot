let config = require('../../config.json');

module.exports = {
    context: {
        links: config.navigation.secondary,
    },
};
