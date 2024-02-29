module.exports = function( grunt ) {

	const sass = require('node-sass');

	// Load tasks.
	require( 'matchdep' ).filterDev(['grunt-*']).forEach( grunt.loadNpmTasks );

	// Project configuration.
	grunt.initConfig({
		sass: {
			all: {
				expand: true,
				cwd: "scss",
				dest: "stylesheets",
				ext: '.css',
				src: [ '**/*.scss' ],
				options: {
					implementation: sass,
					outputStyle: 'expanded'
				}
			}
		},
		watch: {
			all: {
				files: [ "scss/**/*.scss" ],
				tasks: [ "sass:all" ],
				options: {
					spawn: false
				}
			}
		}
	});

	grunt.registerTask( 'default', [ 'sass:all' ] );
};

