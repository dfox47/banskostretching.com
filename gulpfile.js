// npm install -g gulp-cli
// npm install gulp vinyl-ftp gulp-concat gulp-csso gulp-rename gulp-sass gulp-uglify --save-dev

const fs            = require('fs')
const config        = JSON.parse(fs.readFileSync('../config.json'))
let cssMinify       = require('gulp-csso')
let concat          = require('gulp-concat')
let ftp             = require('vinyl-ftp')
let gulp            = require('gulp')
let gutil           = require('gulp-util')
let rename          = require('gulp-rename')
// let sass                = require('gulp-sass')
let sass            = require('gulp-sass')(require('sass'))
let uglify          = require('gulp-uglify')

// FTP config
const host          = config.host
const password      = config.password
const port          = config.port
const user          = config.user

const remoteTheme           = '/public_html/wp-content/themes/banskostretching/'
const remoteCss             = remoteTheme + 'css/'
const remoteFunctionsParts  = remoteTheme + 'functions-parts/'
const remoteIncludes        = remoteTheme + 'includes/'
const remoteJs              = remoteTheme + 'js/'
const remoteTemplateParts   = remoteTheme + 'template-parts/'
const remoteTranslations    = remoteTheme + 'translations/'
const remoteWoocommerce     = remoteTheme + 'woocommerce/'

const localTheme            = 'wp-content/themes/banskostretching/'
const localCss              = localTheme + 'css/'
const localFunctionsParts   = localTheme + 'functions-parts/'
const localIncludes         = localTheme + 'includes/'
const localJs               = localTheme + 'js/'
const localTemplateParts    = localTheme + 'template-parts/'
const localTranslations     = localTheme + 'translations/'
const localWoocommerce      = localTheme + 'woocommerce/'

function getFtpConnection() {
  return ftp.create({
    host: host,
    log: gutil.log,
    password: password,
    parallel: 3,
    port: port,
    user: user,
  });
}

let conn = getFtpConnection()

gulp.task('copy_html', function () {
  return gulp.src(localTheme + '*.php').pipe(conn.dest(remoteTheme))
})

gulp.task('css', function () {
  return gulp
    .src(localCss + 'styles.scss')
    .pipe(sass())
    .pipe(cssMinify())
    .pipe(
      rename({
        basename: 'styles',
      })
    )
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(conn.dest(remoteTheme))
})

gulp.task('copy_css', function () {
  return gulp.src(localCss + '**/*').pipe(conn.dest(remoteCss))
})

gulp.task('copy_includes', function () {
  return gulp.src(localIncludes + '**/*').pipe(conn.dest(remoteIncludes))
})

gulp.task('copy_js', function () {
  return gulp.src(localJs + '**/*.js').pipe(conn.dest(remoteJs))
})

gulp.task('copy_translations', function () {
  return gulp.src(localTranslations + '**/*').pipe(conn.dest(remoteTranslations))
})

gulp.task('js', function () {
  return (
    gulp
      .src([
        localJs + '**/*.js',
      ])
      .pipe(concat('all.js'))
      .pipe(uglify())
      .pipe(
        rename({
          suffix: '.min',
        })
      )
      .pipe(conn.dest(remoteTheme))
  )
})

gulp.task('copy_template_parts', function () {
  return gulp.src(localTemplateParts + '**/*').pipe(conn.dest(remoteTemplateParts))
})

gulp.task('copy_functions_parts', function () {
  return gulp.src(localFunctionsParts + '**/*').pipe(conn.dest(remoteFunctionsParts))
})

gulp.task('copy_woocommerce', function () {
  return gulp.src(localWoocommerce + '**/*').pipe(conn.dest(remoteWoocommerce))
})

gulp.task('watch', function () {
  gulp.watch(localCss + '**/*',               gulp.series('css', 'copy_css'))
  gulp.watch(localFunctionsParts + '**/*',    gulp.series('copy_functions_parts'))
  gulp.watch(localIncludes + '**/*',          gulp.series('copy_includes'))
  gulp.watch(localJs + '**/*.js',             gulp.series('js', 'copy_js'))
  gulp.watch(localTemplateParts + '**/*',     gulp.series('copy_template_parts'))
  gulp.watch(localTheme + '*.php',            gulp.series('copy_html'))
  gulp.watch(localTranslations,               gulp.series('copy_translations'))
  gulp.watch(localWoocommerce,                gulp.series('copy_woocommerce'))
})

gulp.task('default', gulp.series('watch'))
