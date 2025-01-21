let gulp            = require('gulp'),
    replace         = require('gulp-batch-replace'),
    filesExist      = require('files-exist');

gulp.task('bt4', () =>
{
    let diff = {

        '@media (min-width: $screen-xs-min) and (max-width: $screen-sm-max)': '@media (min-width: map-get($grid-breakpoints, xs)) and (max-width: map-get($grid-breakpoints, xs))',
        '@media (min-width: $screen-xs) and (max-width: ($screen-md-min - 1))': '@media (min-width: map-get($grid-breakpoints, xs)) and (max-width: map-get($grid-breakpoints, md)-1)',
        '@media (min-width: $screen-sm-min) and (max-width: $screen-sm-max)': '@include media-breakpoint-only(sm)',

        '@media (min-width: $screen-xs-min)':       '@include media-breakpoint-up(xs)',
        '@media (min-width: $screen-sm)':           '@include media-breakpoint-up(sm)',
        '@media (min-width: $screen-sm-min)':       '@include media-breakpoint-up(sm)',
        '@media (min-width: $screen-md-min)':       '@include media-breakpoint-up(md)',
        '@media (min-width: $screen-md)':           '@include media-breakpoint-up(md)',
        '@media (min-width: $screen-md-max)':       '@include media-breakpoint-up(md)',
        '@media (min-width: $screen-lg-min)':       '@include media-breakpoint-up(lg)',

        '@media (max-width: ($screen-xs-min - 1))': '@include media-breakpoint-down(xs)',
        '@media (max-width: $screen-xs-max)':       '@include media-breakpoint-down(xs)',
        '@media (max-width: ($screen-sm-min - 1))': '@include media-breakpoint-down(sm)',
        '@media (max-width: $screen-sm)':           '@include media-breakpoint-down(sm)',
        '@media (max-width: $screen-sm-min)':       '@include media-breakpoint-down(sm)',
        '@media (max-width: $screen-sm-max)':       '@include media-breakpoint-down(sm)',
        '@media (max-width: $screen-md-max)':       '@include media-breakpoint-down(md)',
        '@media (max-width: $screen-lg-max)':       '@include media-breakpoint-down(lg)',

        '@media (max-width: $screen-xs-min - 1)':   '@include media-breakpoint-down(xs)',
        '@media (max-width: $screen-md-min)':       '@include media-breakpoint-down(md)',

        // bootstrap 2
        '@media (max-width: $screen-xxs)':          '@include media-breakpoint-down(xs)',

        '.col-*-offset-*':	        '.offset-*',
        '.col-*-push-*':	        '.order-*-2',
        '.col-*-pull-*':	        '.order-*-1',
        '.panel':	                '.card',
        '.panel-heading':	        '.card-header',
        '.panel-title':             '.card-title',
        '.panel-body':              '.card-body',
        '.panel-footer':	        '.card-footer',
        '.panel-primary':	        '.card.bg-primary.text-white',
        '.panel-success':	        '.card.bg-success.text-white',
        '.panel-info':	            '.card.text-white.bg-info',
        '.panel-warning':	        '.card.bg-warning',
        '.panel-danger':	        '.card.bg-danger.text-white',
        '.well':	                '.card.card-body',
        '.thumbnail':	            '.card.card-body',
        '.list-inline > li':	    '.list-inline-item',
        '.dropdown-menu > li':	    '.dropdown-item',
        '.nav navbar > li':	        '.nav-item',
        '.nav navbar > li > a':	    '.nav-link',
        '.navbar-right':	        '.ml-auto',
        '.navbar-btn':	            '.nav-item',
        '.navbar-fixed-top':        '.fixed-top',
        '.nav-stacked':             '.flex-column',
        '.btn-default':             '.btn-secondary',
        '.img-responsive':          '.img-fluid',
        '.img-circle':              '.rounded-circle',
        '.img-rounded':             '.rounded',
        //'.form-horizontal':         '', // @note: removed
        '.radio':                   '.form-check',
        '.checkbox':                '.form-check',
        '.input-lg':                '.form-control-lg',
        '.input-sm':                '.form-control-sm',
        '.control-label':           '.form-control-label',
        '.table-condensed':         '.table-sm',
        '.pagination > li':         '.page-item',
        '.pagination > li > a':     '.page-link',
        //'.item':                     '.carousel-item', // @note: this is too much basic word
        '.text-help':               '.form-control-feedback',
        '.pull-right':              '.float-right',
        '.pull-left':               '.float-left',
        '.center-block':            '.mx-auto',
        '.hidden-xs':               '.d-none',
        '.hidden-sm':               '.d-sm-none',
        '.hidden-md':               '.d-md-none',
        '.visible-xs':              '.d-block.d-sm-none',
        '.visible-sm':              '.d-block.d-md-none',
        '.visible-md':              '.d-block.d-lg-none',
        '.visible-lg':              '.d-block.d-xl-none',
        '.label':                   '.badge',
        '.badge':                   '.badge.badge-pill',

        // twig

        'col-xs-':                  'col-',
        'col-md-':                  'col-lg-',
        'col-sm-':                  'col-md-'
    };

    let replaceThis = [];
    Object.keys(diff).forEach(function(key)
    {
        replaceThis.push([key, diff[key]]);
    });

    return gulp
        .src(filesExist('./scss/**'))
        .pipe(replace(replaceThis))
        .pipe(gulp.dest('./build/scss'));
});
