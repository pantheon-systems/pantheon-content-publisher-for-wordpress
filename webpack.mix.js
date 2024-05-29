const mix = require('laravel-mix');

mix.js('assets/js/app.js', 'dist')
	.sass('assets/scss/app.scss', 'dist')
	.setPublicPath('dist');
