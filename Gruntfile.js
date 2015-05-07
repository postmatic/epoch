module.exports = function (grunt) {


    // Project configuration.
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),
        shell: {
            composer: {
                command: 'composer update'
            }
        },
        clean: {
            post_build: [
                'build/'
            ],
            pre_compress: [
                'build/releases'
            ]
        },
        run: {
            tool: {
                cmd: './composer'
            }
        },
        copy: {
            build: {
                options : {
                    mode :true
                },
                src: [
                    '**',
                    '!node_modules/**',
                    '!releases',
                    '!releases/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules',
                    '!.gitattributes',
                    '!composer.lock',
                    '!naming-conventions.txt',
                    '!how-to-grunt.md',
                    '!.travis.yml',
                    '!.scrutinizer.yml',
                    '!phpunit.xml',
                    '!tests/**',
                    '!build',
                    '!build/**'
                ],
                dest: 'build/'
            }
        },
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: 'releases/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'build/',
                src: [
                    '**/*',
                    '!build/*'
                ]
            }
        },
        gitadd: {
            add_zip: {
                options: {
                    force: true
                },
                files: {
                    src: [ 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gittag: {
            addtag: {
                options: {
                    tag: '<%= pkg.version %>',
                    message: 'Version <%= pkg.version %>'
                }
            }
        },
        gitcommit: {
            commit: {
                options: {
                    message: 'Version <%= pkg.version %>',
                    noVerify: true,
                    noStatus: false,
                    allowEmpty: true
                },
                files: {
                    src: [ 'package.json', 'readme.txt', 'plugincore.php', 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gitpush: {
            push: {
                options: {
                    tags: true,
                    remote: 'origin',
                    branch: 'master'
                }
            }
        },
        replace: {
            core_file: {
                src: [ 'plugincore.php' ],
                overwrite: true,
                replacements: [{
                    from: /Version:\s*(.*)/,
                    to: "Version: <%= pkg.version %>"
                }, {
                    from: /define\(\s*'EPOCH_VER',\s*'(.*)'\s*\);/,
                    to: "define( 'EPOCH_VER', '<%= pkg.version %>' );"
                }]
            },
            readme: {
                src: [ 'reamde.txt' ],
                overwrite: true,
                replacements: [{
                    from: /Stable Tag:\s*(.*)/,
                    to: "Stable Tag: <%= pkg.version %>"
                }]
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/css/front/light.min.css': [ 'assets/css/front/light.css', 'assets/css/modals.css' ],
                    'assets/css/front/dark.min.css': [ 'assets/css/front/dark.css', 'assets/css/modals.css' ]
                }
            }
        },
        concat: {
            options: {

            },
            dist: {
                src: [ 'assets/js/wp-baldrick-full.js', 'assets/js/front/helpers.js', 'assets/js/front/epoch.js' ],
                dest: 'assets/js/front/epoch-front-compiled.js'
            }
        },
        uglify: {
            front: {
                files: {
                    'assets/js/front/epoch.min.js': [ 'assets/js/front/epoch-front-compiled.js' ]
                }
            }
        }

    });


    //load modules
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-git' );
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-shell' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-concat' );


    //register default task
    grunt.registerTask( 'default', [ 'cssmin', 'concat', 'uglify' ] );

    //release tasks
    grunt.registerTask( 'version_number', [ 'replace:core_file', 'replace:readme' ] );
    grunt.registerTask( 'pre_vcs', [ 'shell:composer', 'version_number', 'copy', 'compress' ] );
    grunt.registerTask( 'do_git', [ 'gitadd', 'gitcommit', 'gittag', 'gitpush' ] );
    grunt.registerTask( 'just_build', [  'shell:composer', 'copy', 'compress' ] );

    grunt.registerTask( 'release', [ 'default', 'pre_vcs', 'do_git', 'clean:post_build' ] );


};
