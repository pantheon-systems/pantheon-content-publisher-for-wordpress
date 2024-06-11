const mix = require('laravel-mix');
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");
const tailwindcss = require('tailwindcss');
mix.options({
  manifest: false,
  legacyNodePolyfills: true
}).webpackConfig({
  plugins: [
    new NodePolyfillPlugin(),
  ],
  resolve: {
    fallback: {
      fs: false,
      child_process: false
    }
  }
}).js('assets/js/app.js', 'dist').postCss('assets/css/app.css', 'dist', [
  tailwindcss('./tailwind.config.js'),
]);
