const path = require("path");
let mix = require("laravel-mix");

require("./nova.mix");

mix.alias({
    "@": path.join(__dirname, "/vendor/laravel/nova/resources/js/"),
})
    .webpackConfig({ output: { uniqueName: "laravel/nova" } })
    .setPublicPath("dist")
    .js("resources/js/field.js", "js")
    .vue({ version: 3 })
    .css("resources/css/field.css", "css")
    .nova("ardenthq/nova-enhanced-markdown");
