var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var beep    = require('beepbeep');
var exec    = require('child_process').exec;
var gutil   = require('gulp-util');

'use strict';
const release = require('gulp-release');

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

    gulp.watch('./app/public/*.php',  ['behat-watch']);
});

release.register(gulp, {packages: ['app/composer.json', 'package.json']});

gulp.task('default', ['serve']);