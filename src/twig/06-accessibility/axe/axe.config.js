let fs = require('fs');
let fractal = require('../../../../fractal.config');

let axePath = `${fractal.web.get('static.path')}/axe.json`;
let results = fs.existsSync(axePath) ? require(axePath) : [];

module.exports = {
    context: {
        results: results,
    },
};
