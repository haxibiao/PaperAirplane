const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js").postCss(
    "resources/css/app.css",
    "public/css",
    [
        //
    ]
);

// 后台登陆页面
mix.js("resources/js/pages/login/index.js", "public/js/login.js")
    .react()
    .sass("resources/js/pages/login/scss/index.scss", "public/css/login.css");

// 后台管理页面
mix.js("resources/js/pages/admin/index.js", "public/js/admin.js")
    .react()
    .sass("resources/js/pages/admin/scss/index.scss", "public/css/admin.css");

// 用户订阅管理页面
mix.js("resources/js/pages/subscribe/index.js", "public/js/subscribe.js")
    .react()
    .sass(
        "resources/js/pages/subscribe/scss/index.scss",
        "public/css/subscribe.css"
    );
