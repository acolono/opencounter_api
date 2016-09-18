var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var beep    = require('beepbeep');
var sys     = require('sys');
var exec    = require('child_process').exec;
var gutil   = require('gulp-util');
var plumber = require('gulp-plumber');

var onError = function(err) {
    beep([1000, 1000, 1000]);
    gutil.log(gutil.colors.red(err));
}

var onSuccess = function(message) {
    gutil.log(gutil.colors.green(message));
}

gulp.task('behat', function() {
    exec('bin/behat', function(error, stdout) {
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
        server: "./descriptions/behat/reports/html/behat"
    });

    gulp.watch('./public/*.php',  ['behat-watch']);
});


gulp.task('default', ['serve']);