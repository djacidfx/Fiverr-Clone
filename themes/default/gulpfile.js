var gulp = require('gulp');
var browserSync = require('browser-sync').create();
var zip = require('gulp-zip');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');

// Static Server + watching scss/html files
gulp.task('serve', ['sass'], function() {

    browserSync.init({
		proxy: "localhost/microncer-5",
		ghostMode: false,
		port: 8000
    });

    gulp.watch(['scss/*.scss', 'scss/**/*.scss'], ['sass']).on('change', browserSync.reload);
    gulp.watch("./*.html").on('change', browserSync.reload);
});
// ['foo/*', 'bar/*']

gulp.task('sass', function() {
    return gulp.src(['scss/*.scss', 'scss/**/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'expanded', // nested,compact,expanded,compressed
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions', 'ie >= 9', 'Android >= 2.3', 'Firefox >= 14']
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest("./css"))
        .pipe(browserSync.stream());
});


//Watch For changes
gulp.task('watch', ['serve']);


