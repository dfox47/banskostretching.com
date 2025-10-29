let cssMinify = require("gulp-csso");
let concat = require("gulp-concat");
let ftp = require("vinyl-ftp");
let gulp = require("gulp");
let gutil = require("gulp-util");
let rename = require("gulp-rename");
// let sass                = require('gulp-sass');
let sass = require("gulp-sass")(require("sass"));
let uglify = require("gulp-uglify");

// FTP config
let user = "u608786266.banskostretching.com";
let password = "b_eTVP$7U-'%z}qx.Sf])d";
let host = "195.35.49.50";
let port = 21;

let remoteFolder = "/public_html/wp-content/themes/banskostretching/";
let remoteFolderCss = remoteFolder + "css/";
let remoteFolderJs = remoteFolder + "js/";

let localFolder = "wp-content/themes/banskostretching/";
let localFolderCss = localFolder + "css/";
let localFolderJs = localFolder + "js/";

function getFtpConnection() {
  return ftp.create({
    host: host,
    log: gutil.log,
    password: password,
    parallel: 1,
    port: port,
    user: user,
  });
}

let conn = getFtpConnection();

gulp.task("copy_html", function () {
  return gulp.src(localFolder + "*.php").pipe(conn.dest(remoteFolder));
});

gulp.task("copy_template_parts", function () {
  return gulp.src(localFolder + "template-parts/**/*").pipe(conn.dest(remoteFolder + "template-parts/"));
});

gulp.task("css", function () {
  return gulp
    .src(localFolderCss + "styles.scss")
    .pipe(sass())
    .pipe(cssMinify())
   
    .pipe(
      rename({
        basename: "styles",
      })
    )
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(conn.dest(remoteFolder));
});

gulp.task("copy_css", function () {
  return gulp.src(localFolderCss + "**/*").pipe(conn.dest(remoteFolderCss));
});

gulp.task("copy_js", function () {
  return gulp.src(localFolderJs + "**/*.js").pipe(conn.dest(remoteFolderJs));
});

gulp.task("js", function () {
  return (
    gulp
      .src([
        localFolderJs + "**/*.js",
      ])
      .pipe(concat("all.js"))
      // .pipe(uglify())
      .pipe(
        rename({
          suffix: ".min",
        })
      )
      .pipe(conn.dest(remoteFolder))
  );
});

gulp.task("watch", function () {
  gulp.watch(localFolder + "*.php", gulp.series("copy_html"));
  gulp.watch(localFolderCss + "**/*", gulp.series("css", "copy_css"));
  gulp.watch(localFolderJs + "**/*.js", gulp.series("js", "copy_js"));
  gulp.watch(localFolder + "template-parts/**/*", gulp.series("copy_template_parts"));
});

gulp.task("default", gulp.series("watch"));
