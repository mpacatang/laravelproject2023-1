const mix = require('laravel-mix');
const path = require('path')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */




mix.webpackConfig({

    resolve: {
        fallback: {
            "fs": false
        },
        alias: {
            '@': path.resolve('resources'),
            '@js': __dirname + '/resources/js',
            '@docs': __dirname + '/resources/js/pages/docs'
        }
    }
})
    .copyDirectory('resources/assets/', 'public/assets/')
    .ts('resources/js/app.ts', 'public/js')
    .vue({version: 3, })
    .sass('resources/scss/app.scss', 'public/css',)
    // .sass('resources/sass/app-rtl.scss', 'public/css', {}, [
    //     require('rtlcss')()
    // ])
    .sass('resources/scss/icons.scss', 'public/css',)
    .copy('resources/css/', 'public/css/');

if(process.env.NODE_ENV==='production'){
    mix.sass('resources/scss/app-rtl.scss', 'public/css', {}, [
        require('rtlcss')()
    ])
}

// mix.minify('public/js/app.js')

mix.disableNotifications();
