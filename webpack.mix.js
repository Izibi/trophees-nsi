var mix = require('laravel-mix');

mix.setPublicPath('public')
    .js('resources/js/app.js', 'public/js')
    .extract(
        [
            'jquery',
            'popper.js',
            'bootstrap'
        ],
        'public/js/vendor.js'
    )
    .sass('resources/css/app.scss', 'public/css')
    .version();    