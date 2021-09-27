const path = require('path');

module.exports = {
	entry: {
		app: "./src/app",
		assets: "./src/app.assets"
	},
	output: {
		filename: "[name].bundle.js",
		path: path.resolve(__dirname, 'build')
	}
}