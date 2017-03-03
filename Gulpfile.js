'use strict';
const gulp = require('gulp');
const release = require('gulp-release');
var browserSync = require('browser-sync').create();
var beep    = require('beepbeep');
var exec    = require('child_process').exec;
var gutil   = require('gulp-util');

var paths = {
    styles: {
        src: 'app/themes/default_theme/public/css/*.css',
        dest: 'app/public/assets/css'
    },
    scripts: {
        src: 'app/themes/default_theme/public/scripts/**/*.js',
        dest: 'app/public/assets/scripts/'
    }
};

var onError = function(err) {
    beep([1000, 1000, 1000]);
    gutil.log(gutil.colors.red(err));
};

var onSuccess = function(message) {
    gutil.log(gutil.colors.green(message));
};

gulp.task('behat', function() {
    exec('docker exec -t -i opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/behat -c /var/www/opencounter-slim-codenv/behat.yml', function(error, stdout) {
        if(error !== null)
        {
            onError(stdout);
        }
        else
        {
            onSuccess(stdout);
        }
    });
});



// create a task that ensures the `behat` task is complete before
// reloading browsers
gulp.task('behat-watch', ['behat'], function (done) {
    browserSync.reload();
    done();
});

// Static Server + watching scss/html files for tests
gulp.task('serve', ['behat'], function() {

    browserSync.init({
        server: "./app/tests/behat/reports/html/behat"
    });

    gulp.watch('./app/src/*.php',  ['behat-watch']);
});

release.register(gulp, { packages: ['package.json', './**/composer.json'] });


// CSS Copy
gulp.task('copy:css', function(){
    return gulp.src(paths.styles.src)
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(browserSync.stream());
});

gulp.task('default', ['serve']);