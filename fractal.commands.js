// https://fractal.build/guide/cli/custom-commands.html

let fractal = require('./fractal.config');
let { table, getBorderCharacters } = require('table');
let chalk = require('chalk');
let hexes = {
	fastspot: '#DA2315'
};
let collectionAllowList = ['component', 'partial', 'layout', 'page'];

function getCollection(app) {
	return app.components
		.flatten()
		.toArray()
		.filter((component) => {
			if (component.isHidden) return false;

			return collectionAllowList.some(
				(type) => component.getProps().prefix === type
			);
		});
}

function getOutput(collection, headers = []) {
	let data = collection.reduce(
		(accumulator, component) => {
			let status = component.status;

			accumulator.push([
				component.label,
				component.parent.label,
				chalk.bgHex(status.color)(status.label)
			]);

			return accumulator;
		},
		[headers]
	);

	return table(data, {
		border: getBorderCharacters('ramac'),
		singleLine: true
	});
}

function logOutput(collection, output, title = 'Fractal Library Status') {
	this.log(output);
	this.log(chalk.bgHex(hexes.fastspot).whiteBright(title));
	this.log(
		chalk
			.bgHex(hexes.fastspot)
			.whiteBright(`Total Items: ${collection.length}`)
	);
	this.log('\n');
}

function listComponents(args, done) {
	let app = this.fractal;
	let collection = getCollection(app);
	let output = getOutput(collection, [
		chalk.bold('Component'),
		chalk.bold('Type'),
		chalk.bold('Status')
	]);

	logOutput.call(this, collection, output);

	let itemsReadyForCMS = collection.filter(
		(item) => item.status.label.toLowerCase() === 'ready'
	);

	let readyOutput = getOutput(itemsReadyForCMS, [
		chalk.bold('Component'),
		chalk.bold('Type'),
		chalk.bold('Status')
	]);

	logOutput.call(this, itemsReadyForCMS, readyOutput, 'Items Ready for CMS');

	done();
}

fractal.cli.command('list', listComponents, {
	description: 'Lists components in the project'
});
