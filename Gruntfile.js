module.exports = function (grunt) {

    copy_files = [
        '**',
        '!node_modules/**',
        '!release/**',
        '!.git/**',
        '!.sass-cache/**',
        '!Gruntfile.js',
        '!package.json',
        '!.gitignore',
        '!.gitmodules'
    ];

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
                'build/',
                'release/build'
            ],
            pre_compress: [
                'build/releases'
            ]
        },
        copy: {
            main: {
                src:  copy_files,
                dest: 'release/build/<%= pkg.version %>/'
            },
            svn_trunk: {
                options : {
                    mode :true
                },
                src: copy_files,
                dest: 'release/<%= pkg.name %>/trunk/'
            },
            svn_tag: {
                options : {
                    mode :true
                },
                src: copy_files,
                dest: 'release/<%= pkg.name %>/tags/<%= pkg.version %>/'
            }
        },
        run: {
            tool: {
                cmd: './composer'
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
        },
        svn_checkout: {
            make_local: {
                repos: [
                    {
                        path: [ 'release' ],
                        repo: 'http://plugins.svn.wordpress.org/acknowledge-me'
                    }
                ]
            }
        },
        push_svn: {
            options: {
                remove: true,

            },
            main: {
                src: 'release/<%= pkg.name %>',
                dest: 'http://plugins.svn.wordpress.org/acknowledge-me',
                tmp: './.build'
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
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-svn-checkout' );
    grunt.loadNpmTasks( 'grunt-push-svn' );
    grunt.loadNpmTasks( 'grunt-remove' );


    //register default task
    grunt.registerTask( 'default', [ 'cssmin', 'concat', 'uglify' ] );

    //release tasks
    grunt.registerTask( 'version_number', [ 'replace:core_file', 'replace:readme' ] );
    grunt.registerTask( 'pre_vcs', [ 'shell:composer', 'version_number', 'copy', 'compress' ] );
    grunt.registerTask( 'do_git', [ 'gitadd', 'gitcommit', 'gittag', 'gitpush' ] );
    grunt.registerTask( 'just_build', [  'shell:composer', 'copy', 'compress' ] );

    grunt.registerTask( 'release', [ 'default', 'pre_vcs', 'do_git', 'clean:post_build' ] );


};
