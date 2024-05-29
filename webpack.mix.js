const mix = require('laravel-mix');

mix.options({manifest: false}).js('assets/js/app.js', 'dist').sass('assets/scss/app.scss', 'dist');
