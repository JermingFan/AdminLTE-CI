/**
 * @description javascript for gulpfile
 * @author TuzK1ss
 * @date 15/12/10.
 */

// GENERAL
var gulp = require('gulp');
var clean = require('gulp-clean');
var rename = require('gulp-rename');

// SASS
var sass = require('gulp-sass');
var concatCss = require('gulp-concat-css');
var minifyCss = require('gulp-minify-css');

// ES6
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

// HTML
var md5 = require('gulp-md5-plus');

// base path
var basePath = 'static/unicorn/';
var htmlUrl = 'application/views/unicorn/';
var devFile = htmlUrl + 'index-dev.html';
var indexFile = htmlUrl + 'index.html';

// DEFAULT TASK
gulp.task('default', function () {

    console.log('gulp default running...');

    gulp.run(['clean', 'copyHtml', 'sass', 'es6']);
});

// CLEAN TASK

gulp.task('clean', function () {
    gulp.run(['cleanHtml', 'cleanCss', 'cleanScript']);
});

gulp.task('cleanHtml', function () {
   return gulp.src(indexFile)
       .pipe(clean({force : true}));
});

gulp.task('copyHtml', ['cleanHtml'], function () {
   return gulp.src(devFile)
       .pipe(rename('index.html'))
       .pipe(gulp.dest(htmlUrl));
});

gulp.task('cleanCss', function () {
     return gulp
        .src(basePath + 'css')
        .pipe(clean({force : true}));
});

gulp.task('cleanScript', function () {
    return gulp
        .src(basePath + 'scripts/core')
        .pipe(clean({force : true}));
});

// SASS TASK
gulp.task('sass', ['cleanCss', 'copyHtml'], function () {
     gulp.src(basePath + 'sass/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('index.css'))
        .pipe(gulp.dest(basePath + 'css'))
        .pipe(rename('index.min.css'))
        .pipe(minifyCss())
        .pipe(gulp.dest(basePath + 'css'))
        .pipe(md5(16, indexFile))
        .pipe(gulp.dest(basePath + 'css'));
});

// ES6 TASK
gulp.task('es6', ['cleanScript', 'copyHtml'], function () {
    gulp.src(basePath + 'es6/*.es6')
        .pipe(babel({
            presets: ['es2015']
        }))
        .on('error', console.error.bind(console))
        .pipe(concat('index.js'))
        .pipe(gulp.dest(basePath + 'scripts/core'))
        .pipe(rename('index.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(basePath + 'scripts/core'))
        .pipe(md5(16, indexFile))
        .pipe(gulp.dest(basePath + 'scripts/core'));

});

// SASS & ES6 WATCH
gulp.task('watch', function () {
    gulp.run(['sass:watch', 'es6:watch', 'html:watch']);
});

gulp.task('sass:watch', function () {
    gulp.watch(basePath + 'sass/*.scss', ['sass']);
});

gulp.task('es6:watch',  function () {
    gulp.watch(basePath + 'es6/*.es6', ['es6']);
});

gulp.task('html:watch',  function () {
    gulp.watch(devFile, ['default']);
});