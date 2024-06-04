var project                 = 'meet-my-team'; // Project Name.


var gulp = require('gulp');
var zip = require('gulp-zip');
var del = require('del');
var rename = require('gulp-rename');
var gutil = require('gulp-util');
var dirSync = require( 'gulp-directory-sync' );
var removeLines = require('gulp-remove-lines');
var wpPot = require('gulp-wp-pot');
var sort = require('gulp-sort');



gulp.task('zip', (done) => {
    gulp.src('dist/**/*')
        .pipe(rename(function (file) {
            file.dirname = project + '/' + file.dirname;
        }))
        .pipe(zip(project + '-free.zip'))
        .pipe(gulp.dest('zipped'))
    done()
});


gulp.task('clean', () => {
    return del([
        'dist/**/sass/',
        'dist/**/*.css.map',
        'dist/composer.*',
        'dist/vendor/bin/',
        'dist/vendor/squizlabs/',
        'dist/vendor/wp-coding-standards/',
        'dist/vendor/**/.git*',
        'dist/vendor/**/.git*',
        'dist/vendor/**/.travis.yml',
        'dist/vendor/**/.codeclimate.yml',
        'dist/vendor/**/composer.json',
        'dist/vendor/**/package.json',
        'dist/vendor/**/gulpfile.js',
        'dist/vendor/**/*.md'
    ]);
});


gulp.task('sync', () => {
    return gulp.src('.', {allowEmpty: true})
        .pipe(dirSync('meet-my-team', 'dist', {printSummary: true}))
        .on('error', gutil.log);
});

gulp.task('translate', () => {
    return gulp.src(['meet-my-team/**/*.php', '!meet-my-team/{vendor,vendor/**}'])
        .pipe(sort())
        .pipe(wpPot({
            domain: project,
            package: project
        }))
        .on('error', gutil.log)
        .pipe(gulp.dest('meet-my-team/languages/' + project + '.pot'))
        .pipe(gulp.dest('dist/languages/' + project + '.pot'));

});


gulp.task('build', gulp.series('sync', 'clean', 'translate', 'zip'));