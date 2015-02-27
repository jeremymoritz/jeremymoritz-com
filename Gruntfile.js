var lessToCSSFiles = {};
var cssToPleeeaseFiles = {};

function addFilesToProcess(cssPath, baseFilenames) {
	for (var i = 0; i < baseFilenames.length; i++) {
		var filename = cssPath + baseFilenames[i];
		// target.css file: source.less file
		lessToCSSFiles[filename + '.css'] = filename + '.less';
		cssToPleeeaseFiles[filename + '.css'] = filename + '.css';
	}
}

addFilesToProcess('includes/', ['jeremy', 'jeremy_old']);	// process custom css files
addFilesToProcess('support/', ['bootstrap-jm-custom']);	// process custom css files

module.exports = function(grunt) {
	grunt.initConfig({
		less: {
			development: {
				options: {
					compress: true,
					yuicompress: true,
					optimization: 2
				},
				files: lessToCSSFiles
			}
		},
		pleeease: {
			custom: {
				options: {
					optimizers: {
						minifier: false
					}
				},
				files: cssToPleeeaseFiles
			}
		},
		watch: {
			styles: {
				// Which files to watch (all .less files recursively)
				files: [
					'**/*.less'
				],
				tasks: [
					'less',
					'pleeease'
				],
				options: {
					nospawn: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-pleeease');

	grunt.registerTask('default', ['watch']);
};
