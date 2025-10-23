// npm install -g gulp-cli
// npm install gulp vinyl-ftp gulp-util gulp-concat gulp-csso gulp-rename gulp-sass gulp-uglify --save-dev

const fs            = require('fs')
const concat        = require('gulp-concat')
const config        = JSON.parse(fs.readFileSync('../config.json'))
const cssMinify     = require('gulp-csso')
const ftp           = require('vinyl-ftp')
const gulp          = require('gulp')
const gutil         = require('gulp-util')
const rename        = require('gulp-rename')
const sass          = require('gulp-sass')(require('sass'))
const uglify        = require('gulp-uglify')

// FTP config
const host          = config.host
const password      = config.password
const port          = config.port
const user          = config.user

const remoteTheme           = '/public_html/wp-content/themes/banskostretching/'
const remoteCss             = remoteTheme + 'css/'
const remoteJs              = remoteTheme + 'js/'
const remoteTemplateParts   = remoteTheme + 'template-parts/'
const remoteTranslations    = remoteTheme + 'translations/'

const localTheme            = 'wp-content/themes/banskostretching/'
const localCss              = localTheme + 'css/'
const localJs               = localTheme + 'js/'
const localTemplateParts    = localTheme + 'template-parts/'
const localTranslations     = localTheme + 'translations/'

function getFtpConnection() {
  return ftp.create({
    host:           host,
    log:            gutil.log,
    password:       password,
    parallel:       3,
    port:           port,
    timeout:        99999999,
    user:           user
  });
}

const conn = getFtpConnection()



gulp.task('dist', function () {
  return gulp.src(localTheme + '**/*')
    .pipe(conn.dest(localTheme))
})

gulp.task('css', function () {
  return gulp.src(localCss + 'styles.scss')
    .pipe(sass())
    .pipe(cssMinify())
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(conn.dest(remoteTheme))
})

gulp.task('copy_css', function () {
  return gulp.src(localCss + '**/*')
    .pipe(conn.dest(remoteCss))
})

gulp.task('copy_html', function () {
  return gulp.src(localTheme + '*.php')
    .pipe(conn.dest(remoteTheme))
})

gulp.task('copy_template_parts', function () {
  return gulp.src(localTemplateParts + '*.php')
    .pipe(conn.dest(remoteTemplateParts))
})

gulp.task('copy_translations', function () {
  return gulp.src(localTranslations + '**/*')
    .pipe(conn.dest(remoteTranslations))
})

gulp.task('js', function () {
  return gulp.src([
    // localJs + 'jquery-3.7.1.min.js',
    localJs + '**/*.js'
  ])
    .pipe(concat('all.js'))
    // .pipe(uglify())
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(conn.dest(remoteTheme))
})

gulp.task('copy_js', function () {
  return gulp.src(localJs + '**/*.js')
    .pipe(conn.dest(remoteJs))
})

gulp.task('watch', function() {
  gulp.watch(localTheme + '*.php',              gulp.series('copy_html'))
  gulp.watch(localCss + '**/*',                 gulp.series('css', 'copy_css'))
  gulp.watch(localJs + '**/*.js',               gulp.series('js', 'copy_js'))
  gulp.watch(localTemplateParts + '**/*.php',   gulp.series('copy_template_parts'))
  gulp.watch(localTranslations + '**/*',        gulp.series('copy_translations'))
})

gulp.task('default', gulp.series('watch'))