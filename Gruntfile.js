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
        '!.gitmodules',
        '!releases/**',
        '!naming-conventions.txt',
        '!phpunit.xml',
        '!bin/**',
        '!tests/**',
        '!composer.lock',
        '!wp-org-assets/**',
        '!build/**'
    ];

    // Project configuration.
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),
        svn_url: 'https://plugins.svn.wordpress.org/epoch',
        shell: {
            composer: {
                command: 'composer update'
            },
            svn_checkout: {
                command: 'svn checkout --force <%= svn_url %>/trunk build/svn/trunk'
            },
            svn_add: {
                command: "svn stat | grep '^\\?' | awk '{print $2}' | while read file; do test -n $file && svn add $file; done;",
                options: {
                    execOptions: {
                        cwd: 'build/svn/trunk'
                    }
                }
            },
            svn_rm: {
                command: "svn stat | grep '^\\!' | awk '{print $2}' | while read file; do test -n $file && svn rm $file; done;",
                options: {
                    execOptions: {
                        cwd: 'build/svn/trunk'
                    }
                }
            },
            svn_commit: {
                command: 'svn ci --force-interactive -m "Version <%= pkg.version %>"',
                options: {
                    execOptions: {
                        cwd: 'build/svn/trunk'
                    }
                }
            },
            svn_tag: {
                command: 'svn copy --force-interactive -m "Tagging version <%= pkg.version %>" <%= svn_url %>/trunk <%= svn_url %>/tags/<%= pkg.version %>'
            }
        },
        clean: {
            post_build: [
                'build/',
                'release/build',
                './build'
            ],
            pre_compress: [
                'build/releases'
            ],
            pre_svn_deploy: [
                'build/svn'
            ],
            pre_svn_copy: [
                'build/svn/trunk/**/*',
                '!build/svn/trunk/**/*.svn*'
            ]
        },
        copy: {
            build: {
                options : {
                    mode :true
                },
                src: copy_files,
                dest: 'build/<%= pkg.name %>/'
            },
            svn: {
               options: {
                   mode: true
               } ,
                src: copy_files,
                dest: 'build/svn/trunk/'
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
            },
        },
        gittag: {
            addtag: {
                options: {
                    tag: '<%= pkg.version %>',
                    message: 'Version <%= pkg.version %>',
                    force: true
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
                    src: [ 
						'package.json', 
						'composer.lock',
						'readme.txt', 
						'plugincore.php', 
						'releases/<%= pkg.name %>-<%= pkg.version %>.zip',  
						'assets/css/front/light.min.css',
						'assets/css/front/dark.min.css'
					]
                }
            }
        },
        gitpush: {
            push: {
                options: {
                    tags: true,
                    remote: 'origin',
                    branch: 'master',
                    force: true
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
                src: [ 'readme.txt' ],
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
                src: [ 'assets/js/front/helpers.js', 'assets/js/front/epoch.js' ],
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
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-remove' );


    //register default task
    grunt.registerTask( 'default', [ 'cssmin', 'concat', 'uglify' ] );

    //release tasks
    grunt.registerTask( 'version_number', [ 'replace:readme', 'replace:core_file' ] );
    grunt.registerTask( 'pre_vcs', [ 'version_number', 'shell:composer', 'copy:build', 'compress', 'clean:pre_svn_deploy'] );
    grunt.registerTask( 'do_git', [ 'gitadd', 'gitcommit', 'gittag', 'gitpush' ] );
    grunt.registerTask( 'just_build', [ 'shell:composer', 'copy:build', 'compress' ] );
    grunt.registerTask( 'do_svn', [
        'shell:svn_checkout',
        'clean:pre_svn_copy',
        'copy:svn',
        'shell:svn_add',
        'shell:svn_rm',
        'shell:svn_commit',
        'shell:svn_tag'
    ] );

    grunt.registerTask( 'release', [ 'default', 'pre_vcs', 'do_git', 'do_svn', 'clean:post_build' ] );


};
