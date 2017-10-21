'use strict';
const gulp = require('gulp');

var exec = require('child_process').exec;

var paths = {
  styles: {
    src: 'app/themes/default_theme/public/assets/css/*.css',
    dest: 'app/public/assets/css'
  },
  scripts: {
    src: 'app/themes/default_theme/public/scripts/**/*.js',
    dest: 'app/public/assets/scripts/'
  }
};

/*
 * Define our tasks using plain functions
 */
function styles() {
  return gulp.src(paths.styles.src)

    .pipe(gulp.dest(paths.styles.dest));
}

function watch() {
  gulp.watch(paths.styles.src, styles);
}

exports.styles = styles;
exports.watch = watch;

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
var build = gulp.series(
  // clean,
  gulp.parallel(styles)
);

/*
 * You can still use `gulp.task` to expose tasks
 */
gulp.task('build', build);

/*
 * Define default task that can be called by just running `gulp` from cli
 */
gulp.task('default', build);