const gulp = require( 'gulp' );
const clean = require( 'gulp-clean' );
const concatCss = require( 'gulp-concat-css' );
const postcss = require( 'gulp-postcss' );
const rename = require( 'gulp-rename' );
const cssnano = require( 'cssnano' );
const gulpnano = require( 'gulp-cssnano' );

//const { src, dest, watch } = require("gulp");
const sass = require("gulp-sass")(require('sass'));
const sourcemaps = require("gulp-sourcemaps");
const uglify = require("gulp-uglify");

gulp.task( 'watch', function () {
	gulp.watch( [ 'assets/css/**/*.css', 'assets/css/*.scss', 'assets/css/blocks/*.scss', 'assets/css/theme/**/*.scss', 'assets/js/**/*.js' ] ).on(
		'change',
		gulp.series(
			'clean-shared',
			'clean-blocks',
			'minify-shared',
			'minify-blocks',
      'clean-scripts',
      'scripts',
      'styles'
		)
	);
} );

gulp.task( 'clean-shared', function () {
	return gulp
		.src( 'assets/css/style-shared.min.css', {
			read: false,
			allowEmpty: true,
		} )
		.pipe( clean() );
} );

gulp.task( 'clean-blocks', function () {
	return gulp
		.src( 'assets/css/blocks/*.min.css', {
			read: false,
			allowEmpty: true,
		} )
		.pipe( clean() );
} );

gulp.task('clean-scripts', function () {
  return gulp.src('./assets/js/dist/', {read: false, allowEmpty: true})
    .pipe(clean());
});

gulp.task('clean-custom-blocks', function () {
  return gulp.src('./inc/blocks/*/block.min.css', {read: false, allowEmpty: true})
    .pipe(clean());
});

gulp.task( 'minify-shared', function () {
	return gulp
		.src( 'assets/css/*.css' )
		.pipe( concatCss( 'style-shared.min.css' ) )
		//.pipe( postcss( cssnano() ) )
    .pipe(gulpnano())
		.pipe( gulp.dest( 'assets/css/' ) );
} );
/*
gulp.task( 'minify-blocks', function () {
	return gulp
		.src( 'assets/css/blocks/*.css' )
		.pipe( postcss( cssnano() ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'assets/css/blocks' ) );
} );
*/

gulp.task( 'minify-blocks', function () {
	return gulp.src( './assets/css/blocks/*.scss', { base: "./assets/css/blocks"} )
    .pipe(sass({
      outputStyle: "expanded",
      includePaths: ["node_modules/susy/sass"]
    }).on("error", sass.logError))
    .pipe(gulpnano())
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( './assets/css/blocks' ) );
} );

gulp.task( 'minify-custom-blocks', function () {
	return gulp.src( './inc/blocks/*/*.scss', { base: "./inc/blocks/*/"} )
    .pipe(sass({
      outputStyle: "expanded",
      includePaths: ["node_modules/susy/sass"]
    }).on("error", sass.logError))
    .pipe(gulpnano())
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( './inc/blocks/*/' ) );
} );
/* Process main scss, autoprefix, save save expanded to dev, minified to build */
gulp.task("styles", () => {
  return gulp.src("./assets/css/*.scss", { base: "./assets/css"})
      .pipe(sourcemaps.init())
      .pipe(sass({
        outputStyle: "expanded",
        includePaths: ["node_modules/susy/sass"]
      }).on("error", sass.logError))
      //.pipe(autoprefixer())
      .pipe(gulp.dest("./assets/css/theme/dev/" ))
      //.pipe(rename({suffix:".min"}))
      .pipe(gulpnano())
      .pipe(sourcemaps.write("./maps"))
      .pipe(gulp.dest("./" ));
});

/* Process and minify js */
gulp.task("scripts", function() {
  return gulp.src("./assets/js/**/*.js", { base: "./assets/js"})
		.pipe(rename({suffix:".min"}))
    .pipe(uglify())
    .pipe(gulp.dest("./assets/js/dist/"))
});



gulp.task(
	'default',
	gulp.series(
		'clean-shared',
		'clean-blocks',
		'minify-shared',
		'minify-blocks',
    'clean-custom-blocks',
    'minify-custom-blocks',
    'clean-scripts',
    'scripts',
    'styles',
	)
);
