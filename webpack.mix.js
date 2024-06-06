const mix = require('laravel-mix');
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");
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
}).js('assets/js/app.js', 'dist').sass('assets/scss/app.scss', 'dist');
