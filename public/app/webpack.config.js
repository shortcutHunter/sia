const path = require('path');

module.exports = {
	entry: "./app",
	output: {
		filename: "app.js",
		path: path.resolve(__dirname, '../assets')
	}
}