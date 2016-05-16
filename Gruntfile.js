/*global module:false*/
module.exports = function(grunt) {

  // Project configuration
  grunt.initConfig({
    "merge-json": {
      "presets": {
        src: [ 
          "src/Presets/**/*.json"
        ],
        dest: "src/presets.json",
        options: {
          space: 2
        } 
      }
    }
  });

  // These plugins provide necessary tasks
  grunt.loadNpmTasks('grunt-merge-json');

  // Default task
  grunt.registerTask('default', ['merge-json']);
};
