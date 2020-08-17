let config = require(`${process.env.FRACTAL_CWD}/config.json`);

module.exports = {
    context: {
        title: 'Sub Nav Title',
        page: {
            subNav: config.navigation.sub,
        },
    },
};
