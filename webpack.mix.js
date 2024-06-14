const mix = require('laravel-mix');
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");
const tailwindcss = require('tailwindcss');
const postCssPrefixSelector = require('postcss-prefix-selector');

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
  postCssPrefixSelector({
    prefix: '.pcc-content', // Replace with your desired class
    transform(prefix, selector, prefixedSelector) {
      // Exclude 'html' and 'body' tags from being prefixed
      if ( selector === '.pcc-content' || selector === '.pcc-notice' ) {
        return selector;
      }
      return prefixedSelector;
    },
  }),
]);
