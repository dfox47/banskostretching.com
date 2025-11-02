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

let remoteTheme           = '/public_html/wp-content/themes/banskostretching/'
let remoteCss             = remoteTheme + 'css/'
let remoteJs              = remoteTheme + 'js/'
let remoteTemplateParts   = remoteTheme + 'template-parts/'
let remoteIncludes        = remoteTheme + 'includes/'

let localTheme            = 'wp-content/themes/banskostretching/'
let localCss              = localTheme + 'css/'
let localJs               = localTheme + 'js/'
let localTemplateParts    = localTheme + 'template-parts/'
let localIncludes         = localTheme + 'includes/'

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

gulp.task('js', function () {
  return (
    gulp
      .src([
        localJs + '**/*.js',
      ])
      .pipe(concat('all.js'))
      // .pipe(uglify())
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

gulp.task('watch', function () {
  gulp.watch(localCss + '**/*',               gulp.series('css', 'copy_css'))
  gulp.watch(localIncludes + '**/*',          gulp.series('copy_includes'))
  gulp.watch(localJs + '**/*.js',             gulp.series('js', 'copy_js'))
  gulp.watch(localTemplateParts + '**/*',     gulp.series('copy_template_parts'))
  gulp.watch(localTheme + '*.php',            gulp.series('copy_html'))
})

gulp.task('default', gulp.series('watch'))
