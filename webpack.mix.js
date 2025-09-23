const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css')
   .postCss('resources/css/layout.css', 'public/css')
   .copy('node_modules/bootstrap-icons/font/bootstrap-icons.css', 'public/css/bootstrap-icons.css')
   .copy('node_modules/bootstrap-icons/font/fonts', 'public/fonts');
